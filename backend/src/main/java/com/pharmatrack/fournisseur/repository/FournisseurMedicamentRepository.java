package com.pharmatrack.fournisseur.repository;

import com.pharmatrack.fournisseur.entity.FournisseurMedicament;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface FournisseurMedicamentRepository
        extends JpaRepository<FournisseurMedicament, UUID> {

    Page<FournisseurMedicament> findByFournisseurId(UUID fournisseurId, Pageable pageable);

    long countByFournisseurIdAndDisponibleTrue(UUID fournisseurId);

    List<FournisseurMedicament> findByMedicamentIdAndDisponibleTrueOrderByPrixAchatAsc(
            UUID medicamentId);

    Optional<FournisseurMedicament> findByFournisseurIdAndMedicamentId(
            UUID fournisseurId, UUID medicamentId);

    /**
     * Suppliers able to fulfil the whole demand for a medicament, keeping their
     * minimum stock reserved (mirrors the legacy {@code trouverAlternatifs}).
     */
    @Query("""
            SELECT fm FROM FournisseurMedicament fm
            WHERE fm.medicament.id = :medicamentId
              AND fm.disponible = true
              AND (fm.stockDisponible - fm.stockMinimum) >= :quantite
            ORDER BY fm.prixAchat ASC
            """)
    List<FournisseurMedicament> findAlternatifs(@Param("medicamentId") UUID medicamentId,
                                                @Param("quantite") int quantite);
}