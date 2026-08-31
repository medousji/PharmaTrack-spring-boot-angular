package com.pharmatrack.catalog.service;

import com.pharmatrack.catalog.dto.AlerteNiveauDto;
import com.pharmatrack.catalog.dto.AlerteResponse;
import com.pharmatrack.catalog.dto.AlerteTypeDto;
import com.pharmatrack.catalog.entity.Alerte;
import com.pharmatrack.catalog.entity.AlerteNiveau;
import com.pharmatrack.catalog.entity.AlerteType;
import com.pharmatrack.catalog.mapper.AlerteMapper;
import com.pharmatrack.catalog.repository.AlerteRepository;
import com.pharmatrack.common.api.PagedResponse;
import com.pharmatrack.common.api.PageUtils;
import com.pharmatrack.common.error.ResourceNotFoundException;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;
import java.util.List;
import java.util.UUID;

@Service
@Transactional
public class AlerteService {

    private final AlerteRepository repository;
    private final AlerteMapper mapper;

    public AlerteService(AlerteRepository repository, AlerteMapper mapper) {
        this.repository = repository;
        this.mapper = mapper;
    }

    @Transactional(readOnly = true)
    public PagedResponse<AlerteResponse> list(AlerteTypeDto type, AlerteNiveauDto niveau,
                                              Boolean estLue, Integer page, Integer size) {
        Specification<Alerte> spec = Specification.where(null);
        if (type != null) {
            spec = spec.and((root, q, cb) -> cb.equal(root.get("type"), AlerteType.valueOf(type.name())));
        }
        if (niveau != null) {
            spec = spec.and((root, q, cb) -> cb.equal(root.get("niveau"), AlerteNiveau.valueOf(niveau.name())));
        }
        if (estLue != null) {
            spec = spec.and((root, q, cb) -> cb.equal(root.get("estLue"), estLue));
        }
        Pageable pageable = PageRequest.of(PageUtils.clampPage(page), PageUtils.clampSize(size),
                Sort.by(Sort.Direction.DESC, "createdAt"));
        Page<Alerte> result = repository.findAll(spec, pageable);
        List<AlerteResponse> content = result.getContent().stream().map(mapper::toResponse).toList();
        return new PagedResponse<>(content, result.getNumber(), result.getSize(),
                result.getTotalElements(), result.getTotalPages());
    }

    public AlerteResponse markRead(UUID id) {
        Alerte alerte = get(id);
        alerte.setEstLue(true);
        return mapper.toResponse(repository.save(alerte));
    }

    public AlerteResponse resolve(UUID id) {
        Alerte alerte = get(id);
        alerte.setEstLue(true);
        alerte.setResolueAt(Instant.now());
        return mapper.toResponse(repository.save(alerte));
    }

    @Transactional(readOnly = true)
    public long countUnread() {
        return repository.countByEstLueFalse();
    }

    private Alerte get(UUID id) {
        return repository.findById(id)
                .orElseThrow(() -> new ResourceNotFoundException("alerte", id));
    }
}
