package com.pharmatrack.catalog.dto;

import java.math.BigDecimal;
import java.time.Instant;
import java.time.LocalDate;
import java.util.UUID;

/**
 * Matches the OpenAPI {@code MedicamentDetailResponse}: the base
 * {@code MedicamentResponse} enriched with server-side computed stock
 * aggregates (never computed client-side).
 */
public record MedicamentDetailResponse(
        UUID id,
        MedicamentStatutDto statut,
        Instant createdAt,
        Instant updatedAt,
        String codeCip,
        String nomCommercialFr,
        String nomCommercialAr,
        String dci,
        String formePharmaceutique,
        String dosage,
        String conditionnement,
        BigDecimal ppv,
        BigDecimal ph,
        BigDecimal prixBr,
        BigDecimal prixPublic,
        BigDecimal tauxRemboursement,
        String laboratoire,
        String paysOrigine,
        Integer stockMin,
        Integer stockMax,
        Integer seuilAlerte,
        String classeTherapeutique,
        String voieAdministration,
        String contreIndications,
        String effetsIndesirables,
        String interactionsMedicamenteuses,
        String conditionsConservation,
        String codeAtc,
        boolean estPsychotrope,
        boolean estTherLourde,
        boolean estRenouvelable,
        Integer delaiRenouvellement,
        String codeBarre,
        boolean estGenerique,
        UUID medicamentReferenceId,
        // ---- computed stock aggregates ----
        Integer stockActif,
        Integer stockTotal,
        boolean estEnRupture,
        boolean estProchePeremption,
        LocalDate datePeremptionProche,
        BigDecimal valeurStock
) {
}
