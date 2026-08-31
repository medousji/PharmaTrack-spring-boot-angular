<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Lot;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ScanController extends Controller
{
    protected $barcode;
    
    public function __construct()
    {
        $this->barcode = new DNS1D();
    }

    /**
     * Afficher la page de scan
     */
    public function index()
    {
        return view('scan.index');
    }

    /**
     * Traiter un code-barres scanné
     */
    public function traiter(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = trim($request->code);
        $code = trim($code, "\"' \n\r\t");
        
        \Log::info('Scan reçu', ['code' => $code]);

        // 1. Chercher le lot par numéro
        $lot = Lot::where('numero_lot', $code)->first();
        if ($lot) {
            return redirect()->route('lots.show', $lot)
                ->with('success', "Lot trouvé : {$lot->numero_lot}");
        }
        
        // 2. Chercher le médicament par code CIP
        $medicament = Medicament::where('code_cip', $code)->first();
        if ($medicament) {
            return redirect()->route('medicaments.show', $medicament)
                ->with('success', "Médicament trouvé : {$medicament->nom_commercial_fr}");
        }
        
        // 3. Si c'est un nombre, chercher par ID
        if (is_numeric($code)) {
            $lot = Lot::find($code);
            if ($lot) {
                return redirect()->route('lots.show', $lot)
                    ->with('success', "Lot trouvé par ID");
            }
            
            $medicament = Medicament::find($code);
            if ($medicament) {
                return redirect()->route('medicaments.show', $medicament)
                    ->with('success', "Médicament trouvé par ID");
            }
        }
        
        // 4. Essayer de parser le JSON (si le QR contient du JSON)
        try {
            $data = json_decode($code, true);
            if (is_array($data) && isset($data['lot'])) {
                $lot = Lot::where('numero_lot', $data['lot'])->first();
                if ($lot) {
                    return redirect()->route('lots.show', $lot)
                        ->with('success', "Lot trouvé via QR data");
                }
            }
        } catch (\Exception $e) {
            // Pas du JSON, on ignore
        }
        
        return redirect()->route('scan.index')
            ->with('error', "Aucun médicament ou lot trouvé avec le code : $code");
    }

    /**
     * Générer un code-barres pour un médicament (Code 128)
     */
    public function genererCode($id)
    {
        $medicament = Medicament::findOrFail($id);
        $code = $medicament->code_cip ?? '0000000000000';
        
        $barcodeImage = $this->barcode->getBarcodePNG($code, 'C128');
        
        return response($barcodeImage, 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="barcode-'.$medicament->id.'.png"');
    }

    /**
     * Générer un QR code pour un lot (URL vers la page du lot)
     * Utilise le package Simple QrCode (plus fiable)
     */
    public function genererQR($id)
    {
        $lot = Lot::with('medicament')->findOrFail($id);
        
        // URL complète vers la page de détail du lot
        $url = route('lots.show', $lot);
        
        // Générer le QR code en SVG (taille 200px)
        $qrCodeSvg = QrCode::size(200)->generate($url);
        
        return response($qrCodeSvg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'inline; filename="qr-'.$lot->numero_lot.'.svg"');
    }

    /**
     * API pour scanner depuis une app mobile (optionnel)
     */
    public function apiScan(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = trim($request->code);
        
        $lot = Lot::where('numero_lot', $code)->first();
        if ($lot) {
            return response()->json([
                'success' => true,
                'type' => 'lot',
                'id' => $lot->id,
                'numero' => $lot->numero_lot,
                'url' => route('lots.show', $lot)
            ]);
        }
        
        $medicament = Medicament::where('code_cip', $code)->first();
        if ($medicament) {
            return response()->json([
                'success' => true,
                'type' => 'medicament',
                'id' => $medicament->id,
                'nom' => $medicament->nom_commercial_fr,
                'url' => route('medicaments.show', $medicament)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Code non trouvé'
        ], 404);
    }
}