package com.pharmatrack.fournisseur.dto;

import com.pharmatrack.fournisseur.entity.CommandeStatut;

import java.math.BigDecimal;
import java.time.Instant;
import java.time.LocalDate;
import java.util.List;
import java.util.UUID;

/**
 * Purchase order (with lines) as exposed to both the supplier dashboard and the
 * pharmacy order pages.
 */
public record CommandeResponse(
        UUID id,
        String numeroCommande,
        CommandeStatut statut,
        LocalDate dateCommande,
        LocalDate dateLivraisonPrevue,
        BigDecimal totalHt,
        BigDecimal totalTtc,
        UUID fournisseurId,
        String fournisseurNom,
        Instant createdAt,
        List<CommandeLigneResponse> lignes) {
}