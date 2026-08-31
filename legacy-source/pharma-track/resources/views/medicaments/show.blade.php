@extends('layouts.app')

@section('title', $medicament->nom_commercial_fr . ' - Pharma Track')
@section('page-title', $medicament->nom_commercial_fr)
@section('page-icon', 'bi-capsule')

@section('page-actions')
<div class="btn-group">
    <a href="{{ route('medicaments.edit', $medicament) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <form action="{{ route('medicaments.destroy', $medicament) }}" 
          method="POST" 
          style="display: inline;"
          onsubmit="return confirm('Supprimer ce médicament?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash"></i> Supprimer
        </button>
    </form>
</div>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('medicaments.index') }}">Médicaments</a></li>
<li class="breadcrumb-item active">{{ $medicament->nom_commercial_fr }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row g-4">
        <!-- Colonne gauche -->
        <div class="col-md-8">
            <!-- Informations du médicament -->
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-info-circle me-2" style="color: #8b7355;"></i> Informations du médicament
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                             <tr>
                                <th width="40%">Code CIP :</th>
                                <td><code>{{ $medicament->code_cip ?? '—' }}</code></td>
                             </tr>
                             <tr>
                                <th>DCI :</th>
                                <td>{{ $medicament->dci ?? '—' }}</td>
                             </tr>
                             <tr>
                                <th>Nom commercial :</th>
                                <td>{{ $medicament->nom_commercial_fr ?? $medicament->nom ?? '—' }}</td>
                             </tr>
                            @if($medicament->nom_commercial_ar)
                             <tr>
                                <th>Nom commercial (AR) :</th>
                                <td>{{ $medicament->nom_commercial_ar }}</td>
                             </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                             <tr>
                                <th width="40%">Forme :</th>
                                <td>{{ $medicament->forme ?? $medicament->forme_pharmaceutique ?? '—' }}</td>
                             </tr>
                             <tr>
                                <th>Dosage :</th>
                                <td>{{ $medicament->dosage ?? '—' }} {{ $medicament->unite ?? '' }}</td>
                             </tr>
                             <tr>
                                <th>Catégorie :</th>
                                <td>{{ $medicament->categorie ?? $medicament->classe_therapeutique ?? '—' }}</td>
                             </tr>
                             <tr>
                                <th>Statut :</th>
                                <td>
                                    @if($medicament->est_essentiel ?? false)
                                    <span class="badge bg-success me-1">Essentiel</span>
                                    @endif
                                    @if($medicament->est_controle ?? false)
                                    <span class="badge bg-danger">Contrôlé</span>
                                    @endif
                                </td>
                             </tr>
                        </table>
                    </div>
                </div>

                <!-- Informations financières -->
                <h6 class="mt-4 border-bottom pb-2" style="color: #5d4b38; border-color: #e8e4da !important;">
                    <i class="bi bi-cash-stack me-2" style="color: #9caf88;"></i> Informations financières
                </h6>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <table class="table table-sm">
                             <tr>
                                <th width="40%">Prix d'achat :</th>
                                <td>{{ $medicament->prix_achat ? number_format($medicament->prix_achat, 3) : '—' }} TND</td>
                             </tr>
                             <tr>
                                <th>Prix de vente :</th>
                                <td><strong>{{ $medicament->prix_vente ? number_format($medicament->prix_vente, 3) : '—' }} TND</strong></td>
                             </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                             <tr>
                                <th width="40%">Délai approvisionnement :</th>
                                <td>{{ $medicament->delai_appro ?? 7 }} jours</td>
                             </tr>
                             <tr>
                                <th>Créé le :</th>
                                <td>
                                    @if($medicament->created_at)
                                        {{ $medicament->created_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                             </tr>
                             <tr>
                                <th>Modifié le :</th>
                                <td>
                                    @if($medicament->updated_at)
                                        {{ $medicament->updated_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                             </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Code-barres (CIP) -->
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-upc-scan me-2" style="color: #9caf88;"></i> Code-barres
                </h5>
                <div class="text-center">
                    @if($medicament->code_cip)
                        <img src="{{ route('scan.code', $medicament->id) }}" 
                             alt="Code-barres du médicament" 
                             class="img-fluid mb-3"
                             style="max-width: 300px; height: auto;">
                        <p class="mb-2">
                            <strong>Code CIP :</strong> {{ $medicament->code_cip }}
                        </p>
                        <div class="btn-group">
                            <a href="{{ route('scan.code', $medicament->id) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> Télécharger
                            </a>
                            <button class="btn btn-sm btn-outline-secondary" 
                                    onclick="copyToClipboard('{{ $medicament->code_cip }}')">
                                <i class="bi bi-clipboard"></i> Copier
                            </button>
                            <a href="{{ route('scan.index') }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-upc-scan"></i> Scanner
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle"></i>
                            Aucun code CIP défini pour ce médicament.
                        </div>
                        <a href="{{ route('medicaments.edit', $medicament) }}" 
                           class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Ajouter un code CIP
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="col-md-4">
            <!-- Statut du stock -->
            @php
                $stockTotal = $medicament->lots && $medicament->lots->count() > 0 
                    ? $medicament->lots->where('statut', 'actif')->sum('quantite_actuelle') 
                    : ($medicament->quantite ?? 0);
                $stockMin = $medicament->stock_min ?? 10;
                $stockMax = $medicament->stock_max ?? 100;
                $isRupture = $stockTotal < $stockMin;
            @endphp
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-bar-chart me-2" style="color: #e6a57e;"></i> Statut du stock
                </h5>
                <div class="text-center mb-3">
                    <div class="display-4 {{ $isRupture ? 'text-danger' : 'text-success' }}">
                        {{ $stockTotal }}
                    </div>
                    <div class="text-muted">Unités en stock</div>
                </div>
                <div class="progress mb-3" style="height: 25px; background: #e8e4da;">
                    @php
                        $percentage = $stockMax > 0 ? min(100, ($stockTotal / $stockMax) * 100) : 0;
                        $color = $isRupture ? 'danger' : ($percentage < 30 ? 'warning' : 'success');
                    @endphp
                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%">
                        {{ number_format($percentage, 1) }}%
                    </div>
                </div>
                <table class="table table-sm">
                     <tr>
                        <th>Stock minimum :</th>
                        <td class="text-end">{{ $stockMin }}</td>
                     </tr>
                     <tr>
                        <th>Stock maximum :</th>
                        <td class="text-end">{{ $stockMax }}</td>
                     </tr>
                     <tr>
                        <th>Statut :</th>
                        <td class="text-end">
                            @if($isRupture)
                            <span class="badge bg-danger">RUPTURE</span>
                            @elseif($stockTotal <= $stockMin * 1.5)
                            <span class="badge bg-warning">FAIBLE</span>
                            @else
                            <span class="badge bg-success">NORMAL</span>
                            @endif
                        </td>
                     </tr>
                </table>
            </div>

            <!-- QR Code -->
            <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-qr-code me-2" style="color: #d4af37;"></i> QR Code
                </h5>
                <div class="text-center">
                    @if($medicament->qr_code)
                        {!! $medicament->qr_code !!}
                    @else
                        <img src="{{ route('medicaments.qr', $medicament->id) }}" alt="QR Code" style="max-width: 200px;">
                    @endif
                    <p class="mt-2"><small>Scannez pour accéder à cette fiche</small></p>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color: #d4af37;"></i> Actions rapides
                </h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('medicaments.index') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #d4af37; color: white; border: none;">
                        <i class="bi bi-list me-2"></i>Retour à la liste
                    </a>
                    <a href="{{ route('medicaments.edit', $medicament) }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #e6a57e; color: white; border: none;">
                        <i class="bi bi-pencil me-2"></i>Modifier
                    </a>
                    <a href="{{ route('scan.index') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #9caf88; color: white; border: none;">
                        <i class="bi bi-upc-scan me-2"></i>Scanner un code
                    </a>
                    <a href="{{ route('home') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #8b7355; color: white; border: none;">
                        <i class="bi bi-house me-2"></i>Accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Créer une notification temporaire
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            <i class="bi bi-check-circle"></i> 
            Code CIP copié !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }, function(err) {
        console.error('Erreur de copie: ', err);
        alert('Erreur lors de la copie');
    });
}
</script>
@endpush