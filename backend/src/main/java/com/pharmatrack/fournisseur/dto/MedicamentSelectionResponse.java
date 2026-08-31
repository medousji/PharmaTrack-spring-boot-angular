package com.pharmatrack.fournisseur.dto;

import java.util.UUID;

/**
 * Lightweight medicament row for the "choose a medicament to order" screen.
 */
public record MedicamentSelectionResponse(
        UUID id,
        String codeCip,
        String nomCommercialFr,
        String dci,
        String formePharmaceutique,
        String dosage) {
}