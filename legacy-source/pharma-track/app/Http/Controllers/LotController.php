<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Medicament;
use App\Models\Mouvement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LotController extends Controller
{
    /**
     * ✅ CONSTRUCTEUR - VÉRIFICATION DES PERMISSIONS
     * Seules les méthodes d'écriture sont protégées
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Vérifier si l'utilisateur est connecté
            if (!auth()->check()) {
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté.');
            }
            
            // Vérifier le rôle pour les méthodes d'écriture
            if (!in_array(auth()->user()->role, ['admin', 'pharmacien'])) {
                return redirect()->route('dashboard')
                    ->with('error', '❌ Accès non autorisé. Vous devez être admin ou pharmacien pour gérer les lots.');
            }
            
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy', 'ajusterStock']);
    }

    /**
     * Affiche la liste des lots
     */
    public function index()
    {
        // Récupérer tous les lots avec leur médicament associé
        $lots = Lot::with('medicament')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('lots.index', compact('lots'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $medicaments = Medicament::orderBy('nom_commercial_fr')->get();
        return view('lots.create', compact('medicaments'));
    }

    /**
     * Enregistre un nouveau lot
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_lot' => 'required|string|max:50|unique:lots',
            'medicament_id' => 'required|exists:medicaments,id',
            'quantite_initial' => 'required|integer|min:0',
            'quantite_actuelle' => 'required|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'date_peremption' => 'nullable|date',
            'fournisseur' => 'nullable|string|max:255',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'numero_facture' => 'nullable|string|max:100',
            'conditionnement' => 'nullable|string|max:255',
            'emplacement' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'statut' => 'required|in:actif,epuise,perime,retire',
        ]);
        
        $lot = Lot::create($validated);
        
        // Récupérer la première pharmacie disponible
        $pharmacie = \App\Models\Pharmacie::first();
        $pharmacieId = $pharmacie ? $pharmacie->id : null;
        
        // ✅ ENREGISTRER LE MOUVEMENT D'ENTRÉE INITIAL
        if ($lot->quantite_actuelle > 0) {
            Mouvement::create([
                'lot_id' => $lot->id,
                'pharmacie_id' => $pharmacieId,
                'type' => 'entree',
                'quantite' => $lot->quantite_actuelle,
                'reference' => 'CREATION_' . date('YmdHis'),
                'raison' => 'Création du lot',
                'user_id' => auth()->id(),
                'scanned_at' => now(),
            ]);
        }
        
        return redirect()->route('lots.show', $lot)
            ->with('success', '✅ Lot créé avec succès!');
    }

    /**
     * Affiche les détails d'un lot
     */
    public function show(Lot $lot)
    {
        // Charger les relations medicament et mouvements
        $lot->load('medicament', 'mouvements.user');
        return view('lots.show', compact('lot'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Lot $lot)
    {
        $medicaments = Medicament::orderBy('nom_commercial_fr')->get();
        return view('lots.edit', compact('lot', 'medicaments'));
    }

    /**
     * Met à jour un lot existant
     */
    public function update(Request $request, Lot $lot)
    {
        $validated = $request->validate([
            'numero_lot' => 'required|string|max:50|unique:lots,numero_lot,' . $lot->id,
            'medicament_id' => 'required|exists:medicaments,id',
            'quantite_initial' => 'required|integer|min:0',
            'quantite_actuelle' => 'required|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'date_peremption' => 'nullable|date',
            'fournisseur' => 'nullable|string|max:255',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'numero_facture' => 'nullable|string|max:100',
            'conditionnement' => 'nullable|string|max:255',
            'emplacement' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'statut' => 'required|in:actif,epuise,perime,retire',
        ]);
        
        $lot->update($validated);
        
        return redirect()->route('lots.show', $lot)
            ->with('success', '✅ Lot mis à jour avec succès!');
    }

    /**
     * Supprime un lot
     */
    public function destroy(Lot $lot)
    {
        // Supprimer d'abord les mouvements associés
        $lot->mouvements()->delete();
        
        // Suppression définitive
        $lot->forceDelete();
        
        return redirect()->route('lots.index')
            ->with('success', '✅ Lot supprimé avec succès!');
    }

    /**
     * Ajuste le stock d'un lot
     */
    public function ajusterStock(Request $request, Lot $lot)
    {
        $request->validate([
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'raison' => 'nullable|string|max:500',
        ]);
        
        $ancienneQuantite = $lot->quantite_actuelle;
        
        if ($request->type === 'entree') {
            $lot->quantite_actuelle += $request->quantite;
        } else {
            $lot->quantite_actuelle -= $request->quantite;
        }
        
        // Éviter les quantités négatives
        if ($lot->quantite_actuelle < 0) {
            $lot->quantite_actuelle = 0;
        }
        
        // Mettre à jour le statut si quantité = 0
        if ($lot->quantite_actuelle == 0 && $lot->statut === 'actif') {
            $lot->statut = 'epuise';
        } elseif ($lot->quantite_actuelle > 0 && $lot->statut === 'epuise') {
            $lot->statut = 'actif';
        }
        
        $lot->save();
        
        // Récupérer la première pharmacie disponible
        $pharmacie = \App\Models\Pharmacie::first();
        $pharmacieId = $pharmacie ? $pharmacie->id : null;
        
        // ✅ TRACER LE MOUVEMENT (ENTRÉE OU SORTIE)
        try {
            Mouvement::create([
                'lot_id' => $lot->id,
                'pharmacie_id' => $pharmacieId,
                'type' => $request->type,
                'quantite' => $request->quantite,
                'reference' => strtoupper($request->type) . '_' . date('YmdHis'),
                'raison' => $request->raison ?? ($request->type === 'entree' ? 'Ajustement manuel entrée' : 'Sortie manuelle'),
                'user_id' => auth()->id(),
                'scanned_at' => now(),
            ]);
            
            \Log::info('Mouvement enregistré', [
                'lot_id' => $lot->id, 
                'type' => $request->type,
                'quantite' => $request->quantite,
                'pharmacie_id' => $pharmacieId
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur enregistrement mouvement: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
        
        // Vérifier et créer des alertes si nécessaire
        $this->verifierAlertesLot($lot);
        
        return redirect()->route('lots.show', $lot)
            ->with('success', '✅ Stock ajusté avec succès! (Ancien: ' . $ancienneQuantite . ', Nouveau: ' . $lot->quantite_actuelle . ')');
    }
    
    /**
     * Vérifie et crée des alertes pour un lot (stock faible, expiration)
     */
    protected function verifierAlertesLot(Lot $lot)
    {
        // Vérifier stock faible (seulement si le médicament existe)
        if ($lot->medicament && $lot->quantite_actuelle > 0 && 
            $lot->quantite_actuelle < ($lot->medicament->stock_min ?? 10)) {
            
            // Créer une alerte si elle n'existe pas déjà (non lue)
            $alerteExistante = \App\Models\Alerte::where('type', 'stock')
                ->where('est_lue', false)
                ->whereJsonContains('donnees_concernees->lot_id', $lot->id)
                ->exists();
            
            if (!$alerteExistante) {
                \App\Models\Alerte::create([
                    'type' => 'stock',
                    'niveau' => 'eleve',
                    'message' => "Stock faible pour le lot {$lot->numero_lot} : {$lot->quantite_actuelle} unités restantes",
                    'donnees_concernees' => [
                        'lot_id' => $lot->id,
                        'medicament_id' => $lot->medicament_id,
                        'stock_actuel' => $lot->quantite_actuelle,
                        'stock_min' => $lot->medicament->stock_min ?? 10,
                    ],
                ]);
            }
        }
        
        // Vérifier expiration proche (≤ 30 jours)
        if ($lot->date_peremption && $lot->date_peremption <= now()->addDays(30)) {
            $joursRestants = now()->diffInDays($lot->date_peremption);
            
            $alerteExistante = \App\Models\Alerte::where('type', 'expiration')
                ->where('est_lue', false)
                ->whereJsonContains('donnees_concernees->lot_id', $lot->id)
                ->exists();
            
            if (!$alerteExistante) {
                \App\Models\Alerte::create([
                    'type' => 'expiration',
                    'niveau' => $joursRestants <= 7 ? 'critique' : 'eleve',
                    'message' => "Le lot {$lot->numero_lot} expire dans {$joursRestants} jours",
                    'donnees_concernees' => [
                        'lot_id' => $lot->id,
                        'medicament_id' => $lot->medicament_id,
                        'jours_restants' => $joursRestants,
                    ],
                ]);
            }
        }
    }
}