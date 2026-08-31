package com.pharmatrack.catalog.controller;

import com.pharmatrack.catalog.dto.LotCreateRequest;
import com.pharmatrack.catalog.dto.LotResponse;
import com.pharmatrack.catalog.dto.LotUpdateRequest;
import com.pharmatrack.catalog.dto.StockAdjustmentRequest;
import com.pharmatrack.catalog.dto.StockAdjustmentResponse;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.service.LotService;
import com.pharmatrack.common.api.PagedResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseStatus;
import org.springframework.web.bind.annotation.RestController;

import java.util.UUID;

@Tag(name = "Lots")
@RestController
@RequestMapping(value = "/api/v1/lots", produces = MediaType.APPLICATION_JSON_VALUE)
public class LotController {

    private final LotService service;

    public LotController(LotService service) {
        this.service = service;
    }

    @GetMapping
    public PagedResponse<LotResponse> list(
            @RequestParam(required = false) Integer prochePeremptionJours,
            @RequestParam(required = false) LotStatut statut,
            @RequestParam(defaultValue = "0") Integer page,
            @RequestParam(defaultValue = "20") Integer size) {
        return service.listAll(prochePeremptionJours, statut, page, size);
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    @PreAuthorize("hasAnyRole('ADMIN', 'PHARMACIEN')")
    public LotResponse receive(@Valid @RequestBody LotCreateRequest request) {
        return service.receive(request);
    }

    @GetMapping("/{id}")
    public LotResponse get(@PathVariable UUID id) {
        return service.get(id);
    }

    @PutMapping("/{id}")
    @PreAuthorize("hasAnyRole('ADMIN', 'PHARMACIEN')")
    public LotResponse update(@PathVariable UUID id, @Valid @RequestBody LotUpdateRequest request) {
        return service.update(id, request);
    }

    @PostMapping("/{id}/adjust-stock")
    @PreAuthorize("hasAnyRole('ADMIN', 'PHARMACIEN')")
    public StockAdjustmentResponse adjustStock(@PathVariable UUID id,
                                               @Valid @RequestBody StockAdjustmentRequest request) {
        return service.adjustStock(id, request);
    }
}
