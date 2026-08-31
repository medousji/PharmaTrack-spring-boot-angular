package com.pharmatrack.catalog.repository;

import com.pharmatrack.catalog.entity.Alerte;
import com.pharmatrack.catalog.entity.AlerteNiveau;
import com.pharmatrack.catalog.entity.AlerteType;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.Collection;
import java.util.List;
import java.util.UUID;

public interface AlerteRepository extends JpaRepository<Alerte, UUID>,
        JpaSpecificationExecutor<Alerte> {

    Page<Alerte> findByType(AlerteType type, Pageable pageable);

    Page<Alerte> findByNiveau(AlerteNiveau niveau, Pageable pageable);

    Page<Alerte> findByEstLue(boolean estLue, Pageable pageable);

    long countByEstLueFalse();

    @Query("""
            SELECT a FROM Alerte a
            WHERE a.type = :type
              AND a.resolueAt IS NULL
              AND a.lot.id IN :lotIds
            """)
    List<Alerte> findOpenForLots(@Param("type") AlerteType type,
                                 @Param("lotIds") Collection<UUID> lotIds);

    /**
     * All unresolved alerts of a type (mirrors the partial unique index guard
     * {@code uq_alertes_type_lot_open}: a row only frees its slot once resolved).
     */
    @Query("SELECT a FROM Alerte a WHERE a.type = :type AND a.resolueAt IS NULL")
    List<Alerte> findOpenByType(@Param("type") AlerteType type);
}
