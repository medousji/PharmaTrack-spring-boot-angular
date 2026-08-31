package com.pharmatrack.catalog.controller;

import com.pharmatrack.catalog.dto.MedicamentCreateRequest;
import com.pharmatrack.catalog.dto.MedicamentDetailResponse;
import com.pharmatrack.catalog.dto.MedicamentResponse;
import com.pharmatrack.catalog.dto.MedicamentUpdateRequest;
import com.pharmatrack.catalog.dto.LotResponse;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.entity.MedicamentStatut;
import com.pharmatrack.catalog.service.LotService;
import com.pharmatrack.catalog.service.MedicamentService;
import com.pharmatrack.common.api.PagedResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.DeleteMapping;
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

@Tag(name = "Medicaments")
@RestController
@RequestMapping(value = "/api/v1/medicaments", produces = MediaType.APPLICATION_JSON_VALUE)
public class MedicamentController {

    private final MedicamentService service;
    private final LotService lotService;

    public MedicamentController(MedicamentService service, LotService lotService) {
        this.service = service;
        this.lotService = lotService;
    }

    @GetMapping
    public PagedResponse<MedicamentDetailResponse> list(
            @RequestParam(required = false) String search,
            @RequestParam(required = false) String classeTherapeutique,
            @RequestParam(required = false) MedicamentStatut statut,
            @RequestParam(required = false) Boolean enRupture,
            @RequestParam(required = false) Boolean prochePeremption,
            @RequestParam(defaultValue = "0") Integer page,
            @RequestParam(defaultValue = "20") Integer size) {
        return service.list(search, classeTherapeutique, statut, enRupture,
                prochePeremption, page, size);
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    @PreAuthorize("hasAnyRole('ADMIN', 'PHARMACIEN')")
    public MedicamentResponse create(@Valid @RequestBody MedicamentCreateRequest request) {
        return service.create(request);
    }

    @GetMapping("/{id}")
    public MedicamentDetailResponse get(@PathVariable UUID id) {
        return service.getDetail(id);
    }

    @PutMapping("/{id}")
    @PreAuthorize("hasAnyRole('ADMIN', 'PHARMACIEN')")
    public MedicamentResponse update(@PathVariable UUID id,
                                     @Valid @RequestBody MedicamentUpdateRequest request) {
        return service.update(id, request);
    }

    @DeleteMapping("/{id}")
    @ResponseStatus(HttpStatus.NO_CONTENT)
    @PreAuthorize("hasRole('ADMIN')")
    public void retire(@PathVariable UUID id) {
        service.retire(id);
    }

    @GetMapping("/{id}/lots")
    public PagedResponse<LotResponse> lots(@PathVariable UUID id,
                                           @RequestParam(required = false) LotStatut statut,
                                           @RequestParam(defaultValue = "0") Integer page,
                                           @RequestParam(defaultValue = "20") Integer size) {
        return lotService.listByMedicament(id, statut, page, size);
    }

    @GetMapping("/{id}/prochain-lot")
    public LotResponse prochainLot(@PathVariable UUID id) {
        return lotService.nextToDispense(id);
    }
}
