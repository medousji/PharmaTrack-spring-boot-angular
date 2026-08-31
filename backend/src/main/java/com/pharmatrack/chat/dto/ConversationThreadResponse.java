package com.pharmatrack.chat.dto;

import java.util.List;
import java.util.UUID;

public record ConversationThreadResponse(
        UUID contactId,
        String contactNom,
        String contactRole,
        List<MessageResponse> messages) {
}