package com.pharmatrack.fournisseur.dto;

/**
 * Supplier dashboard counters (mirrors legacy {@code $stats}).
 */
public record FournisseurStatsResponse(
        long commandesEncours,
        long commandesLivrees,
        long produitsDisponibles) {
}