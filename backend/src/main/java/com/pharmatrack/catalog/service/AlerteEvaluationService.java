package com.pharmatrack.catalog.service;

import com.pharmatrack.catalog.dto.AlerteEvaluationSummary;

/**
 * Evaluates all alert rules (rupture / low-stock / expiration) in a single
 * pass of SQL aggregation. Runs as a scheduled job and is exposed as a manual
 * re-evaluate endpoint. The Epic 3 implementation performs the evaluation in
 * the database (GROUP BY / HAVING), never by loading tables into memory.
 */
public interface AlerteEvaluationService {

    AlerteEvaluationSummary reEvaluate();
}
