package com.pharmatrack.fournisseur.dto;

import java.math.BigDecimal;
import java.util.UUID;

/**
 * Result of a stock-availability check placed against one supplier+medicament
 * (mirrors legacy {@code CommandeFournisseurService::verifierDisponibilite}).
 */
public record DisponibiliteResponse(
        boolean disponible,
        String type,
        String raison,
        Integer quantiteDisponible,
        Integer quantiteManquante,
        Integer stockMinimum,
        Integer stockActuel,
        BigDecimal prixAchat,
        String medicamentNom,
        UUID fournisseurId,
        String fournisseurNom) {
}