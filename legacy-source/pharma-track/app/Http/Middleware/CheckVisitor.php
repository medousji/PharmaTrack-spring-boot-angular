<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\LotController;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// ============================================
// ENREGISTRER LE MIDDLEWARE
// ============================================
Route::aliasMiddleware('role', CheckRole::class);

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
// ROUTES PROTÉGÉES (AUTH REQUISE)
// ============================================

Route::middleware(['auth'])->group(function () {
    
    // ============================================
    // DASHBOARD
    // ============================================
    Route::get('/dashboard', function () {
        $total = \App\Models\Medicament::count();
        $ruptures = 0;
        $medicaments = \App\Models\Medicament::with('lots')->get();
        
        foreach ($medicaments as $medicament) {
            $stockTotal = $medicament->lots->where('statut', 'actif')->sum('quantite_actuelle');
            if ($stockTotal < $medicament->stock_min) {
                $ruptures++;
            }
        }
        
        return view('dashboard', [
            'totalMedicaments' => $total,
            'ruptures' => $ruptures,
            'alertesNonLues' => \App\Models\Alerte::where('est_lue', false)->count() ?? 0,
            'lotsProches' => \App\Models\Lot::where('date_peremption', '<=', now()->addDays(30))->count() ?? 0
        ]);
    })->name('dashboard');
    
    // ============================================
    // PROFIL UTILISATEUR
    // ============================================
    
    Route::get('/profile', function() {
        $user = auth()->user();
        $user->roleName = ucfirst($user->role);
        $user->statusName = $user->is_active ? 'Actif' : 'Inactif';
        return view('profile.index', compact('user'));
    })->name('profile.index');
    
    Route::put('/profile', function(Request $request) {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        
        $user->update($validated);
        
        return redirect()->route('profile.index')
            ->with('success', 'Profil mis à jour avec succès');
    })->name('profile.update');
    
    Route::put('/profile/password', function(Request $request) {
        $user = auth()->user();
        
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.'
            ]);
        }
        
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return back()->with('password_success', 'Mot de passe changé avec succès !');
        
    })->name('profile.password.update');
    
    // ============================================
    // MÉDICAMENTS - CORRIGÉ !
    // ============================================
    
    // Routes en lecture seule - pour tous les utilisateurs connectés
    Route::get('/medicaments', [MedicamentController::class, 'index'])->name('medicaments.index');
    Route::get('/medicaments/{medicament}', [MedicamentController::class, 'show'])->name('medicaments.show');
    
    // Routes d'écriture - UNIQUEMENT pour admin et pharmacien
    Route::middleware(['role:admin,pharmacien'])->group(function () {
        // CRÉATION
        Route::get('/medicaments/create', [MedicamentController::class, 'create'])->name('medicaments.create');
        Route::post('/medicaments', [MedicamentController::class, 'store'])->name('medicaments.store');
        
        // MODIFICATION
        Route::get('/medicaments/{medicament}/edit', [MedicamentController::class, 'edit'])->name('medicaments.edit');
        Route::put('/medicaments/{medicament}', [MedicamentController::class, 'update'])->name('medicaments.update');
        
        // SUPPRESSION
        Route::delete('/medicaments/{medicament}', [MedicamentController::class, 'destroy'])->name('medicaments.destroy');
    });
    
    // ============================================
    // ALERTES
    // ============================================
    
    Route::get('/alertes', function() {
        $user = auth()->user();
        
        $alertes = \App\Models\Alerte::with(['medicament', 'lot', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('alertes.index', [
            'alertes' => $alertes,
            'isVisiteur' => $user->role === 'visiteur'
        ]);
    })->name('alertes.index');
    
    // Création d'alertes - UNIQUEMENT admin/pharmacien
    Route::middleware(['role:admin,pharmacien'])->group(function () {
        Route::get('/alertes/create', function() {
            $medicaments = \App\Models\Medicament::all();
            $lots = \App\Models\Lot::with('medicament')->get();
            return view('alertes.create', compact('medicaments', 'lots'));
        })->name('alertes.create');
        
        Route::post('/alertes', function(Request $request) {
            $validated = $request->validate([
                'type' => 'required|string|max:50',
                'message' => 'required|string|max:500',
                'priorite' => 'required|in:faible,moyen,eleve',
                'medicament_id' => 'nullable|exists:medicaments,id',
                'lot_id' => 'nullable|exists:lots,id',
            ]);
            
            $validated['user_id'] = auth()->id();
            $validated['est_lue'] = false;
            
            \App\Models\Alerte::create($validated);
            
            return redirect()->route('alertes.index')
                ->with('success', 'Alerte créée avec succès !');
        })->name('alertes.store');
        
        // Actions sur les alertes
        Route::post('/alertes/{id}/lire', function($id) {
            $alerte = \App\Models\Alerte::findOrFail($id);
            $alerte->update(['est_lue' => true]);
            return redirect()->route('alertes.index')->with('success', 'Alerte marquée comme lue.');
        })->name('alertes.marquer-lue');
        
        Route::post('/alertes/{id}/non-lue', function($id) {
            $alerte = \App\Models\Alerte::findOrFail($id);
            $alerte->update(['est_lue' => false]);
            return redirect()->route('alertes.index')->with('success', 'Alerte marquée comme non lue.');
        })->name('alertes.marquer-non-lue');
        
        Route::delete('/alertes/{id}', function($id) {
            $alerte = \App\Models\Alerte::findOrFail($id);
            $alerte->delete();
            return redirect()->route('alertes.index')->with('success', 'Alerte supprimée avec succès.');
        })->name('alertes.destroy');
    });
    
    // ============================================
    // LOTS - CORRIGÉ !
    // ============================================
    
    // Routes en lecture seule - pour tous
    Route::get('/lots', [LotController::class, 'index'])->name('lots.index');
    Route::get('/lots/{lot}', [LotController::class, 'show'])->name('lots.show');
    
    // Routes d'écriture - UNIQUEMENT admin/pharmacien
    Route::middleware(['role:admin,pharmacien'])->group(function () {
        Route::get('/lots/create', [LotController::class, 'create'])->name('lots.create');
        Route::post('/lots', [LotController::class, 'store'])->name('lots.store');
        Route::get('/lots/{lot}/edit', [LotController::class, 'edit'])->name('lots.edit');
        Route::put('/lots/{lot}', [LotController::class, 'update'])->name('lots.update');
        Route::delete('/lots/{lot}', [LotController::class, 'destroy'])->name('lots.destroy');
        Route::post('/lots/{lot}/ajuster-stock', [LotController::class, 'ajusterStock'])->name('lots.ajuster-stock');
    });
    
    // ============================================
    // ADMIN ROUTES - UNIQUEMENT administrateurs
    // ============================================
    
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/admin/clear-cache', [AdminController::class, 'clearCache'])->name('admin.clearCache');
        Route::post('/admin/users/{user}/promouvoir', [AdminController::class, 'promouvoir'])->name('admin.users.promouvoir');
        Route::post('/admin/users/{user}/retrograder', [AdminController::class, 'retrograder'])->name('admin.users.retrograder');
    });
    
});

// ============================================
// ROUTES UTILITAIRES ET API
// ============================================

Route::get('/health', function() {
    return response()->json([
        'status' => 'ok', 
        'app' => 'Pharma Track', 
        'time' => now(),
        'version' => '1.0.0'
    ]);
});

Route::get('/api/user-role', function() {
    if (auth()->check()) {
        return response()->json([
            'role' => auth()->user()->role,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'isVisitor' => auth()->user()->role === 'visiteur',
            'isAdmin' => auth()->user()->role === 'admin',
            'isPharmacien' => auth()->user()->role === 'pharmacien'
        ]);
    }
    return response()->json([
        'role' => null, 
        'isVisitor' => false,
        'isAdmin' => false,
        'isPharmacien' => false
    ]);
})->name('api.user-role');

// ============================================
// ROUTE FALLBACK
// ============================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});