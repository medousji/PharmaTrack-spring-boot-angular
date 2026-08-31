package com.pharmatrack.catalog.dto;

import java.time.Instant;

/**
 * Matches the OpenAPI {@code AlerteEvaluationSummary}. Returned by the manual
 * "re-evaluate now" endpoint and the scheduled job (Epic 3).
 */
public record AlerteEvaluationSummary(
        int rupturesCreees,
        int expirationsCreees,
        int stocksFaiblesCrees,
        int total,
        Instant evaluatedAt
) {
}
