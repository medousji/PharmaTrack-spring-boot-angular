package com.pharmatrack.chat.repository;

import com.pharmatrack.chat.entity.Message;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface MessageRepository extends JpaRepository<Message, UUID> {

    List<Message> findByCommandeIdOrderByCreatedAtAsc(UUID commandeId);

    Optional<Message> findFirstByCommandeIdOrderByCreatedAtDesc(UUID commandeId);

    long countByCommandeIdAndDestinataireIdAndEstLuFalse(UUID commandeId, UUID destinataireId);

    long countByExpediteurIdAndDestinataireIdAndEstLuFalse(UUID expediteurId, UUID destinataireId);

    /**
     * Full thread between two users (both directions), oldest first.
     */
    @Query("""
            SELECT m FROM Message m
            WHERE (m.expediteurId = :a AND m.destinataireId = :b)
               OR (m.expediteurId = :b AND m.destinataireId = :a)
            ORDER BY m.createdAt ASC
            """)
    List<Message> findThread(@Param("a") UUID a, @Param("b") UUID b);

    /**
     * Latest message between two users (both directions).
     */
    @Query("""
            SELECT m FROM Message m
            WHERE (m.expediteurId = :a AND m.destinataireId = :b)
               OR (m.expediteurId = :b AND m.destinataireId = :a)
            ORDER BY m.createdAt DESC
            """)
    List<Message> findLatest(@Param("a") UUID a, @Param("b") UUID b, Pageable pageable);

    @Modifying
    @Query("""
            UPDATE Message m SET m.estLu = true
            WHERE m.destinataireId = :userId
              AND m.estLu = false
              AND (:commandeId IS NULL OR m.commandeId = :commandeId)
            """)
    int markLu(@Param("userId") UUID userId, @Param("commandeId") UUID commandeId);
}