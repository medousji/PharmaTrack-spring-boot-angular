package com.pharmatrack.chat.entity;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;
import org.hibernate.annotations.UuidGenerator;

import java.time.Instant;
import java.util.UUID;

/**
 * A chat message between two users, optionally bound to a
 * {@code CommandeFournisseur}. Mirrors the legacy {@code messages} table
 * (expediteur/destinataire/commande nullable cascade).
 */
@Entity
@Table(name = "messages")
public class Message {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @Column(name = "expediteur_id", nullable = false)
    private UUID expediteurId;

    @Column(name = "destinataire_id", nullable = false)
    private UUID destinataireId;

    @Column(name = "commande_id")
    private UUID commandeId;

    @Column(name = "message", nullable = false, columnDefinition = "text")
    private String message;

    @Column(name = "est_lu", nullable = false)
    private boolean estLu = false;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private Instant createdAt;

    @UpdateTimestamp
    @Column(name = "updated_at")
    private Instant updatedAt;

    public UUID getId() {
        return id;
    }

    public UUID getExpediteurId() {
        return expediteurId;
    }

    public void setExpediteurId(UUID expediteurId) {
        this.expediteurId = expediteurId;
    }

    public UUID getDestinataireId() {
        return destinataireId;
    }

    public void setDestinataireId(UUID destinataireId) {
        this.destinataireId = destinataireId;
    }

    public UUID getCommandeId() {
        return commandeId;
    }

    public void setCommandeId(UUID commandeId) {
        this.commandeId = commandeId;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public boolean isEstLu() {
        return estLu;
    }

    public void setEstLu(boolean estLu) {
        this.estLu = estLu;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }

    public Instant getUpdatedAt() {
        return updatedAt;
    }
}