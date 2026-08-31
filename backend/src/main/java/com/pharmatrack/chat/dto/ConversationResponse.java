package com.pharmatrack.chat.dto;

import java.time.Instant;
import java.util.UUID;

/**
 * A direct (commande-less) conversation entry: the contact user plus the last
 * message preview and the unread count for the current user.
 */
public record ConversationResponse(
        UUID contactId,
        String nom,
        String role,
        String dernierMessage,
        Instant date,
        int nonLus) {
}