package com.pharmatrack.catalog.controller;

import com.pharmatrack.catalog.dto.MouvementResponse;
import com.pharmatrack.catalog.dto.MouvementTypeDto;
import com.pharmatrack.catalog.service.MouvementService;
import com.pharmatrack.common.api.PagedResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.time.LocalDate;
import java.util.UUID;

@Tag(name = "Mouvements")
@RestController
@RequestMapping(value = "/api/v1/mouvements", produces = MediaType.APPLICATION_JSON_VALUE)
public class MouvementController {

    private final MouvementService service;

    public MouvementController(MouvementService service) {
        this.service = service;
    }

    @GetMapping
    public PagedResponse<MouvementResponse> query(
            @RequestParam(required = false) UUID lotId,
            @RequestParam(required = false) UUID pharmacieId,
            @RequestParam(required = false) MouvementTypeDto type,
            @RequestParam(required = false) LocalDate from,
            @RequestParam(required = false) LocalDate to,
            @RequestParam(defaultValue = "0") Integer page,
            @RequestParam(defaultValue = "20") Integer size) {
        return service.query(lotId, pharmacieId, type, from, to, page, size);
    }
}
