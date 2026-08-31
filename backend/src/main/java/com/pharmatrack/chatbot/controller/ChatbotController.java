package com.pharmatrack.chatbot.controller;

import com.pharmatrack.chatbot.dto.ChatbotHistoryItem;
import com.pharmatrack.chatbot.dto.ChatbotMessageRequest;
import com.pharmatrack.chatbot.dto.ChatbotResponse;
import com.pharmatrack.chatbot.service.ChatbotService;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.common.security.CurrentUser;
import jakarta.validation.Valid;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;
import java.util.UUID;

/**
 * Assistant Pharma IA endpoints (mirror of {@code /assistant} and
 * {@code /assistant/message}). The print/export-PDF variant is handled by the
 * frontend (browser print) so no extra server dependency is needed.
 */
@RestController
@RequestMapping("/api/v1/assistant")
@PreAuthorize("hasAnyRole('ADMIN', 'PHARMACIEN')")
public class ChatbotController {

    private final ChatbotService service;
    private final CurrentUser currentUser;

    public ChatbotController(ChatbotService service, CurrentUser currentUser) {
        this.service = service;
        this.currentUser = currentUser;
    }

    @GetMapping("/historique")
    public List<ChatbotHistoryItem> historique() {
        return service.historique(uid());
    }

    @PostMapping("/message")
    public ChatbotResponse message(@Valid @RequestBody ChatbotMessageRequest request) {
        return service.message(request.message(), uid());
    }

    private UUID uid() {
        return currentUser.id().orElseThrow(() -> new UnauthorizedException(
                "Un utilisateur authentifié est requis."));
    }
}