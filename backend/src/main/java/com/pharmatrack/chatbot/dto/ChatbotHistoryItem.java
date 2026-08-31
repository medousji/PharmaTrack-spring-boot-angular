package com.pharmatrack.chatbot.dto;

import java.time.Instant;
import java.util.Map;
import java.util.UUID;

public record ChatbotHistoryItem(
        UUID id,
        String question,
        String reponse,
        String intention,
        Map<String, Object> donnees,
        Instant createdAt) {
}