package com.pharmatrack.catalog.dto;

/**
 * Shared enumeration matching the OpenAPI {@code MedicamentStatut} schema.
 * Duplicated from the entity enum so controllers depend only on DTO types.
 */
public enum MedicamentStatutDto {
    actif, inactif, retire
}
