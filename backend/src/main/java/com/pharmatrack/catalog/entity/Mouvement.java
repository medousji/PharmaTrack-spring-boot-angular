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
import org.hibernate.annotations.UuidGenerator;

import java.time.Instant;
import java.util.UUID;

/**
 * Append-only stock ledger entry. Each movement snapshots the lot quantity
 * before and after so the ledger is auditable without trusting current state.
 * This table is never updated or deleted.
 *
 * <p>{@code pharmacieId}/{@code userId} are kept as scalar UUID columns for
 * now; they become proper FK columns once the pharmacie/auth modules land
 * (Epic 1 / procurement). {@code medicament_id} is intentionally <b>not</b>
 * duplicated here - it is always derivable via the lot.
 */
@Entity
@Table(name = "mouvements", indexes = {
        @jakarta.persistence.Index(name = "idx_mouvements_lot", columnList = "lot_id"),
        @jakarta.persistence.Index(name = "idx_mouvements_pharmacie", columnList = "pharmacie_id"),
        @jakarta.persistence.Index(name = "idx_mouvements_type", columnList = "type"),
        @jakarta.persistence.Index(name = "idx_mouvements_created", columnList = "created_at")
})
public class Mouvement {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "lot_id", nullable = false)
    private Lot lot;

    @Column(name = "pharmacie_id")
    private UUID pharmacieId;

    @Column(name = "user_id")
    private UUID userId;

    @Enumerated(EnumType.STRING)
    @Column(name = "type", nullable = false, length = 16)
    private MouvementType type;

    @Column(name = "quantite", nullable = false)
    private Integer quantite;

    @Column(name = "quantite_avant", nullable = false)
    private Integer quantiteAvant;

    @Column(name = "quantite_apres", nullable = false)
    private Integer quantiteApres;

    @Column(name = "reference")
    private String reference;

    @Column(name = "motif")
    private String motif;

    @Column(name = "scanned_at")
    private Instant scannedAt;

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

    public UUID getPharmacieId() {
        return pharmacieId;
    }

    public void setPharmacieId(UUID pharmacieId) {
        this.pharmacieId = pharmacieId;
    }

    public UUID getUserId() {
        return userId;
    }

    public void setUserId(UUID userId) {
        this.userId = userId;
    }

    public MouvementType getType() {
        return type;
    }

    public void setType(MouvementType type) {
        this.type = type;
    }

    public Integer getQuantite() {
        return quantite;
    }

    public void setQuantite(Integer quantite) {
        this.quantite = quantite;
    }

    public Integer getQuantiteAvant() {
        return quantiteAvant;
    }

    public void setQuantiteAvant(Integer quantiteAvant) {
        this.quantiteAvant = quantiteAvant;
    }

    public Integer getQuantiteApres() {
        return quantiteApres;
    }

    public void setQuantiteApres(Integer quantiteApres) {
        this.quantiteApres = quantiteApres;
    }

    public String getReference() {
        return reference;
    }

    public void setReference(String reference) {
        this.reference = reference;
    }

    public String getMotif() {
        return motif;
    }

    public void setMotif(String motif) {
        this.motif = motif;
    }

    public Instant getScannedAt() {
        return scannedAt;
    }

    public void setScannedAt(Instant scannedAt) {
        this.scannedAt = scannedAt;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }
}
