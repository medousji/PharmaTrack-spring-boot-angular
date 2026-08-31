package com.pharmatrack.chat.dto;

import com.pharmatrack.fournisseur.entity.CommandeStatut;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.UUID;

/**
 * One order-linked conversation entry: the commande header plus the latest
 * message preview and the unread count for the current user.
 */
public record CommandeChatResponse(
        UUID id,
        String numeroCommande,
        String fournisseurNom,
        CommandeStatut statut,
        BigDecimal totalTtc,
        Instant createdAt,
        String dernierMessage,
        Instant dateDernierMessage,
        int nonLus) {
}