@extends('layouts.app')

@section('title', 'Commande #' . $commande->numero_commande)
@section('page-title', 'Commande #' . $commande->numero_commande)
@section('page-icon', 'bi-receipt')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-light p-4 rounded-4 mb-3" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #5d4b38;">Commande #{{ $commande->numero_commande }}</h5>
                        <small class="text-muted" style="color: #9c8a78;">Date: {{ $commande->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <a href="{{ route('fournisseur.commandes') }}" class="btn btn-sm rounded-pill" style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="card-light p-4 rounded-4 mb-3" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h6 class="fw-bold mb-3" style="color: #5d4b38;">Informations générales</h6>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted" style="color: #9c8a78;">Fournisseur</small>
                        <p class="fw-bold" style="color: #5d4b38;">{{ $commande->fournisseur->raison_sociale ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted" style="color: #9c8a78;">Statut</small>
                        <p>
                            @php
                                $statutColors = [
                                    'en_attente' => '#e6a57e',
                                    'confirmee' => '#d4af37',
                                    'preparation' => '#9caf88',
                                    'expediee' => '#8b7355',
                                    'livree' => '#5d4b38',
                                    'annulee' => '#c4b5a0'
                                ];
                                $color = $statutColors[$commande->statut] ?? '#9c8a78';
                            @endphp
                            <span class="badge px-3 py-2" style="background: {{ $color }}; color: white;">
                                {{ ucfirst($commande->statut) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Détail des produits commandés -->
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h6 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-box-seam me-2" style="color: #d4af37;"></i> Produits commandés
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead style="background: #f5efe8;">
                            <tr>
                                <th style="color: #5d4b38;">Médicament</th>
                                <th style="color: #5d4b38;">Quantité</th>
                                <th style="color: #5d4b38;">Prix unitaire</th>
                                <th style="color: #5d4b38;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commande->lignes as $ligne)
                            <tr>
                                <td><strong style="color: #5d4b38;">{{ $ligne->medicament->nom_commercial_fr ?? 'N/A' }}</strong></td>
                                <td>
                                    <span class="badge" style="background: #d4af37; color: white; font-size: 0.9rem;">
                                        {{ $ligne->quantite }} unités
                                    </span>
                                </td>
                                <td>{{ number_format($ligne->prix_unitaire, 3) }} TND</td>
                                <td>{{ number_format($ligne->total_ligne, 3) }} TND</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold" style="color: #5d4b38;">Total TTC :</td>
                                <td class="fw-bold" style="color: #d4af37; font-size: 1.1rem;">{{ number_format($commande->total_ttc, 3) }} TND</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="{{ route('fournisseur.commandes') }}" class="btn rounded-pill px-4 py-2" style="background: #d4af37; color: white; border: none;">
                    <i class="bi bi-list me-2"></i> Voir toutes les commandes
                </a>
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
    .btn {
        transition: all 0.2s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
</style>
@endpush