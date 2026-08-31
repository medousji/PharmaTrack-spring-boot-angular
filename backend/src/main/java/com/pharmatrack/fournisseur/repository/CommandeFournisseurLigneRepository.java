package com.pharmatrack.fournisseur.repository;

import com.pharmatrack.fournisseur.entity.CommandeFournisseurLigne;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;
import java.util.UUID;

public interface CommandeFournisseurLigneRepository
        extends JpaRepository<CommandeFournisseurLigne, UUID> {

    List<CommandeFournisseurLigne> findByCommandeId(UUID commandeId);
}