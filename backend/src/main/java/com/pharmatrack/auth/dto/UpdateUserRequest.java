package com.pharmatrack.auth.dto;

import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.entity.UserStatus;
import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;

import java.util.UUID;

/**
 * Body for editing an existing account. Password is optional: when blank, the
 * current password is left untouched.
 */
public record UpdateUserRequest(
        @NotBlank @Size(max = 100) String name,
        @NotBlank @Email @Size(max = 255) String email,
        UserRole role,
        UserStatus status,
        UUID pharmacieId,
        @Size(min = 8, max = 72) String password
) {
}