package com.pharmatrack.common.security;

import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Component;

import java.util.Optional;
import java.util.UUID;

/**
 * Resolves the current authenticated user id from the Spring Security
 * context. Fully wired once Epic 1 (JWT authentication) lands; returns empty
 * for unauthenticated contexts so catalog operations remain testable now.
 */
@Component
public class CurrentUser {

    public Optional<UUID> id() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        if (auth == null || !auth.isAuthenticated() || "anonymousUser".equals(auth.getPrincipal())) {
            return Optional.empty();
        }
        if (auth.getPrincipal() instanceof UUID uuid) {
            return Optional.ofNullable(uuid);
        }
        if (auth.getPrincipal() instanceof AuthPrincipal principal) {
            return Optional.ofNullable(principal.id());
        }
        return Optional.empty();
    }
}
