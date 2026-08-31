package com.pharmatrack.catalog.mapper;

import com.pharmatrack.catalog.dto.LotCreateRequest;
import com.pharmatrack.catalog.dto.LotResponse;
import com.pharmatrack.catalog.dto.LotUpdateRequest;
import com.pharmatrack.catalog.entity.Lot;
import org.mapstruct.Mapper;
import org.mapstruct.Mapping;
import org.mapstruct.MappingTarget;
import org.mapstruct.ReportingPolicy;

import java.time.LocalDate;
import java.time.temporal.ChronoUnit;

@Mapper(componentModel = "spring", unmappedTargetPolicy = ReportingPolicy.IGNORE)
public interface LotMapper {

    @Mapping(target = "id", ignore = true)
    @Mapping(target = "medicament", ignore = true)
    @Mapping(target = "quantiteActuelle", ignore = true)
    @Mapping(target = "statut", ignore = true)
    @Mapping(target = "createdAt", ignore = true)
    @Mapping(target = "updatedAt", ignore = true)
    Lot toEntity(LotCreateRequest request);

    @Mapping(target = "id", ignore = true)
    @Mapping(target = "medicament", ignore = true)
    @Mapping(target = "quantiteInitiale", ignore = true)
    @Mapping(target = "quantiteActuelle", ignore = true)
    @Mapping(target = "numeroLot", ignore = true)
    @Mapping(target = "dateFabrication", ignore = true)
    @Mapping(target = "datePeremption", ignore = true)
    @Mapping(target = "fournisseurNom", ignore = true)
    @Mapping(target = "dateReception", ignore = true)
    @Mapping(target = "prixAchat", ignore = true)
    @Mapping(target = "numeroFacture", ignore = true)
    @Mapping(target = "statut", ignore = true)
    @Mapping(target = "createdAt", ignore = true)
    @Mapping(target = "updatedAt", ignore = true)
    void update(LotUpdateRequest request, @MappingTarget Lot target);

    @Mapping(target = "medicamentId", source = "medicament.id")
    @Mapping(target = "joursAvantPeremption", expression = "java(joursAvantPeremption(entity))")
    LotResponse toResponse(Lot entity);

    default long joursAvantPeremption(Lot lot) {
        return lot.getDatePeremption() == null ? 0L
                : ChronoUnit.DAYS.between(LocalDate.now(), lot.getDatePeremption());
    }
}
