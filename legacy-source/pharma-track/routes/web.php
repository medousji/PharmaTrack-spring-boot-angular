<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ConformiteController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommandeFournisseurController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\TestPredictionController;

// ============================================
// ROUTES PUBLIQUES
// ============================================
Route::get('/', function () {
    $total = \App\Models\Medicament::count();
    return view('home', [
        'totalMedicaments' => $total,
        'ruptures' => 0,
        'alertes' => 0,
        'lotsProches' => 0
    ]);
})->name('home');

// ============================================
// AUTHENTIFICATION
// ============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ============================================
// ✅ TOUTES LES ROUTES PROTÉGÉES
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // ---------- DASHBOARD ----------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ---------- PROFIL ----------
    Route::get('/profile', function() {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    })->name('profile.index');
    
    Route::put('/profile', function(Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        $user->update($validated);
        return redirect()->route('profile.index')->with('success', 'Profil mis à jour');
    })->name('profile.update');
    
    Route::put('/profile/password', function(Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe incorrect']);
        }
        
        $user->password = Hash::make($validated['password']);
        $user->save();
        return back()->with('password_success', 'Mot de passe changé !');
    })->name('profile.password.update');
    
    // ============================================
    // ✅ MÉDICAMENTS
    // ============================================
    Route::get('/medicaments/create', [MedicamentController::class, 'create'])->name('medicaments.create');
    Route::post('/medicaments', [MedicamentController::class, 'store'])->name('medicaments.store');
    Route::get('/medicaments', [MedicamentController::class, 'index'])->name('medicaments.index');
    Route::get('/medicaments/{medicament}', [MedicamentController::class, 'show'])->name('medicaments.show');
    Route::get('/medicaments/{medicament}/edit', [MedicamentController::class, 'edit'])->name('medicaments.edit');
    Route::put('/medicaments/{medicament}', [MedicamentController::class, 'update'])->name('medicaments.update');
    Route::delete('/medicaments/{medicament}', [MedicamentController::class, 'destroy'])->name('medicaments.destroy');
    
    // ============================================
    // ✅ LOTS
    // ============================================
    Route::get('/lots/create', [LotController::class, 'create'])->name('lots.create');
    Route::post('/lots', [LotController::class, 'store'])->name('lots.store');
    Route::post('/lots/{lot}/ajuster-stock', [LotController::class, 'ajusterStock'])->name('lots.ajuster-stock');
    Route::get('/lots', [LotController::class, 'index'])->name('lots.index');
    Route::get('/lots/{lot}', [LotController::class, 'show'])->name('lots.show');
    Route::get('/lots/{lot}/edit', [LotController::class, 'edit'])->name('lots.edit');
    Route::put('/lots/{lot}', [LotController::class, 'update'])->name('lots.update');
    Route::delete('/lots/{lot}', [LotController::class, 'destroy'])->name('lots.destroy');
    
    // ============================================
    // ✅ ALERTES
    // ============================================
    Route::get('/alertes/create', [AlerteController::class, 'create'])->name('alertes.create');
    Route::post('/alertes', [AlerteController::class, 'store'])->name('alertes.store');
    Route::post('/alertes/{alerte}/lire', [AlerteController::class, 'marquerLue'])->name('alertes.marquer-lue');
    Route::post('/alertes/{alerte}/non-lue', [AlerteController::class, 'marquerNonLue'])->name('alertes.marquer-non-lue');
    Route::get('/alertes', [AlerteController::class, 'index'])->name('alertes.index');
    Route::get('/alertes/{alerte}', [AlerteController::class, 'show'])->name('alertes.show');
    Route::get('/alertes/{alerte}/edit', [AlerteController::class, 'edit'])->name('alertes.edit');
    Route::put('/alertes/{alerte}', [AlerteController::class, 'update'])->name('alertes.update');
    Route::delete('/alertes/{alerte}', [AlerteController::class, 'destroy'])->name('alertes.destroy');
    Route::get('/alertes/{alerte}', [AlerteController::class, 'show'])->name('alertes.show');
    Route::get('/medicaments/{medicament}/qr', [MedicamentController::class, 'generateQr'])->name('medicaments.qr');

    // ============================================
    // ✅ FOURNISSEURS
    // ============================================
    Route::middleware(['auth'])->prefix('fournisseur')->name('fournisseur.')->group(function () {
        Route::get('/dashboard', [FournisseurController::class, 'dashboard'])->name('dashboard');
        Route::get('/commandes', [FournisseurController::class, 'commandes'])->name('commandes');
        Route::post('/commandes/{id}/expedier', [FournisseurController::class, 'expedier'])->name('commandes.expedier');
        Route::get('/prix', [FournisseurController::class, 'prix'])->name('prix');
        Route::post('/prix', [FournisseurController::class, 'mettreAJourPrix'])->name('prix.update');
    });
    
    // ============================================
    // ✅ CHAT (MESSAGERIE)
    // ============================================
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{commande}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{commande}', [ChatController::class, 'envoyer'])->name('chat.envoyer');
    Route::get('/chat/conversation/{user}', [ChatController::class, 'conversation'])->name('chat.conversation');
    Route::post('/chat/envoyer/direct', [ChatController::class, 'envoyerDirect'])->name('chat.envoyer.direct');
    
    // ============================================
    // ✅ CHATBOT IA (ASSISTANT PHARMA)
    // ============================================
    Route::get('/assistant', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/assistant/message', [ChatbotController::class, 'message'])->name('chatbot.message');
    Route::get('/assistant/export-pdf', [ChatbotController::class, 'exportPDF'])->name('chatbot.export-pdf');
    
    // ============================================
    // ✅ COMMANDES FOURNISSEURS
    // ============================================
    Route::get('/commandes/selection', [CommandeFournisseurController::class, 'selection'])->name('commandes.selection');
    Route::get('/commander/{medicament}', [CommandeFournisseurController::class, 'commander'])->name('commandes.creer');
    Route::post('/commander/passer', [CommandeFournisseurController::class, 'passerCommande'])->name('commandes.passer');
    Route::get('/commande/{commande}', [CommandeFournisseurController::class, 'show'])->name('commandes.show');
    
    // ============================================
    // ✅ ADMIN
    // ============================================
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/pending', [AdminController::class, 'pendingUsers'])->name('admin.users.pending');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::post('/admin/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::delete('/admin/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('admin.users.reject');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::put('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/clear-cache', [AdminController::class, 'clearCache'])->name('admin.clearCache');
    
    // ============================================
    // ✅ RELANCES FOURNISSEURS
    // ============================================
    Route::post('/admin/fournisseurs/{fournisseur}/relancer', [AdminController::class, 'relancerFournisseur'])->name('admin.fournisseurs.relancer');
    
    // ============================================
    // ✅ SCAN CODES-BARRES
    // ============================================
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/traiter', [ScanController::class, 'traiter'])->name('scan.traiter');
    Route::get('/scan/code/{id}', [ScanController::class, 'genererCode'])->name('scan.code');
    Route::get('/scan/qr/{id}', [ScanController::class, 'genererQR'])->name('scan.qr');
    
    // ============================================
    // ✅ PRÉDICTIONS IA
    // ============================================
    Route::get('/predictions', [PredictionController::class, 'index'])->name('predictions.index');
    Route::get('/predictions/commandes', [PredictionController::class, 'recommandations'])->name('predictions.commandes');
    Route::get('/predictions/export', [PredictionController::class, 'exportCSV'])->name('predictions.export');
    Route::get('/predictions/refresh', [PredictionController::class, 'refresh'])->name('predictions.refresh');
    Route::get('/test-prediction', [TestPredictionController::class, 'test']);

    // ⭐⭐⭐ API pour les prédictions (format JSON) - CORRIGÉE ⭐⭐⭐
    Route::get('/api/predictions', [PredictionController::class, 'getPredictions'])->name('api.predictions');
    
    // ============================================
    // ✅ CONFORMITÉ ONP
    // ============================================
    Route::get('/conformite', [ConformiteController::class, 'index'])->name('conformite.index');
    Route::get('/conformite/rapport', [ConformiteController::class, 'genererRapport'])->name('conformite.rapport');
    Route::get('/conformite/pdf', [ConformiteController::class, 'exporterPDF'])->name('conformite.pdf');
});

// ============================================
// API PUBLIQUES
// ============================================
Route::get('/api/user-role', function() {
    if (auth()->check()) {
        return response()->json([
            'role' => auth()->user()->role,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'isAdmin' => auth()->user()->role === 'admin',
            'isPharmacien' => auth()->user()->role === 'pharmacien',
            'isVisitor' => auth()->user()->role === 'visiteur',
            'isFournisseur' => auth()->user()->role === 'fournisseur'
        ]);
    }
    return response()->json(['role' => null]);
})->name('api.user-role');

// ============================================
// API COMMANDES (vérification disponibilité)
// ============================================
Route::post('/api/verifier-disponibilite', [CommandeFournisseurController::class, 'verifierDisponibilite'])->name('api.verifier-disponibilite');

// ============================================
// FALLBACK
// ============================================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});