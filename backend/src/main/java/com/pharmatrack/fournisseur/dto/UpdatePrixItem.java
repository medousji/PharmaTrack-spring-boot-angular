package com.pharmatrack.fournisseur.dto;

import java.math.BigDecimal;
import java.util.UUID;

/**
 * One row of the supplier bulk price/stock update.
 */
public record UpdatePrixItem(
        UUID id,
        BigDecimal prixAchat,
        Integer stockDisponible,
        Boolean disponible) {
}