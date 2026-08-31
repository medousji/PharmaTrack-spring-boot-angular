package com.pharmatrack.auth.mapper;

import com.pharmatrack.auth.dto.AuthUserResponse;
import com.pharmatrack.auth.entity.User;
import org.mapstruct.Mapper;
import org.mapstruct.Mapping;
import org.mapstruct.Named;
import org.mapstruct.ReportingPolicy;

@Mapper(componentModel = "spring", unmappedTargetPolicy = ReportingPolicy.IGNORE)
public interface AuthUserMapper {

    @Mapping(target = "pharmacieId", source = "pharmacie.id")
    @Mapping(target = "pharmacieNom", source = "pharmacie.nom")
    @Mapping(target = "status", expression = "java(entity.getStatus().name())")
    @Mapping(target = "isApproved", expression = "java(entity.isApproved())")
    AuthUserResponse toResponse(User entity);
}
