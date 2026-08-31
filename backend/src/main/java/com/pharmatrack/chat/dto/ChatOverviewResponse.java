package com.pharmatrack.chat.dto;

import java.util.List;

/**
 * Everything the Chat page needs on first load and every poll: both
 * conversation families plus a global unread counter for the badge.
 */
public record ChatOverviewResponse(
        List<CommandeChatResponse> commandes,
        List<ConversationResponse> conversations,
        long totalNonLus) {
}