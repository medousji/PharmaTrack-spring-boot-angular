package com.pharmatrack.catalog.entity;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.JdbcTypeCode;
import org.hibernate.annotations.UuidGenerator;
import org.hibernate.type.SqlTypes;

import java.time.Instant;
import java.util.LinkedHashMap;
import java.util.Map;
import java.util.UUID;

/**
 * An operational alert (rupture, low stock, expiration, quality, other).
 *
 * <p>{@code donneesConcernees} is backed by a Postgres {@code jsonb} column
 * bound as a structured {@link Map}. The Epic 3 evaluation engine serializes
 * a strongly-typed DTO into this map; it is never treated as an untyped ad
 * hoc array.
 */
@Entity
@Table(name = "alertes", indexes = {
        @jakarta.persistence.Index(name = "idx_alertes_type", columnList = "type"),
        @jakarta.persistence.Index(name = "idx_alertes_niveau", columnList = "niveau"),
        @jakarta.persistence.Index(name = "idx_alertes_lue", columnList = "est_lue"),
        @jakarta.persistence.Index(name = "idx_alertes_lot", columnList = "lot_id")
})
public class Alerte {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "lot_id")
    private Lot lot;

    @Enumerated(EnumType.STRING)
    @Column(name = "type", nullable = false, length = 16)
    private AlerteType type;

    @Enumerated(EnumType.STRING)
    @Column(name = "niveau", nullable = false, length = 16)
    private AlerteNiveau niveau;

    @Column(name = "message", nullable = false)
    private String message;

    @JdbcTypeCode(SqlTypes.JSON)
    @Column(name = "donnees_concernees", columnDefinition = "jsonb")
    private Map<String, Object> donneesConcernees = new LinkedHashMap<>();

    @Column(name = "est_lue", nullable = false)
    private boolean estLue = false;

    @Column(name = "resolue_at")
    private Instant resolueAt;

    @CreationTimestamp
    @Column(name = "created_at", nullable = false, updatable = false)
    private Instant createdAt;

    public UUID getId() {
        return id;
    }

    public Lot getLot() {
        return lot;
    }

    public void setLot(Lot lot) {
        this.lot = lot;
    }

    public AlerteType getType() {
        return type;
    }

    public void setType(AlerteType type) {
        this.type = type;
    }

    public AlerteNiveau getNiveau() {
        return niveau;
    }

    public void setNiveau(AlerteNiveau niveau) {
        this.niveau = niveau;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public Map<String, Object> getDonneesConcernees() {
        return donneesConcernees;
    }

    public void setDonneesConcernees(Map<String, Object> donneesConcernees) {
        this.donneesConcernees = donneesConcernees;
    }

    public boolean isEstLue() {
        return estLue;
    }

    public void setEstLue(boolean estLue) {
        this.estLue = estLue;
    }

    public Instant getResolueAt() {
        return resolueAt;
    }

    public void setResolueAt(Instant resolueAt) {
        this.resolueAt = resolueAt;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }
}
