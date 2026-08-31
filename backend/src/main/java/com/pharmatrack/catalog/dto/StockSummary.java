package com.pharmatrack.catalog.dto;

import java.math.BigDecimal;
import java.util.UUID;

/**
 * SQL-backed aggregate over a medicament's lots (GROUP BY medicament_id).
 * Populated by JPA constructor expressions, never by client-side loops.
 */
public record StockSummary(UUID medicamentId, long stockActif, long stockTotal, BigDecimal valeurStock) {
}
