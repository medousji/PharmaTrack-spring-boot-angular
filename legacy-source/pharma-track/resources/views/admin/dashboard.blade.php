@extends('layouts.app')

@section('title', 'Dashboard Administration - Pharma Track')
@section('page-title', 'Dashboard Administration')
@section('page-icon', 'bi-gear-fill')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            👋 Bonjour, <span style="color: #d4af37;">{{ auth()->user()->name }}</span>
                        </h2>
                        <p class="mb-0" style="color: #9c8a78;">
                            <i class="bi bi-calendar3 me-2" style="color: #d4af37;"></i>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </p>
                    </div>
                    <div class="d-none d-md-block">
                        <div style="background: #f0ebe4; padding: 1rem; border-radius: 50%;">
                            <i class="bi bi-gear-fill fs-1" style="color: #8b7355;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row g-4 mb-4">
        <!-- Utilisateurs -->
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Utilisateurs</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $stats['users']['total'] }}</h2>
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <span class="badge" style="background: #d4af37; color: white;">Admin: {{ $stats['users']['admins'] }}</span>
                            <span class="badge" style="background: #9caf88; color: white;">Pharma: {{ $stats['users']['pharmaciens'] }}</span>
                            <span class="badge" style="background: #9c8a78; color: white;">Vis: {{ $stats['users']['visiteurs'] }}</span>
                        </div>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-people fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Médicaments -->
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Médicaments</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $stats['medicaments']['total'] }}</h2>
                        <small style="color: #9c8a78;">Total en base</small>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-capsule fs-1" style="color: #9caf88;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lots -->
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Lots</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $stats['lots']['total'] }}</h2>
                        <div class="d-flex gap-2 mt-2">
                            <span class="badge" style="background: #9caf88; color: white;">Actifs: {{ $stats['lots']['actifs'] }}</span>
                            <span class="badge" style="background: #e6a57e; color: white;">Périmés: {{ $stats['lots']['perimes'] }}</span>
                        </div>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-boxes fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Alertes</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $stats['alertes']['total'] }}</h2>
                        <small style="color: #e6a57e;">Non lues: {{ $stats['alertes']['non_lues'] }}</small>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-bell fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux récents -->
    <div class="row g-4 mb-4">
        <!-- Derniers utilisateurs -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-people-fill me-2" style="color: #d4af37;"></i>Derniers utilisateurs
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Nom</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Email</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Rôle</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_users as $user)
                            <tr>
                                <td class="fw-semibold" style="color: #5d4b38;">{{ $user->name }}</td>
                                <td style="color: #8b7355;">{{ $user->email }}</td>
                                <td>
                                    <span class="badge" style="background: {{ $user->role === 'admin' ? '#d4af37' : ($user->role === 'pharmacien' ? '#9caf88' : '#9c8a78') }}; color: white;">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td style="color: #9c8a78;">{{ $user->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color: #9c8a78;">Aucun utilisateur</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-link text-decoration-none mt-3 p-0" style="color: #d4af37;">
                    Voir tous les utilisateurs <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Derniers médicaments -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-capsule-pill me-2" style="color: #9caf88;"></i>Derniers médicaments
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="color: #9c8a78; border-bottom: 2px solid #9caf88;">Nom</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #9caf88;">DCI</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #9caf88;">Forme</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #9caf88;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_medicaments as $medicament)
                            <tr>
                                <td class="fw-semibold" style="color: #5d4b38;">{{ $medicament->nom_commercial_fr ?? $medicament->nom }}</td>
                                <td style="color: #8b7355;">{{ $medicament->dci ?? '—' }}</td>
                                <td style="color: #8b7355;">{{ $medicament->forme ?? '—' }}</td>
                                <td style="color: #9c8a78;">{{ $medicament->created_at ? $medicament->created_at->format('d/m/Y') : '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color: #9c8a78;">Aucun médicament</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('medicaments.index') }}" class="btn btn-link text-decoration-none mt-3 p-0" style="color: #9caf88;">
                    Voir tous les médicaments <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-12">
            <div class="quick-actions p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color: #d4af37;"></i>Actions rapides
                </h5>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('admin.users.create') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #d4af37; color: white; border: none;">
                        <i class="bi bi-person-plus me-2"></i>Nouvel utilisateur
                    </a>
                    <a href="{{ route('medicaments.create') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #9caf88; color: white; border: none;">
                        <i class="bi bi-plus-circle me-2"></i>Nouveau médicament
                    </a>
                    <a href="{{ route('lots.create') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #e6a57e; color: white; border: none;">
                        <i class="bi bi-box me-2"></i>Nouveau lot
                    </a>
                    <a href="{{ route('admin.stats') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #9c8a78; color: white; border: none;">
                        <i class="bi bi-graph-up me-2"></i>Statistiques
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #8b7355; color: white; border: none;">
                        <i class="bi bi-gear me-2"></i>Paramètres
                    </a>
                    <form action="{{ route('admin.clearCache') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn rounded-pill px-4 py-2" 
                                style="background: #e6a57e; color: white; border: none;"
                                onclick="return confirm('Vider le cache ?')">
                            <i class="bi bi-trash me-2"></i>Vider le cache
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Animations pour le thème beige */
    .stat-card {
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease;
        animation-fill-mode: both;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
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
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    
    .btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background: #faf7f2 !important;
        transform: translateX(5px);
    }
    
    .badge {
        transition: all 0.3s ease;
    }
    
    .badge:hover {
        transform: scale(1.05);
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .btn {
            width: 100%;
        }
    }
</style>
@endpush