package com.pharmatrack.fournisseur.dto;

import jakarta.validation.constraints.Min;
import jakarta.validation.constraints.NotNull;

import java.util.UUID;

/**
 * Payload for creating a purchase order (legacy {@code POST /commander/passer}).
 */
public record PasserCommandeRequest(
        @NotNull UUID fournisseurMedicamentId,
        @Min(1) int quantite) {
}