package com.pharmatrack.auth.dto;

import java.util.UUID;

/** Lightweight pharmacy reference for selectors (registration, admin forms). */
public record PharmacieResponse(
        UUID id,
        String nom,
        String adresse,
        String telephone
) {
}