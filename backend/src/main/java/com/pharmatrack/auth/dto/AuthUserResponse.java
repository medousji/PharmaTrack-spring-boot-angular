package com.pharmatrack.auth.dto;

import com.pharmatrack.auth.entity.UserRole;

import java.time.Instant;
import java.util.UUID;

/**
 * Public user identity returned after login/refresh and for the admin user
 * management screens.
 */
public record AuthUserResponse(
        UUID id,
        String name,
        String email,
        UserRole role,
        boolean isApproved,
        String status,
        UUID pharmacieId,
        String pharmacieNom,
        Instant createdAt,
        Instant lastLoginAt,
        Instant approvedAt
) {
}
