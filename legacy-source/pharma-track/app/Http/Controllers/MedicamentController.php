<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // ← QR Code

class MedicamentController extends Controller
{
    /**
     * ✅ CONSTRUCTEUR - VÉRIFICATION DES PERMISSIONS
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté.');
            }
            
            if (!in_array(auth()->user()->role, ['admin', 'pharmacien'])) {
                return redirect()->route('dashboard')
                    ->with('error', '❌ Accès non autorisé. Vous devez être admin ou pharmacien.');
            }
            
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * ✅ Affiche la liste des médicaments
     */
    public function index(Request $request)
    {
        $query = Medicament::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_commercial_fr', 'like', "%$search%")
                  ->orWhere('nom_commercial_ar', 'like', "%$search%")
                  ->orWhere('dci', 'like', "%$search%")
                  ->orWhere('code_cip', 'like', "%$search%");
            });
        }
        
        $medicaments = $query->orderBy('created_at', 'desc')->paginate(15);
        $userRole = auth()->check() ? auth()->user()->role : 'visiteur';
        
        return view('medicaments.index', compact('medicaments', 'userRole'));
    }

    /**
     * ✅ Affiche le formulaire de création
     */
    public function create()
    {
        return view('medicaments.create');
    }

    /**
     * ✅ Enregistre un nouveau médicament
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code_cip' => 'required|string|max:50|unique:medicaments,code_cip',
                'dci' => 'required|string|max:255',
                'nom_commercial_fr' => 'required|string|max:255',
                'nom_commercial_ar' => 'nullable|string|max:255',
                'forme' => 'required|string|max:100',
                'dosage' => 'required|numeric|min:0',
                'unite' => 'required|string|max:20',
                'categorie' => 'required|string|max:100',
                'prix_achat' => 'required|numeric|min:0',
                'prix_vente' => 'required|numeric|min:0',
                'delai_appro' => 'required|integer|min:1',
                'stock_min' => 'required|integer|min:0',
                'stock_max' => 'required|integer|min:0|gt:stock_min',
                'est_essentiel' => 'nullable|boolean',
                'est_controle' => 'nullable|boolean',
            ]);

            $validated['nom'] = $validated['nom_commercial_fr'];
            $validated['est_essentiel'] = $request->has('est_essentiel');
            $validated['est_controle'] = $request->has('est_controle');
            
            $medicament = Medicament::create($validated);

            // === GÉNÉRATION DU QR CODE ===
            $url = route('medicaments.show', $medicament->id);
            $qrCode = QrCode::size(200)->generate($url);
            $medicament->qr_code = $qrCode;
            $medicament->save();
            // =============================

            return redirect()->route('medicaments.index')
                ->with('success', '✅ Médicament "' . $medicament->nom_commercial_fr . '" ajouté avec succès.');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erreur : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * ✅ Affiche les détails d'un médicament
     */
    public function show(Medicament $medicament)
    {
        $medicament->load('lots');
        $stockTotal = $medicament->lots->where('statut', 'actif')->sum('quantite_actuelle');
        $isRupture = $stockTotal < $medicament->stock_min;
        
        return view('medicaments.show', compact('medicament', 'stockTotal', 'isRupture'));
    }

    /**
     * ✅ Affiche le formulaire d'édition
     */
    public function edit(Medicament $medicament)
    {
        return view('medicaments.edit', compact('medicament'));
    }

    /**
     * ✅ Met à jour un médicament
     */
    public function update(Request $request, Medicament $medicament)
    {
        try {
            $validated = $request->validate([
                'code_cip' => 'required|string|max:50|unique:medicaments,code_cip,' . $medicament->id,
                'dci' => 'required|string|max:255',
                'nom_commercial_fr' => 'required|string|max:255',
                'nom_commercial_ar' => 'nullable|string|max:255',
                'forme' => 'required|string|max:100',
                'dosage' => 'required|numeric|min:0',
                'unite' => 'required|string|max:20',
                'categorie' => 'required|string|max:100',
                'prix_achat' => 'required|numeric|min:0',
                'prix_vente' => 'required|numeric|min:0',
                'delai_appro' => 'required|integer|min:1',
                'stock_min' => 'required|integer|min:0',
                'stock_max' => 'required|integer|min:0|gt:stock_min',
                'est_essentiel' => 'nullable|boolean',
                'est_controle' => 'nullable|boolean',
            ]);

            $validated['est_essentiel'] = $request->has('est_essentiel');
            $validated['est_controle'] = $request->has('est_controle');
            $validated['nom'] = $validated['nom_commercial_fr'];

            $medicament->update($validated);

            // Optionnel : régénérer le QR code si l’URL change (rare)
            // Si vous voulez mettre à jour le QR code (par exemple si l’ID ne change pas, l’URL reste la même)
            // $medicament->qr_code = QrCode::size(200)->generate(route('medicaments.show', $medicament->id));
            // $medicament->save();

            return redirect()->route('medicaments.show', $medicament)
                ->with('success', '✅ Médicament modifié avec succès.');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erreur : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * ✅ Supprime un médicament
     */
    public function destroy(Medicament $medicament)
    {
        try {
            if ($medicament->lots()->count() > 0) {
                return redirect()->route('medicaments.index')
                    ->with('error', '❌ Impossible de supprimer ce médicament car il a des lots associés.');
            }
            
            $nom = $medicament->nom_commercial_fr;
            $medicament->delete();

            return redirect()->route('medicaments.index')
                ->with('success', '✅ Médicament "' . $nom . '" supprimé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Génère le QR code à la volée (optionnel, si vous ne stockez pas en base)
     */
    public function generateQR(Medicament $medicament)
    {
        $url = route('medicaments.show', $medicament->id);
        $qrCode = QrCode::size(200)->generate($url);
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}