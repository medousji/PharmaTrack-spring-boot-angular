<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlerteController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }
            if (!in_array(auth()->user()->role, ['admin', 'pharmacien', 'fournisseur'])) {
                return redirect()->route('dashboard')->with('error', '❌ Accès non autorisé.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy', 'marquerLue', 'marquerNonLue']);
    }

    /**
     * Affiche la liste des alertes (filtrée par rôle)
     */
    public function index()
    {
        $user = Auth::user();
        $query = Alerte::query();
        
        // Exclure les types inscription et approbation pour TOUS les utilisateurs
        $query->whereNotIn('type', ['inscription', 'approbation']);
        
        if ($user->role === 'fournisseur') {
            // ⭐ Fournisseur : voir UNIQUEMENT ses propres alertes ⭐
            $fournisseur = Fournisseur::where('user_id', $user->id)->first();
            if ($fournisseur) {
                $query->where('donnees_concernees->fournisseur_id', $fournisseur->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->role === 'pharmacien') {
            // ⭐ Pharmacien : voir les alertes médicaments (pas les alertes fournisseurs) ⭐
            $query->where(function($q) {
                $q->whereNull('donnees_concernees->fournisseur_id')
                  ->orWhere('donnees_concernees->fournisseur_id', 0);
            });
        } elseif ($user->role === 'admin') {
            // ⭐ Admin : voir les alertes médicaments (pas les alertes fournisseurs) ⭐
            $query->where(function($q) {
                $q->whereNull('donnees_concernees->fournisseur_id')
                  ->orWhere('donnees_concernees->fournisseur_id', 0);
            });
        }
        
        $alertes = $query->latest()->paginate(50);
        
        // Statistiques avec le même filtre
        $nonLuesQuery = clone $query;
        $nonLues = $nonLuesQuery->where('est_lue', false)->count();
        
        $totalQuery = clone $query;
        $totalAlertes = $totalQuery->count();
        
        $prioriteQuery = clone $query;
        $prioriteElevee = $prioriteQuery->where('est_lue', false)
            ->where(function($q) {
                $q->where('niveau', 'eleve')->orWhere('niveau', 'critique');
            })->count();
        
        return view('alertes.index', compact('alertes', 'totalAlertes', 'nonLues', 'prioriteElevee'));
    }

    /**
     * Afficher le détail d'une alerte (avec vérification d'accès)
     */
    public function show(Alerte $alerte)
    {
        $user = Auth::user();
        
        // Vérifier que ce n'est pas une alerte inscription/approbation
        if (in_array($alerte->type, ['inscription', 'approbation'])) {
            abort(404, 'Page non trouvée.');
        }
        
        if ($user->role === 'fournisseur') {
            $fournisseur = Fournisseur::where('user_id', $user->id)->first();
            $fournisseurId = $alerte->donnees_concernees['fournisseur_id'] ?? null;
            if (!$fournisseur || $fournisseur->id != $fournisseurId) {
                abort(403, 'Accès non autorisé à cette alerte.');
            }
        } elseif ($user->role === 'pharmacien') {
            // Vérifier que ce n'est pas une alerte fournisseur
            $fournisseurId = $alerte->donnees_concernees['fournisseur_id'] ?? null;
            if ($fournisseurId && $fournisseurId > 0) {
                abort(403, 'Accès non autorisé à cette alerte.');
            }
        } elseif ($user->role === 'admin') {
            // Vérifier que ce n'est pas une alerte fournisseur
            $fournisseurId = $alerte->donnees_concernees['fournisseur_id'] ?? null;
            if ($fournisseurId && $fournisseurId > 0) {
                abort(403, 'Accès non autorisé à cette alerte.');
            }
        }
        
        return view('alertes.show', compact('alerte'));
    }

    /**
     * Formulaire de création d'alerte
     */
    public function create()
    {
        return view('alertes.create');
    }

    /**
     * Enregistrer une nouvelle alerte
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'lot_id' => 'required|exists:lots,id',
            'message' => 'required|string',
            'niveau' => 'required|in:faible,moyen,eleve',
        ]);
        $validated['est_lue'] = false;
        Alerte::create($validated);
        return redirect()->route('alertes.index')->with('success', '✅ Alerte créée.');
    }

    /**
     * Formulaire d'édition d'alerte
     */
    public function edit(Alerte $alerte)
    {
        return view('alertes.edit', compact('alerte'));
    }

    /**
     * Mettre à jour une alerte
     */
    public function update(Request $request, Alerte $alerte)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'message' => 'required|string',
            'niveau' => 'required|in:faible,moyen,eleve',
        ]);
        $alerte->update($validated);
        return redirect()->route('alertes.index')->with('success', '✅ Alerte mise à jour.');
    }

    /**
     * Supprimer une alerte
     */
    public function destroy(Alerte $alerte)
    {
        $alerte->delete();
        return redirect()->route('alertes.index')->with('success', '✅ Alerte supprimée.');
    }

    /**
     * Marquer une alerte comme lue
     */
    public function marquerLue(Alerte $alerte)
    {
        $alerte->update(['est_lue' => true]);
        return redirect()->back()->with('success', '✅ Alerte marquée comme lue.');
    }

    /**
     * Marquer une alerte comme non lue
     */
    public function marquerNonLue(Alerte $alerte)
    {
        $alerte->update(['est_lue' => false]);
        return redirect()->back()->with('success', '✅ Alerte marquée comme non lue.');
    }
}