package com.pharmatrack.catalog.mapper;

import com.pharmatrack.catalog.dto.MedicamentCreateRequest;
import com.pharmatrack.catalog.dto.MedicamentDetailResponse;
import com.pharmatrack.catalog.dto.MedicamentResponse;
import com.pharmatrack.catalog.dto.MedicamentUpdateRequest;
import com.pharmatrack.catalog.entity.Medicament;
import org.mapstruct.Mapper;
import org.mapstruct.Mapping;
import org.mapstruct.MappingTarget;
import org.mapstruct.ReportingPolicy;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.UUID;

@Mapper(componentModel = "spring", unmappedTargetPolicy = ReportingPolicy.IGNORE)
public interface MedicamentMapper {

    @Mapping(target = "id", ignore = true)
    @Mapping(target = "medicamentReference", ignore = true)
    @Mapping(target = "generiques", ignore = true)
    @Mapping(target = "statut", ignore = true)
    @Mapping(target = "createdAt", ignore = true)
    @Mapping(target = "updatedAt", ignore = true)
    Medicament toEntity(MedicamentCreateRequest request);

    @Mapping(target = "id", ignore = true)
    @Mapping(target = "medicamentReference", ignore = true)
    @Mapping(target = "generiques", ignore = true)
    @Mapping(target = "createdAt", ignore = true)
    @Mapping(target = "updatedAt", ignore = true)
    void update(MedicamentUpdateRequest request, @MappingTarget Medicament target);

    @Mapping(target = "medicamentReferenceId", source = "medicamentReference.id")
    MedicamentResponse toResponse(Medicament entity);

    default MedicamentDetailResponse toDetail(
            Medicament entity,
            Integer stockActif,
            Integer stockTotal,
            boolean estEnRupture,
            boolean estProchePeremption,
            LocalDate datePeremptionProche,
            BigDecimal valeurStock) {
        MedicamentResponse base = toResponse(entity);
        return new MedicamentDetailResponse(
                base.id(), base.statut(), base.createdAt(), base.updatedAt(),
                base.codeCip(), base.nomCommercialFr(), base.nomCommercialAr(), base.dci(),
                base.formePharmaceutique(), base.dosage(), base.conditionnement(),
                base.ppv(), base.ph(), base.prixBr(), base.prixPublic(), base.tauxRemboursement(),
                base.laboratoire(), base.paysOrigine(), base.stockMin(), base.stockMax(),
                base.seuilAlerte(), base.classeTherapeutique(), base.voieAdministration(),
                base.contreIndications(), base.effetsIndesirables(), base.interactionsMedicamenteuses(),
                base.conditionsConservation(), base.codeAtc(), base.estPsychotrope(),
                base.estTherLourde(), base.estRenouvelable(), base.delaiRenouvellement(),
                base.codeBarre(), base.estGenerique(), base.medicamentReferenceId(),
                stockActif, stockTotal, estEnRupture, estProchePeremption, datePeremptionProche, valeurStock);
    }

    default UUID map(Medicament medicament) {
        return medicament == null ? null : medicament.getId();
    }

    default boolean bool(java.lang.Boolean value) {
        return value != null && value;
    }
}
