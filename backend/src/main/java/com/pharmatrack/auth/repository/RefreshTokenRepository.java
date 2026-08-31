package com.pharmatrack.auth.repository;

import com.pharmatrack.auth.entity.RefreshToken;
import org.springframework.data.jpa.repository.JpaRepository;

import java.time.Instant;
import java.util.Optional;
import java.util.UUID;

public interface RefreshTokenRepository extends JpaRepository<RefreshToken, UUID> {

    Optional<RefreshToken> findByJti(UUID jti);

    long deleteByExpiresAtBefore(Instant now);

    void deleteByUserId(UUID userId);
}
