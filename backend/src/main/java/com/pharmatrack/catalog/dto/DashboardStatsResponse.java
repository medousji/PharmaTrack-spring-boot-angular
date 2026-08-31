package com.pharmatrack.catalog.dto;

import java.util.List;
import java.util.Map;

/**
 * Aggregated figures and chart datasets for the dashboard. Mirrors the legacy
 * Laravel dashboard (stat cards + charts) but computed from the normalized
 * schema (classe_thérapeutique, forme_pharmaceutique, laboratoire, ...).
 *
 * @param statutMedicaments "actif" / "inactif" / "retire" counts
 * @param statutLots        "actif" / "epuise" / "perime" / "bloque" counts
 * @param alertesParType    "expiration" / "stock" / "rupture" / "qualite" / "autre"
 */
public record DashboardStatsResponse(
        long totalMedicaments,
        long ruptures,
        long alertesNonLues,
        long lotsProches,
        Map<String, Long> statutMedicaments,
        Map<String, Long> statutLots,
        Map<String, Long> alertesParType,
        List<LabelCount> categories,
        List<LabelCount> laboratoires,
        List<LabelCount> formes,
        List<TopMedicament> topMedicaments,
        List<DateCount> evolutionMedicaments,
        List<DateCount> lotsExpiration,
        List<DateCount> evolutionAlertes
) {
}