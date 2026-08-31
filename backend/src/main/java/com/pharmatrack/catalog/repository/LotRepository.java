package com.pharmatrack.catalog.repository;

import com.pharmatrack.catalog.entity.Lot;
import com.pharmatrack.catalog.entity.LotStatut;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.time.LocalDate;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface LotRepository extends JpaRepository<Lot, UUID> {

    Page<Lot> findByMedicamentId(UUID medicamentId, Pageable pageable);

    Page<Lot> findByMedicamentIdAndStatut(UUID medicamentId, LotStatut statut, Pageable pageable);

    Page<Lot> findByStatut(LotStatut statut, Pageable pageable);

    @Query("""
            SELECT l FROM Lot l
            WHERE (:statut IS NULL OR l.statut = :statut)
              AND l.datePeremption <= :horizon
            """)
    Page<Lot> findByExpiryBefore(@Param("horizon") LocalDate horizon,
                                 @Param("statut") LotStatut statut, Pageable pageable);

    /**
     * FEFO: the next lot to dispense for a medicament - the earliest-expiring
     * lot that still has available quantity and is not blocked/perimed.
     */
    @Query("""
            SELECT l FROM Lot l
            WHERE l.medicament.id = :medicamentId
              AND l.statut = :statut
              AND l.quantiteActuelle > 0
            ORDER BY l.datePeremption ASC, l.createdAt ASC
            """)
    List<Lot> findNextToDispense(@Param("medicamentId") UUID medicamentId,
                                 @Param("statut") LotStatut statut, Pageable pageable);

    Optional<Lot> findByNumeroLot(String numeroLot);

    /**
     * Active lots with remaining quantity expiring within a window
     * (used by the alert evaluation engine).
     */
    @Query("""
            SELECT l FROM Lot l
            WHERE l.statut = :statut
              AND l.quantiteActuelle > 0
              AND l.datePeremption BETWEEN :from AND :to
            ORDER BY l.datePeremption ASC
            """)
    List<Lot> findExpiringBetween(@Param("statut") LotStatut statut,
                                  @Param("from") LocalDate from,
                                  @Param("to") LocalDate to);
}
