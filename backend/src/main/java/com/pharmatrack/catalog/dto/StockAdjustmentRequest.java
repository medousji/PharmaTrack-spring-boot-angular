package com.pharmatrack.catalog.dto;

import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Positive;

/**
 * Matches the OpenAPI {@code StockAdjustmentRequest}. {@code type=sortie}
 * decrements; {@code entree}/{@code ajustement} can increment or set.
 */
public record StockAdjustmentRequest(
        @NotNull MouvementTypeDto type,
        @Positive Integer quantite,
        String motif,
        String reference
) {
}
