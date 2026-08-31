package com.pharmatrack.fournisseur.mapper;

import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.fournisseur.dto.CommandeLigneResponse;
import com.pharmatrack.fournisseur.dto.CommandeResponse;
import com.pharmatrack.fournisseur.dto.FournisseurMedicamentResponse;
import com.pharmatrack.fournisseur.dto.MedicamentSelectionResponse;
import com.pharmatrack.fournisseur.entity.CommandeFournisseur;
import com.pharmatrack.fournisseur.entity.CommandeFournisseurLigne;
import com.pharmatrack.fournisseur.entity.FournisseurMedicament;
import org.springframework.stereotype.Component;

import java.util.List;
import java.util.Optional;
import java.util.UUID;
import java.util.function.Function;

/**
 * Maps supplier/order entities into the API response DTOs, computing the
 * per-line fulfilment fields (stock restant, quantité manquante/livrable)
 * exactly like the legacy supplier screens.
 */
@Component
public class FournisseurMapper {

    public MedicamentSelectionResponse toSelection(Medicament m) {
        return new MedicamentSelectionResponse(
                m.getId(), m.getCodeCip(),
                m.getNomCommercialFr(), m.getDci(),
                m.getFormePharmaceutique(), m.getDosage());
    }

    public FournisseurMedicamentResponse toFmResponse(FournisseurMedicament fm) {
        Medicament m = fm.getMedicament();
        return new FournisseurMedicamentResponse(
                fm.getId(),
                fm.getFournisseur().getId(),
                fm.getFournisseur().getRaisonSociale(),
                m.getId(),
                m.getNomCommercialFr(),
                m.getDci(),
                m.getFormePharmaceutique(),
                m.getDosage(),
                fm.getReferenceFournisseur(),
                fm.getPrixAchat(),
                fm.getPrixPublic(),
                fm.getStockDisponible(),
                fm.getStockMinimum(),
                fm.getDelaiLivraison(),
                fm.isDisponible(),
                fm.getDerniereMiseAJour());
    }

    /**
     * @param commande the order to map
     * @param stockFournisseur current supplier-side stock for
     * {@code (fournisseurId, medicamentId)} used to derive restant/manquant/livrable
     */
    public CommandeResponse toCommande(CommandeFournisseur commande,
                                       Function<UUID, Optional<FournisseurMedicament>> stockFournisseur) {
        List<CommandeLigneResponse> lignes = commande.getLignes().stream()
                .map(l -> toLigne(l, stockFournisseur.apply(l.getMedicament().getId())))
                .toList();
        return new CommandeResponse(
                commande.getId(),
                commande.getNumeroCommande(),
                commande.getStatut(),
                commande.getDateCommande(),
                commande.getDateLivraisonPrevue(),
                commande.getTotalHt(),
                commande.getTotalTtc(),
                commande.getFournisseur().getId(),
                commande.getFournisseur().getRaisonSociale(),
                commande.getCreatedAt(),
                lignes);
    }

    private CommandeLigneResponse toLigne(CommandeFournisseurLigne ligne,
                                          Optional<FournisseurMedicament> stockFournisseur) {
        int stockRestant = stockFournisseur.map(FournisseurMedicament::getStockDisponible).orElse(0);
        int quantiteManquante = Math.max(0, ligne.getQuantite() - stockRestant);
        int quantiteLivrable = Math.min(ligne.getQuantite(), stockRestant);
        String nom = ligne.getMedicament().getNomCommercialFr() != null
                ? ligne.getMedicament().getNomCommercialFr() : "N/A";
        return new CommandeLigneResponse(
                ligne.getId(),
                ligne.getMedicament().getId(),
                nom,
                ligne.getQuantite(),
                ligne.getQuantiteDemandee(),
                ligne.getStockAvant(),
                stockRestant,
                quantiteManquante,
                quantiteLivrable,
                ligne.getPrixUnitaire(),
                ligne.getTotalLigne());
    }
}