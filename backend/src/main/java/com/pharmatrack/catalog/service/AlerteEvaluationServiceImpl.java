package com.pharmatrack.catalog.service;

import com.pharmatrack.catalog.dto.AlerteEvaluationSummary;
import com.pharmatrack.catalog.dto.StockSummary;
import com.pharmatrack.catalog.entity.Alerte;
import com.pharmatrack.catalog.entity.AlerteNiveau;
import com.pharmatrack.catalog.entity.AlerteType;
import com.pharmatrack.catalog.entity.Lot;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.catalog.entity.MedicamentStatut;
import com.pharmatrack.catalog.repository.AlerteRepository;
import com.pharmatrack.catalog.repository.LotRepository;
import com.pharmatrack.catalog.repository.MedicamentRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;
import java.time.LocalDate;
import java.time.temporal.ChronoUnit;
import java.util.HashMap;
import java.util.HashSet;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.UUID;

/**
 * Evaluates the three alert rules in a single pass of SQL aggregation:
 * <ul>
 *   <li><b>rupture</b> — medicament with zero active stock (critical alert);</li>
 *   <li><b>stock</b> — active stock above zero but below {@code stock_min};</li>
 *   <li><b>expiration</b> — active lot expiring within 30 days (critical ≤ 7 days).</li>
 * </ul>
 *
 * <p>Existing open alerts are reused: expiration alerts are de-duplicated per
 * lot (mirrored by the partial unique index), stock/rupture alerts per
 * medicament (looked up by id in the JSON payload). Re-runs are therefore
 * idempotent and only report freshly-created alerts in the summary.
 */
@Service
public class AlerteEvaluationServiceImpl implements AlerteEvaluationService {

    static final int EXPIRY_HORIZON_DAYS = 30;
    static final int EXPIRY_CRITIQUE_DAYS = 7;

    private final MedicamentRepository medicamentRepository;
    private final LotRepository lotRepository;
    private final AlerteRepository alerteRepository;

    public AlerteEvaluationServiceImpl(
            MedicamentRepository medicamentRepository,
            LotRepository lotRepository,
            AlerteRepository alerteRepository) {
        this.medicamentRepository = medicamentRepository;
        this.lotRepository = lotRepository;
        this.alerteRepository = alerteRepository;
    }

    @Override
    @Transactional
    public AlerteEvaluationSummary reEvaluate() {
        int ruptures = verifierRuptures();
        int stocksFaibles = verifierStocksFaibles();
        int expirations = verifierExpirations(LocalDate.now());
        return new AlerteEvaluationSummary(
                ruptures, expirations, stocksFaibles,
                ruptures + expirations + stocksFaibles,
                Instant.now());
    }

    private int verifierRuptures() {
        List<Medicament> medicaments = medicamentRepository
                .findAllNonRetired(MedicamentStatut.retire);
        Map<UUID, Long> stockActif = chargeStockActif();
        Set<UUID> dejaAlertes = alertesMedicamentsOuvertes(AlerteType.rupture);
        int count = 0;

        for (Medicament m : medicaments) {
            long stock = stockActif.getOrDefault(m.getId(), 0L);
            if (stock != 0 || m.getStockMin() == null || m.getStockMin() <= 0) {
                continue;
            }
            if (!dejaAlertes.add(m.getId())) {
                continue;
            }
            Map<String, Object> donnees = new LinkedHashMap<>();
            donnees.put("medicament_id", m.getId().toString());
            donnees.put("nom_medicament", m.getNomCommercialFr());
            donnees.put("stock_actuel", 0);
            donnees.put("stock_min", m.getStockMin());
            alerteRepository.save(newAlerte(AlerteType.rupture, AlerteNiveau.critique,
                    "Rupture de stock : " + m.getNomCommercialFr(), null, donnees));
            count++;
        }
        return count;
    }

