package com.pharmatrack.fournisseur.dto;

import jakarta.validation.constraints.Min;
import jakarta.validation.constraints.NotNull;

import java.util.UUID;

/**
 * Payload for the availability check (legacy {@code POST /api/verifier-disponibilite}).
 */
public record VerifierDisponibiliteRequest(
        @NotNull UUID fournisseurMedicamentId,
        @Min(1) int quantite) {
}