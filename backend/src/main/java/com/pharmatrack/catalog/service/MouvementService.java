package com.pharmatrack.catalog.service;

import com.pharmatrack.catalog.dto.MouvementResponse;
import com.pharmatrack.catalog.dto.MouvementTypeDto;
import com.pharmatrack.catalog.entity.Mouvement;
import com.pharmatrack.catalog.entity.MouvementType;
import com.pharmatrack.catalog.mapper.MouvementMapper;
import com.pharmatrack.catalog.repository.MouvementRepository;
import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.api.PageUtils;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.time.ZoneOffset;
import java.util.List;
import java.util.UUID;

@Service
@Transactional(readOnly = true)
public class MouvementService {

    private final MouvementRepository repository;
    private final MouvementMapper mapper;

    public MouvementService(MouvementRepository repository, MouvementMapper mapper) {
        this.repository = repository;
        this.mapper = mapper;
    }

    public PagedResponse<MouvementResponse> query(
            UUID lotId, UUID pharmacieId, MouvementTypeDto type,
            LocalDate from, LocalDate to, Integer page, Integer size) {

        Specification<Mouvement> spec = Specification.where(null);
        if (lotId != null) {
            spec = spec.and((root, q, cb) -> cb.equal(root.get("lot").get("id"), lotId));
        }
        if (pharmacieId != null) {
            spec = spec.and((root, q, cb) -> cb.equal(root.get("pharmacieId"), pharmacieId));
        }
        if (type != null) {
            spec = spec.and((root, q, cb) ->
                    cb.equal(root.get("type"), MouvementType.valueOf(type.name())));
        }
        if (from != null) {
            var start = from.atStartOfDay().toInstant(ZoneOffset.UTC);
            spec = spec.and((root, q, cb) -> cb.greaterThanOrEqualTo(root.get("createdAt"), start));
        }
        if (to != null) {
            var end = to.plusDays(1).atStartOfDay().toInstant(ZoneOffset.UTC);
            spec = spec.and((root, q, cb) -> cb.lessThan(root.get("createdAt"), end));
        }

        Pageable pageable = PageRequest.of(PageUtils.clampPage(page), PageUtils.clampSize(size),
                Sort.by(Sort.Direction.DESC, "createdAt"));
        Page<Mouvement> result = repository.findAll(spec, pageable);
        List<MouvementResponse> content = result.getContent().stream().map(mapper::toResponse).toList();
        return new PagedResponse<>(content, result.getNumber(), result.getSize(),
                result.getTotalElements(), result.getTotalPages());
    }
}
