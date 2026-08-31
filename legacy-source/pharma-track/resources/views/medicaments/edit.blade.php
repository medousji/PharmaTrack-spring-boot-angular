@extends('layouts.app')

@section('title', 'Modifier médicament - Pharma Track')

@section('page-title', 'Modifier le médicament')
@section('page-icon', 'bi-pencil')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('medicaments.index') }}">Médicaments</a></li>
<li class="breadcrumb-item"><a href="{{ route('medicaments.show', $medicament) }}">{{ $medicament->nom_commercial_fr }}</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="bi bi-pencil"></i> Modifier : {{ $medicament->nom_commercial_fr }}
        </h5>
    </div>
    
    <div class="card-body">
        <form action="{{ route('medicaments.update', $medicament) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Informations de base -->
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-info-circle"></i> Informations de base
                </h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Code CIP</label>
                        <input type="text" name="code_cip" 
                               class="form-control @error('code_cip') is-invalid @enderror"
                               value="{{ old('code_cip', $medicament->code_cip) }}" 
                               required>
                        @error('code_cip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">DCI</label>
                        <input type="text" name="dci" 
                               class="form-control @error('dci') is-invalid @enderror"
                               value="{{ old('dci', $medicament->dci) }}" 
                               required>
                        @error('dci')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Nom commercial (FR)</label>
                        <input type="text" name="nom_commercial_fr" 
                               class="form-control @error('nom_commercial_fr') is-invalid @enderror"
                               value="{{ old('nom_commercial_fr', $medicament->nom_commercial_fr) }}" 
                               required>
                        @error('nom_commercial_fr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom commercial (AR)</label>
                        <input type="text" name="nom_commercial_ar" 
                               class="form-control @error('nom_commercial_ar') is-invalid @enderror"
                               value="{{ old('nom_commercial_ar', $medicament->nom_commercial_ar) }}">
                        @error('nom_commercial_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Caractéristiques -->
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-capsule"></i> Caractéristiques
                </h6>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Forme</label>
                        <input type="text" name="forme" 
                               class="form-control @error('forme') is-invalid @enderror"
                               value="{{ old('forme', $medicament->forme) }}" 
                               required>
                        @error('forme')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Dosage</label>
                        <input type="number" step="0.01" name="dosage" 
                               class="form-control @error('dosage') is-invalid @enderror"
                               value="{{ old('dosage', $medicament->dosage) }}" 
                               required>
                        @error('dosage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Unité</label>
                        <input type="text" name="unite" 
                               class="form-control @error('unite') is-invalid @enderror"
                               value="{{ old('unite', $medicament->unite) }}" 
                               required>
                        @error('unite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Catégorie</label>
                        <input type="text" name="categorie" 
                               class="form-control @error('categorie') is-invalid @enderror"
                               value="{{ old('categorie', $medicament->categorie) }}" 
                               required>
                        @error('categorie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Prix d'achat (TND)</label>
                        <input type="number" step="0.001" name="prix_achat" 
                               class="form-control @error('prix_achat') is-invalid @enderror"
                               value="{{ old('prix_achat', $medicament->prix_achat) }}" 
                               required>
                        @error('prix_achat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Prix de vente (TND)</label>
                        <input type="number" step="0.001" name="prix_vente" 
                               class="form-control @error('prix_vente') is-invalid @enderror"
                               value="{{ old('prix_vente', $medicament->prix_vente) }}" 
                               required>
                        @error('prix_vente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Délai approvisionnement (jours)</label>
                        <input type="number" name="delai_appro" 
                               class="form-control @error('delai_appro') is-invalid @enderror"
                               value="{{ old('delai_appro', $medicament->delai_appro) }}" 
                               required>
                        @error('delai_appro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Gestion des stocks -->
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-box-seam"></i> Gestion des stocks
                </h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Stock minimum</label>
                        <input type="number" name="stock_min" 
                               class="form-control @error('stock_min') is-invalid @enderror"
                               value="{{ old('stock_min', $medicament->stock_min) }}" 
                               required>
                        @error('stock_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Stock maximum</label>
                        <input type="number" name="stock_max" 
                               class="form-control @error('stock_max') is-invalid @enderror"
                               value="{{ old('stock_max', $medicament->stock_max) }}" 
                               required>
                        @error('stock_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Options -->
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="bi bi-gear"></i> Options
                </h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="est_essentiel" 
                                   class="form-check-input" id="est_essentiel"
                                   {{ old('est_essentiel', $medicament->est_essentiel) ? 'checked' : '' }}>
                            <label class="form-check-label" for="est_essentiel">
                                Médicament essentiel
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="est_controle" 
                                   class="form-check-input" id="est_controle"
                                   {{ old('est_controle', $medicament->est_controle) ? 'checked' : '' }}>
                            <label class="form-check-label" for="est_controle">
                                Substance contrôlée
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Boutons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('medicaments.show', $medicament) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Annuler
                </a>
                
                <div>
                    <button type="reset" class="btn btn-outline-danger me-2">
                        <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validation stock max > stock min
        const stockMin = document.querySelector('input[name="stock_min"]');
        const stockMax = document.querySelector('input[name="stock_max"]');
        
        function validateStock() {
            const min = parseInt(stockMin.value) || 0;
            const max = parseInt(stockMax.value) || 0;
            
            if (max > 0 && min > 0 && max < min) {
                stockMax.setCustomValidity('Le stock maximum doit être supérieur au stock minimum');
            } else {
                stockMax.setCustomValidity('');
            }
        }
        
        stockMin.addEventListener('input', validateStock);
        stockMax.addEventListener('input', validateStock);
    });
</script>
@endpush