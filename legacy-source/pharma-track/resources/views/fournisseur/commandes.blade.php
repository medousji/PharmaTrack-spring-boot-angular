@extends('layouts.app')

@section('title', 'Commandes reçues - Pharma Track')
@section('page-title', 'Commandes reçues')
@section('page-icon', 'bi-truck')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('fournisseur.dashboard') }}" style="color: #9c8a78;">Dashboard</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Commandes</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-truck me-2" style="color: #d4af37;"></i>Commandes reçues
                        </h2>
                        <p class="mb-0" style="color: #9c8a78;">
                            Gérez les commandes passées par les pharmacies
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('fournisseur.dashboard') }}" class="btn px-4 py-2 rounded-pill" 
                           style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-light p-3 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span class="fw-semibold" style="color: #5d4b38;">Filtrer par statut :</span>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('fournisseur.commandes') }}" class="btn btn-sm rounded-pill px-3" style="background: {{ !request()->has('statut') ? '#d4af37' : '#f0ebe4' }}; color: {{ !request()->has('statut') ? 'white' : '#8b7355' }};">Tous</a>
                        <a href="{{ route('fournisseur.commandes', ['statut' => 'en_attente']) }}" class="btn btn-sm rounded-pill px-3" style="background: {{ request()->statut == 'en_attente' ? '#d4af37' : '#f0ebe4' }}; color: {{ request()->statut == 'en_attente' ? 'white' : '#8b7355' }};">En attente</a>
                        <a href="{{ route('fournisseur.commandes', ['statut' => 'confirmee']) }}" class="btn btn-sm rounded-pill px-3" style="background: {{ request()->statut == 'confirmee' ? '#d4af37' : '#f0ebe4' }}; color: {{ request()->statut == 'confirmee' ? 'white' : '#8b7355' }};">Confirmée</a>
                        <a href="{{ route('fournisseur.commandes', ['statut' => 'preparation']) }}" class="btn btn-sm rounded-pill px-3" style="background: {{ request()->statut == 'preparation' ? '#d4af37' : '#f0ebe4' }}; color: {{ request()->statut == 'preparation' ? 'white' : '#8b7355' }};">Préparation</a>
                        <a href="{{ route('fournisseur.commandes', ['statut' => 'partiel']) }}" class="btn btn-sm rounded-pill px-3" style="background: {{ request()->statut == 'partiel' ? '#d4af37' : '#f0ebe4' }}; color: {{ request()->statut == 'partiel' ? 'white' : '#8b7355' }};">Partielle</a>
                        <a href="{{ route('fournisseur.commandes', ['statut' => 'expediee']) }}" class="btn btn-sm rounded-pill px-3" style="background: {{ request()->statut == 'expediee' ? '#d4af37' : '#f0ebe4' }}; color: {{ request()->statut == 'expediee' ? 'white' : '#8b7355' }};">Expédiée</a>
                        <a href="{{ route('fournisseur.commandes', ['statut' => 'livree']) }}" class="btn btn-sm rounded-pill px-3" style="background: {{ request()->statut == 'livree' ? '#d4af37' : '#f0ebe4' }}; color: {{ request()->statut == 'livree' ? 'white' : '#8b7355' }};">Livrée</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="row">
        <div class="col-12">
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                
                @if($commandes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: #f5efe8;">
                            <tr>
                                <th style="color: #5d4b38;">N° Commande</th>
                                <th style="color: #5d4b38;">Date</th>
                                <th style="color: #5d4b38;">Médicament</th>
                                <th style="color: #5d4b38;">Quantité</th>
                                <th style="color: #5d4b38;">Livré / En attente</th>
                                <th style="color: #5d4b38;">Prix unitaire</th>
                                <th style="color: #5d4b38;">Total</th>
                                <th style="color: #5d4b38;">Statut</th>
                                <th style="color: #5d4b38;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commandes as $commande)
                                @foreach($commande->lignes as $ligne)
                                @php
                                    $quantiteCommandee = $ligne->quantite;
                                    $quantiteDemandee = $ligne->quantite_demandee ?? $quantiteCommandee;
                                    $stockAvant = $ligne->stock_avant ?? 0;
                                    $manquant = max(0, $quantiteDemandee - $stockAvant);
                                    $estPartiel = ($manquant > 0 && $quantiteCommandee > 0 && $quantiteCommandee < $quantiteDemandee);
                                @endphp
                                <tr>
                                    @if($loop->first)
                                        <td rowspan="{{ $commande->lignes->count() }}" style="color: #d4af37; font-weight: bold; vertical-align: middle;">
                                            #{{ $commande->numero_commande }}
                                            <small class="d-block text-muted">{{ $commande->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td rowspan="{{ $commande->lignes->count() }}" style="vertical-align: middle;">
                                            {{ $commande->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    @endif
                                    <td><strong>{{ $ligne->medicament->nom_commercial_fr ?? 'N/A' }}</strong></td>
                                    <td>
                                        <span class="badge" style="background: #d4af37; color: white;">
                                            {{ $quantiteDemandee }} unités
                                        </span>
                                    </td>
                                    <td>
                                        @if($estPartiel)
                                            <div>
                                                <span class="badge" style="background: #d4af37; color: white;">
                                                    📦 {{ $quantiteCommandee }} / {{ $quantiteDemandee }}
                                                </span>
                                                <div class="mt-1">
                                                    <span class="badge bg-warning">
                                                        ⏳ {{ $manquant }} unités en attente
                                                    </span>
                                                </div>
                                                <small class="d-block text-muted mt-1">
                                                    Stock dispo: {{ $stockAvant }}
                                                </small>
                                            </div>
                                        @elseif($quantiteDemandee > $quantiteCommandee)
                                            <div>
                                                <span class="badge" style="background: #e6a57e; color: white;">
                                                    ⚠️ {{ $quantiteCommandee }} / {{ $quantiteDemandee }}
                                                </span>
                                                <div class="mt-1">
                                                    <span class="badge bg-danger">
                                                        🔴 Manque {{ $manquant }} unités
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <span class="badge" style="background: #9caf88; color: white;">
                                                    ✅ Commande complète
                                                </span>
                                                <small class="d-block text-muted mt-1">
                                                    Stock: {{ $stockAvant }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ number_format($ligne->prix_unitaire, 3) }} TND</strong></td>
                                    @if($loop->first)
                                        <td rowspan="{{ $commande->lignes->count() }}" style="vertical-align: middle; font-weight: bold; color: #d4af37;">
                                            {{ number_format($commande->total_ttc, 3) }} TND
                                        </td>
                                        <td rowspan="{{ $commande->lignes->count() }}" style="vertical-align: middle;">
                                            @php
                                                $statutColors = [
                                                    'en_attente' => ['bg' => '#e6a57e', 'text' => 'En attente'],
                                                    'confirmee' => ['bg' => '#d4af37', 'text' => 'Confirmée'],
                                                    'preparation' => ['bg' => '#9caf88', 'text' => 'Préparation'],
                                                    'partiel' => ['bg' => '#e6a57e', 'text' => 'Partielle'],
                                                    'expediee' => ['bg' => '#8b7355', 'text' => 'Expédiée'],
                                                    'livree' => ['bg' => '#5d4b38', 'text' => 'Livrée'],
                                                    'annulee' => ['bg' => '#c4b5a0', 'text' => 'Annulée']
                                                ];
                                                $statut = $statutColors[$commande->statut] ?? ['bg' => '#9c8a78', 'text' => ucfirst($commande->statut)];
                                            @endphp
                                            <span class="badge px-3 py-2" style="background: {{ $statut['bg'] }}; color: white;">
                                                {{ $statut['text'] }}
                                            </span>
                                        </td>
                                        <td rowspan="{{ $commande->lignes->count() }}" style="vertical-align: middle;">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm rounded-circle" 
                                                        style="width: 32px; height: 32px; background: #f5efe8; color: #8b7355;"
                                                        data-bs-toggle="modal" data-bs-target="#detailsModal{{ $commande->id }}"
                                                        title="Voir détails">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                
                                                @if(in_array($commande->statut, ['en_attente', 'confirmee', 'preparation', 'partiel']))
                                                    @if($manquant > 0 && !$estPartiel)
                                                        <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">
                                                            Stock insuffisant
                                                        </span>
                                                    @else
                                                        <form action="{{ route('fournisseur.commandes.expedier', $commande->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm rounded-circle" 
                                                                    style="width: 32px; height: 32px; background: #d4af37; color: white;"
                                                                    title="Expédier"
                                                                    onclick="return confirm('Confirmer l\'expédition de cette commande ?')">
                                                                <i class="bi bi-truck"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $commandes->links() }}
                </div>

                @else
                <div class="text-center py-5" style="color: #9c8a78;">
                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                    <p>Aucune commande reçue pour le moment.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals pour les détails -->
@foreach($commandes as $commande)
<div class="modal fade" id="detailsModal{{ $commande->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #d4af37; color: white;">
                <h5 class="modal-title">Détails commande #{{ $commande->numero_commande }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">Date commande</small>
                        <p class="fw-bold">{{ $commande->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Statut</small>
                        <p>
                            @php
                                $statutColors = [
                                    'en_attente' => '#e6a57e',
                                    'confirmee' => '#d4af37',
                                    'preparation' => '#9caf88',
                                    'partiel' => '#e6a57e',
                                    'expediee' => '#8b7355',
                                    'livree' => '#5d4b38'
                                ];
                                $color = $statutColors[$commande->statut] ?? '#9c8a78';
                            @endphp
                            <span class="badge px-3 py-2" style="background: {{ $color }};">{{ ucfirst($commande->statut) }}</span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Total</small>
                        <p class="fw-bold text-success">{{ number_format($commande->total_ttc, 3) }} TND</p>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-3">📦 Produits commandés</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead style="background: #f5efe8;">
                            <tr>
                                <th>Médicament</th>
                                <th>Quantité demandée</th>
                                <th>Quantité livrée</th>
                                <th>En attente</th>
                                <th>Stock avant</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commande->lignes as $ligne)
                            @php
                                $quantiteLivree = $ligne->quantite;
                                $quantiteDemandee = $ligne->quantite_demandee ?? $quantiteLivree;
                                $stockAvant = $ligne->stock_avant ?? 0;
                                $enAttente = max(0, $quantiteDemandee - $quantiteLivree);
                            @endphp
                            <tr>
                                <td><strong>{{ $ligne->medicament->nom_commercial_fr ?? 'N/A' }}</strong></td>
                                <td>{{ $quantiteDemandee }} unités</strong></td>
                                <td>{{ $quantiteLivree }} unités</strong></td>
                                <td>
                                    @if($enAttente > 0)
                                        <span class="badge bg-warning">⏳ {{ $enAttente }} unités</span>
                                    @else
                                        <span class="badge bg-success">✅ Aucune</span>
                                    @endif
                                 </strong></td>
                                <td>{{ $stockAvant }} unités</strong></td>
                                <td>{{ number_format($ligne->prix_unitaire, 3) }} TND</strong></td>
                                <td>{{ number_format($ligne->total_ligne, 3) }} TND</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total TTC :</strong></td>
                                <td class="fw-bold" style="color: #d4af37;">{{ number_format($commande->total_ttc, 3) }} TND</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                @if(in_array($commande->statut, ['en_attente', 'confirmee', 'preparation', 'partiel']))
                    <form action="{{ route('fournisseur.commandes.expedier', $commande->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn" style="background: #d4af37; color: white;">Expédier</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@push('styles')
<style>
    .card-light, .welcome-card {
        transition: all 0.3s ease;
    }
    .card-light:hover, .welcome-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .btn-group .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(212, 175, 55, 0.05);
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
    .modal-content {
        border-radius: 15px;
        border: none;
    }
</style>
@endpush