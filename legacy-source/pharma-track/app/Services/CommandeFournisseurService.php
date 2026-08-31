<?php

namespace App\Services;

use App\Models\FournisseurMedicament;
use App\Models\CommandeFournisseur;
use App\Models\CommandeFournisseurLigne;
use App\Models\Alerte;
use Illuminate\Support\Facades\Log;

class CommandeFournisseurService
{
    /**
     * Vérifier la disponibilité d'un médicament chez un fournisseur
     * Prend en compte le stock minimum (stock réservé)
     */
    public function verifierDisponibilite($fournisseurMedicamentId, $quantiteDemandee)
    {
        $fm = FournisseurMedicament::with('fournisseur', 'medicament')->find($fournisseurMedicamentId);
        
        if (!$fm) {
            return [
                'disponible' => false,
                'raison' => 'Produit non trouvé'
            ];
        }
        
        if (!$fm->disponible) {
            return [
                'disponible' => false,
                'raison' => 'Produit indisponible chez ce fournisseur'
            ];
        }
        
        // Récupérer le stock minimum (10 par défaut)
        $stockMinimum = $fm->stock_minimum ?? 10;
        $stockDisponiblePourVente = $fm->stock_disponible - $stockMinimum;
        
        // Stock minimum non atteint
        if ($stockDisponiblePourVente < 0) {
            return [
                'disponible' => false,
                'type' => 'stock_minimum',
                'raison' => 'Stock minimum requis non atteint. Stock minimum: ' . $stockMinimum . ' unités. Stock actuel: ' . $fm->stock_disponible,
                'stock_actuel' => $fm->stock_disponible,
                'stock_minimum' => $stockMinimum,
                'medicament_nom' => $fm->medicament->nom_commercial_fr ?? 'N/A'
            ];
        }
        
        // Commande complète (dans la limite du stock disponible pour vente)
        if ($stockDisponiblePourVente >= $quantiteDemandee) {
            return [
                'disponible' => true,
                'type' => 'complet',
                'quantite_disponible' => $stockDisponiblePourVente,
                'fournisseur' => $fm->fournisseur,
                'prix' => $fm->prix_achat,
                'medicament_nom' => $fm->medicament->nom_commercial_fr ?? 'N/A',
                'stock_minimum' => $stockMinimum,
                'stock_actuel' => $fm->stock_disponible
            ];
        }
        
        // Commande partielle (stock disponible pour vente insuffisant mais > 0)
        if ($stockDisponiblePourVente > 0) {
            return [
                'disponible' => true,
                'type' => 'partiel',
                'quantite_disponible' => $stockDisponiblePourVente,
                'quantite_manquante' => $quantiteDemandee - $stockDisponiblePourVente,
                'fournisseur' => $fm->fournisseur,
                'prix' => $fm->prix_achat,
                'medicament_nom' => $fm->medicament->nom_commercial_fr ?? 'N/A',
                'stock_minimum' => $stockMinimum,
                'stock_actuel' => $fm->stock_disponible
            ];
        }
        
        // Rupture totale (plus rien à vendre)
        return [
            'disponible' => false,
            'type' => 'indisponible',
            'raison' => 'Stock insuffisant. Stock minimum requis: ' . $stockMinimum . ' unités. Stock actuel: ' . $fm->stock_disponible,
            'stock_actuel' => $fm->stock_disponible,
            'stock_minimum' => $stockMinimum,
            'medicament_nom' => $fm->medicament->nom_commercial_fr ?? 'N/A'
        ];
    }
    
    /**
     * Trouver des fournisseurs alternatifs pour un médicament
     */
    public function trouverAlternatifs($medicamentId, $quantiteDemandee)
    {
        $alternatifs = FournisseurMedicament::where('medicament_id', $medicamentId)
            ->where('disponible', true)
            ->whereRaw('stock_disponible - IFNULL(stock_minimum, 10) >= ?', [$quantiteDemandee])
            ->with('fournisseur', 'medicament')
            ->orderBy('prix_achat', 'asc')
            ->get();
        
        return $alternatifs;
    }
    
