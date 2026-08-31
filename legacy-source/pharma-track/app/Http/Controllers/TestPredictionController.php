<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class TestPredictionController extends Controller
{
    public function test()
    {
        // Tester si l'API Python répond
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:5000/health');
            $apiStatus = $response->json();
        } catch (\Exception $e) {
            $apiStatus = ['status' => 'error', 'message' => $e->getMessage()];
        }
        
        // Tester une prédiction
        $medicaments = [
            ['id' => 1, 'nom' => 'Paracétamol', 'stock' => 50, 'vente_moyenne' => 10],
            ['id' => 2, 'nom' => 'Ibuprofène', 'stock' => 15, 'vente_moyenne' => 8]
        ];
        
        try {
            $prediction = Http::timeout(10)->post('http://127.0.0.1:5000/api/predict/rupture', [
                'medicaments' => $medicaments,
                'delai_livraison' => 7
            ]);
            $predictionResult = $prediction->json();
        } catch (\Exception $e) {
            $predictionResult = ['error' => $e->getMessage()];
        }
        
        return response()->json([
            'api_python_status' => $apiStatus,
            'prediction_test' => $predictionResult,
            'message' => 'Connexion Laravel ↔ Python réussie !'
        ]);
    }
}