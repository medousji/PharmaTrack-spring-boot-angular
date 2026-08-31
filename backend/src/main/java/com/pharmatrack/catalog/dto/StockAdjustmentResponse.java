package com.pharmatrack.catalog.dto;

/**
 * Matches the OpenAPI {@code StockAdjustmentResponse}: the adjusted lot plus
 * the resulting audited Mouvement.
 */
public record StockAdjustmentResponse(LotResponse lot, MouvementResponse mouvement) {
}
