package com.pharmatrack.auth.dto;

import java.util.Map;

/** Aggregate counters for the admin user management screen. */
public record AdminUserStatsResponse(
        long total,
        long enAttente,
        long admins,
        long pharmaciens,
        long fournisseurs,
        long visiteurs,
        Map<String, Long> parStatut
) {
}