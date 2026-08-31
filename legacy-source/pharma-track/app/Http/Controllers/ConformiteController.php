<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Lot;
use App\Models\Alerte;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;  // ← Décommentez cette ligne

class ConformiteController extends Controller
{
    public function index()
    {
        return view('conformite.index');
    }
    
    public function genererRapport()
    {
        $medicaments = Medicament::with('lots')->get();
        
        $stats = [
            'total_medicaments' => Medicament::count(),
            'lots_perimes' => Lot::where('date_peremption', '<', now())->count(),
            'lots_proches' => Lot::whereBetween('date_peremption', [now(), now()->addDays(30)])->count(),
            'alertes_non_lues' => Alerte::where('est_lue', false)->count(),
        ];
        
        $conformite = [
            'reglementations' => [
                'Loi 85-87 relative à la pharmacie' => true,
                'Décret 91-356 relatif aux médicaments' => true,
                'Arrêté du 22 janvier 2015 sur les bonnes pratiques' => true,
                'Normes ISO 9001' => true,
            ],
            'certifications' => [
                'Enregistrement ONP' => 'Valide jusqu\'au 31/12/2026',
                'Licence d\'exploitation' => 'N° PH-2024-0123',
                'Agrément du pharmacien' => 'N° 12345',
            ]
        ];
        
        return view('conformite.rapport', compact('stats', 'conformite'));
    }
    
    public function exporterPDF()
    {
        $medicaments = Medicament::with('lots')->get();
        
        $data = [
            'date_rapport' => now()->format('d/m/Y H:i'),
            'total_medicaments' => Medicament::count(),
            'lots_perimes' => Lot::where('date_peremption', '<', now())->count(),
            'lots_proches' => Lot::whereBetween('date_peremption', [now(), now()->addDays(30)])->count(),
            'alertes_non_lues' => Alerte::where('est_lue', false)->count(),
            'medicaments' => $medicaments
        ];
        
        $pdf = Pdf::loadView('conformite.pdf', $data);
        return $pdf->download('rapport-conformite-onp-'.now()->format('Y-m-d').'.pdf');
    }
}