    /**
     * Créer une commande et mettre à jour le stock
     */
    public function creerCommande($fournisseurMedicamentId, $quantite, $pharmacieId = null)
    {
        $fm = FournisseurMedicament::with('fournisseur', 'medicament')->find($fournisseurMedicamentId);
        $verification = $this->verifierDisponibilite($fournisseurMedicamentId, $quantite);
        
        if (!$verification['disponible']) {
            return [
                'success' => false,
                'message' => $verification['raison'],
                'stock_actuel' => $verification['stock_actuel'] ?? 0,
                'stock_minimum' => $verification['stock_minimum'] ?? 10,
                'alternatifs' => $this->trouverAlternatifs($fm->medicament_id, $quantite)
            ];
        }
        
        $stockMinimum = $fm->stock_minimum ?? 10;
        $quantiteDisponibleVente = $fm->stock_disponible - $stockMinimum;
        $quantiteCommandee = min($quantite, $quantiteDisponibleVente);
        $quantiteManquante = $quantite - $quantiteCommandee;
        $stockAvant = $fm->stock_disponible;
        $nomMedicament = $fm->medicament->nom_commercial_fr ?? 'Médicament';
        
        // Créer la commande
        $commande = CommandeFournisseur::create([
            'numero_commande' => 'CMD-' . date('YmdHis') . '-' . rand(100, 999),
            'fournisseur_id' => $fm->fournisseur_id,
            'pharmacie_id' => $pharmacieId,
            'user_id' => auth()->id(),
            'date_commande' => now(),
            'statut' => $quantiteManquante > 0 ? 'partiel' : 'confirmee',
            'total_ht' => $quantiteCommandee * $fm->prix_achat,
            'total_ttc' => $quantiteCommandee * $fm->prix_achat,
            'notes' => $quantiteManquante > 0 ? "Commande partielle. Manque {$quantiteManquante} unités. Stock minimum réservé: {$stockMinimum}" : null
        ]);
        
        // Ajouter la ligne de commande
        CommandeFournisseurLigne::create([
            'commande_id' => $commande->id,
            'medicament_id' => $fm->medicament_id,
            'quantite' => $quantiteCommandee,
            'quantite_demandee' => $quantite,
            'stock_avant' => $stockAvant,
            'prix_unitaire' => $fm->prix_achat,
            'total_ligne' => $quantiteCommandee * $fm->prix_achat
        ]);
        
        // Mettre à jour le stock (garder le stock minimum)
        $nouveauStock = $stockMinimum;
        $fm->stock_disponible = $nouveauStock;
        $fm->save();
        
        Log::info('Commande créée avec stock minimum', [
            'commande_id' => $commande->id,
            'quantite_commandee' => $quantiteCommandee,
            'quantite_manquante' => $quantiteManquante,
            'stock_avant' => $stockAvant,
            'stock_apres' => $nouveauStock,
            'stock_minimum' => $stockMinimum
        ]);
        
        // Vérifier l'état du stock et créer l'alerte appropriée
        $this->verifierEtatStock($fm, $nomMedicament);
        
        return [
            'success' => true,
            'type' => $quantiteManquante > 0 ? 'partiel' : 'complet',
            'commande' => $commande,
            'quantite_commandee' => $quantiteCommandee,
            'quantite_manquante' => $quantiteManquante,
            'stock_avant' => $stockAvant,
            'stock_apres' => $nouveauStock,
            'stock_minimum' => $stockMinimum,
            'message' => $quantiteManquante > 0 
                ? "Commande partielle : {$quantiteCommandee} unités commandées. {$quantiteManquante} unités en attente. Stock minimum réservé: {$stockMinimum}"
                : "Commande complète : {$quantiteCommandee} unités commandées. Stock minimum réservé: {$stockMinimum}"
        ];
    }
    
    /**
     * Vérifier l'état du stock et créer une alerte appropriée (uniquement pour le fournisseur)
     */
    private function verifierEtatStock($fournisseurMedicament, $nomMedicament)
    {
        $stockRestant = $fournisseurMedicament->stock_disponible;
        $stockMinimum = $fournisseurMedicament->stock_minimum ?? 10;
        $seuilAlerte = $fournisseurMedicament->seuil_reapprovisionnement ?? 20;
        
        // Stock au niveau du minimum (alerte pour le fournisseur uniquement)
        if ($stockRestant <= $stockMinimum) {
            Alerte::create([
                'type' => 'stock_minimum_atteint',
                'niveau' => 'eleve',
                'message' => "⚠️ STOCK MINIMUM ATTEINT : {$nomMedicament}. Stock restant: {$stockRestant} unités (minimum: {$stockMinimum}). Veuillez réapprovisionner.",
                'est_lue' => false,
                'donnees_concernees' => [
                    'fournisseur_id' => $fournisseurMedicament->fournisseur_id,
                    'medicament_id' => $fournisseurMedicament->medicament_id,
                    'medicament_nom' => $nomMedicament,
                    'stock_restant' => $stockRestant,
                    'stock_minimum' => $stockMinimum,
                    'for_fournisseur' => true
                ]
            ]);
            Log::warning('Stock minimum atteint', [
                'medicament' => $nomMedicament,
                'stock_restant' => $stockRestant,
                'stock_minimum' => $stockMinimum
            ]);
        }
        // Stock faible (proche du minimum)
        elseif ($stockRestant <= $seuilAlerte && $stockRestant > $stockMinimum) {
            Alerte::create([
                'type' => 'stock_faible',
                'niveau' => 'moyen',
                'message' => "📉 STOCK FAIBLE : {$nomMedicament}. Stock restant: {$stockRestant} unités. Seuil d'alerte: {$seuilAlerte}",
                'est_lue' => false,
                'donnees_concernees' => [
                    'fournisseur_id' => $fournisseurMedicament->fournisseur_id,
                    'medicament_id' => $fournisseurMedicament->medicament_id,
                    'medicament_nom' => $nomMedicament,
                    'stock_restant' => $stockRestant,
                    'seuil_alerte' => $seuilAlerte,
                    'for_fournisseur' => true
                ]
            ]);
            Log::info('Stock faible', [
                'medicament' => $nomMedicament,
                'stock_restant' => $stockRestant,
                'seuil_alerte' => $seuilAlerte
            ]);
        }
    }
}