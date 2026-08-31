package com.pharmatrack.auth.dto;

/**
 * The pair of JWT tokens returned on login and refresh, plus the user identity.
 */
public record TokenResponse(
        String accessToken,
        String refreshToken,
        long expiresInSeconds,
        String tokenType,
        AuthUserResponse user
) {
}
