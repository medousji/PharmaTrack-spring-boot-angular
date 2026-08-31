package com.pharmatrack.chatbot.entity;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.JdbcTypeCode;
import org.hibernate.annotations.UpdateTimestamp;
import org.hibernate.annotations.UuidGenerator;
import org.hibernate.type.SqlTypes;

import java.time.Instant;
import java.util.Map;
import java.util.UUID;

/**
 * One assistant exchange. Mirrors legacy {@code chatbot_conversations}: the
 * question, the generated answer, the detected intent and any structured
 * payload (e.g. a pending order awaiting confirmation).
 */
@Entity
@Table(name = "chatbot_conversations")
public class ChatbotConversation {

    @Id
    @GeneratedValue
    @UuidGenerator
    private UUID id;

    @Column(name = "user_id", nullable = false)
    private UUID userId;

    @Column(name = "question", nullable = false, columnDefinition = "text")
    private String question;

    @Column(name = "reponse", nullable = false, columnDefinition = "text")
    private String reponse;

    @Column(name = "intention")
    private String intention;

    @JdbcTypeCode(SqlTypes.JSON)
    @Column(name = "donnees", columnDefinition = "jsonb")
    private Map<String, Object> donnees;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private Instant createdAt;

    @UpdateTimestamp
    @Column(name = "updated_at")
    private Instant updatedAt;

    public UUID getId() {
        return id;
    }

    public UUID getUserId() {
        return userId;
    }

    public void setUserId(UUID userId) {
        this.userId = userId;
    }

    public String getQuestion() {
        return question;
    }

    public void setQuestion(String question) {
        this.question = question;
    }

    public String getReponse() {
        return reponse;
    }

    public void setReponse(String reponse) {
        this.reponse = reponse;
    }

    public String getIntention() {
        return intention;
    }

    public void setIntention(String intention) {
        this.intention = intention;
    }

    public Map<String, Object> getDonnees() {
        return donnees;
    }

    public void setDonnees(Map<String, Object> donnees) {
        this.donnees = donnees;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }

    public Instant getUpdatedAt() {
        return updatedAt;
    }
}