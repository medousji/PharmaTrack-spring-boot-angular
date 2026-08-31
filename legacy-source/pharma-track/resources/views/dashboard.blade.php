@extends('layouts.app')

@section('title', 'Tableau de Bord - Pharma Track')
@section('page-title', 'Tableau de Bord')
@section('page-icon', 'bi-grid-3x3-gap-fill')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête avec bienvenue -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            👋 Bonjour, <span style="color: #8b7355;">{{ auth()->user()->name }}</span>
                        </h2>
                        <p class="mb-0" style="color: #9c8a78;">
                            <i class="bi bi-calendar3 me-2" style="color: #d4af37;"></i>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </p>
                    </div>
                    <div class="d-none d-md-block">
                        <div style="background: #f0ebe4; padding: 1rem; border-radius: 50%;">
                            <i class="bi bi-capsule-pill fs-1" style="color: #8b7355;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Médicaments</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $totalMedicaments ?? 0 }}</h2>
                        <span class="stat-trend small" style="color: #9caf88;">
                            <i class="bi bi-arrow-up"></i> +12% ce mois
                        </span>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-capsule-pill fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: #e8e4da;">
                    <div class="progress-bar" style="width: 75%; background: #d4af37;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Ruptures</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $ruptures ?? 0 }}</h2>
                        <span class="stat-trend small" style="color: #e6a57e;">
                            <i class="bi bi-arrow-down"></i> -5% vs hier
                        </span>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-exclamation-triangle fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: #e8e4da;">
                    <div class="progress-bar" style="width: 25%; background: #e6a57e;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Alertes</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $alertesNonLues ?? 0 }}</h2>
                        <span class="stat-trend small" style="color: #fadfad;">
                            <i class="bi bi-exclamation-circle"></i> {{ $alertesNonLues ?? 0 }} non lues
                        </span>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-bell fs-1" style="color: #fadfad;"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: #e8e4da;">
                    <div class="progress-bar" style="width: 60%; background: #fadfad;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Lots proches</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $lotsProches ?? 0 }}</h2>
                        <span class="stat-trend small" style="color: #9caf88;">
                            <i class="bi bi-clock"></i> Expirent bientôt
                        </span>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-calendar-exclamation fs-1" style="color: #9caf88;"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: #e8e4da;">
                    <div class="progress-bar" style="width: 40%; background: #9caf88;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et actions -->
    <div class="row g-4 mb-4">
        <!-- Graphique circulaire -->
        <div class="col-md-4">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-pie-chart-fill me-2" style="color: #d4af37;"></i>Répartition par catégorie
                </h5>
                <div class="chart-container text-center py-3">
                    @if(!empty($categories))
                        <canvas id="categorieChart" style="height: 250px;"></canvas>
                    @else
                        <div class="text-center py-5" style="color: #9c8a78;">
                            <i class="bi bi-pie-chart fs-1 mb-2 d-block"></i>
                            <p>Aucune donnée de catégorie disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top médicaments -->
        <div class="col-md-4">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-bar-chart-fill me-2" style="color: #9caf88;"></i>Top 5 médicaments
                </h5>
                <div class="ranking-list">
                    @forelse($topMedicaments ?? [] as $index => $medicament)
                    <div class="ranking-item d-flex align-items-center mb-3 p-2 rounded-3" style="background: #faf7f2;">
                        <span class="ranking-number rounded-circle me-3 fw-bold d-flex align-items-center justify-content-center" 
                             style="width: 30px; height: 30px; background: #d4af37; color: white;">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold" style="color: #5d4b38;">{{ $medicament->nom_commercial_fr ?? 'Sans nom' }}</span>
                                <span class="fw-bold" style="color: #d4af37;">{{ $medicament->stock_total ?? 0 }}</span>
                            </div>
                            <div class="progress mt-1" style="height: 4px; background: #e8e4da;">
                                @php
                                    $maxStock = $topMedicaments->max('stock_total') ?? 1;
                                    $percentage = (($medicament->stock_total ?? 0) / $maxStock) * 100;
                                @endphp
                                <div class="progress-bar" style="width: {{ $percentage }}%; background: #d4af37;"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4" style="color: #9c8a78;">
                        <i class="bi bi-archive fs-1 mb-2"></i>
                        <p>Aucun médicament avec stock</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('medicaments.index') }}" class="btn btn-link text-decoration-none mt-3 p-0" style="color: #8b7355;">
                    Voir tous les médicaments <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Alertes récentes -->
        <div class="col-md-4">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-bell-fill me-2" style="color: #fadfad;"></i>Alertes récentes
                </h5>
                <div class="alert-list">
                    @forelse($alertesRecentes ?? [] as $alerte)
                    <div class="alert-item p-3 mb-2 rounded-3" style="background: #faf7f2; border-left: 4px solid #fadfad;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge mb-2" style="background: #fadfad; color: #5d4b38;">
                                    {{ $alerte->type ?? 'Info' }}
                                </span>
                                <p class="mb-1 fw-semibold" style="color: #5d4b38;">{{ $alerte->message ?? 'Message' }}</p>
                                <small style="color: #9c8a78;">{{ $alerte->created_at ? $alerte->created_at->diffForHumans() : 'Récent' }}</small>
                            </div>
                            <i class="bi bi-exclamation-triangle-fill fs-4" style="color: #fadfad;"></i>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4" style="color: #9c8a78;">
                        <i class="bi bi-check-circle-fill fs-1 mb-2" style="color: #9caf88;"></i>
                        <p>Aucune alerte non lue</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('alertes.index') }}" class="btn btn-link text-decoration-none mt-3 p-0" style="color: #8b7355;">
                    Voir toutes les alertes <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Évolution sur 30 jours -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-graph-up-arrow me-2" style="color: #9caf88;"></i>Évolution des ajouts (30 jours)
                </h5>
                @if(!empty($evolution))
                    <canvas id="evolutionChart" style="height: 300px;"></canvas>
                @else
                    <div class="text-center py-5" style="color: #9c8a78;">
                        <i class="bi bi-graph-up fs-1 mb-2 d-block"></i>
                        <p>Aucune donnée d'évolution disponible</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-calendar-exclamation me-2" style="color: #e6a57e;"></i>Lots expirant bientôt
                </h5>
                @if(!empty($lotsExpiration))
                    <canvas id="expirationChart" style="height: 300px;"></canvas>
                @else
                    <div class="text-center py-5" style="color: #9c8a78;">
                        <i class="bi bi-calendar-x fs-1 mb-2 d-block"></i>
                        <p>Aucun lot n'expire dans les 30 prochains jours</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="quick-actions p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color: #d4af37;"></i>Actions rapides
                </h5>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('medicaments.create') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #d4af37; color: white; border: none;">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter médicament
                    </a>
                    <a href="{{ route('lots.create') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #9caf88; color: white; border: none;">
                        <i class="bi bi-plus-circle me-2"></i>Nouveau lot
                    </a>
                    <a href="{{ route('scan.index') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #e6a57e; color: white; border: none;">
                        <i class="bi bi-upc-scan me-2"></i>Scanner
                    </a>
                    <a href="{{ route('predictions.index') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #8b7355; color: white; border: none;">
                        <i class="bi bi-robot me-2"></i>Prédictions IA
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
    .ranking-item {
        transition: all 0.3s ease;
    }
    .ranking-item:hover {
        transform: translateX(5px);
        background: #f5efe8 !important;
    }
    .alert-item {
        transition: all 0.3s ease;
    }
    .alert-item:hover {
        transform: translateX(5px);
        background: #f5efe8 !important;
    }
    .card-light {
        transition: all 0.3s ease;
    }
    .card-light:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.15) !important;
    }
    .welcome-card {
        transition: all 0.3s ease;
    }
    .welcome-card:hover {
        box-shadow: 0 10px 25px rgba(139, 115, 85, 0.1) !important;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des catégories
    @if(!empty($categories))
        const categories = @json($categories);
        const categorieCtx = document.getElementById('categorieChart');
        if (categorieCtx && Object.keys(categories).length > 0) {
            new Chart(categorieCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(categories),
                    datasets: [{
                        data: Object.values(categories),
                        backgroundColor: ['#d4af37', '#9caf88', '#e6a57e', '#8b7355', '#c4b5a0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { color: '#5d4b38', font: { size: 11 } }
                        }
                    }
                }
            });
        }
    @endif

    // Graphique d'évolution
    @if(!empty($evolution))
        const evolution = @json($evolution);
        const evolutionCtx = document.getElementById('evolutionChart');
        if (evolutionCtx && evolution.length > 0) {
            new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: evolution.map(e => {
                        const date = new Date(e.date);
                        return date.toLocaleDateString('fr-FR');
                    }),
                    datasets: [{
                        label: 'Nouveaux médicaments',
                        data: evolution.map(e => e.total),
                        borderColor: '#9caf88',
                        backgroundColor: 'rgba(156, 175, 136, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { 
                            grid: { color: '#e8e4da' },
                            ticks: { color: '#5d4b38' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#5d4b38', rotation: 45, maxRotation: 45 }
                        }
                    }
                }
            });
        }
    @endif

    // Graphique d'expiration
    @if(!empty($lotsExpiration))
        const expiration = @json($lotsExpiration);
        const expirationCtx = document.getElementById('expirationChart');
        if (expirationCtx && expiration.length > 0) {
            new Chart(expirationCtx, {
                type: 'bar',
                data: {
                    labels: expiration.map(e => {
                        const date = new Date(e.date);
                        return date.toLocaleDateString('fr-FR');
                    }),
                    datasets: [{
                        label: 'Lots expirant',
                        data: expiration.map(e => e.total),
                        backgroundColor: '#e6a57e',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { 
                            grid: { color: '#e8e4da' },
                            ticks: { color: '#5d4b38' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#5d4b38', rotation: 45, maxRotation: 45 }
                        }
                    }
                }
            });
        }
    @endif
});
</script>
@endpush