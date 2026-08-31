@extends('layouts.app')

@section('title', 'Gestion des Lots - Pharma Track')
@section('page-title', '')  {{-- Vide pour éviter le doublon --}}
@section('page-icon', 'bi-box')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Breadcrumb personnalisé -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" style="color: #9c8a78; text-decoration: none; transition: color 0.3s ease;">
                            <i class="bi bi-house-door me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">
                        Gestion des Lots
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- En-tête avec titre et bouton d'ajout -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-box me-2" style="color: #d4af37;"></i>Gestion des Lots
                        </h2>
                        <p class="mb-0" style="color: #9c8a78;">
                            <i class="bi bi-database me-2"></i>Total: <span class="fw-bold" style="color: #8b7355;">{{ count($lots) }}</span> lots
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('lots.create') }}" class="btn rounded-pill px-4 py-2" 
                           style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-plus-circle me-2"></i>Nouveau lot
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="search-card p-3 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <form method="GET" action="{{ route('lots.index') }}" class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                <i class="bi bi-search" style="color: #d4af37;"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Rechercher par numéro de lot, médicament..."
                                   value="{{ request('search') }}"
                                   style="border-color: #e8e4da; background: transparent;">
                        </div>
                    </div>
                    <button type="submit" class="btn px-4" style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                        Rechercher
                    </button>
                    @if(request('search'))
                        <a href="{{ route('lots.index') }}" class="btn px-4" style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau des lots -->
    <div class="row">
        <div class="col-12">
            <div class="table-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3" style="color: #8b7355; font-weight: 600; border-bottom: 2px solid #d4af37;">ID</th>
                                <th style="color: #8b7355; font-weight: 600; border-bottom: 2px solid #d4af37;">Numéro Lot</th>
                                <th style="color: #8b7355; font-weight: 600; border-bottom: 2px solid #d4af37;">Médicament</th>
                                <th style="color: #8b7355; font-weight: 600; border-bottom: 2px solid #d4af37;">Quantité</th>
                                <th style="color: #8b7355; font-weight: 600; border-bottom: 2px solid #d4af37;">Date Péremption</th>
                                <th style="color: #8b7355; font-weight: 600; border-bottom: 2px solid #d4af37;">Statut</th>
                                <th style="color: #8b7355; font-weight: 600; border-bottom: 2px solid #d4af37; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lots as $lot)
                            <tr class="align-middle" style="border-bottom: 1px solid #f0ebe4;">
                                <td class="ps-3">
                                    <span class="badge py-2 px-3" style="background: #f5efe8; color: #8b7355; font-family: monospace;">
                                        {{ $lot['id'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold" style="color: #5d4b38;">
                                        {{ $lot['numero_lot'] ?? $lot['numero'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold" style="color: #5d4b38;">
                                            {{ $lot['medicament_nom'] ?? $lot['medicament']['nom_commercial_fr'] ?? 'N/A' }}
                                        </span>
                                        @if(isset($lot['medicament_code']))
                                            <small style="color: #9c8a78;">{{ $lot['medicament_code'] }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold" style="color: #5d4b38;">{{ $lot['quantite_actuelle'] ?? $lot['quantite'] ?? '0' }}</span>
                                    <small style="color: #9c8a78;">/{{ $lot['quantite_initial'] ?? $lot['quantite_init'] ?? '0' }}</small>
                                </td>
                                <td>
                                    @php
                                        $datePeremption = isset($lot['date_peremption']) ? \Carbon\Carbon::parse($lot['date_peremption']) : null;
                                        if($datePeremption) {
                                            $daysLeft = now()->diffInDays($datePeremption, false);
                                            $isExpired = $daysLeft <= 0;
                                            $isNear = $daysLeft > 0 && $daysLeft <= 30;
                                        }
                                    @endphp
                                    
                                    @if($datePeremption)
                                        @if($isExpired)
                                            <span class="badge" style="background: #e6a57e; color: white;">{{ $datePeremption->format('d/m/Y') }}</span>
                                            <small class="d-block text-danger">Périmé</small>
                                        @elseif($isNear)
                                            <span style="color: #5d4b38;">{{ $datePeremption->format('d/m/Y') }}</span>
                                            <small class="d-block" style="color: #e6a57e;">{{ $daysLeft }} jours restants</small>
                                        @else
                                            <span style="color: #5d4b38;">{{ $datePeremption->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statut = $lot['statut'] ?? 'inconnu';
                                        $statutColors = [
                                            'actif' => ['bg' => '#9caf88', 'text' => 'white'],
                                            'epuise' => ['bg' => '#e6a57e', 'text' => 'white'],
                                            'perime' => ['bg' => '#e6a57e', 'text' => 'white'],
                                            'retire' => ['bg' => '#9c8a78', 'text' => 'white'],
                                        ];
                                        $color = $statutColors[$statut] ?? ['bg' => '#9c8a78', 'text' => 'white'];
                                    @endphp
                                    <span class="badge" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                        {{ ucfirst($statut) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('lots.show', $lot['id']) }}" 
                                           class="btn btn-sm rounded-circle" 
                                           style="width: 35px; height: 35px; background: #f5efe8; color: #8b7355; display: flex; align-items: center; justify-content: center;"
                                           title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('lots.edit', $lot['id']) }}" 
                                           class="btn btn-sm rounded-circle" 
                                           style="width: 35px; height: 35px; background: #f5efe8; color: #d4af37; display: flex; align-items: center; justify-content: center;"
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('lots.destroy', $lot['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm rounded-circle" 
                                                    style="width: 35px; height: 35px; background: #f5efe8; color: #e6a57e; display: flex; align-items: center; justify-content: center; border: none;"
                                                    title="Supprimer"
                                                    onclick="return confirm('Voulez-vous vraiment supprimer ce lot ?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div style="color: #9c8a78;">
                                        <i class="bi bi-archive display-1 mb-3"></i>
                                        <p class="mb-0">Aucun lot trouvé</p>
                                        @if(request('search'))
                                            <a href="{{ route('lots.index') }}" class="btn mt-3" style="background: #f0ebe4; color: #8b7355;">
                                                Effacer la recherche
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div style="color: #9c8a78;">
                        Affichage de {{ count($lots) }} lots
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer d'information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="info-card p-3 rounded-4 d-flex justify-content-between align-items-center flex-wrap gap-2" 
                 style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div>
                    <i class="bi bi-clock-history me-2" style="color: #d4af37;"></i>
                    <span style="color: #9c8a78;">Dernière mise à jour :</span>
                    <span class="fw-semibold ms-1" style="color: #5d4b38;">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <i class="bi bi-person-circle me-2" style="color: #d4af37;"></i>
                    <span style="color: #9c8a78;">Connecté en tant que :</span>
                    <span class="fw-semibold ms-1" style="color: #5d4b38;">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Personnalisation de la pagination */
    .pagination {
        gap: 5px;
    }
    
    .page-link {
        border: 1px solid #e8e4da;
        background: #ffffff;
        color: #8b7355;
        border-radius: 8px !important;
        margin: 0 2px;
        transition: all 0.3s ease;
    }
    
    .page-link:hover {
        background: #f5efe8;
        border-color: #d4af37;
        color: #5d4b38;
        transform: translateY(-2px);
    }
    
    .page-item.active .page-link {
        background: #d4af37;
        border-color: #d4af37;
        color: white;
    }
    
    .page-item.disabled .page-link {
        background: #f5efe8;
        border-color: #e8e4da;
        color: #9c8a78;
    }
    
    /* Animation des lignes du tableau */
    tbody tr {
        transition: all 0.3s ease;
    }
    
    tbody tr:hover {
        background: #faf7f2 !important;
        transform: translateX(5px);
    }
    
    /* Animation des boutons d'action */
    .btn.rounded-circle {
        transition: all 0.3s ease;
    }
    
    .btn.rounded-circle:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(139, 115, 85, 0.2);
    }
    
    /* Style pour les badges de statut */
    .badge {
        transition: all 0.3s ease;
    }
    
    .badge:hover {
        transform: scale(1.05);
    }
    
    /* Style pour le bouton Nouveau lot */
    .btn[style*="background: #d4af37"]:hover {
        filter: brightness(1.1);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }
        
        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e8e4da;
            border-radius: 8px;
        }
        
        .table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border: none;
            border-bottom: 1px solid #f0ebe4;
        }
        
        .table tbody td:last-child {
            border-bottom: none;
        }
        
        .table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #8b7355;
            margin-right: 1rem;
        }
    }
</style>
@endpush