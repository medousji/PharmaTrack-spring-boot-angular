package com.pharmatrack.chat.dto;

import java.util.List;

public record CommandeThreadResponse(
        com.pharmatrack.fournisseur.dto.CommandeResponse commande,
        List<MessageResponse> messages) {
}