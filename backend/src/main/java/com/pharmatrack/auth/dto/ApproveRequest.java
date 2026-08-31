package com.pharmatrack.auth.dto;

import com.pharmatrack.auth.entity.UserRole;
import jakarta.validation.constraints.NotNull;

import java.util.UUID;

public record ApproveRequest(
        @NotNull boolean approved,
        UserRole role,
        UUID pharmacieId
) {
}
