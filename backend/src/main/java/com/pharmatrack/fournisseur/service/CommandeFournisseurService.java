package com.pharmatrack.fournisseur.service;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.repository.UserRepository;
import com.pharmatrack.catalog.entity.Alerte;
import com.pharmatrack.catalog.entity.AlerteNiveau;
import com.pharmatrack.catalog.entity.AlerteType;
import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.catalog.repository.AlerteRepository;
import com.pharmatrack.catalog.repository.MedicamentRepository;
import com.pharmatrack.common.api.PageUtils;
import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.error.ResourceNotFoundException;
import com.pharmatrack.fournisseur.dto.CommandeResponse;
import com.pharmatrack.fournisseur.dto.CommandeResult;
import com.pharmatrack.fournisseur.dto.DisponibiliteResponse;
import com.pharmatrack.fournisseur.dto.FournisseurMedicamentResponse;
import com.pharmatrack.fournisseur.dto.MedicamentSelectionResponse;
import com.pharmatrack.fournisseur.entity.CommandeFournisseur;
import com.pharmatrack.fournisseur.entity.CommandeFournisseurLigne;
import com.pharmatrack.fournisseur.entity.CommandeStatut;
import com.pharmatrack.fournisseur.entity.FournisseurMedicament;
import com.pharmatrack.fournisseur.mapper.FournisseurMapper;
import com.pharmatrack.fournisseur.repository.CommandeFournisseurRepository;
import com.pharmatrack.fournisseur.repository.FournisseurMedicamentRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.util.StringUtils;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.UUID;
import java.util.concurrent.ThreadLocalRandom;
import java.util.function.Function;

/**
 * Pharmacy-side purchase order flow: pick a medicament, list its suppliers
 * (cheapest first), verify availability, place the order (full or partial) and
 * consult an order. Mirror of the legacy
 * {@code CommandeFournisseurService}+{@code CommandeFournisseurController}.
 */
@Service
@Transactional
public class CommandeFournisseurService {

    private static final Logger log = LoggerFactory.getLogger(CommandeFournisseurService.class);

    private final MedicamentRepository medicamentRepository;
    private final FournisseurMedicamentRepository fournisseurMedicamentRepository;
    private final CommandeFournisseurRepository commandeRepository;
    private final AlerteRepository alerteRepository;
    private final UserRepository userRepository;
    private final FournisseurMapper mapper;

    public CommandeFournisseurService(MedicamentRepository medicamentRepository,
                                      FournisseurMedicamentRepository fournisseurMedicamentRepository,
                                      CommandeFournisseurRepository commandeRepository,
                                      AlerteRepository alerteRepository,
                                      UserRepository userRepository,
                                      FournisseurMapper mapper) {
        this.medicamentRepository = medicamentRepository;
        this.fournisseurMedicamentRepository = fournisseurMedicamentRepository;
        this.commandeRepository = commandeRepository;
        this.alerteRepository = alerteRepository;
        this.userRepository = userRepository;
        this.mapper = mapper;
    }

    @Transactional(readOnly = true)
    public PagedResponse<MedicamentSelectionResponse> selection(String search,
                                                                Integer page, Integer size) {
        Pageable pageable = PageRequest.of(PageUtils.clampPage(page), PageUtils.clampSize(size),
                Sort.by(Sort.Direction.ASC, "nomCommercialFr"));
        String term = search == null ? "" : search.trim();
        Page<Medicament> result = StringUtils.hasText(term)
                ? medicamentRepository
                        .findByCodeCipContainingIgnoreCaseOrNomCommercialFrContainingIgnoreCaseOrNomCommercialArContainingIgnoreCaseOrDciContainingIgnoreCase(
                                term, term, term, term, pageable)
                : medicamentRepository.findAll(pageable);

        List<MedicamentSelectionResponse> content = result.getContent().stream()
                .map(mapper::toSelection)
                .toList();
        return new PagedResponse<>(content, result.getNumber(), result.getSize(),
                result.getTotalElements(), result.getTotalPages());
    }

    @Transactional(readOnly = true)
    public List<FournisseurMedicamentResponse> fournisseursPourMedicament(UUID medicamentId) {
        return fournisseurMedicamentRepository
                .findByMedicamentIdAndDisponibleTrueOrderByPrixAchatAsc(medicamentId).stream()
                .map(mapper::toFmResponse)
                .toList();
    }

