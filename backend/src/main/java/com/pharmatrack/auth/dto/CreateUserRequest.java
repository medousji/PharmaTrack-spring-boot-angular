package com.pharmatrack.auth.dto;

import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.entity.UserStatus;
import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Size;

import java.util.UUID;

/**
 * Body for an administrator creating a new account. Admin-created users are
 * approved immediately; the caller can set the status when exercised rights
 * matter (inactive/suspended users cannot log in).
 */
public record CreateUserRequest(
        @NotBlank @Size(max = 100) String name,
        @NotBlank @Email @Size(max = 255) String email,
        @NotBlank @Size(min = 8, max = 72) String password,
        @NotNull UserRole role,
        UserStatus status,
        UUID pharmacieId
) {
}