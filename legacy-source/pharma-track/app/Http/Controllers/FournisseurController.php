<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\CommandeFournisseur;
use App\Models\FournisseurMedicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FournisseurController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Récupère ou crée automatiquement la fiche fournisseur
     */
    private function getOrCreateFournisseur()
    {
        $user = Auth::user();
        
        if ($user->role !== 'fournisseur') {
            return null;
        }
        
        $fournisseur = Fournisseur::where('user_id', $user->id)->first();
        
        if (!$fournisseur) {
            $fournisseur = Fournisseur::create([
                'user_id' => $user->id,
                'raison_sociale' => $user->name,
                'matricule_fiscal' => null,
                'adresse' => null,
                'telephone' => null,
                'email_pro' => $user->email,
                'delai_livraison_moyen' => 7,
                'est_actif' => true
            ]);
        }
        
        return $fournisseur;
    }

    /**
     * Dashboard du fournisseur
     */
    public function dashboard()
    {
        $fournisseur = $this->getOrCreateFournisseur();
        
        if (!$fournisseur) {
            return redirect()->route('home')->with('error', 'Compte fournisseur non trouvé.');
        }
        
        // Statistiques
        $stats = [
            'commandes_encours' => 0,
            'commandes_livrees' => 0,
            'produits_disponibles' => FournisseurMedicament::where('fournisseur_id', $fournisseur->id)
                ->where('disponible', true)
                ->count(),
        ];
        
        // Récupérer les commandes si la table existe
        if (Schema::hasTable('commandes_fournisseurs')) {
            try {
                $stats['commandes_encours'] = CommandeFournisseur::where('fournisseur_id', $fournisseur->id)
                    ->whereIn('statut', ['en_attente', 'confirmee', 'preparation'])
                    ->count();
                $stats['commandes_livrees'] = CommandeFournisseur::where('fournisseur_id', $fournisseur->id)
                    ->where('statut', 'livree')
                    ->count();
            } catch (\Exception $e) {
                // Ignorer les erreurs
            }
        }
        
        // Dernières commandes avec les relations
        $commandes = collect();
        if (Schema::hasTable('commandes_fournisseurs')) {
            try {
                $commandes = CommandeFournisseur::where('fournisseur_id', $fournisseur->id)
                    ->with('lignes.medicament')
                    ->latest()
                    ->take(10)
                    ->get();
                
                // Calculer le stock restant pour chaque ligne de commande
                foreach ($commandes as $commande) {
                    foreach ($commande->lignes as $ligne) {
                        $fm = FournisseurMedicament::where('fournisseur_id', $fournisseur->id)
                            ->where('medicament_id', $ligne->medicament_id)
                            ->first();
                        $ligne->stock_restant = $fm ? $fm->stock_disponible : 0;
                        $ligne->quantite_manquante = max(0, $ligne->quantite - $ligne->stock_restant);
                        $ligne->quantite_livrable = min($ligne->quantite, $ligne->stock_restant);
                    }
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs
            }
        }
        
        return view('fournisseur.dashboard', compact('fournisseur', 'stats', 'commandes'));
    }

    /**
     * Liste des commandes reçues avec filtres
     */
    public function commandes(Request $request)
    {
        $fournisseur = $this->getOrCreateFournisseur();
        
        if (!$fournisseur) {
            return redirect()->route('home')->with('error', 'Compte fournisseur non trouvé.');
        }
        
        $query = CommandeFournisseur::where('fournisseur_id', $fournisseur->id);
        
        // Récupérer le statut depuis l'URL ou la requête
        $statut = $request->route('statut') ?? $request->get('statut');
        
        // Filtrer par statut si demandé et si différent de 'tous'
        if ($statut && $statut !== 'tous') {
            $query->where('statut', $statut);
        }
        
        // Charger les relations lignes et medicament
        $commandes = $query->with('lignes.medicament')
            ->latest()
            ->paginate(20);
        
        // Calculer le stock restant et la quantité manquante pour chaque ligne
        foreach ($commandes as $commande) {
            foreach ($commande->lignes as $ligne) {
                $fm = FournisseurMedicament::where('fournisseur_id', $fournisseur->id)
                    ->where('medicament_id', $ligne->medicament_id)
                    ->first();
                $ligne->stock_restant = $fm ? $fm->stock_disponible : 0;
                $ligne->quantite_manquante = max(0, $ligne->quantite - $ligne->stock_restant);
                $ligne->quantite_livrable = min($ligne->quantite, $ligne->stock_restant);
            }
        }
        
        return view('fournisseur.commandes', compact('commandes', 'fournisseur'));
    }

    /**
     * Marquer une commande comme expédiée
     */
    public function expedier($id)
    {
        $fournisseur = $this->getOrCreateFournisseur();
        
        if (!$fournisseur) {
            return redirect()->back()->with('error', 'Compte fournisseur non trouvé.');
        }
        
        $commande = CommandeFournisseur::where('id', $id)
            ->where('fournisseur_id', $fournisseur->id)
            ->firstOrFail();
        
        $commande->update([
            'statut' => 'expediee',
            'date_livraison_prevue' => now()->addDays($fournisseur->delai_livraison_moyen)
        ]);
        
        // Créer une notification pour le pharmacien
        try {
            \App\Models\Alerte::create([
                'type' => 'expedition',
                'niveau' => 'info',
                'message' => "La commande #{$commande->numero_commande} a été expédiée",
                'est_lue' => false,
                'donnees_concernees' => [
                    'commande_id' => $commande->id,
                    'fournisseur_id' => $fournisseur->id
                ]
            ]);
        } catch (\Exception $e) {
            // Ignorer
        }
        
        return redirect()->back()->with('success', 'Commande marquée comme expédiée.');
    }

    /**
     * Gestion des prix et stocks
     */
    public function prix()
    {
        $fournisseur = $this->getOrCreateFournisseur();
        
        if (!$fournisseur) {
            return redirect()->route('home')->with('error', 'Compte fournisseur non trouvé.');
        }
        
        $prix = FournisseurMedicament::where('fournisseur_id', $fournisseur->id)
            ->with('medicament')
            ->paginate(30);
        
        return view('fournisseur.prix', compact('prix'));
    }

    /**
     * Mettre à jour les prix et les stocks
     */
    public function mettreAJourPrix(Request $request)
    {
        $fournisseur = $this->getOrCreateFournisseur();
        
        if (!$fournisseur) {
            return redirect()->back()->with('error', 'Compte fournisseur non trouvé.');
        }
        
        foreach ($request->prix as $id => $prixData) {
            FournisseurMedicament::where('id', $id)
                ->where('fournisseur_id', $fournisseur->id)
                ->update([
                    'prix_achat' => $prixData['prix_achat'] ?? null,
                    'stock_disponible' => $prixData['stock_disponible'] ?? 0,
                    'disponible' => isset($prixData['disponible']) ? true : false
                ]);
        }
        
        return redirect()->back()->with('success', 'Prix et stocks mis à jour avec succès.');
    }
}