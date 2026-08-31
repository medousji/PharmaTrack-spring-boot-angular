package com.pharmatrack.catalog.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;

import java.math.BigDecimal;
import java.util.UUID;

/**
 * Matches the OpenAPI {@code MedicamentUpdateRequest}: all create fields plus
 * an optional {@code statut} to transition lifecycle.
 */
public record MedicamentUpdateRequest(
        @NotBlank String codeCip,
        @NotBlank String nomCommercialFr,
        String nomCommercialAr,
        @NotBlank String dci,
        @NotBlank String formePharmaceutique,
        @NotBlank String dosage,
        String conditionnement,
        BigDecimal ppv,
        BigDecimal ph,
        BigDecimal prixBr,
        BigDecimal prixPublic,
        BigDecimal tauxRemboursement,
        String laboratoire,
        String paysOrigine,
        @NotNull Integer stockMin,
        @NotNull Integer stockMax,
        @NotNull Integer seuilAlerte,
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
        MedicamentStatutDto statut
) {
}
