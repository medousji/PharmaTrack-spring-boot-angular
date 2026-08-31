package com.pharmatrack.chat.mapper;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.chat.dto.CommandeChatResponse;
import com.pharmatrack.chat.dto.ConversationResponse;
import com.pharmatrack.chat.dto.MessageResponse;
import com.pharmatrack.chat.entity.Message;
import com.pharmatrack.fournisseur.entity.CommandeFournisseur;
import org.springframework.stereotype.Component;

import java.time.Instant;
import java.util.Optional;

@Component
public class ChatMapper {

    public MessageResponse toMessage(Message m, Optional<User> expediteur, Optional<User> destinataire) {
        return new MessageResponse(
                m.getId(),
                m.getExpediteurId(),
                expediteur.map(User::getName).orElse("Utilisateur"),
                m.getDestinataireId(),
                destinataire.map(User::getName).orElse("Utilisateur"),
                m.getCommandeId(),
                m.getMessage(),
                m.isEstLu(),
                m.getCreatedAt());
    }

    public CommandeChatResponse toCommandeChat(CommandeFournisseur c,
                                               String dernierMessage,
                                               Instant dateDernierMessage,
                                               int nonLus) {
        return new CommandeChatResponse(
                c.getId(),
                c.getNumeroCommande(),
                c.getFournisseur().getRaisonSociale(),
                c.getStatut(),
                c.getTotalTtc(),
                c.getCreatedAt(),
                dernierMessage,
                dateDernierMessage,
                nonLus);
    }

    public ConversationResponse toConversation(User contact, Message dernier, int nonLus) {
        return new ConversationResponse(
                contact.getId(),
                contact.getName(),
                contact.getRole().name(),
                dernier.getMessage(),
                dernier.getCreatedAt(),
                nonLus);
    }
}