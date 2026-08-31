<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Alerte;
use App\Models\Lot;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Afficher la page d'accueil.
     */
    public function index()
    {
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
        
        $alertes = Alerte::where('est_lue', false)->count();
        $lotsProches = Lot::where('date_peremption', '<=', now()->addDays(30))
                         ->where('statut', 'actif')
                         ->count();

        return view('home', [
            'totalMedicaments' => $totalMedicaments,
            'ruptures' => $ruptures,
            'alertes' => $alertes,
            'lotsProches' => $lotsProches
        ]);
    }
}