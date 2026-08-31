package com.pharmatrack.catalog.dto;

import java.time.LocalDate;
import java.util.UUID;

/**
 * A lot whose expiry falls within the near-expiry horizon, returned by a
 * SQL-filtered query. The service keeps the earliest date per medicament.
 */
public record LotExpirySummary(UUID medicamentId, LocalDate datePeremption) {
}
