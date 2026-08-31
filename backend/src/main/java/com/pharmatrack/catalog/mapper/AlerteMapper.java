package com.pharmatrack.catalog.mapper;

import com.pharmatrack.catalog.dto.AlerteResponse;
import com.pharmatrack.catalog.entity.Alerte;
import org.mapstruct.Mapper;
import org.mapstruct.Mapping;
import org.mapstruct.ReportingPolicy;

@Mapper(componentModel = "spring", unmappedTargetPolicy = ReportingPolicy.IGNORE)
public interface AlerteMapper {

    @Mapping(target = "lotId", source = "lot.id")
    AlerteResponse toResponse(Alerte entity);
}
