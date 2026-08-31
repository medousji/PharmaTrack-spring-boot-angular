package com.pharmatrack.fournisseur.dto;

import java.util.List;

/**
 * Outcome of placing an order: the created commande when successful, or the
 * reason + alternative suppliers when stock cannot cover the demand.
 */
public record CommandeResult(
        boolean success,
        String type,
        CommandeResponse commande,
        int quantiteCommandee,
        int quantiteManquante,
        int stockAvant,
        int stockApres,
        int stockMinimum,
        String message,
        List<FournisseurMedicamentResponse> alternatifs) {
}