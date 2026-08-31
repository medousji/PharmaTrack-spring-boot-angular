@extends('layouts.app')

@section('title', 'Paramètres système - Pharma Track')
@section('page-title', '')
@section('page-icon', 'bi-gear-fill')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-gear-fill me-2" style="color: #d4af37;"></i>Paramètres système
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('home') }}" style="color: #9c8a78; text-decoration: none;">Accueil</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" style="color: #9c8a78; text-decoration: none;">Administration</a>
                                </li>
                                <li class="breadcrumb-item active" style="color: #d4af37;">Paramètres</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="btn px-4 py-2 rounded-pill" 
                           style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informations système -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-info-circle-fill me-2" style="color: #d4af37;"></i>Informations système
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="info-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #5d4b38; font-weight: 600;">
                                    <i class="bi bi-app-indicator me-2" style="color: #d4af37;"></i>Nom de l'application
                                </span>
                                <span style="color: #8b7355;">{{ $settings['app_name'] ?? 'Pharma Track' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #5d4b38; font-weight: 600;">
                                    <i class="bi bi-code-slash me-2" style="color: #9caf88;"></i>Version
                                </span>
                                <span style="color: #8b7355;">{{ $settings['version'] ?? '1.0.0' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #5d4b38; font-weight: 600;">
                                    <i class="bi bi-cloud me-2" style="color: #9caf88;"></i>Environnement
                                </span>
                                <span style="color: #8b7355;">
                                    <span class="badge px-2 py-1" style="background: {{ ($settings['app_env'] ?? 'local') === 'production' ? '#9caf88' : '#e6a57e' }}; color: white;">
                                        {{ $settings['app_env'] ?? 'local' }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #5d4b38; font-weight: 600;">
                                    <i class="bi bi-database me-2" style="color: #d4af37;"></i>Base de données
                                </span>
                                <span style="color: #8b7355;">{{ $settings['db_connection'] ?? 'mysql' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #5d4b38; font-weight: 600;">
                                    <i class="bi bi-clock me-2" style="color: #d4af37;"></i>Fuseau horaire
                                </span>
                                <span style="color: #8b7355;">{{ $settings['timezone'] ?? 'UTC' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="info-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #5d4b38; font-weight: 600;">
                                    <i class="bi bi-php me-2" style="color: #d4af37;"></i>Version PHP
                                </span>
                                <span style="color: #8b7355;">{{ phpversion() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="info-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #5d4b38; font-weight: 600;">
                                    <i class="bi bi-laravel me-2" style="color: #d4af37;"></i>Version Laravel
                                </span>
                                <span style="color: #8b7355;">{{ app()->version() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-tools me-2" style="color: #d4af37;"></i>Maintenance
                </h5>

                <div class="row g-3">
                    <!-- Vider le cache -->
                    <div class="col-md-12">
                        <div class="maintenance-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="color: #5d4b38; font-weight: 600;">
                                        <i class="bi bi-trash me-2" style="color: #e6a57e;"></i>Vider les caches
                                    </span>
                                    <p class="small text-muted mb-0 mt-1">Vider le cache de l'application, des vues, routes et config</p>
                                </div>
                                <form action="{{ route('admin.clearCache') }}" method="POST" onsubmit="return confirm('⚠️ Vider le cache peut temporairement ralentir l\'application. Continuer ?');">
                                    @csrf
                                    <button type="submit" class="btn rounded-pill px-4 py-2" 
                                            style="background: #d4af37; color: white; border: none;">
                                        <i class="bi bi-broom me-2"></i>Vider le cache
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Optimiser la base de données -->
                    <div class="col-md-12">
                        <div class="maintenance-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="color: #5d4b38; font-weight: 600;">
                                        <i class="bi bi-database-check me-2" style="color: #9caf88;"></i>Optimiser la base de données
                                    </span>
                                    <p class="small text-muted mb-0 mt-1">Optimiser les tables et nettoyer les données temporaires</p>
                                </div>
                                <button type="button" class="btn rounded-pill px-4 py-2" 
                                        style="background: #9caf88; color: white; border: none; opacity: 0.6;" disabled>
                                    <i class="bi bi-database me-2"></i>Optimiser
                                    <span class="small">(Bientôt disponible)</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sauvegarde -->
                    <div class="col-md-12">
                        <div class="maintenance-item p-3 rounded-3" style="background: #f5efe8;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="color: #5d4b38; font-weight: 600;">
                                        <i class="bi bi-database-down me-2" style="color: #8b7355;"></i>Sauvegarde
                                    </span>
                                    <p class="small text-muted mb-0 mt-1">Exporter une sauvegarde de la base de données</p>
                                </div>
                                <button type="button" class="btn rounded-pill px-4 py-2" 
                                        style="background: #8b7355; color: white; border: none; opacity: 0.6;" disabled>
                                    <i class="bi bi-download me-2"></i>Sauvegarder
                                    <span class="small">(Bientôt disponible)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message de succès après vidage du cache -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mt-3 mb-0" style="background: #d4edda; border: none; color: #155724;">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mt-3 mb-0" style="background: #f8d7da; border: none; color: #721c24;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-light {
        transition: all 0.3s ease;
    }
    .card-light:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.15) !important;
    }
    .info-item, .maintenance-item {
        transition: all 0.3s ease;
    }
    .info-item:hover, .maintenance-item:hover {
        transform: translateX(5px);
        background: #f0ebe4 !important;
    }
    .welcome-card {
        transition: all 0.3s ease;
    }
    .welcome-card:hover {
        box-shadow: 0 10px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        .btn {
            width: 100%;
        }
    }
</style>
@endpush