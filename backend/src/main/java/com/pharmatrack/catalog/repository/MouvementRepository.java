package com.pharmatrack.catalog.repository;

import com.pharmatrack.catalog.entity.Mouvement;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.JpaSpecificationExecutor;

import java.util.UUID;

/**
 * Stock ledger. Filters for lot/pharmacie/type/date-range are expressed as
 * {@link org.springframework.data.jpa.domain.Specification}s in the service.
 */
public interface MouvementRepository extends JpaRepository<Mouvement, UUID>,
        JpaSpecificationExecutor<Mouvement> {

    Page<Mouvement> findByLotId(UUID lotId, Pageable pageable);
}
