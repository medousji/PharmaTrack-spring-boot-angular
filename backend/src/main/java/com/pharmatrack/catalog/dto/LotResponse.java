package com.pharmatrack.catalog.dto;

import java.math.BigDecimal;
import java.time.Instant;
import java.time.LocalDate;
import java.util.UUID;

/**
 * Matches the OpenAPI {@code LotResponse}, including the computed
 * {@code joursAvantPeremption}.
 */
public record LotResponse(
        UUID id,
        UUID medicamentId,
        String numeroLot,
        LocalDate dateFabrication,
        LocalDate datePeremption,
        Integer quantiteInitiale,
        Integer quantiteActuelle,
        String fournisseurNom,
        LocalDate dateReception,
        LotStatutDto statut,
        BigDecimal prixAchat,
        BigDecimal prixVente,
        String emplacement,
        Long joursAvantPeremption,
        Instant createdAt,
        Instant updatedAt
) {
}
