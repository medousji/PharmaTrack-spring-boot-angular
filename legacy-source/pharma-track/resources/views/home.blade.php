@extends('layouts.app')

@section('title', 'Pharma Track - Accueil')
@section('page-title', '')
@section('page-icon', 'bi-house')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            
            <!-- Message de bienvenue si connecté -->
            @auth
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" style="background: #d4edda; border: none;" role="alert">
                    <i class="bi bi-check-circle-fill me-2" style="color: #155724;"></i>
                    <strong>Bienvenue, {{ Auth::user()->name }} !</strong> Vous êtes connecté en tant que <strong class="text-capitalize">{{ Auth::user()->role }}</strong>.
                    <hr style="margin: 10px 0; border-color: #c3e6cb;">
                    <div class="d-flex gap-2 justify-content-center">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm rounded-pill px-4 py-2" style="background: #d4af37; color: white;">
                                <i class="bi bi-speedometer2 me-1"></i> Accéder au Dashboard Admin
                            </a>
                        @elseif(Auth::user()->role === 'fournisseur')
                            <a href="{{ route('fournisseur.dashboard') }}" class="btn btn-sm rounded-pill px-4 py-2" style="background: #d4af37; color: white;">
                                <i class="bi bi-building me-1"></i> Accéder à mon Espace Fournisseur
                            </a>
                        @elseif(Auth::user()->role === 'pharmacien')
                            <a href="{{ route('dashboard') }}" class="btn btn-sm rounded-pill px-4 py-2" style="background: #d4af37; color: white;">
                                <i class="bi bi-speedometer2 me-1"></i> Accéder au Dashboard
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-sm rounded-pill px-4 py-2" style="background: #d4af37; color: white;">
                                <i class="bi bi-eye me-1"></i> Accéder au Dashboard
                            </a>
                        @endif
                        
                        <!-- Formulaire de déconnexion avec CSRF token -->
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm rounded-pill px-4 py-2" style="background: #e6a57e; color: white; border: none;">
                                <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info rounded-3 mb-4" style="background: #e8f4f8; border: none; color: #0c5460;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Vous n'êtes pas connecté. <a href="{{ route('login') }}" style="color: #d4af37; font-weight: bold;">Connectez-vous</a> pour accéder à votre espace personnel.
                </div>
            @endauth

            <h1 class="display-4 fw-bold mb-4" style="color: #5d4b38;">
                <span style="color: #d4af37;">Pharma</span>Track
            </h1>
            <p class="lead mb-5" style="color: #8b7355;">
                Solution innovante de gestion des stocks médicaux pour les pharmacies tunisiennes
            </p>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-lg rounded-pill px-5 py-3" 
                           style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                        </a>
                    @elseif(Auth::user()->role === 'fournisseur')
                        <a href="{{ route('fournisseur.dashboard') }}" class="btn btn-lg rounded-pill px-5 py-3" 
                           style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-building me-2"></i>Mon Espace Fournisseur
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-lg rounded-pill px-5 py-3" 
                           style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-speedometer2 me-2"></i>Tableau de Bord
                        </a>
                    @endif
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-lg rounded-pill px-5 py-3" 
                       style="background: #d4af37; color: white; border: none;">
                        <i class="bi bi-speedometer2 me-2"></i>Tableau de Bord
                    </a>
                @endauth
                
                <a href="{{ route('medicaments.index') }}" class="btn btn-lg rounded-pill px-5 py-3" 
                   style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                    <i class="bi bi-capsule me-2"></i>Gérer les Médicaments
                </a>
            </div>
            
            <div class="row mt-5 pt-5">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background: #ffffff;">
                        <i class="bi bi-robot fs-1 mb-3" style="color: #d4af37;"></i>
                        <h5 style="color: #5d4b38;">Prédictions IA</h5>
                        <p style="color: #9c8a78;">Anticipez la demande avec notre intelligence artificielle</p>
                        <a href="{{ route('predictions.index') }}" class="stretched-link"></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background: #ffffff;">
                        <i class="bi bi-shield-check fs-1 mb-3" style="color: #d4af37;"></i>
                        <h5 style="color: #5d4b38;">Conformité ONP</h5>
                        <p style="color: #9c8a78;">Rapports réglementaires conformes aux normes tunisiennes</p>
                        <a href="{{ route('conformite.index') }}" class="stretched-link"></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background: #ffffff;">
                        <i class="bi bi-capsule-pill fs-1 mb-3" style="color: #d4af37;"></i>
                        <h5 style="color: #5d4b38;">Médicaments</h5>
                        <p style="color: #9c8a78;">Gestion complète des stocks et des lots</p>
                        <a href="{{ route('medicaments.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 pt-4 text-center" style="color: #9c8a78;">
                <small>© {{ date('Y') }} Pharma Track - Gestion des stocks médicaux</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(212, 175, 55, 0.1) !important;
    }
    
    .card i {
        transition: all 0.3s ease;
    }
    
    .card:hover i {
        transform: scale(1.1);
    }
</style>
@endpush