<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PredictionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la page des prédictions
     */
    public function index()
    {
        return view('predictions.index');
    }

    /**
     * API pour récupérer les prédictions (appelée par AJAX)
     */
    public function getPredictions()
    {
        try {
            // Récupérer tous les médicaments (sans filtre pour debug)
            $medicaments = Medicament::all();
            
            $results = [];
            $totalStock = 0;
            $totalPred7j = 0;
            $totalPred30j = 0;
            $totalACommander = 0;
            
            foreach ($medicaments as $med) {
                // Récupérer le stock actuel
                $stockActuel = $med->quantite ?? $med->stock_actif ?? 0;
                
                // Calculer une vente moyenne estimée
                // Si le médicament a des commandes, on calcule, sinon valeur par défaut
                $venteMoyenne = $this->calculateEstimatedSales($med);
                
                // Prédictions
                $prediction7j = $venteMoyenne * 7;
                $prediction30j = $venteMoyenne * 30;
                $quantiteRecommande = max(0, ceil($prediction30j - $stockActuel));
                
                // Déterminer le risque et le statut
                if ($stockActuel <= 0) {
                    $risque = 'rupture_immediate';
                    $statut = '🔴 RUPTURE IMMÉDIATE';
                    $statutClass = 'danger';
                    $message = 'Commander d\'urgence !';
                } elseif ($stockActuel < $prediction7j) {
                    $risque = 'critique';
                    $statut = '🟠 STOCK CRITIQUE';
                    $statutClass = 'warning';
                    $message = 'Commander rapidement';
                } elseif ($stockActuel < $prediction30j) {
                    $risque = 'attention';
                    $statut = '🟡 ATTENTION';
                    $statutClass = 'warning';
                    $message = 'Prévoir une commande';
                } else {
                    $risque = 'normal';
                    $statut = '🟢 NORMAL';
                    $statutClass = 'success';
                    $message = 'Stock suffisant';
                }
                
                $results[] = [
                    'id' => $med->id,
                    'medicament_id' => $med->id,
                    'nom' => $med->nom ?? $med->nom_commercial_fr ?? 'Médicament',
                    'dci' => $med->dci ?? '',
                    'stock_actuel' => $stockActuel,
                    'prediction_7j' => round($prediction7j, 1),
                    'prediction_30j' => round($prediction30j, 0),
                    'quantite_recommandee' => $quantiteRecommande,
                    'rupture_risque' => $risque,
                    'rupture_message' => $message,
                    'statut' => $statut,
                    'statut_class' => $statutClass
                ];
                
                $totalStock += $stockActuel;
                $totalPred7j += $prediction7j;
                $totalPred30j += $prediction30j;
                $totalACommander += $quantiteRecommande;
            }
            
            // Trier par risque (les plus critiques d'abord)
            $ordreRisque = [
                'rupture_immediate' => 0, 
                'critique' => 1, 
                'attention' => 2, 
                'normal' => 3
            ];
            
            usort($results, function($a, $b) use ($ordreRisque) {
                $risqueA = $ordreRisque[$a['rupture_risque']] ?? 4;
                $risqueB = $ordreRisque[$b['rupture_risque']] ?? 4;
                return $risqueA - $risqueB;
            });
            
            // Vérifier si l'API Python est disponible
            $apiAvailable = $this->isPythonApiAvailable();
            
            return response()->json([
                'success' => true,
                'api_available' => $apiAvailable,
                'statistiques' => [
                    'total_stock' => $totalStock,
                    'prediction_7j' => round($totalPred7j, 1),
                    'prediction_30j' => round($totalPred30j, 0),
                    'a_commander' => round($totalACommander, 0)
                ],
                'medicaments' => $results
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur dans getPredictions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    
    /**
     * Calculer les ventes estimées par jour
     */
    private function calculateEstimatedSales($medicament)
    {
        // Valeur par défaut basée sur le stock minimum
        $stockMin = $medicament->stock_min ?? 10;
        
        // Estimer les ventes quotidiennes (par défaut 5% du stock min)
        $defaultVentes = max(1, round($stockMin * 0.05, 2));
        
        try {
            // Si le médicament a des lots, calculer les ventes réelles
            if (method_exists($medicament, 'lots') && $medicament->lots()->count() > 0) {
                $totalInitial = $medicament->lots()->sum('quantite_initiale');
                $totalActuel = $medicament->lots()->sum('quantite_actuelle');
                $totalVendu = $totalInitial - $totalActuel;
                
                if ($totalVendu > 0) {
                    // Supposer que les ventes sont sur 30 jours
                    return round($totalVendu / 30, 2);
                }
            }
            
            // Si le médicament a des commandes
            if (method_exists($medicament, 'commandes')) {
                $commandesCount = $medicament->commandes()->count();
                if ($commandesCount > 0) {
                    return round($commandesCount / 30, 2);
                }
            }
            
        } catch (\Exception $e) {
            // Ignorer l'erreur et utiliser la valeur par défaut
        }
        
        return $defaultVentes;
    }
    
    /**
     * Vérifier si l'API Python est disponible
     */
    private function isPythonApiAvailable()
    {
        try {
            $apiUrl = env('PREDICTION_API_URL', 'http://127.0.0.1:5000');
            $response = Http::timeout(2)->get($apiUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Rafraîchir les prédictions (pour l'API)
     */
    public function refresh()
    {
        return $this->getPredictions();
    }
    
    /**
     * Exporter les prédictions en CSV
     */
    public function exportCSV()
    {
        try {
            $medicaments = Medicament::all();
            
            $handle = fopen('php://temp', 'w+');
            
            // En-têtes CSV
            fputcsv($handle, [
                'ID', 'Médicament', 'DCI', 'Stock Actuel', 
                'Prédiction 7j', 'Prédiction 30j', 'Quantité Recommandée', 'Statut'
            ]);
            
            foreach ($medicaments as $med) {
                $stockActuel = $med->quantite ?? $med->stock_actif ?? 0;
                $venteMoyenne = $this->calculateEstimatedSales($med);
                $prediction7j = round($venteMoyenne * 7, 1);
                $prediction30j = round($venteMoyenne * 30, 0);
                $quantiteRecommande = max(0, ceil($prediction30j - $stockActuel));
                
                // Déterminer le statut
                if ($stockActuel <= 0) {
                    $statut = 'RUPTURE IMMÉDIATE';
                } elseif ($stockActuel < $prediction7j) {
                    $statut = 'CRITIQUE';
                } elseif ($stockActuel < $prediction30j) {
                    $statut = 'ATTENTION';
                } else {
                    $statut = 'NORMAL';
                }
                
                fputcsv($handle, [
                    $med->id,
                    $med->nom ?? $med->nom_commercial_fr ?? 'Médicament',
                    $med->dci ?? '',
                    $stockActuel,
                    $prediction7j,
                    $prediction30j,
                    $quantiteRecommande,
                    $statut
                ]);
            }
            
            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);
            
            return response($csvContent)
                ->withHeaders([
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="predictions_' . date('Y-m-d') . '.csv"',
                ]);
                
        } catch (\Exception $e) {
            Log::error('Erreur export CSV: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'exportation');
        }
    }
    
    /**
     * Recommandations de commandes
     */
    public function recommandations()
    {
        try {
            $medicaments = Medicament::all();
            $recommandations = [];
            
            foreach ($medicaments as $med) {
                $stockActuel = $med->quantite ?? $med->stock_actif ?? 0;
                $venteMoyenne = $this->calculateEstimatedSales($med);
                $prediction30j = $venteMoyenne * 30;
                $quantiteRecommande = max(0, ceil($prediction30j - $stockActuel));
                
                if ($quantiteRecommande > 0) {
                    $recommandations[] = [
                        'medicament_id' => $med->id,
                        'nom' => $med->nom ?? $med->nom_commercial_fr ?? 'Médicament',
                        'stock_actuel' => $stockActuel,
                        'quantite_recommandee' => $quantiteRecommande,
                        'urgence' => $stockActuel <= 0 ? 'URGENTE' : ($stockActuel < $venteMoyenne * 7 ? 'ÉLEVÉE' : 'NORMALE')
                    ];
                }
            }
            
            // Trier par urgence
            $ordreUrgence = ['URGENTE' => 0, 'ÉLEVÉE' => 1, 'NORMALE' => 2];
            usort($recommandations, function($a, $b) use ($ordreUrgence) {
                return $ordreUrgence[$a['urgence']] - $ordreUrgence[$b['urgence']];
            });
            
            return response()->json([
                'success' => true,
                'recommandations' => $recommandations,
                'total_a_commander' => array_sum(array_column($recommandations, 'quantite_recommandee'))
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}