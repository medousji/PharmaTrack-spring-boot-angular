package com.pharmatrack.catalog.dto;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.UUID;

/**
 * Matches the OpenAPI {@code MedicamentResponse}: the full create payload plus
 * id, statut and audit timestamps.
 */
public record MedicamentResponse(
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
        UUID medicamentReferenceId
) {
}
