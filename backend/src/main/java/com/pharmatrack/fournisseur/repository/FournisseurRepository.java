package com.pharmatrack.fournisseur.repository;

import com.pharmatrack.fournisseur.entity.Fournisseur;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface FournisseurRepository extends JpaRepository<Fournisseur, UUID> {

    Optional<Fournisseur> findByUserId(UUID userId);

    List<Fournisseur> findByEstActifTrue();
}