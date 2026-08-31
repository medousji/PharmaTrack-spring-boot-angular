package com.pharmatrack.catalog.dto;

import java.time.LocalDate;
import java.util.UUID;

/**
 * A medicament ranked by its active stock (dashboard "top médicaments").
 */
public record TopMedicament(UUID id, String nom, long stock) {
}