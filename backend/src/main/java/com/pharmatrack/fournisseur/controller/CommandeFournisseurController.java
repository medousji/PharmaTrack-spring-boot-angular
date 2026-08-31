package com.pharmatrack.fournisseur.controller;

import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.common.security.CurrentUser;
import com.pharmatrack.fournisseur.dto.CommandeResponse;
import com.pharmatrack.fournisseur.dto.CommandeResult;
import com.pharmatrack.fournisseur.dto.DisponibiliteResponse;
import com.pharmatrack.fournisseur.dto.FournisseurMedicamentResponse;
import com.pharmatrack.fournisseur.dto.MedicamentSelectionResponse;
import com.pharmatrack.fournisseur.dto.PasserCommandeRequest;
import com.pharmatrack.fournisseur.dto.VerifierDisponibiliteRequest;
import com.pharmatrack.fournisseur.service.CommandeFournisseurService;
import jakarta.validation.Valid;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

/**
 * Pharmacy-side endpoints: medicament selection, supplier listing, order
 * placement and order detail. Mirrors legacy
 * {@code commandes.selection|creer|passer} and the AJAX availability flow.
 */
@RestController
@RequestMapping("/api/v1/commandes")
public class CommandeFournisseurController {

    private final CommandeFournisseurService service;
    private final CurrentUser currentUser;

    public CommandeFournisseurController(CommandeFournisseurService service,
                                         CurrentUser currentUser) {
        this.service = service;
        this.currentUser = currentUser;
    }

    @GetMapping("/medicaments")
    @PreAuthorize("hasRole('PHARMACIEN')")
    public PagedResponse<MedicamentSelectionResponse> medicaments(
            @RequestParam(required = false) String search,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "20") int size) {
        return service.selection(search, page, size);
    }

    @GetMapping("/medicaments/{medicamentId}/fournisseurs")
    @PreAuthorize("hasRole('PHARMACIEN')")
    public List<FournisseurMedicamentResponse> fournisseurs(
            @PathVariable UUID medicamentId) {
        return service.fournisseursPourMedicament(medicamentId);
    }

    @PostMapping("/verifier-disponibilite")
    @PreAuthorize("hasRole('PHARMACIEN')")
    public DisponibiliteResponse verifierDisponibilite(
            @Valid @RequestBody VerifierDisponibiliteRequest request) {
        return service.verifierDisponibilite(request.fournisseurMedicamentId(),
                request.quantite());
    }

    @PostMapping("/passer")
    @PreAuthorize("hasRole('PHARMACIEN')")
    public CommandeResult passer(@Valid @RequestBody PasserCommandeRequest request) {
        UUID userId = currentUser.id().orElseThrow(() -> new UnauthorizedException(
                "Un utilisateur authentifié est requis."));
        return service.passerCommande(request.fournisseurMedicamentId(),
                request.quantite(), userId);
    }

    @GetMapping("/{id}")
    @PreAuthorize("hasAnyRole('PHARMACIEN', 'FOURNISSEUR')")
    public CommandeResponse detail(@PathVariable UUID id) {
        return service.getDetail(id);
    }
}