    @Transactional(readOnly = true)
    public DisponibiliteResponse verifierDisponibilite(UUID fournisseurMedicamentId, int quantite) {
        FournisseurMedicament fm = fournisseurMedicamentRepository
                .findById(fournisseurMedicamentId).orElse(null);
        if (fm == null) {
            return new DisponibiliteResponse(false, "produit_non_trouve",
                    "Produit non trouvé", null, null, null, null, null, null, null, null);
        }
        if (!fm.isDisponible()) {
            return new DisponibiliteResponse(false, "indisponible_fournisseur",
                    "Produit indisponible chez ce fournisseur", null, null,
                    fm.getStockMinimum(), fm.getStockDisponible(), fm.getPrixAchat(),
                    nom(fm), fm.getFournisseur().getId(), fm.getFournisseur().getRaisonSociale());
        }
        int stockMinimum = fm.getStockMinimum();
        int stockDisponiblePourVente = fm.getStockDisponible() - stockMinimum;

        if (stockDisponiblePourVente < 0) {
            return new DisponibiliteResponse(false, "stock_minimum",
                    "Stock minimum requis non atteint. Stock minimum: " + stockMinimum
                            + " unités. Stock actuel: " + fm.getStockDisponible(),
                    null, null, stockMinimum, fm.getStockDisponible(), fm.getPrixAchat(),
                    nom(fm), fm.getFournisseur().getId(), fm.getFournisseur().getRaisonSociale());
        }
        if (stockDisponiblePourVente >= quantite) {
            return new DisponibiliteResponse(true, "complet", null,
                    stockDisponiblePourVente, 0, stockMinimum, fm.getStockDisponible(),
                    fm.getPrixAchat(), nom(fm), fm.getFournisseur().getId(),
                    fm.getFournisseur().getRaisonSociale());
        }
        if (stockDisponiblePourVente > 0) {
            return new DisponibiliteResponse(true, "partiel", null,
                    stockDisponiblePourVente, quantite - stockDisponiblePourVente,
                    stockMinimum, fm.getStockDisponible(), fm.getPrixAchat(), nom(fm),
                    fm.getFournisseur().getId(), fm.getFournisseur().getRaisonSociale());
        }
        return new DisponibiliteResponse(false, "indisponible",
                "Stock insuffisant. Stock minimum requis: " + stockMinimum
                        + " unités. Stock actuel: " + fm.getStockDisponible(),
                null, null, stockMinimum, fm.getStockDisponible(), fm.getPrixAchat(),
                nom(fm), fm.getFournisseur().getId(), fm.getFournisseur().getRaisonSociale());
    }

    public CommandeResult passerCommande(UUID fournisseurMedicamentId, int quantite, UUID userId) {
        FournisseurMedicament fm = fournisseurMedicamentRepository
                .findById(fournisseurMedicamentId)
                .orElseThrow(() -> new ResourceNotFoundException(
                        "fournisseur_medicament", fournisseurMedicamentId));

        DisponibiliteResponse verification = verifierDisponibilite(fournisseurMedicamentId, quantite);
        if (!verification.disponible()) {
            return new CommandeResult(
                    false,
                    verification.type(),
                    null,
                    0, 0,
                    verification.stockActuel() == null ? 0 : verification.stockActuel(),
                    0,
                    verification.stockMinimum() == null ? 10 : verification.stockMinimum(),
                    verification.raison(),
                    alternatifs(fm.getMedicament().getId(), quantite));
        }

        int stockMinimum = fm.getStockMinimum();
        int quantiteDisponibleVente = fm.getStockDisponible() - stockMinimum;
        int quantiteCommandee = Math.min(quantite, quantiteDisponibleVente);
        int quantiteManquante = quantite - quantiteCommandee;
        int stockAvant = fm.getStockDisponible();
        String nomMedicament = nom(fm);

        BigDecimal prixUnitaire = fm.getPrixAchat();
        BigDecimal total = prixUnitaire.multiply(BigDecimal.valueOf(quantiteCommandee));

        CommandeFournisseur commande = new CommandeFournisseur();
        commande.setNumeroCommande(genererNumero());
        commande.setFournisseur(fm.getFournisseur());
        commande.setPharmacieId(pharmacieIdOf(userId));
        commande.setUserId(userId);
        commande.setDateCommande(LocalDate.now());
        commande.setStatut(quantiteManquante > 0 ? CommandeStatut.partiel : CommandeStatut.confirmee);
        commande.setTotalHt(total);
        commande.setTotalTtc(total);
        commande.setNotes(quantiteManquante > 0
                ? "Commande partielle. Manque " + quantiteManquante
                        + " unités. Stock minimum réservé: " + stockMinimum
                : null);

        CommandeFournisseurLigne ligne = new CommandeFournisseurLigne();
        ligne.setCommande(commande);
        ligne.setMedicament(fm.getMedicament());
        ligne.setQuantite(quantiteCommandee);
        ligne.setQuantiteDemandee(quantite);
        ligne.setStockAvant(stockAvant);
        ligne.setPrixUnitaire(prixUnitaire);
        ligne.setTotalLigne(total);
        commande.getLignes().add(ligne);

        commandeRepository.save(commande);

        int nouveauStock = stockMinimum;
        fm.setStockDisponible(nouveauStock);
        fournisseurMedicamentRepository.save(fm);

        log.info("Commande créée avec stock minimum [commande={}, quantite_commandee={}, "
                        + "quantite_manquante={}, stock_avant={}, stock_apres={}, stock_minimum={}]",
                commande.getNumeroCommande(), quantiteCommandee, quantiteManquante,
                stockAvant, nouveauStock, stockMinimum);

        verifierEtatStock(fm, nomMedicament);

        String message = quantiteManquante > 0
                ? "Commande partielle : " + quantiteCommandee + " unités commandées. "
                        + quantiteManquante + " unités en attente. Stock minimum réservé: "
                        + stockMinimum
                : "Commande complète : " + quantiteCommandee
                        + " unités commandées. Stock minimum réservé: " + stockMinimum;

        return new CommandeResult(
                true,
                quantiteManquante > 0 ? "partiel" : "complet",
                mapper.toCommande(commande, stockLookup(fm.getFournisseur().getId())),
                quantiteCommandee, quantiteManquante,
                stockAvant, nouveauStock, stockMinimum,
                message,
                List.of());
    }

