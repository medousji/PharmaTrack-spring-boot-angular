package com.pharmatrack.catalog.repository;

import com.pharmatrack.catalog.dto.LotExpirySummary;
import com.pharmatrack.catalog.dto.StockSummary;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.catalog.entity.MedicamentStatut;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.time.LocalDate;
import java.util.Collection;
import java.util.List;
import java.util.UUID;

public interface MedicamentRepository extends JpaRepository<Medicament, UUID>,
        JpaSpecificationExecutor<Medicament> {

    boolean existsByCodeCip(String codeCip);

    Page<Medicament> findByCodeCipContainingIgnoreCaseOrNomCommercialFrContainingIgnoreCaseOrNomCommercialArContainingIgnoreCaseOrDciContainingIgnoreCase(
            String codeCip, String nomFr, String nomAr, String dci, Pageable pageable);

    /**
     * SQL aggregate (GROUP BY) of active stock, total stock and stock value for
     * the given medicaments. No per-lot loops in application memory.
     */
    @Query("""
            SELECT new com.pharmatrack.catalog.dto.StockSummary(
                l.medicament.id,
                COALESCE(SUM(CASE WHEN l.statut = :actif THEN l.quantiteActuelle ELSE 0 END), 0),
                COALESCE(SUM(CASE WHEN l.statut <> :perime THEN l.quantiteActuelle ELSE 0 END), 0),
                COALESCE(SUM(CASE WHEN l.statut = :actif THEN l.quantiteActuelle * l.prixAchat ELSE 0 END), 0)
            )
            FROM Lot l
            WHERE l.medicament.id IN :medicamentIds
            GROUP BY l.medicament.id
            """)
    List<StockSummary> aggregateStock(
            @Param("medicamentIds") Collection<UUID> medicamentIds,
            @Param("actif") LotStatut actif,
            @Param("perime") LotStatut perime);

    /**
     * Lots with available quantity expiring within the horizon, for the given
     * medicaments. Service picks the earliest date per medicament.
     */
    @Query("""
            SELECT new com.pharmatrack.catalog.dto.LotExpirySummary(l.medicament.id, l.datePeremption)
            FROM Lot l
            WHERE l.medicament.id IN :medicamentIds
              AND l.statut = :actif
              AND l.quantiteActuelle > 0
              AND l.datePeremption BETWEEN :from AND :to
            ORDER BY l.datePeremption ASC
            """)
    List<LotExpirySummary> findNearExpiry(
            @Param("medicamentIds") Collection<UUID> medicamentIds,
            @Param("actif") LotStatut actif,
            @Param("from") LocalDate from,
            @Param("to") LocalDate to);

    /**
     * Number of medicaments currently below their minimum stock (rupture),
     * via SQL aggregation - used by dashboard counts.
     */
    @Query("""
            SELECT COUNT(m)
            FROM Medicament m
            WHERE m.statut = :actif
              AND (
                SELECT COALESCE(SUM(CASE WHEN l.statut = :lotActif THEN l.quantiteActuelle ELSE 0 END), 0)
                FROM Lot l WHERE l.medicament = m
              ) < m.stockMin
            """)
    long countEnRupture(@Param("actif") MedicamentStatut medicamentStatut,
                        @Param("lotActif") LotStatut lotActif);

    /**
     * Grouped active/total stock and stock value for every medicament.
     * Medicaments without any lots are absent (treated as zero stock).
     */
    @Query("""
            SELECT new com.pharmatrack.catalog.dto.StockSummary(
                l.medicament.id,
                COALESCE(SUM(CASE WHEN l.statut = :actif THEN l.quantiteActuelle ELSE 0 END), 0),
                COALESCE(SUM(CASE WHEN l.statut <> :perime THEN l.quantiteActuelle ELSE 0 END), 0),
                COALESCE(SUM(CASE WHEN l.statut = :actif THEN l.quantiteActuelle * l.prixAchat ELSE 0 END), 0)
            )
            FROM Lot l
            GROUP BY l.medicament.id
            """)
    List<StockSummary> aggregateStockAll(@Param("actif") LotStatut actif,
                                         @Param("perime") LotStatut perime);

    /**
     * Every medicament that is not retired (still in the care catalogue).
     */
    @Query("SELECT m FROM Medicament m WHERE m.statut <> :retire")
    List<Medicament> findAllNonRetired(@Param("retire") MedicamentStatut retire);
}
