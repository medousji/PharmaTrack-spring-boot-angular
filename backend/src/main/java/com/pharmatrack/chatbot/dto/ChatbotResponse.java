package com.pharmatrack.chatbot.dto;

import java.util.Map;

/**
 * Answer payload matching the legacy chatbot JSON contract
 * ({@code success}, {@code reponse}, plus the modern {@code intention} and
 * structured {@code donnees} used to drive the UI and the order confirmation
 * flow).
 */
public record ChatbotResponse(
        boolean success,
        String reponse,
        String intention,
        Map<String, Object> donnees) {
}