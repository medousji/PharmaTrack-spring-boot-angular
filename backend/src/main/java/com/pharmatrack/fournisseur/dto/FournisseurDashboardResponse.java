package com.pharmatrack.fournisseur.dto;

import java.util.List;

/**
 * Supplier dashboard aggregate: supplier identity, stats and the last 10
 * received orders.
 */
public record FournisseurDashboardResponse(
        java.util.UUID fournisseurId,
        String raisonSociale,
        int delaiLivraisonMoyen,
        FournisseurStatsResponse stats,
        List<CommandeResponse> dernieresCommandes) {
}