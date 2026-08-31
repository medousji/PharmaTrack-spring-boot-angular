package com.pharmatrack.chat.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;

import java.util.UUID;

public record EnvoyerMessageRequest(
        @NotBlank @Size(max = 1000) String message,
        UUID destinataireId,
        UUID commandeId) {
}