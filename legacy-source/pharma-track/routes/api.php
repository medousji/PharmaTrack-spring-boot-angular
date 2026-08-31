<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ConformiteController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommandeFournisseurController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================
// ROUTES PUBLIQUES (SANS AUTHENTIFICATION)
// ============================================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ============================================
// ROUTES PROTÉGÉES (AVEC AUTH SANCTUM)
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    
    // ---------- AUTHENTIFICATION ----------
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user-role', [AuthController::class, 'getUserRole']);
    
    // ---------- DASHBOARD ----------
    Route::get('/dashboard/stats', [DashboardController::class, 'index']);
    
    // ---------- MÉDICAMENTS ----------
    Route::get('/medicaments', [MedicamentController::class, 'index']);
    Route::get('/medicaments/{medicament}', [MedicamentController::class, 'show']);
    
    // ---------- LOTS ----------
    Route::get('/lots', [LotController::class, 'index']);
    Route::get('/lots/{lot}', [LotController::class, 'show']);
    
    // ---------- ALERTES ----------
    Route::get('/alertes', [AlerteController::class, 'index']);
    
    // ---------- SCAN ----------
    Route::post('/scan', [ScanController::class, 'traiter']);
    
    // ---------- PRÉDICTIONS IA (CORRIGÉE) ----------
    // ⚠️ IMPORTANT: Utilisez 'getPredictions' et NON 'api'
    Route::get('/predictions', [PredictionController::class, 'getPredictions']);
    
    // ---------- CONFORMITÉ ONP ----------
    Route::get('/conformite', [ConformiteController::class, 'index']);
    
    // ---------- COMMANDES FOURNISSEURS ----------
    Route::post('/verifier-disponibilite', [CommandeFournisseurController::class, 'verifierDisponibilite']);
    
    // ---------- PROFIL UTILISATEUR ----------
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});

// ============================================
// ROUTES ADMIN (ROLE ADMIN REQUIS)
// ============================================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/clear-cache', [AdminController::class, 'clearCache']);
});

// ============================================
// ROUTE DE TEST (POUR VÉRIFIER QUE L'API FONCTIONNE)
// ============================================
Route::get('/health', function() {
    return response()->json([
        'status' => 'ok',
        'api_version' => '1.0',
        'timestamp' => now()->toIso8601String(),
        'message' => 'API Pharma Track est opérationnelle'
    ]);
});