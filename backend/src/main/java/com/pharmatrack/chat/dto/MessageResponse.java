package com.pharmatrack.chat.dto;

import java.time.Instant;
import java.util.UUID;

public record MessageResponse(
        UUID id,
        UUID expediteurId,
        String expediteurNom,
        UUID destinataireId,
        String destinataireNom,
        UUID commandeId,
        String message,
        boolean estLu,
        Instant createdAt) {
}