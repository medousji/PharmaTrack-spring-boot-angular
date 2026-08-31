package com.pharmatrack.fournisseur.dto;

import java.math.BigDecimal;
import java.util.UUID;

/**
 * Order line with the supplier-side fulfilment info computed for the supplier
 * screens: stock remaining, quantity still pending and quantity deliverable.
 */
public record CommandeLigneResponse(
        UUID id,
        UUID medicamentId,
        String medicamentNom,
        int quantite,
        int quantiteDemandee,
        int stockAvant,
        int stockRestant,
        int quantiteManquante,
        int quantiteLivrable,
        BigDecimal prixUnitaire,
        BigDecimal totalLigne) {
}