package com.pharmatrack.common.security;

import com.pharmatrack.auth.entity.User;
import io.jsonwebtoken.Claims;
import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.security.Keys;
import org.springframework.stereotype.Component;

import javax.crypto.SecretKey;
import java.time.Instant;
import java.util.Date;
import java.util.Optional;
import java.util.UUID;

/**
 * Real JJWT-based token service. Issues and verifies HS256 access and refresh
 * tokens (the legacy login endpoint never issued API tokens - this fixes that).
 *
 * Access tokens carry {@code sub}=user id, {@code role}, {@code jti}, issuer,
 * audience and expiry. Refresh tokens carry {@code sub}, {@code jti} and a
 * {@code type=refresh} claim so the two token classes cannot be confused.
 */
@Component
public class JwtTokenProvider {

    public static final String CLAIM_ROLE = "role";
    public static final String CLAIM_JTI = "jti";
    public static final String CLAIM_TYPE = "type";
    public static final String TYPE_ACCESS = "access";
    public static final String TYPE_REFRESH = "refresh";

    private final JwtProperties properties;
    private final SecretKey key;

    public JwtTokenProvider(JwtProperties properties) {
        this.properties = properties;
        this.key = Keys.hmacShaKeyFor(
                java.util.Base64.getDecoder().decode(properties.getSecret()));
    }

    public String issueAccessToken(User user) {
        Instant now = Instant.now();
        Instant exp = now.plus(properties.getAccessTokenTtl());
        return Jwts.builder()
                .subject(user.getId().toString())
                .claim(CLAIM_ROLE, user.getRole().name())
                .claim(CLAIM_TYPE, TYPE_ACCESS)
                .id(UUID.randomUUID().toString())
                .issuer("pharmatrack")
                .audience().add("pharmatrack-api").and()
                .issuedAt(Date.from(now))
                .expiration(Date.from(exp))
                .signWith(key)
                .compact();
    }

    public String issueRefreshToken(User user, UUID jti) {
        Instant now = Instant.now();
        Instant exp = now.plus(properties.getRefreshTokenTtl());
        return Jwts.builder()
                .subject(user.getId().toString())
                .claim(CLAIM_TYPE, TYPE_REFRESH)
                .id(jti.toString())
                .issuer("pharmatrack")
                .audience().add("pharmatrack-api").and()
                .issuedAt(Date.from(now))
                .expiration(Date.from(exp))
                .signWith(key)
                .compact();
    }

    /**
     * Parse and verify an access token, returning an {@link AuthPrincipal}
     * (id + role) used to populate the security context.
     */
    public Optional<AuthPrincipal> parseAccessToken(String token) {
        return parse(token).filter(claims -> TYPE_ACCESS.equals(claims.get(CLAIM_TYPE)))
                .map(claims -> new AuthPrincipal(
                        UUID.fromString(claims.getSubject()),
                        claims.get(CLAIM_ROLE, String.class)));
    }

    /**
     * Parse and verify a refresh token. The returned jti is used to look up and
     * validate the persisted token (rotation / blacklist check).
     */
    public Optional<RefreshTokenClaims> parseRefreshToken(String token) {
        return parse(token).filter(claims -> TYPE_REFRESH.equals(claims.get(CLAIM_TYPE)))
                .map(claims -> new RefreshTokenClaims(
                        UUID.fromString(claims.getSubject()),
                        UUID.fromString(claims.getId()),
                        claims.getExpiration().toInstant()));
    }

    private Optional<Claims> parse(String token) {
        try {
            Claims claims = Jwts.parser()
                    .verifyWith(key)
                    .requireIssuer("pharmatrack")
                    .build()
                    .parseSignedClaims(token)
                    .getPayload();
            return Optional.of(claims);
        } catch (Exception ex) {
            return Optional.empty();
        }
    }

    public record RefreshTokenClaims(UUID userId, UUID jti, Instant expiresAt) {
    }
}
