package com.pharmatrack.fournisseur.service;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.repository.UserRepository;
import com.pharmatrack.catalog.entity.Alerte;
import com.pharmatrack.catalog.entity.AlerteNiveau;
import com.pharmatrack.catalog.entity.AlerteType;
import com.pharmatrack.catalog.repository.AlerteRepository;
import com.pharmatrack.common.api.PageUtils;
import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.error.ForbiddenException;
import com.pharmatrack.common.error.ResourceNotFoundException;
import com.pharmatrack.fournisseur.dto.CommandeResponse;
import com.pharmatrack.fournisseur.dto.FournisseurDashboardResponse;
import com.pharmatrack.fournisseur.dto.FournisseurMedicamentResponse;
import com.pharmatrack.fournisseur.dto.FournisseurStatsResponse;
import com.pharmatrack.fournisseur.dto.UpdatePrixItem;
import com.pharmatrack.fournisseur.dto.UpdatePrixRequest;
import com.pharmatrack.fournisseur.entity.CommandeFournisseur;
import com.pharmatrack.fournisseur.entity.CommandeStatut;
import com.pharmatrack.fournisseur.entity.Fournisseur;
import com.pharmatrack.fournisseur.entity.FournisseurMedicament;
import com.pharmatrack.fournisseur.mapper.FournisseurMapper;
import com.pharmatrack.fournisseur.repository.CommandeFournisseurRepository;
import com.pharmatrack.fournisseur.repository.FournisseurMedicamentRepository;
import com.pharmatrack.fournisseur.repository.FournisseurRepository;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.UUID;

/**
 * Supplier-facing operations: auto-provision the {@code Fournisseur} record
 * from the authenticated user (role {@code fournisseur}), then expose the
 * dashboard, received orders, expedition and the price/stock management screen —
 * all scoped to the caller's own supplier account.
 */
@Service
@Transactional
public class FournisseurService {

    private final FournisseurRepository fournisseurRepository;
    private final FournisseurMedicamentRepository fournisseurMedicamentRepository;
    private final CommandeFournisseurRepository commandeRepository;
    private final AlerteRepository alerteRepository;
    private final UserRepository userRepository;
    private final FournisseurMapper mapper;

    public FournisseurService(FournisseurRepository fournisseurRepository,
                              FournisseurMedicamentRepository fournisseurMedicamentRepository,
                              CommandeFournisseurRepository commandeRepository,
                              AlerteRepository alerteRepository,
                              UserRepository userRepository,
                              FournisseurMapper mapper) {
        this.fournisseurRepository = fournisseurRepository;
        this.fournisseurMedicamentRepository = fournisseurMedicamentRepository;
        this.commandeRepository = commandeRepository;
        this.alerteRepository = alerteRepository;
        this.userRepository = userRepository;
        this.mapper = mapper;
    }

    /**
     * Supplier record for the current user, created on first access
     * (mirrors legacy {@code FournisseurController::getOrCreateFournisseur}).
     */
    public Fournisseur getOrCreateFournisseur(UUID userId) {
        User user = userRepository.findById(userId)
                .orElseThrow(() -> new ResourceNotFoundException("user", userId));
        if (user.getRole() != UserRole.fournisseur) {
            throw new ForbiddenException("Seul un compte fournisseur peut accéder à cet espace.");
        }
        return fournisseurRepository.findByUserId(userId)
                .orElseGet(() -> {
                    Fournisseur fournisseur = new Fournisseur();
                    fournisseur.setUserId(userId);
                    fournisseur.setRaisonSociale(user.getName());
                    fournisseur.setEmailPro(user.getEmail());
                    fournisseur.setDelaiLivraisonMoyen(7);
                    fournisseur.setEstActif(true);
                    return fournisseurRepository.save(fournisseur);
                });
    }

    @Transactional(readOnly = true)
    public FournisseurDashboardResponse dashboard(UUID userId) {
        Fournisseur fournisseur = getOrCreateFournisseur(userId);

        long encours = commandeRepository.countByFournisseurIdAndStatutIn(fournisseur.getId(),
                List.of(CommandeStatut.en_attente, CommandeStatut.confirmee, CommandeStatut.preparation));
        long livrees = commandeRepository.countByFournisseurIdAndStatut(fournisseur.getId(),
                CommandeStatut.livree);
        long produitsDisponibles = fournisseurMedicamentRepository
                .countByFournisseurIdAndDisponibleTrue(fournisseur.getId());

        List<CommandeResponse> dernieres = commandeRepository
                .findTop10ByFournisseurIdOrderByCreatedAtDesc(fournisseur.getId()).stream()
                .map(c -> mapper.toCommande(c, stockLookup(fournisseur.getId())))
                .toList();

        return new FournisseurDashboardResponse(
                fournisseur.getId(),
                fournisseur.getRaisonSociale(),
                fournisseur.getDelaiLivraisonMoyen(),
                new FournisseurStatsResponse(encours, livrees, produitsDisponibles),
                dernieres);
    }

