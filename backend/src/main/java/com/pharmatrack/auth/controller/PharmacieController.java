package com.pharmatrack.auth.controller;

import com.pharmatrack.auth.dto.PharmacieResponse;
import com.pharmatrack.auth.service.PharmacieService;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@Tag(name = "Pharmacies")
@RestController
@RequestMapping(value = "/api/v1/pharmacies", produces = MediaType.APPLICATION_JSON_VALUE)
public class PharmacieController {

    private final PharmacieService service;

    public PharmacieController(PharmacieService service) {
        this.service = service;
    }

    @GetMapping
    public List<PharmacieResponse> list() {
        return service.listAll();
    }
}