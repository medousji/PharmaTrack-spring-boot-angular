@extends('layouts.app')

@section('title', 'Statistiques - Pharma Track')
@section('page-title', '')
@section('page-icon', 'bi-graph-up')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-graph-up me-2" style="color: #d4af37;"></i>Statistiques
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('home') }}" style="color: #9c8a78; text-decoration: none;">Accueil</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" style="color: #9c8a78; text-decoration: none;">Administration</a>
                                </li>
                                <li class="breadcrumb-item active" style="color: #d4af37;">Statistiques</li>
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
        <!-- Cartes principales -->
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4 text-center" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="rounded-circle p-3 d-inline-flex mb-3" style="background: #f5efe8;">
                    <i class="bi bi-people fs-1" style="color: #d4af37;"></i>
                </div>
                <h3 class="fw-bold mb-0" style="color: #5d4b38;">
                    {{ ($stats['users_by_role']['admin'] ?? 0) + ($stats['users_by_role']['pharmacien'] ?? 0) + ($stats['users_by_role']['visiteur'] ?? 0) }}
                </h3>
                <span class="text-muted">Total utilisateurs</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4 text-center" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="rounded-circle p-3 d-inline-flex mb-3" style="background: #f5efe8;">
                    <i class="bi bi-capsule-pill fs-1" style="color: #9caf88;"></i>
                </div>
                <h3 class="fw-bold mb-0" style="color: #5d4b38;">{{ $stats['total_medicaments'] ?? 0 }}</h3>
                <span class="text-muted">Total médicaments</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4 text-center" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="rounded-circle p-3 d-inline-flex mb-3" style="background: #f5efe8;">
                    <i class="bi bi-box-seam fs-1" style="color: #e6a57e;"></i>
                </div>
                <h3 class="fw-bold mb-0" style="color: #5d4b38;">{{ $stats['total_lots'] ?? 0 }}</h3>
                <span class="text-muted">Total lots</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4 text-center" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="rounded-circle p-3 d-inline-flex mb-3" style="background: #f5efe8;">
                    <i class="bi bi-building fs-1" style="color: #8b7355;"></i>
                </div>
                <h3 class="fw-bold mb-0" style="color: #5d4b38;">{{ $stats['total_pharmacies'] ?? 0 }}</h3>
                <span class="text-muted">Total pharmacies</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <!-- Statistiques par rôle -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-person-badge me-2" style="color: #d4af37;"></i>Statistiques par rôle
                </h5>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td style="color: #5d4b38;">
                                    <i class="bi bi-shield-lock-fill me-2" style="color: #d4af37;"></i>Administrateurs
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold" style="color: #5d4b38;">{{ $stats['users_by_role']['admin'] ?? 0 }}</span>
                                </td>
                                <td class="text-end" style="width: 100px;">
                                    <div class="progress" style="height: 8px; background: #e8e4da;">
                                        @php
                                            $totalUsers = ($stats['users_by_role']['admin'] ?? 0) + ($stats['users_by_role']['pharmacien'] ?? 0) + ($stats['users_by_role']['visiteur'] ?? 0);
                                            $totalUsers = max($totalUsers, 1);
                                            $adminPercent = (($stats['users_by_role']['admin'] ?? 0) / $totalUsers) * 100;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $adminPercent }}%; background: #d4af37;"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #5d4b38;">
                                    <i class="bi bi-hospital me-2" style="color: #9caf88;"></i>Pharmaciens
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold" style="color: #5d4b38;">{{ $stats['users_by_role']['pharmacien'] ?? 0 }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="height: 8px; background: #e8e4da;">
                                        @php
                                            $pharmaPercent = (($stats['users_by_role']['pharmacien'] ?? 0) / $totalUsers) * 100;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $pharmaPercent }}%; background: #9caf88;"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #5d4b38;">
                                    <i class="bi bi-eye me-2" style="color: #e6a57e;"></i>Visiteurs
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold" style="color: #5d4b38;">{{ $stats['users_by_role']['visiteur'] ?? 0 }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="height: 8px; background: #e8e4da;">
                                        @php
                                            $visiteurPercent = (($stats['users_by_role']['visiteur'] ?? 0) / $totalUsers) * 100;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $visiteurPercent }}%; background: #e6a57e;"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 pt-2 border-top" style="border-color: #e8e4da;">
                    <div class="d-flex justify-content-between">
                        <span style="color: #5d4b38;">Total</span>
                        <span class="fw-bold" style="color: #5d4b38;">{{ $totalUsers }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisateurs par statut -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-toggle2-on me-2" style="color: #d4af37;"></i>Utilisateurs par statut
                </h5>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td style="color: #5d4b38;">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #9caf88;"></i>Actifs
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold" style="color: #5d4b38;">{{ $stats['users_by_status']['active'] ?? 0 }}</span>
                                </td>
                                <td class="text-end" style="width: 100px;">
                                    <div class="progress" style="height: 8px; background: #e8e4da;">
                                        @php
                                            $actifPercent = (($stats['users_by_status']['active'] ?? 0) / $totalUsers) * 100;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $actifPercent }}%; background: #9caf88;"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #5d4b38;">
                                    <i class="bi bi-x-circle-fill me-2" style="color: #e6a57e;"></i>Inactifs
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold" style="color: #5d4b38;">{{ $stats['users_by_status']['inactive'] ?? 0 }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="height: 8px; background: #e8e4da;">
                                        @php
                                            $inactifPercent = (($stats['users_by_status']['inactive'] ?? 0) / $totalUsers) * 100;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $inactifPercent }}%; background: #e6a57e;"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #5d4b38;">
                                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: #8b7355;"></i>Suspendus
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold" style="color: #5d4b38;">{{ $stats['users_by_status']['suspended'] ?? 0 }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="height: 8px; background: #e8e4da;">
                                        @php
                                            $suspenduPercent = (($stats['users_by_status']['suspended'] ?? 0) / $totalUsers) * 100;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $suspenduPercent }}%; background: #8b7355;"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <!-- Graphique circulaire - Répartition par rôle -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-pie-chart-fill me-2" style="color: #d4af37;"></i>Répartition par rôle
                </h5>
                <div class="text-center py-3">
                    <canvas id="roleChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique circulaire - Répartition par statut -->
        <div class="col-md-6">
            <div class="card-light p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-pie-chart-fill me-2" style="color: #d4af37;"></i>Répartition par statut
                </h5>
                <div class="text-center py-3">
                    <canvas id="statusChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des rôles
        const roleCtx = document.getElementById('roleChart').getContext('2d');
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: ['Administrateurs', 'Pharmaciens', 'Visiteurs'],
                datasets: [{
                    data: [
                        {{ $stats['users_by_role']['admin'] ?? 0 }}, 
                        {{ $stats['users_by_role']['pharmacien'] ?? 0 }}, 
                        {{ $stats['users_by_role']['visiteur'] ?? 0 }}
                    ],
                    backgroundColor: ['#d4af37', '#9caf88', '#e6a57e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#5d4b38', font: { size: 12 } }
                    }
                }
            }
        });

        // Graphique des statuts
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Actifs', 'Inactifs', 'Suspendus'],
                datasets: [{
                    data: [
                        {{ $stats['users_by_status']['active'] ?? 0 }}, 
                        {{ $stats['users_by_status']['inactive'] ?? 0 }}, 
                        {{ $stats['users_by_status']['suspended'] ?? 0 }}
                    ],
                    backgroundColor: ['#9caf88', '#e6a57e', '#8b7355'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#5d4b38', font: { size: 12 } }
                    }
                }
            }
        });
    });
</script>
@endpush

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
        box-shadow: 0 10px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush