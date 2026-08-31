package com.pharmatrack.fournisseur.dto;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.UUID;

/**
 * Catalogue line shown in the supplier price/stock management screen and in the
 * pharmacy order forms (cheapest-first supplier list, stock validation).
 */
public record FournisseurMedicamentResponse(
        UUID id,
        UUID fournisseurId,
        String fournisseurNom,
        UUID medicamentId,
        String medicamentNom,
        String dci,
        String formePharmaceutique,
        String dosage,
        String referenceFournisseur,
        BigDecimal prixAchat,
        BigDecimal prixPublic,
        int stockDisponible,
        int stockMinimum,
        Integer delaiLivraison,
        boolean disponible,
        LocalDate derniereMiseAJour) {
}