<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\FournisseurMedicament;
use App\Models\CommandeFournisseur;
use App\Models\CommandeFournisseurLigne;
use App\Services\CommandeFournisseurService;
use Illuminate\Http\Request;

class CommandeFournisseurController extends Controller
{
    protected $commandeService;
    
    public function __construct(CommandeFournisseurService $commandeService)
    {
        $this->middleware('auth');
        $this->commandeService = $commandeService;
    }
    
    /**
     * Page de sélection des médicaments
     */
    public function selection()
    {
        $medicaments = Medicament::orderBy('nom_commercial_fr')->paginate(20);
        return view('commandes.selection', compact('medicaments'));
    }
    
    /**
     * Formulaire de commande pour un médicament
     */
    public function commander($medicamentId)
    {
        $medicament = Medicament::findOrFail($medicamentId);
        
        // Récupérer les fournisseurs associés à ce médicament
        // Utiliser les bons noms de colonnes : 'disponible' au lieu de 'est_actif'
        $fournisseurs = FournisseurMedicament::where('medicament_id', $medicamentId)
            ->where('disponible', 1)  // ← 'disponible' (1 = actif, 0 = inactif)
            ->with('fournisseur')
            ->orderBy('prix_achat', 'asc')  // ← 'prix_achat'
            ->get();
        
        // Debug : Vérifier si des fournisseurs sont trouvés
        if ($fournisseurs->isEmpty()) {
            \Log::warning('Aucun fournisseur pour le médicament ID: ' . $medicamentId);
        }
        
        return view('commandes.creer', compact('medicament', 'fournisseurs'));
    }
    
    /**
     * Vérifier la disponibilité avant commande (AJAX)
     */
    public function verifierDisponibilite(Request $request)
    {
        $request->validate([
            'fournisseur_medicament_id' => 'required|exists:fournisseur_medicaments,id',
            'quantite' => 'required|integer|min:1'
        ]);
        
        $resultat = $this->commandeService->verifierDisponibilite(
            $request->fournisseur_medicament_id,
            $request->quantite
        );
        
        return response()->json($resultat);
    }
    
    /**
     * Passer une commande
     */
    public function passerCommande(Request $request)
    {
        $request->validate([
            'fournisseur_medicament_id' => 'required|exists:fournisseur_medicaments,id',
            'quantite' => 'required|integer|min:1'
        ]);
        
        $resultat = $this->commandeService->creerCommande(
            $request->fournisseur_medicament_id,
            $request->quantite,
            auth()->user()->pharmacie_id ?? null
        );
        
        if (!$resultat['success']) {
            return redirect()->back()
                ->with('error', $resultat['message'])
                ->with('alternatifs', $resultat['alternatifs'] ?? []);
        }
        
        return redirect()->route('commandes.show', $resultat['commande']->id)
            ->with('success', $resultat['message'])
            ->with('quantite_manquante', $resultat['quantite_manquante'] ?? 0);
    }
    
    /**
     * Afficher les détails d'une commande
     */
    public function show($id)
    {
        $commande = CommandeFournisseur::with('fournisseur', 'lignes.medicament')->findOrFail($id);
        return view('commandes.show', compact('commande'));
    }
}