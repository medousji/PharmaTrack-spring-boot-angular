package com.pharmatrack.catalog.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Positive;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.UUID;

/**
 * Matches the OpenAPI {@code LotCreateRequest}. Receiving a lot creates the
 * initial "entree" Mouvement.
 */
public record LotCreateRequest(
        @NotNull UUID medicamentId,
        @NotBlank String numeroLot,
        LocalDate dateFabrication,
        @NotNull LocalDate datePeremption,
        @NotNull @Positive Integer quantiteInitiale,
        String fournisseurNom,
        LocalDate dateReception,
        @NotNull BigDecimal prixAchat,
        @NotNull BigDecimal prixVente,
        String numeroFacture,
        String emplacement,
        String observations
) {
}
