@extends('layouts.app')

@section('title', 'Dashboard Fournisseur - Pharma Track')
@section('page-title', 'Dashboard Fournisseur')
@section('page-icon', 'bi-building')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Dashboard Fournisseur</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête avec bienvenue -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            👋 Bonjour, <span style="color: #d4af37;">{{ $fournisseur->raison_sociale }}</span>
                        </h2>
                        <p class="mb-0" style="color: #9c8a78;">
                            <i class="bi bi-calendar3 me-2" style="color: #d4af37;"></i>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </p>
                    </div>
                    <div class="d-none d-md-block">
                        <div style="background: #f0ebe4; padding: 1rem; border-radius: 50%;">
                            <i class="bi bi-building fs-1" style="color: #d4af37;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Commandes en cours</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $stats['commandes_encours'] ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-truck fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: #e8e4da;">
                    <div class="progress-bar" style="width: 60%; background: #d4af37;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Commandes livrées</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $stats['commandes_livrees'] ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-check-circle-fill fs-1" style="color: #9caf88;"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: #e8e4da;">
                    <div class="progress-bar" style="width: 75%; background: #9caf88;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Produits disponibles</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $stats['produits_disponibles'] ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-capsule-pill fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: #e8e4da;">
                    <div class="progress-bar" style="width: 50%; background: #e6a57e;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières commandes -->
    <div class="row">
        <div class="col-12">
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-clock-history me-2" style="color: #d4af37;"></i>Dernières commandes reçues
                </h5>
                
                @if($commandes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: #f5efe8;">
                            <tr>
                                <th style="color: #5d4b38;">N° Commande</th>
                                <th style="color: #5d4b38;">Date</th>
                                <th style="color: #5d4b38;">Total</th>
                                <th style="color: #5d4b38;">Statut</th>
                                <th style="color: #5d4b38;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commandes as $commande)
                            <tr>
                                <td style="color: #8b7355;">#{{ $commande->numero_commande }}</td>
                                <td style="color: #8b7355;">{{ $commande->created_at->format('d/m/Y') }}</td>
                                <td style="color: #d4af37; font-weight: bold;">{{ number_format($commande->total_ttc, 3) }} TND</td>
                                <td>
                                    @php
                                        $statutColors = [
                                            'en_attente' => '#e6a57e',
                                            'confirmee' => '#d4af37',
                                            'preparation' => '#9caf88',
                                            'expediee' => '#8b7355',
                                            'livree' => '#5d4b38'
                                        ];
                                        $color = $statutColors[$commande->statut] ?? '#9c8a78';
                                    @endphp
                                    <span class="badge px-3 py-2" style="background: {{ $color }}; color: white;">
                                        {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    @if(in_array($commande->statut, ['en_attente', 'confirmee', 'preparation']))
                                    <form action="{{ route('fournisseur.commandes.expedier', $commande->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm rounded-pill" 
                                                style="background: #d4af37; color: white; border: none;"
                                                onclick="return confirm('Confirmer l\'expédition de cette commande ?')">
                                            <i class="bi bi-truck"></i> Expédier
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4" style="color: #9c8a78;">
                    <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                    <span>Aucune commande reçue pour le moment.</span>
                </div>
                @endif
                
                <div class="mt-3 text-end">
                    <a href="{{ route('fournisseur.commandes') }}" class="btn btn-link text-decoration-none" style="color: #d4af37;">
                        Voir toutes les commandes <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="quick-actions p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color: #d4af37;"></i>Actions rapides
                </h5>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('fournisseur.prix') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #d4af37; color: white; border: none;">
                        <i class="bi bi-tag me-2"></i>Gérer mes prix
                    </a>
                    <a href="{{ route('fournisseur.commandes') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #9caf88; color: white; border: none;">
                        <i class="bi bi-box-seam me-2"></i>Voir les commandes
                    </a>
                    <a href="{{ route('profile.index') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #e6a57e; color: white; border: none;">
                        <i class="bi bi-person-gear me-2"></i>Mon profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .stat-icon {
        transition: all 0.3s ease;
    }
    .stat-card:hover .stat-icon {
        transform: rotate(5deg) scale(1.1);
    }
    .card-light {
        transition: all 0.3s ease;
    }
    .card-light:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.15) !important;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    .welcome-card {
        transition: all 0.3s ease;
    }
    .welcome-card:hover {
        box-shadow: 0 10px 25px rgba(139, 115, 85, 0.1) !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(212, 175, 55, 0.05);
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
</style>
@endpush