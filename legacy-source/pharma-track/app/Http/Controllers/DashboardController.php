<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Alerte;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics and charts.
     */
    public function index()
    {
        // Statistiques de base
        $totalMedicaments = Medicament::count();
        
        // Calculer les ruptures
        $ruptures = 0;
        $medicaments = Medicament::with('lots')->get();
        foreach ($medicaments as $medicament) {
            $stockTotal = $medicament->lots->where('statut', 'actif')->sum('quantite_actuelle');
            if ($stockTotal < $medicament->stock_min) {
                $ruptures++;
            }
        }
        
        $alertesNonLues = Alerte::where('est_lue', false)->count();
        $lotsProches = Lot::where('date_peremption', '<=', now()->addDays(30))
                         ->where('statut', 'actif')
                         ->count();

        // ============================================
        // DONNÉES POUR LES GRAPHIQUES
        // ============================================
        
        // 1. Répartition par catégorie
        $categories = Medicament::select('categorie', DB::raw('count(*) as total'))
            ->whereNotNull('categorie')
            ->groupBy('categorie')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->pluck('total', 'categorie');

        // 2. Top 10 médicaments par stock
        $topMedicaments = Medicament::withCount(['lots as stock_total' => function($q) {
                $q->where('statut', 'actif')
                  ->select(DB::raw('COALESCE(SUM(quantite_actuelle), 0)'));
            }])
            ->get(['id', 'nom_commercial_fr', 'stock_total'])
            ->filter(function ($medicament) {
                return (int) $medicament->stock_total > 0;
            })
            ->sortByDesc('stock_total')
            ->take(10)
            ->values();

        // 3. Alertes par type
        $alertesParType = Alerte::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        // 4. Évolution des ajouts (30 jours)
        $evolution = Medicament::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 5. Lots expirant bientôt (30 jours)
        $lotsExpiration = Lot::select(
                DB::raw('DATE(date_peremption) as date'),
                DB::raw('count(*) as total')
            )
            ->whereBetween('date_peremption', [now(), now()->addDays(30)])
            ->where('statut', 'actif')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 6. Répartition des lots par statut
        $lotsStatut = Lot::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // 7. Top laboratoires
        $laboratoires = Medicament::select('laboratoire', DB::raw('count(*) as total'))
            ->whereNotNull('laboratoire')
            ->groupBy('laboratoire')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->pluck('total', 'laboratoire');

        // 8. Répartition par forme
        $formes = Medicament::select('forme', DB::raw('count(*) as total'))
            ->whereNotNull('forme')
            ->groupBy('forme')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->pluck('total', 'forme');

        // 9. Évolution des alertes (7 jours)
        $evolutionAlertes = Alerte::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 10. Statistiques des médicaments par statut
        $statutMedicaments = [
            'actifs' => Medicament::where('statut', 'actif')->count(),
            'inactifs' => Medicament::where('statut', 'inactif')->count(),
            'perimes' => Medicament::where('est_perime', true)->count(),
        ];

        // Regrouper toutes les statistiques dans un tableau
        $stats = [
            'categories' => $categories,
            'top_medicaments' => $topMedicaments,
            'alertes_par_type' => $alertesParType,
            'evolution' => $evolution,
            'lots_expiration' => $lotsExpiration,
            'statut_lots' => $lotsStatut,
            'laboratoires' => $laboratoires,
            'formes' => $formes,
            'evolution_alertes' => $evolutionAlertes,
            'statut_medicaments' => $statutMedicaments,
        ];

        return view('dashboard', compact(
            'totalMedicaments',
            'ruptures',
            'alertesNonLues',
            'lotsProches',
            'stats'
        ));
    }
}