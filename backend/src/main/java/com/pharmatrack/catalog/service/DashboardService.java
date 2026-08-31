package com.pharmatrack.catalog.service;

import com.pharmatrack.catalog.dto.DateCount;
import com.pharmatrack.catalog.dto.DashboardStatsResponse;
import com.pharmatrack.catalog.dto.LabelCount;
import com.pharmatrack.catalog.dto.StockSummary;
import com.pharmatrack.catalog.dto.TopMedicament;
import com.pharmatrack.catalog.entity.Alerte;
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
import java.time.ZoneOffset;
import java.util.Comparator;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.UUID;
import java.util.stream.Collectors;

/**
 * Single dashboard aggregation endpoint. All chart datasets come from a small
 * number of bounded repository reads, with grouping done in memory (the data
 * volumes here are small and bounded; stock figures still use SQL GROUP BY).
 */
@Service
public class DashboardService {

    private static final int TOP_MEDICAMENTS = 10;
    private static final int EXPIRY_WINDOW_DAYS = 30;
    private static final int MEDICAMENT_EVOLUTION_DAYS = 30;
    private static final int ALERTE_EVOLUTION_DAYS = 7;
    private static final int TOP_LABELS = 8;

    private final MedicamentRepository medicamentRepository;
    private final LotRepository lotRepository;
    private final AlerteRepository alerteRepository;

    public DashboardService(MedicamentRepository medicamentRepository,
                            LotRepository lotRepository,
                            AlerteRepository alerteRepository) {
        this.medicamentRepository = medicamentRepository;
        this.lotRepository = lotRepository;
        this.alerteRepository = alerteRepository;
    }

    @Transactional(readOnly = true)
    public DashboardStatsResponse stats() {
        LocalDate today = LocalDate.now(ZoneOffset.UTC);
        List<Medicament> medicaments = medicamentRepository.findAll();
        List<Lot> lots = lotRepository.findAll();
        List<Alerte> alertes = alerteRepository.findAll();

        List<Medicament> actifs = medicaments.stream()
                .filter(m -> m.getStatut() != MedicamentStatut.retire)
                .toList();

        return new DashboardStatsResponse(
                actifs.size(),
                medicamentRepository.countEnRupture(
                        MedicamentStatut.actif, LotStatut.actif),
                alerteRepository.countByEstLueFalse(),
                lotsWithinWindow(lots, today),
                statutMedicaments(medicaments),
                statutLots(lots),
                alertesParType(alertes),
                topLabels(medicaments, m -> m.getClasseTherapeutique()),
                topLabels(medicaments, m -> m.getLaboratoire()),
                topLabels(medicaments, m -> m.getFormePharmaceutique()),
                topMedicaments(actifs),
                evolution(today, MEDICAMENT_EVOLUTION_DAYS, medicaments.stream()
                        .map(Medicament::getCreatedAt).toList()),
                lotsExpiration(lots, today),
                evolution(today, ALERTE_EVOLUTION_DAYS, alertes.stream()
                        .map(Alerte::getCreatedAt).toList()));
    }

    private long lotsWithinWindow(List<Lot> lots, LocalDate today) {
        LocalDate horizon = today.plusDays(EXPIRY_WINDOW_DAYS);
        return lots.stream()
                .filter(l -> l.getStatut() == LotStatut.actif)
                .filter(l -> l.getQuantiteActuelle() > 0)
                .filter(l -> !l.getDatePeremption().isBefore(today))
                .filter(l -> !l.getDatePeremption().isAfter(horizon))
                .count();
    }

    private Map<String, Long> statutMedicaments(List<Medicament> medicaments) {
        return medicaments.stream().collect(Collectors.groupingBy(
                m -> m.getStatut().name(), LinkedHashMap::new, Collectors.counting()));
    }

    private Map<String, Long> statutLots(List<Lot> lots) {
        return lots.stream().collect(Collectors.groupingBy(
                l -> l.getStatut().name(), LinkedHashMap::new, Collectors.counting()));
    }

    private Map<String, Long> alertesParType(List<Alerte> alertes) {
        return alertes.stream().collect(Collectors.groupingBy(
                a -> a.getType().name(), LinkedHashMap::new, Collectors.counting()));
    }

    private List<LabelCount> topLabels(List<Medicament> medicaments,
                                       java.util.function.Function<Medicament, String> extractor) {
        return medicaments.stream()
                .map(extractor)
                .filter(java.util.Objects::nonNull)
                .filter(label -> !label.isBlank())
                .collect(Collectors.groupingBy(label -> label, Collectors.counting()))
                .entrySet().stream()
                .sorted(Map.Entry.<String, Long>comparingByValue().reversed())
                .limit(TOP_LABELS)
                .map(e -> new LabelCount(e.getKey(), e.getValue()))
                .toList();
    }

    private List<TopMedicament> topMedicaments(List<Medicament> medicaments) {
        Map<UUID, Long> stock = new LinkedHashMap<>();
        for (StockSummary row : medicamentRepository.aggregateStockAll(
                LotStatut.actif, LotStatut.perime)) {
            stock.put(row.medicamentId(), row.stockActif());
        }
        return medicaments.stream()
                .filter(m -> stock.getOrDefault(m.getId(), 0L) > 0)
                .sorted(Comparator.comparingLong((Medicament m) -> stock.get(m.getId())).reversed())
                .limit(TOP_MEDICAMENTS)
                .map(m -> new TopMedicament(m.getId(), m.getNomCommercialFr(), stock.get(m.getId())))
                .toList();
    }

    private List<DateCount> evolution(LocalDate today, int days, List<Instant> instants) {
        LocalDate from = today.minusDays(days - 1L);
        return instants.stream()
                .filter(java.util.Objects::nonNull)
                .map(instant -> instant.atZone(ZoneOffset.UTC).toLocalDate())
                .filter(date -> !date.isBefore(from))
                .collect(Collectors.groupingBy(date -> date,
                        LinkedHashMap::new, Collectors.counting()))
                .entrySet().stream()
                .sorted(Map.Entry.comparingByKey())
                .map(e -> new DateCount(e.getKey(), e.getValue()))
                .toList();
    }

    private List<DateCount> lotsExpiration(List<Lot> lots, LocalDate today) {
        LocalDate horizon = today.plusDays(EXPIRY_WINDOW_DAYS);
        return lots.stream()
                .filter(l -> l.getStatut() == LotStatut.actif)
                .filter(l -> l.getQuantiteActuelle() > 0)
                .filter(l -> !l.getDatePeremption().isBefore(today))
                .filter(l -> !l.getDatePeremption().isAfter(horizon))
                .collect(Collectors.groupingBy(Lot::getDatePeremption,
                        LinkedHashMap::new, Collectors.counting()))
                .entrySet().stream()
                .sorted(Map.Entry.comparingByKey())
                .map(e -> new DateCount(e.getKey(), e.getValue()))
                .toList();
    }
}