    private int verifierStocksFaibles() {
        List<Medicament> medicaments = medicamentRepository
                .findAllNonRetired(MedicamentStatut.retire);
        Map<UUID, Long> stockActif = chargeStockActif();
        Set<UUID> dejaAlertes = alertesMedicamentsOuvertes(AlerteType.stock);
        int count = 0;

        for (Medicament m : medicaments) {
            long stock = stockActif.getOrDefault(m.getId(), 0L);
            if (stock <= 0 || m.getStockMin() == null || m.getStockMin() <= 0) {
                continue;
            }
            if (stock >= m.getStockMin()) {
                continue;
            }
            if (!dejaAlertes.add(m.getId())) {
                continue;
            }
            Map<String, Object> donnees = new LinkedHashMap<>();
            donnees.put("medicament_id", m.getId().toString());
            donnees.put("nom_medicament", m.getNomCommercialFr());
            donnees.put("stock_actuel", stock);
            donnees.put("stock_min", m.getStockMin());
            alerteRepository.save(newAlerte(AlerteType.stock, AlerteNiveau.eleve,
                    "Stock faible pour " + m.getNomCommercialFr() + " : " + stock
                            + " / " + m.getStockMin(),
                    null, donnees));
            count++;
        }
        return count;
    }

    private int verifierExpirations(LocalDate today) {
        LocalDate horizon = today.plusDays(EXPIRY_HORIZON_DAYS);
        List<Lot> lots = lotRepository.findExpiringBetween(LotStatut.actif, today, horizon);
        if (lots.isEmpty()) {
            return 0;
        }
        Set<UUID> lotsAlerte = new HashSet<>(alerteRepository
                .findOpenForLots(AlerteType.expiration,
                        lots.stream().map(Lot::getId).toList())
                .stream()
                .map(a -> a.getLot().getId())
                .toList());

        int count = 0;
        for (Lot lot : lots) {
            long jours = ChronoUnit.DAYS.between(today, lot.getDatePeremption());
            if (jours < 0 || !lotsAlerte.add(lot.getId())) {
                continue;
            }
            AlerteNiveau niveau = jours <= EXPIRY_CRITIQUE_DAYS
                    ? AlerteNiveau.critique : AlerteNiveau.eleve;
            Map<String, Object> donnees = new LinkedHashMap<>();
            donnees.put("lot_id", lot.getId().toString());
            donnees.put("medicament_id", lot.getMedicament().getId().toString());
            donnees.put("numero_lot", lot.getNumeroLot());
            donnees.put("date_peremption", lot.getDatePeremption().toString());
            donnees.put("jours_restants", jours);
            alerteRepository.save(newAlerte(AlerteType.expiration, niveau,
                    "Le lot " + lot.getNumeroLot() + " expire dans " + jours + " jours",
                    lot, donnees));
            count++;
        }
        return count;
    }

    private Map<UUID, Long> chargeStockActif() {
        Map<UUID, Long> stock = new HashMap<>();
        for (StockSummary row : medicamentRepository.aggregateStockAll(
                LotStatut.actif, LotStatut.perime)) {
            stock.put(row.medicamentId(), row.stockActif());
        }
        return stock;
    }

    /**
     * medicament ids already covered by an open (unresolved) alert of the given
     * type — mirrors {@code uq_alertes_type_lot_open}: a slot is only freed by
     * {@code resolue_at}, not by the read flag.
     */
    private Set<UUID> alertesMedicamentsOuvertes(AlerteType type) {
        Set<UUID> ids = new HashSet<>();
        for (Alerte alerte : alerteRepository.findOpenByType(type)) {
            Object value = alerte.getDonneesConcernees().get("medicament_id");
            if (value == null) {
                continue;
            }
            try {
                ids.add(UUID.fromString(value.toString()));
            } catch (IllegalArgumentException ignored) {
                // jsonb payload without a parseable medicament_id
            }
        }
        return ids;
    }

    private Alerte newAlerte(AlerteType type, AlerteNiveau niveau, String message,
                             Lot lot, Map<String, Object> donnees) {
        Alerte alerte = new Alerte();
        alerte.setType(type);
        alerte.setNiveau(niveau);
        alerte.setMessage(message);
        alerte.setLot(lot);
        alerte.setDonneesConcernees(donnees);
        alerte.setEstLue(false);
        return alerte;
    }
}