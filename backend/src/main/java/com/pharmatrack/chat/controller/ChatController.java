package com.pharmatrack.chat.controller;

import com.pharmatrack.chat.dto.ChatOverviewResponse;
import com.pharmatrack.chat.dto.CommandeThreadResponse;
import com.pharmatrack.chat.dto.ConversationThreadResponse;
import com.pharmatrack.chat.dto.EnvoyerMessageRequest;
import com.pharmatrack.chat.dto.MessageResponse;
import com.pharmatrack.chat.service.ChatService;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.common.security.CurrentUser;
import jakarta.validation.Valid;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.UUID;

/**
 * Chat endpoints. Mirrors the legacy {@code ChatController} routes: the
 * conversation overview, the order-linked thread, the direct thread and the
 * message sends (envoyer / envoyerDirect).
 */
@RestController
@RequestMapping("/api/v1/chat")
@PreAuthorize("hasAnyRole('ADMIN', 'PHARMACIEN', 'FOURNISSEUR')")
public class ChatController {

    private final ChatService service;
    private final CurrentUser currentUser;

    public ChatController(ChatService service, CurrentUser currentUser) {
        this.service = service;
        this.currentUser = currentUser;
    }

    @GetMapping("/overview")
    public ChatOverviewResponse overview() {
        return service.overview(uid());
    }

    @GetMapping("/commandes/{commandeId}")
    public CommandeThreadResponse commandeThread(@PathVariable UUID commandeId) {
        return service.commandeThread(commandeId, uid());
    }

    @GetMapping("/conversations/{contactId}")
    public ConversationThreadResponse conversationThread(@PathVariable UUID contactId) {
        return service.conversationThread(contactId, uid());
    }

    @PostMapping("/messages")
    public MessageResponse envoyer(@Valid @RequestBody EnvoyerMessageRequest request) {
        return service.envoyer(request, uid());
    }

    private UUID uid() {
        return currentUser.id().orElseThrow(() -> new UnauthorizedException(
                "Un utilisateur authentifié est requis."));
    }
}