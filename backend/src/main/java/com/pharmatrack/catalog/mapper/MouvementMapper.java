package com.pharmatrack.catalog.mapper;

import com.pharmatrack.catalog.dto.MouvementResponse;
import com.pharmatrack.catalog.entity.Mouvement;
import org.mapstruct.Mapper;
import org.mapstruct.Mapping;
import org.mapstruct.ReportingPolicy;

@Mapper(componentModel = "spring", unmappedTargetPolicy = ReportingPolicy.IGNORE)
public interface MouvementMapper {

    @Mapping(target = "lotId", source = "lot.id")
    MouvementResponse toResponse(Mouvement entity);
}
