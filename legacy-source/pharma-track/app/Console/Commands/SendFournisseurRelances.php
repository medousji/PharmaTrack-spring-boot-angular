<?php

namespace App\Console\Commands;

use App\Models\Fournisseur;
use App\Models\FournisseurMedicament;
use App\Models\Alerte;
use App\Models\Message;
use Illuminate\Console\Command;

class SendFournisseurRelances extends Command
{
    protected $signature = 'fournisseur:relances {--force : Forcer l\'envoi même si déjà envoyé récemment}';
    protected $description = 'Envoie des messages de relance aux fournisseurs pour les stocks faibles ou ruptures';

    public function handle()
    {
        $fournisseurs = Fournisseur::where('est_actif', true)
            ->where('relance_active', true)
            ->get();
        
        $relancesEnvoyees = 0;
        
        foreach ($fournisseurs as $fournisseur) {
            // Vérifier les produits en rupture ou stock faible
            $produitsCritiques = FournisseurMedicament::where('fournisseur_id', $fournisseur->id)
                ->where(function($q) {
                    $q->where('stock_disponible', '<=', 0)
                      ->orWhereRaw('stock_disponible <= stock_minimum');
                })
                ->with('medicament')
                ->get();
            
            if ($produitsCritiques->count() > 0) {
                // Vérifier si on doit envoyer une relance (pas plus d'une fois par jour)
                $derniereRelance = $fournisseur->derniere_relance;
                $doitEnvoyer = $this->option('force') || !$derniereRelance || $derniereRelance->diffInHours(now()) >= 24;
                
                if ($doitEnvoyer) {
                    $this->envoyerRelance($fournisseur, $produitsCritiques);
                    $relancesEnvoyees++;
                }
            }
        }
        
        $this->info("✅ {$relancesEnvoyees} relance(s) envoyée(s) avec succès.");
    }
    
    private function envoyerRelance($fournisseur, $produitsCritiques)
    {
        // Créer un message dans le chat pour le fournisseur
        $admin = \App\Models\User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->error('Aucun administrateur trouvé pour envoyer la relance.');
            return;
        }
        
        $message = "🔄 **RELANCE AUTOMATIQUE**\n\n";
        $message .= "Bonjour,\n\n";
        $message .= "Voici la liste des produits nécessitant un réapprovisionnement :\n\n";
        
        foreach ($produitsCritiques as $produit) {
            $nomMedicament = $produit->medicament->nom_commercial_fr ?? 'Médicament';
            $stockActuel = $produit->stock_disponible;
            $stockMinimum = $produit->stock_minimum ?? 10;
            
            if ($stockActuel <= 0) {
                $message .= "• 🔴 **RUPTURE** : {$nomMedicament} - Stock: 0 unité\n";
            } else {
                $message .= "• ⚠️ **STOCK FAIBLE** : {$nomMedicament} - Stock: {$stockActuel}/{$stockMinimum} unités\n";
            }
        }
        
        $message .= "\nMerci de bien vouloir procéder au réapprovisionnement dès que possible.\n\n";
        $message .= "Cordialement,\nL'équipe Pharma Track";
        
        // Créer le message
        Message::create([
            'expediteur_id' => $admin->id,
            'destinataire_id' => $fournisseur->user_id,
            'message' => $message,
            'est_lu' => false
        ]);
        
        // Mettre à jour les infos de relance
        $fournisseur->update([
            'derniere_relance' => now(),
            'nb_relances' => $fournisseur->nb_relances + 1
        ]);
        
        // Créer une alerte pour le fournisseur
        Alerte::create([
            'type' => 'relance',
            'niveau' => 'moyen',
            'message' => "📨 Nouvelle relance automatique. {$produitsCritiques->count()} produit(s) nécessitent votre attention.",
            'est_lue' => false,
            'donnees_concernees' => [
                'fournisseur_id' => $fournisseur->id,
                'nb_produits' => $produitsCritiques->count()
            ]
        ]);
        
        $this->info("Relance envoyée à {$fournisseur->raison_sociale} ({$produitsCritiques->count()} produits critiques)");
    }
}