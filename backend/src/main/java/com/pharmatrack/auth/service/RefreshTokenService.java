package com.pharmatrack.auth.service;

import com.pharmatrack.auth.entity.RefreshToken;
import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.repository.RefreshTokenRepository;
import com.pharmatrack.common.error.ApiException;
import com.pharmatrack.common.security.JwtProperties;
import com.pharmatrack.common.security.JwtTokenProvider;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.time.Instant;
import java.util.HexFormat;
import java.util.Optional;
import java.util.UUID;

/**
 * Persists and validates refresh tokens so they can be rotated and revoked.
 * Only the SHA-256 hash plus the unique {@code jti} is stored - the raw token
 * is never persisted.
 */
@Service
public class RefreshTokenService {

    private final RefreshTokenRepository repository;
    private final JwtTokenProvider tokenProvider;
    private final JwtProperties jwtProperties;

    public RefreshTokenService(RefreshTokenRepository repository,
                               JwtTokenProvider tokenProvider,
                               JwtProperties jwtProperties) {
        this.repository = repository;
        this.tokenProvider = tokenProvider;
        this.jwtProperties = jwtProperties;
    }

    /**
     * Issue and persist a new refresh token for the user.
     */
    @Transactional
    public IssuedRefreshToken issue(User user) {
        UUID jti = UUID.randomUUID();
        String raw = tokenProvider.issueRefreshToken(user, jti);
        Instant expiresAt = Instant.now().plus(jwtProperties.getRefreshTokenTtl());

        RefreshToken entity = new RefreshToken();
        entity.setUser(user);
        entity.setJti(jti);
        entity.setTokenHash(hash(raw));
        entity.setExpiresAt(expiresAt);
        entity.setRevoked(false);
        repository.save(entity);
        return new IssuedRefreshToken(raw, jti, expiresAt);
    }

    /**
     * Validate a raw refresh token (signature, existence, non-revoked,
     * non-expired) against the stored record, then rotate it: revoke the
     * presented token and issue a fresh one (refresh-token rotation).
     */
    @Transactional
    public IssuedRefreshToken rotate(String rawRefreshToken, User user) {
        RefreshToken stored = lookupValid(rawRefreshToken);

        stored.setRevoked(true);
        stored.setRevokedAt(Instant.now());
        IssuedRefreshToken next = issue(user);
        stored.setReplacedBy(next.jti());
        repository.save(stored);
        return next;
    }

    @Transactional
    public void revoke(String rawRefreshToken) {
        tokenProvider.parseRefreshToken(rawRefreshToken)
                .flatMap(claims -> repository.findByJti(claims.jti()))
                .ifPresent(token -> {
                    token.setRevoked(true);
                    token.setRevokedAt(Instant.now());
                    repository.save(token);
                });
    }

    @Transactional
    public long purgeExpired() {
        return repository.deleteByExpiresAtBefore(Instant.now());
    }

    private RefreshToken lookupValid(String rawRefreshToken) {
        Optional<RefreshToken> stored = tokenProvider.parseRefreshToken(rawRefreshToken)
                .flatMap(claims -> repository.findByJti(claims.jti()));
        RefreshToken token = stored.orElseThrow(InvalidTokenException::new);
        if (token.isRevoked() || token.getExpiresAt().isBefore(Instant.now())) {
            throw new InvalidTokenException();
        }
        return token;
    }

    private static String hash(String raw) {
        try {
            byte[] digest = MessageDigest.getInstance("SHA-256")
                    .digest(raw.getBytes(StandardCharsets.UTF_8));
            return HexFormat.of().formatHex(digest);
        } catch (NoSuchAlgorithmException e) {
            throw new IllegalStateException("SHA-256 unavailable", e);
        }
    }

    public record IssuedRefreshToken(String raw, UUID jti, Instant expiresAt) {
    }

    public static class InvalidTokenException extends ApiException {
        public InvalidTokenException() {
            super("Invalid refresh token", 401, "The refresh token is invalid, expired or revoked.");
        }
    }
}
