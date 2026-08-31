<?php
namespace App\Services;

use App\Models\Medicament;
use App\Models\Lot;
use App\Models\Alerte;
use Carbon\Carbon;

class AlerteService
{
    public function verifierToutesAlertes()
    {
        $this->verifierPeremptions();
        $this->verifierRuptures();
        $this->verifierStockCritique();
        return true;
    }
    
    private function verifierPeremptions()
    {
        $lotsProches = Lot::prochePeremption(30)->get();
        
        foreach ($lotsProches as $lot) {
            $jours = $lot->jours_avant_peremption;
            
            if ($jours > 0) {
                $niveau = $jours <= 7 ? 'critique' : ($jours <= 15 ? 'moyen' : 'bas');
                
                Alerte::create([
                    'type' => 'peremption',
                    'niveau' => $niveau,
                    'message' => "Lot {$lot->numero_lot} ({$lot->medicament->nom_commercial_fr}) expire dans {$jours} jours",
                    'donnees_concernees' => [
                        'lot_id' => $lot->id,
                        'medicament' => $lot->medicament->nom_commercial_fr,
                        'date_peremption' => $lot->date_peremption->format('d/m/Y'),
                        'jours_restants' => $jours
                    ]
                ]);
            }
        }
    }
    
    private function verifierRuptures()
    {
        $medicaments = Medicament::with('lots')->get();
        
        foreach ($medicaments as $medicament) {
            if ($medicament->isEnRupture()) {
                Alerte::create([
                    'type' => 'rupture',
                    'niveau' => 'critique',
                    'message' => "Rupture de stock: {$medicament->nom_commercial_fr}",
                    'donnees_concernees' => [
                        'medicament_id' => $medicament->id,
                        'stock_actuel' => $medicament->stock_total,
                        'stock_min' => $medicament->stock_min,
                        'jours_couvrance' => $medicament->jours_couvrance
                    ]
                ]);
            }
        }
    }
    
    private function verifierStockCritique()
    {
        $medicamentsEssentiels = Medicament::where('est_essentiel', true)->get();
        
        foreach ($medicamentsEssentiels as $medicament) {
            $joursCouvrance = $medicament->jours_couvrance;
            
            if ($joursCouvrance <= 7) {
                Alerte::create([
                    'type' => 'stock_critique',
                    'niveau' => 'critique',
                    'message' => "Stock critique pour {$medicament->nom_commercial_fr} ({$joursCouvrance} jours restants)",
                    'donnees_concernees' => [
                        'medicament_id' => $medicament->id,
                        'jours_couvrance' => $joursCouvrance,
                        'stock_actuel' => $medicament->stock_total
                    ]
                ]);
            }
        }
    }
}