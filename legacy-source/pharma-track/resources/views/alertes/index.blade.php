@extends('layouts.app')

@section('title', 'Gestion des Alertes - Pharma Track')
@section('page-title', '')
@section('page-icon', 'bi-bell')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Gestion des Alertes</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec logo -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-bell fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Gestion des Alertes</h1>
            <p class="text-muted mb-0" style="color: #9c8a78;">Surveillez les stocks critiques et les péremptions</p>
        </div>
    </div>

    <!-- Cartes de statistiques - utilisent les variables du contrôleur -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Total Alertes</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $totalAlertes }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-bell fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Non lues</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #e6a57e;">{{ $nonLues }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-envelope-open fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Priorité Élevée</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #e6a57e;">{{ $prioriteElevee }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-exclamation-triangle fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
        <h5 class="fw-bold mb-3" style="color: #5d4b38;">
            <i class="bi bi-funnel me-2" style="color: #d4af37;"></i>Filtres
        </h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" style="color: #5d4b38;">Type d'alerte</label>
                <select class="form-select rounded-pill" style="border-color: #e8e4da;">
                    <option>Tous les types</option>
                    <option>Expiration</option>
                    <option>Stock faible</option>
                    <option>Rupture</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="color: #5d4b38;">Priorité</label>
                <select class="form-select rounded-pill" style="border-color: #e8e4da;">
                    <option>Toutes les priorités</option>
                    <option>Faible</option>
                    <option>Moyenne</option>
                    <option>Élevée</option>
                    <option>Critique</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="color: #5d4b38;">Statut</label>
                <select class="form-select rounded-pill" style="border-color: #e8e4da;">
                    <option>Tous les statuts</option>
                    <option>Lue</option>
                    <option>Non lue</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tableau des alertes -->
    <div class="card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Type</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Message</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Priorité</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Date</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Statut</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alertes as $alerte)
                    <tr>
                        <td style="color: #5d4b38;">{{ $alerte->type ?? 'Alerte' }}</td>
                        <td style="color: #5d4b38;">{{ $alerte->message ?? 'Sans message' }}</td>
                        <td>
                            @php
                                $priorite = $alerte->niveau ?? 'faible';
                                $color = in_array($priorite, ['eleve', 'critique', 'Élevé']) ? '#e6a57e' : ($priorite === 'moyen' || $priorite === 'Moyen' ? '#d4af37' : '#9caf88');
                            @endphp
                            <span class="badge px-3 py-2" style="background: {{ $color }}; color: white;">
                                {{ ucfirst($priorite) }}
                            </span>
                        </td>
                        <td style="color: #8b7355;">{{ $alerte->created_at ? $alerte->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</td>
                        <td>
                            @if($alerte->est_lue)
                                <span class="badge px-3 py-2" style="background: #9caf88; color: white;">Lue</span>
                            @else
                                <span class="badge px-3 py-2" style="background: #e6a57e; color: white;">Non lue</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                @if(!$alerte->est_lue)
                                    <form action="{{ route('alertes.marquer-lue', $alerte->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm rounded-circle" style="width: 35px; height: 35px; background: #f5efe8; color: #d4af37; border: none;" title="Marquer comme lue">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('alertes.show', $alerte->id) }}" class="btn btn-sm rounded-circle" style="width: 35px; height: 35px; background: #f5efe8; color: #8b7355; display: flex; align-items: center; justify-content: center;" title="Voir détails">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('alertes.destroy', $alerte->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm rounded-circle" style="width: 35px; height: 35px; background: #f5efe8; color: #e6a57e; border: none;" title="Supprimer" onclick="return confirm('Supprimer cette alerte ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4" style="color: #9c8a78;">
                            <i class="bi bi-bell-slash fs-1 mb-2 d-block"></i>
                            Aucune alerte
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small style="color: #9c8a78;">Affichage de {{ $alertes->count() }} alerte(s) sur {{ $alertes->total() }}</small>
            {{ $alertes->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease;
        animation-fill-mode: both;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
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
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .table tbody tr {
        transition: all 0.2s;
    }
    .table tbody tr:hover {
        background: #faf7f2 !important;
    }
    .btn.rounded-circle {
        transition: all 0.2s;
    }
    .btn.rounded-circle:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(139, 115, 85, 0.2);
    }
    .form-select {
        transition: all 0.2s;
    }
    .form-select:hover {
        border-color: #d4af37;
    }
    .form-select:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
    }
    .pagination {
        justify-content: center;
    }
    .pagination .page-link {
        color: #5d4b38;
        border-color: #e8e4da;
        margin: 0 3px;
        border-radius: 8px;
    }
    .pagination .page-item.active .page-link {
        background-color: #d4af37;
        border-color: #d4af37;
        color: white;
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
</style>
@endpush