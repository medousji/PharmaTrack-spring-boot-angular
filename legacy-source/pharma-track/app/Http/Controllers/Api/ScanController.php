<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Medicament;
use App\Models\Mouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function scanner(Request $request)
    {
        $request->validate([
            'code_barres' => 'required|string',
            'pharmacie_id' => 'required|exists:pharmacies,id',
            'type_mouvement' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1'
        ]);
        
        DB::beginTransaction();
        
        try {
            $medicament = Medicament::where('code_cip', $request->code_barres)->first();
            
            if (!$medicament) {
                return response()->json([
                    'error' => 'Médicament non trouvé'
                ], 404);
            }
            
            if ($request->type_mouvement === 'entree') {
                $lot = $this->gererEntree($medicament, $request);
            } else {
                $lot = $this->gererSortie($medicament, $request);
            }
            
            $mouvement = Mouvement::create([
                'lot_id' => $lot->id,
                'pharmacie_id' => $request->pharmacie_id,
                'type' => $request->type_mouvement,
                'quantite' => $request->quantite,
                'user_id' => auth()->id(),
                'scanned_at' => now()
            ]);
            
            if ($request->type_mouvement === 'entree') {
                $lot->quantite_actuelle += $request->quantite;
            } else {
                $lot->quantite_actuelle -= $request->quantite;
                
                if ($lot->quantite_actuelle <= 0) {
                    $lot->statut = 'epuise';
                }
            }
            
            $lot->save();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'medicament' => $medicament,
                'lot' => $lot,
                'mouvement' => $mouvement,
                'stock_restant' => $lot->quantite_actuelle
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function gererEntree($medicament, $request)
    {
        return Lot::create([
            'medicament_id' => $medicament->id,
            'numero_lot' => $request->numero_lot ?? 'LOT-' . time(),
            'date_fabrication' => $request->date_fabrication ?? now()->subMonths(6),
            'date_peremption' => $request->date_peremption ?? now()->addYears(2),
            'quantite_initial' => $request->quantite,
            'quantite_actuelle' => 0,
            'fournisseur' => $request->fournisseur ?? 'Inconnu',
            'date_reception' => now(),
            'statut' => 'actif'
        ]);
    }
    
    private function gererSortie($medicament, $request)
    {
        $lot = Lot::where('medicament_id', $medicament->id)
            ->where('statut', 'actif')
            ->where('quantite_actuelle', '>=', $request->quantite)
            ->where('date_peremption', '>', now())
            ->orderBy('date_peremption')
            ->first();
            
        if (!$lot) {
            throw new \Exception('Aucun lot disponible pour cette sortie');
        }
        
        return $lot;
    }
}