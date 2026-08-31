package com.pharmatrack.fournisseur.controller;

import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.common.security.CurrentUser;
import com.pharmatrack.fournisseur.dto.CommandeResponse;
import com.pharmatrack.fournisseur.dto.FournisseurDashboardResponse;
import com.pharmatrack.fournisseur.dto.UpdatePrixRequest;
import com.pharmatrack.fournisseur.dto.FournisseurMedicamentResponse;
import com.pharmatrack.fournisseur.entity.CommandeStatut;
import com.pharmatrack.fournisseur.service.FournisseurService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.util.Optional;
import java.util.UUID;

/**
 * Supplier-facing endpoints: dashboard, received orders, shipping and price
 * management. Mirrors legacy {@code fournisseur.dashboard|commandes|prix}.
 */
@RestController
@RequestMapping("/api/v1/fournisseur")
public class FournisseurController {

    private final FournisseurService service;
    private final CurrentUser currentUser;

    public FournisseurController(FournisseurService service, CurrentUser currentUser) {
        this.service = service;
        this.currentUser = currentUser;
    }

    @GetMapping("/dashboard")
    @PreAuthorize("hasRole('FOURNISSEUR')")
    public FournisseurDashboardResponse dashboard() {
        return service.dashboard(userId());
    }

    @GetMapping("/commandes")
    @PreAuthorize("hasRole('FOURNISSEUR')")
    public PagedResponse<CommandeResponse> commandes(
            @RequestParam(required = false) String statut,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "20") int size) {
        return service.commandes(userId(),
                statut == null || statut.isBlank() ? null : CommandeStatut.valueOf(statut),
                page, size);
    }

    @PostMapping("/commandes/{id}/expedier")
    @PreAuthorize("hasRole('FOURNISSEUR')")
    public CommandeResponse expedier(@PathVariable UUID id) {
        return service.expedier(userId(), id);
    }

    @GetMapping("/prix")
    @PreAuthorize("hasRole('FOURNISSEUR')")
    public PagedResponse<FournisseurMedicamentResponse> prix(
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "20") int size) {
        return service.prix(userId(), page, size);
    }

    @PutMapping("/prix")
    @PreAuthorize("hasRole('FOURNISSEUR')")
    public ResponseEntity<Void> updatePrix(@RequestBody UpdatePrixRequest request) {
        service.updatePrix(userId(), request);
        return ResponseEntity.status(HttpStatus.NO_CONTENT).build();
    }

    private UUID userId() {
        Optional<UUID> id = currentUser.id();
        return id.orElseThrow(() -> new UnauthorizedException(
                "Un utilisateur authentifié est requis."));
    }
}