package com.pharmatrack.catalog.dto;

import java.time.Instant;
import java.util.UUID;

/**
 * Matches the OpenAPI {@code MouvementResponse}.
 */
public record MouvementResponse(
        UUID id,
        UUID lotId,
        UUID pharmacieId,
        UUID userId,
        MouvementTypeDto type,
        Integer quantite,
        Integer quantiteAvant,
        Integer quantiteApres,
        String reference,
        String motif,
        Instant scannedAt,
        Instant createdAt
) {
}
