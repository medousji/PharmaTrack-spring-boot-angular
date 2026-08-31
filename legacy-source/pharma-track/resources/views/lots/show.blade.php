@extends('layouts.app')

@section('title', 'Détails du lot - Pharma Track')
@section('page-title', 'Détails du lot')
@section('page-icon', 'bi-box-seam')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('lots.index') }}" style="color: #9c8a78;">Lots</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">{{ is_object($lot) ? $lot->numero_lot : ($lot['numero_lot'] ?? 'Détails') }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    @php
        // Convertir en tableau si c'est un objet, pour uniformiser
        $lotData = is_object($lot) ? $lot->toArray() : (is_array($lot) ? $lot : []);
        $lotId = $lotData['id'] ?? $lotData['lot'] ?? 0;
    @endphp

    <div class="row g-4">
        <!-- Colonne principale -->
        <div class="col-md-7">
            <!-- Informations du lot -->
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-info-circle me-2" style="color: #8b7355;"></i> Informations du lot
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th style="color: #5d4b38;">Numéro de lot :</th><td style="color: #8b7355;"><strong>{{ $lotData['numero_lot'] ?? 'N/A' }}</strong></td></tr>
                            <tr><th style="color: #5d4b38;">Médicament :</th>
                                <td>
                                    @php
                                        $medicamentNom = $lotData['medicament']['nom_commercial_fr'] ?? $lotData['medicament_nom'] ?? 'N/A';
                                        $medicamentId = $lotData['medicament']['id'] ?? $lotData['medicament_id'] ?? null;
                                    @endphp
                                    @if($medicamentId)
                                        <a href="{{ route('medicaments.show', $medicamentId) }}" style="color: #d4af37;">{{ $medicamentNom }}</a>
                                    @else
                                        <span style="color: #8b7355;">{{ $medicamentNom }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr><th style="color: #5d4b38;">Quantité initiale :</th><td style="color: #8b7355;">{{ $lotData['quantite_initial'] ?? 0 }} unités</td></tr>
                            <tr><th style="color: #5d4b38;">Quantité actuelle :</th><td style="color: #8b7355;"><strong class="{{ ($lotData['quantite_actuelle'] ?? 0) <= 50 ? 'text-danger' : 'text-success' }}">{{ $lotData['quantite_actuelle'] ?? 0 }} unités</strong></td></tr>
                            <tr><th style="color: #5d4b38;">Statut :</th>
                                <td>
                                    @php
                                        $statut = $lotData['statut'] ?? 'inconnu';
                                        $badgeColor = match($statut) {
                                            'actif' => '#9caf88',
                                            'epuise' => '#e6a57e',
                                            'perime' => '#8b7355',
                                            default => '#c4b5a0'
                                        };
                                    @endphp
                                    <span class="badge px-3 py-2" style="background: {{ $badgeColor }}; color: white;">{{ ucfirst($statut) }}</span>
                                  </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th style="color: #5d4b38;">Date fabrication :</th><td style="color: #8b7355;">{{ isset($lotData['date_fabrication']) && $lotData['date_fabrication'] ? \Carbon\Carbon::parse($lotData['date_fabrication'])->format('d/m/Y') : 'N/A' }}</td></tr>
                            <tr><th style="color: #5d4b38;">Date péremption :</th>
                                <td style="color: #8b7355;">
                                    @php
                                        $datePeremption = $lotData['date_peremption'] ?? null;
                                        $isProche = $datePeremption && \Carbon\Carbon::parse($datePeremption)->diffInDays(now()) <= 30;
                                    @endphp
                                    <span class="{{ $isProche ? 'text-danger fw-bold' : '' }}">
                                        {{ $datePeremption ? \Carbon\Carbon::parse($datePeremption)->format('d/m/Y') : 'N/A' }}
                                        @if($isProche) <i class="bi bi-exclamation-triangle ms-1"></i> @endif
                                    </span>
                                </td>
                            </tr>
                            <tr><th style="color: #5d4b38;">Fournisseur :</th><td style="color: #8b7355;">{{ $lotData['fournisseur'] ?? 'N/A' }}</td></tr>
                            <tr><th style="color: #5d4b38;">Prix d'achat :</th><td style="color: #8b7355;">{{ isset($lotData['prix_achat']) && $lotData['prix_achat'] ? number_format($lotData['prix_achat'], 3) : '—' }} TND</td></tr>
                            <tr><th style="color: #5d4b38;">Prix de vente :</th><td style="color: #8b7355;">{{ isset($lotData['prix_vente']) && $lotData['prix_vente'] ? number_format($lotData['prix_vente'], 3) : '—' }} TND</td></tr>
                            <tr><th style="color: #5d4b38;">Valeur totale :</th><td style="color: #8b7355;"><strong>{{ number_format(($lotData['quantite_actuelle'] ?? 0) * ($lotData['prix_achat'] ?? 0), 3) }} TND</strong></td></tr>
                        </table>
                    </div>
                </div>
                @if(!empty($lotData['emplacement']))
                <div class="mt-3 pt-2 border-top" style="border-color: #e8e4da !important;">
                    <span style="color: #5d4b38;"><i class="bi bi-pin-map-fill me-2"></i>Emplacement :</span>
                    <span style="color: #8b7355;">{{ $lotData['emplacement'] }}</span>
                </div>
                @endif
            </div>

            <!-- Historique des mouvements -->
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-clock-history me-2" style="color: #9caf88;"></i> Historique des mouvements
                </h5>
                @if(!empty($lotData['mouvements']) && count($lotData['mouvements']) > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead style="background: #f5efe8;">
                            <tr>
                                <th style="color: #5d4b38;">Date</th>
                                <th style="color: #5d4b38;">Type</th>
                                <th style="color: #5d4b38;">Quantité</th>
                                <th style="color: #5d4b38;">Motif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lotData['mouvements'] as $mouvement)
                            <tr>
                                <td style="color: #8b7355;">{{ $mouvement['created_at'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge px-2 py-1" style="background: {{ ($mouvement['type'] ?? '') === 'entree' ? '#9caf88' : '#e6a57e' }}; color: white;">
                                        {{ ucfirst($mouvement['type'] ?? '?') }}
                                    </span>
                                </td>
                                <td style="color: #8b7355;">{{ $mouvement['quantite'] ?? 0 }}</td>
                                <td style="color: #8b7355;">{{ $mouvement['motif'] ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4" style="color: #9c8a78;">
                    <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                    <span>Aucun mouvement enregistré pour ce lot.</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="col-md-5">
            <!-- QR Code du lot -->
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-qr-code me-2" style="color: #d4af37;"></i> QR Code du lot
                </h5>
                <div class="text-center">
                    @if($lotId)
                        <img src="{{ route('scan.qr', $lotId) }}" 
                             alt="QR Code du lot" 
                             style="max-width: 200px;"
                             onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display:none; color:#e6a57e;" class="mb-2">⚠️ QR Code non disponible</div>
                        <p class="mt-2 mb-0"><small style="color: #9c8a78;">N° Lot : <strong>{{ $lotData['numero_lot'] ?? 'N/A' }}</strong></small></p>
                        <div class="btn-group mt-3">
                            <a href="{{ route('scan.qr', $lotId) }}" target="_blank" class="btn btn-sm rounded-pill" style="background: #d4af37; color: white;">
                                <i class="bi bi-download"></i> Télécharger
                            </a>
                            <a href="{{ route('scan.index') }}" class="btn btn-sm rounded-pill" style="background: #9caf88; color: white;">
                                <i class="bi bi-upc-scan"></i> Scanner
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">QR Code non disponible (ID manquant)</div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color: #e6a57e;"></i> Actions
                </h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('lots.edit', $lotId) }}" class="btn rounded-pill py-2" style="background: #d4af37; color: white;">
                        <i class="bi bi-pencil me-2"></i> Modifier le lot
                    </a>
                    <button type="button" class="btn rounded-pill py-2" style="background: #9caf88; color: white;" data-bs-toggle="modal" data-bs-target="#ajusterStockModal">
                        <i class="bi bi-arrow-left-right me-2"></i> Ajuster le stock
                    </button>
                    <button type="button" class="btn rounded-pill py-2" style="background: #e6a57e; color: white;" data-bs-toggle="modal" data-bs-target="#retirerLotModal">
                        <i class="bi bi-trash me-2"></i> Retirer le lot
                    </button>
                    <a href="{{ route('lots.index') }}" class="btn rounded-pill py-2" style="background: #8b7355; color: white;">
                        <i class="bi bi-arrow-left me-2"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Information système -->
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-server me-2" style="color: #8b7355;"></i> Information système
                </h5>
                <table class="table table-sm table-borderless">
                    <tr><th style="color: #5d4b38;">Créé le :</th><td style="color: #8b7355;">{{ isset($lotData['created_at']) ? \Carbon\Carbon::parse($lotData['created_at'])->format('d/m/Y H:i') : 'N/A' }}</td></tr>
                    <tr><th style="color: #5d4b38;">Modifié le :</th><td style="color: #8b7355;">{{ isset($lotData['updated_at']) ? \Carbon\Carbon::parse($lotData['updated_at'])->format('d/m/Y H:i') : 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajuster Stock -->
<div class="modal fade" id="ajusterStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #5d4b38;">
                    <i class="bi bi-arrow-left-right me-2" style="color: #d4af37;"></i>Ajuster le stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lots.ajuster-stock', $lotId) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Type d'ajustement</label>
                        <select name="type" class="form-select" style="border-color: #e8e4da;" required>
                            <option value="entree">Entrée de stock (+)</option>
                            <option value="sortie">Sortie de stock (-)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Quantité</label>
                        <input type="number" name="quantite" class="form-control" style="border-color: #e8e4da;" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Motif / Raison</label>
                        <textarea name="motif" class="form-control" style="border-color: #e8e4da;" rows="2" placeholder="Ex: Réception commande, Vente, Perte..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn" style="background: #d4af37; color: white;">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Retirer Lot (version corrigée) -->
<div class="modal fade" id="retirerLotModal" tabindex="-1" aria-labelledby="retirerLotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #e6a57e; color: white;">
                <h5 class="modal-title" id="retirerLotModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Retirer le lot
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lots.destroy', $lotId) }}" method="POST" id="retirerLotForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Attention :</strong> Cette action est irréversible.
                    </div>
                    <p>Êtes-vous sûr de vouloir retirer le lot <strong>{{ $lotData['numero_lot'] ?? 'LOT-001' }}</strong> ?</p>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="confirmRetrait" style="cursor: pointer;">
                        <label class="form-check-label" for="confirmRetrait" style="cursor: pointer;">
                            Je confirme le retrait de ce lot
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger" id="btnConfirmerRetrait" disabled>
                        <i class="bi bi-trash me-1"></i>Confirmer le retrait
                    </button>
                </div>
            </form>
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
        transition: all 0.2s;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    .form-control:focus, .form-select:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de la case à cocher pour le retrait
        const confirmCheckbox = document.getElementById('confirmRetrait');
        const confirmButton = document.getElementById('btnConfirmerRetrait');
        
        if (confirmCheckbox && confirmButton) {
            confirmCheckbox.addEventListener('change', function() {
                confirmButton.disabled = !this.checked;
            });
        }
        
        // Gestion du modal pour réinitialiser la checkbox quand on ferme
        const modal = document.getElementById('retirerLotModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                if (confirmCheckbox) {
                    confirmCheckbox.checked = false;
                    confirmButton.disabled = true;
                }
            });
        }
    });
</script>
@endpush