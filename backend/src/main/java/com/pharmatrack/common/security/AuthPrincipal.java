package com.pharmatrack.common.security;

import java.util.UUID;

/**
 * Principal placed in the Spring Security context once a JWT is validated
 * (Epic 1). Carries the user id and role so authorization can be centralized
 * via method security rather than scatters through controllers.
 */
public record AuthPrincipal(UUID id, String role) {
}
