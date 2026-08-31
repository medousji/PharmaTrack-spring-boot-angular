package com.pharmatrack.fournisseur.repository;

import com.pharmatrack.fournisseur.entity.CommandeFournisseur;
import com.pharmatrack.fournisseur.entity.CommandeStatut;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.Collection;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface CommandeFournisseurRepository extends JpaRepository<CommandeFournisseur, UUID> {

    Page<CommandeFournisseur> findByFournisseurId(UUID fournisseurId, Pageable pageable);

    Page<CommandeFournisseur> findByFournisseurIdAndStatut(UUID fournisseurId,
                                                           CommandeStatut statut,
                                                           Pageable pageable);

    List<CommandeFournisseur> findTop10ByFournisseurIdOrderByCreatedAtDesc(UUID fournisseurId);

    List<CommandeFournisseur> findByFournisseurIdOrderByCreatedAtDesc(UUID fournisseurId);

    List<CommandeFournisseur> findAllByOrderByCreatedAtDesc();

    long countByFournisseurIdAndStatutIn(UUID fournisseurId,
                                         Collection<CommandeStatut> statuts);

    long countByFournisseurIdAndStatut(UUID fournisseurId, CommandeStatut statut);

    Optional<CommandeFournisseur> findByIdAndFournisseurId(UUID id, UUID fournisseurId);
}