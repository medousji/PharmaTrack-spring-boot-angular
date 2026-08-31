@extends('layouts.app')

@section('title', 'Rapport de conformité ONP - Pharma Track')
@section('page-title', '') {{-- vide pour éviter le doublon --}}
@section('page-icon', 'bi-file-text')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('conformite.index') }}" style="color: #9c8a78;">Conformité ONP</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Rapport</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec logo -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-file-text fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Rapport de conformité ONP</h1>
            <p class="text-muted mb-0" style="color: #9c8a78;">Généré le {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Statistiques générales -->
        <div class="col-md-6">
            <div class="card p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-bar-chart-steps me-2" style="color: #d4af37;"></i>Statistiques générales
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <td style="color: #5d4b38;">Total médicaments</td>
                        <td class="fw-bold text-end" style="color: #5d4b38;">{{ $stats['total_medicaments'] ?? 3 }}</td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Lots périmés</td>
                        <td class="fw-bold text-end" style="color: #e6a57e;">{{ $stats['lots_perimes'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Lots proches expiration</td>
                        <td class="fw-bold text-end" style="color: #e6a57e;">{{ $stats['lots_proches'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Alertes non lues</td>
                        <td class="fw-bold text-end" style="color: #e6a57e;">{{ $stats['alertes_non_lues'] ?? 0 }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Conformité réglementaire -->
        <div class="col-md-6">
            <div class="card p-4 rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-check-circle me-2" style="color: #d4af37;"></i>Conformité ONP
                </h5>
                <ul class="list-group list-group-flush">
                    @foreach($conformite['reglementations'] ?? [] as $reg => $conforme)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">{{ $reg }}</span>
                        @if($conforme)
                            <span class="badge px-3 py-2" style="background: #9caf88; color: white;">Conforme</span>
                        @else
                            <span class="badge px-3 py-2" style="background: #e6a57e; color: white;">Non conforme</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Liste des médicaments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-capsule me-2" style="color: #d4af37;"></i>Liste des médicaments
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Code CIP</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Nom</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">DCI</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Stock</th>
                                <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicaments ?? [] as $med)
                            <tr>
                                <td><code style="color: #5d4b38;">{{ $med->code_cip ?? '—' }}</code></td>
                                <td style="color: #5d4b38;">{{ $med->nom_commercial_fr ?? $med->nom }}</td>
                                <td style="color: #8b7355;">{{ $med->dci ?? '—' }}</td>
                                <td style="color: #5d4b38;">{{ $med->stock_actuel ?? $med->quantite ?? 0 }}</td>
                                <td>
                                    @if(($med->stock_actuel ?? $med->quantite ?? 0) > 0)
                                        <span class="badge px-3 py-2" style="background: #9caf88; color: white;">En stock</span>
                                    @else
                                        <span class="badge px-3 py-2" style="background: #e6a57e; color: white;">Rupture</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color: #9c8a78;">Aucun médicament</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pied de page du rapport -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="p-4 rounded-4 text-center" style="background: #ffffff; border: 1px solid #e8e4da;">
                <small style="color: #9c8a78;">
                    <i class="bi bi-printer me-1"></i> Document généré automatiquement par Pharma Track - Système de gestion des stocks médicaux<br>
                    Conforme aux réglementations de l'Office National de Pharmacie (ONP) - Tunisie
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
</style>
@endpush