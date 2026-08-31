package com.pharmatrack.chatbot.repository;

import com.pharmatrack.chatbot.entity.ChatbotConversation;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface ChatbotConversationRepository extends JpaRepository<ChatbotConversation, UUID> {

    List<ChatbotConversation> findTop20ByUserIdOrderByCreatedAtDesc(UUID userId);

    Optional<ChatbotConversation> findFirstByUserIdOrderByCreatedAtDesc(UUID userId);
}