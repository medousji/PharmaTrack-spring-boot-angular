package com.pharmatrack.catalog.dto;

import java.math.BigDecimal;

/**
 * Matches the OpenAPI {@code LotUpdateRequest}. Quantity is deliberately NOT
 * settable here - use the /adjust-stock endpoint so every change is audited.
 */
public record LotUpdateRequest(
        String emplacement,
        String observations,
        LotStatutDto statut,
        BigDecimal prixVente
) {
}
