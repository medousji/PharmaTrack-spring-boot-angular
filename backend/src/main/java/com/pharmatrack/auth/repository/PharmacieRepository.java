package com.pharmatrack.auth.repository;

import com.pharmatrack.auth.entity.Pharmacie;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.Optional;
import java.util.UUID;

public interface PharmacieRepository extends JpaRepository<Pharmacie, UUID> {

    Optional<Pharmacie> findByLicenceNumber(String licenceNumber);
}
