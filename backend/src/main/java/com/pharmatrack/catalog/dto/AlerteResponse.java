package com.pharmatrack.catalog.dto;

import java.time.Instant;
import java.util.Map;
import java.util.UUID;

/**
 * Matches the OpenAPI {@code AlerteResponse}. {@code donneesConcernees} is a
 * structured payload: the serialized typed alert DTO, not an ad hoc array.
 */
public record AlerteResponse(
        UUID id,
        UUID lotId,
        AlerteTypeDto type,
        AlerteNiveauDto niveau,
        String message,
        Map<String, Object> donneesConcernees,
        boolean estLue,
        Instant resolueAt,
        Instant createdAt
) {
}
