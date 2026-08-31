package com.pharmatrack.auth.service;

import com.pharmatrack.auth.dto.PharmacieResponse;
import com.pharmatrack.auth.repository.PharmacieRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

/**
 * Read-only pharmacy references used by selectors (registration form, admin
 * user forms).
 */
@Service
public class PharmacieService {

    private final PharmacieRepository pharmacieRepository;

    public PharmacieService(PharmacieRepository pharmacieRepository) {
        this.pharmacieRepository = pharmacieRepository;
    }

    @Transactional(readOnly = true)
    public List<PharmacieResponse> listAll() {
        return pharmacieRepository.findAll().stream()
                .map(p -> new PharmacieResponse(
                        p.getId(), p.getNom(), p.getAdresse(), p.getTelephone()))
                .toList();
    }
}