    @Transactional(readOnly = true)
    public PagedResponse<CommandeResponse> commandes(UUID userId, CommandeStatut statut,
                                                     Integer page, Integer size) {
        Fournisseur fournisseur = getOrCreateFournisseur(userId);
        Pageable pageable = PageRequest.of(PageUtils.clampPage(page), PageUtils.clampSize(size),
                Sort.by(Sort.Direction.DESC, "createdAt"));
        Page<CommandeFournisseur> result = statut == null
                ? commandeRepository.findByFournisseurId(fournisseur.getId(), pageable)
                : commandeRepository.findByFournisseurIdAndStatut(fournisseur.getId(), statut, pageable);

        List<CommandeResponse> content = result.getContent().stream()
                .map(c -> mapper.toCommande(c, stockLookup(fournisseur.getId())))
                .toList();
        return new PagedResponse<>(content, result.getNumber(), result.getSize(),
                result.getTotalElements(), result.getTotalPages());
    }

    public CommandeResponse expedier(UUID userId, UUID commandeId) {
        Fournisseur fournisseur = getOrCreateFournisseur(userId);
        CommandeFournisseur commande = commandeRepository.findByIdAndFournisseurId(commandeId,
                        fournisseur.getId())
                .orElseThrow(() -> new ResourceNotFoundException("commande", commandeId));

        commande.setStatut(CommandeStatut.expediee);
        commande.setDateLivraisonPrevue(LocalDate.now().plusDays(fournisseur.getDelaiLivraisonMoyen()));
        commandeRepository.save(commande);

        Alerte alerte = new Alerte();
        alerte.setType(AlerteType.autre);
        alerte.setNiveau(AlerteNiveau.faible);
        alerte.setMessage("La commande #" + commande.getNumeroCommande() + " a été expédiée");
        alerte.setEstLue(false);
        Map<String, Object> donnees = new LinkedHashMap<>();
        donnees.put("commande_id", commande.getId().toString());
        donnees.put("fournisseur_id", fournisseur.getId().toString());
        alerte.setDonneesConcernees(donnees);
        alerteRepository.save(alerte);

        return mapper.toCommande(commande, stockLookup(fournisseur.getId()));
    }

    @Transactional(readOnly = true)
    public PagedResponse<FournisseurMedicamentResponse> prix(UUID userId,
                                                             Integer page, Integer size) {
        Fournisseur fournisseur = getOrCreateFournisseur(userId);
        Pageable pageable = PageRequest.of(PageUtils.clampPage(page), PageUtils.clampSize(size),
                Sort.by(Sort.Direction.ASC, "medicament.nomCommercialFr"));
        Page<FournisseurMedicament> result = fournisseurMedicamentRepository
                .findByFournisseurId(fournisseur.getId(), pageable);

        List<FournisseurMedicamentResponse> content = result.getContent().stream()
                .map(mapper::toFmResponse)
                .toList();
        return new PagedResponse<>(content, result.getNumber(), result.getSize(),
                result.getTotalElements(), result.getTotalPages());
    }

    public void updatePrix(UUID userId, UpdatePrixRequest request) {
        Fournisseur fournisseur = getOrCreateFournisseur(userId);
        if (request == null || request.prix() == null) {
            return;
        }
        for (UpdatePrixItem item : request.prix()) {
            fournisseurMedicamentRepository.findById(item.id()).ifPresent(fm -> {
                if (!fm.getFournisseur().getId().equals(fournisseur.getId())) {
                    return;
                }
                if (item.prixAchat() != null) {
                    fm.setPrixAchat(item.prixAchat());
                }
                if (item.stockDisponible() != null) {
                    fm.setStockDisponible(item.stockDisponible());
                }
                if (item.disponible() != null) {
                    fm.setDisponible(item.disponible());
                }
                fm.setDerniereMiseAJour(LocalDate.now());
                fournisseurMedicamentRepository.save(fm);
            });
        }
    }

    private java.util.function.Function<UUID,
            java.util.Optional<FournisseurMedicament>> stockLookup(UUID fournisseurId) {
        return medicamentId -> fournisseurMedicamentRepository
                .findByFournisseurIdAndMedicamentId(fournisseurId, medicamentId);
    }
}