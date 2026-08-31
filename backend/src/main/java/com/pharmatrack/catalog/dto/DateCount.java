package com.pharmatrack.catalog.dto;

import java.time.LocalDate;

/**
 * A per-day count for dashboard evolution charts.
 */
public record DateCount(LocalDate date, long total) {
}