    @Transactional(readOnly = true)
    public CommandeResponse getDetail(UUID commandeId) {
        CommandeFournisseur commande = commandeRepository.findById(commandeId)
                .orElseThrow(() -> new ResourceNotFoundException("commande", commandeId));
        return mapper.toCommande(commande,
                stockLookup(commande.getFournisseur().getId()));
    }

    /**
     * Create a supplier-only stock alert after an order drives the supplier
     * stock to its minimum (mirrors legacy {@code verifierEtatStock}).
     */
    private void verifierEtatStock(FournisseurMedicament fm, String nomMedicament) {
        int stockRestant = fm.getStockDisponible();
        int stockMinimum = fm.getStockMinimum();
        int seuilAlerte = fm.getSeuilReapprovisionnement();

        if (stockRestant <= stockMinimum) {
            Alerte alerte = new Alerte();
            alerte.setType(AlerteType.stock);
            alerte.setNiveau(AlerteNiveau.eleve);
            alerte.setMessage("STOCK MINIMUM ATTEINT : " + nomMedicament
                    + ". Stock restant: " + stockRestant + " unités (minimum: " + stockMinimum
                    + "). Veuillez réapprovisionner.");
            alerte.setEstLue(false);
            alerte.setDonneesConcernees(donneesFournisseur(fm, nomMedicament, stockRestant,
                    null, "stock_minimum_atteint"));
            alerteRepository.save(alerte);
            log.warn("Stock minimum atteint [medicament={}, stock_restant={}, stock_minimum={}]",
                    nomMedicament, stockRestant, stockMinimum);
        } else if (stockRestant <= seuilAlerte) {
            Alerte alerte = new Alerte();
            alerte.setType(AlerteType.stock);
            alerte.setNiveau(AlerteNiveau.moyen);
            alerte.setMessage("STOCK FAIBLE : " + nomMedicament + ". Stock restant: "
                    + stockRestant + " unités. Seuil d'alerte: " + seuilAlerte);
            alerte.setEstLue(false);
            alerte.setDonneesConcernees(donneesFournisseur(fm, nomMedicament, stockRestant,
                    seuilAlerte, "stock_faible"));
            alerteRepository.save(alerte);
            log.info("Stock faible [medicament={}, stock_restant={}, seuil_alerte={}]",
                    nomMedicament, stockRestant, seuilAlerte);
        }
    }

    private Map<String, Object> donneesFournisseur(FournisseurMedicament fm, String nomMedicament,
                                                   int stockRestant, Integer seuilAlerte,
                                                   String typeDetail) {
        Map<String, Object> donnees = new LinkedHashMap<>();
        donnees.put("fournisseur_id", fm.getFournisseur().getId().toString());
        donnees.put("medicament_id", fm.getMedicament().getId().toString());
        donnees.put("medicament_nom", nomMedicament);
        donnees.put("stock_restant", stockRestant);
        donnees.put("stock_minimum", fm.getStockMinimum());
        if (seuilAlerte != null) {
            donnees.put("seuil_alerte", seuilAlerte);
        }
        donnees.put("for_fournisseur", true);
        donnees.put("type_detail", typeDetail);
        return donnees;
    }

    private List<FournisseurMedicamentResponse> alternatifs(UUID medicamentId, int quantite) {
        return fournisseurMedicamentRepository.findAlternatifs(medicamentId, quantite).stream()
                .map(mapper::toFmResponse)
                .toList();
    }

    private UUID pharmacieIdOf(UUID userId) {
        return userRepository.findById(userId)
                .map(User::getPharmacie)
                .flatMap(p -> Optional.ofNullable(p.getId()))
                .orElse(null);
    }

    private Function<UUID, Optional<FournisseurMedicament>> stockLookup(UUID fournisseurId) {
        return medicamentId -> fournisseurMedicamentRepository
                .findByFournisseurIdAndMedicamentId(fournisseurId, medicamentId);
    }

    private String genererNumero() {
        return "CMD-" + LocalDateTime.now().format(
                DateTimeFormatter.ofPattern("yyyyMMddHHmmss")) + "-"
                + ThreadLocalRandom.current().nextInt(100, 1000);
    }

    private String nom(FournisseurMedicament fm) {
        String nom = fm.getMedicament().getNomCommercialFr();
        return nom != null ? nom : "Médicament";
    }
}