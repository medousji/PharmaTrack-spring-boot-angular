package com.pharmatrack.catalog.controller;

import com.pharmatrack.catalog.dto.AlerteEvaluationSummary;
import com.pharmatrack.catalog.dto.AlerteNiveauDto;
import com.pharmatrack.catalog.dto.AlerteResponse;
import com.pharmatrack.catalog.dto.AlerteTypeDto;
import com.pharmatrack.catalog.service.AlerteEvaluationService;
import com.pharmatrack.catalog.service.AlerteService;
import com.pharmatrack.common.api.PagedResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.http.MediaType;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.util.UUID;

@Tag(name = "Alertes")
@RestController
@RequestMapping(value = "/api/v1/alertes", produces = MediaType.APPLICATION_JSON_VALUE)
public class AlerteController {

    private final AlerteService service;
    private final AlerteEvaluationService evaluationService;

    public AlerteController(AlerteService service, AlerteEvaluationService evaluationService) {
        this.service = service;
        this.evaluationService = evaluationService;
    }

    @GetMapping
    public PagedResponse<AlerteResponse> list(
            @RequestParam(required = false) AlerteTypeDto type,
            @RequestParam(required = false) AlerteNiveauDto niveau,
            @RequestParam(required = false) Boolean estLue,
            @RequestParam(defaultValue = "0") Integer page,
            @RequestParam(defaultValue = "20") Integer size) {
        return service.list(type, niveau, estLue, page, size);
    }

    @PostMapping
    @PreAuthorize("hasRole('ADMIN')")
    public AlerteEvaluationSummary reEvaluate() {
        return evaluationService.reEvaluate();
    }

    @PostMapping("/{id}/lire")
    public AlerteResponse markRead(@PathVariable UUID id) {
        return service.markRead(id);
    }

    @PostMapping("/{id}/resoudre")
    public AlerteResponse resolve(@PathVariable UUID id) {
        return service.resolve(id);
    }
}
