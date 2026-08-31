<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicament;
use App\Models\Lot;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $medicaments = Medicament::with(['lots' => function($query) {
            $query->where('statut', 'actif');
        }])->get();
        
        return response()->json([
            'total_medicaments' => $medicaments->count(),
            'stock_total' => $medicaments->sum('stock_total'),
            'medicaments' => $medicaments->map(function($med) {
                return [
                    'id' => $med->id,
                    'nom' => $med->nom_commercial_fr,
                    'dci' => $med->dci,
                    'stock' => $med->stock_total,
                    'stock_min' => $med->stock_min,
                    'rupture' => $med->isEnRupture(),
                    'jours_couvrance' => $med->jours_couvrance
                ];
            })
        ]);
    }
    
    public function medicamentsCritiques()
    {
        $medicaments = Medicament::where('est_essentiel', true)->get();
        
        $critiques = $medicaments->filter(function($med) {
            return $med->jours_couvrance <= 7 || $med->isEnRupture();
        });
        
        return response()->json([
            'critiques' => $critiques->map(function($med) {
                return [
                    'id' => $med->id,
                    'nom' => $med->nom_commercial_fr,
                    'stock' => $med->stock_total,
                    'stock_min' => $med->stock_min,
                    'jours_couvrance' => $med->jours_couvrance,
                    'urgence' => $med->jours_couvrance <= 3 ? 'HAUTE' : ($med->jours_couvrance <= 7 ? 'MOYENNE' : 'BASSE')
                ];
            })
        ]);
    }
    
    public function prochePeremption()
    {
        $lots = Lot::prochePeremption(30)->with('medicament')->get();
        
        return response()->json([
            'lots' => $lots->map(function($lot) {
                return [
                    'lot' => $lot->numero_lot,
                    'medicament' => $lot->medicament->nom_commercial_fr,
                    'date_peremption' => $lot->date_peremption->format('d/m/Y'),
                    'jours_restants' => $lot->jours_avant_peremption,
                    'quantite' => $lot->quantite_actuelle
                ];
            })
        ]);
    }
}