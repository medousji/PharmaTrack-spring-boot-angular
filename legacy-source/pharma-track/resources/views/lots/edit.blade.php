@extends('layouts.app')

@section('title', 'Modifier le lot - Pharma Track')
@section('page-title', 'Modifier le lot')
@section('page-icon', 'bi-pencil-square')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('lots.index') }}" style="color: #9c8a78;">Lots</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Modifier lot #{{ is_object($lot) ? $lot->numero_lot : ($lot['numero_lot'] ?? 'N/A') }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @php
                // Récupérer l'ID du lot (objet ou tableau)
                $lotId = is_object($lot) ? $lot->id : ($lot['id'] ?? $lot['lot'] ?? 0);
            @endphp
            <form action="{{ route('lots.update', $lotId) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-light p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                    <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                        <i class="bi bi-pencil-square me-2" style="color: #d4af37;"></i> Informations du lot
                    </h5>

                    <div class="row g-3">
                        <!-- Numéro de lot -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Numéro de lot <span class="text-danger">*</span></label>
                            <input type="text" name="numero_lot" class="form-control @error('numero_lot') is-invalid @enderror" 
                                   value="{{ old('numero_lot', is_object($lot) ? $lot->numero_lot : ($lot['numero_lot'] ?? '')) }}" 
                                   style="border-color: #e8e4da;" required>
                            @error('numero_lot') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Médicament (sélection) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Médicament <span class="text-danger">*</span></label>
                            <select name="medicament_id" class="form-select @error('medicament_id') is-invalid @enderror" style="border-color: #e8e4da;" required>
                                <option value="">-- Choisir un médicament --</option>
                                @php
                                    $medicaments = \App\Models\Medicament::orderBy('nom_commercial_fr')->get();
                                    $selectedMedId = old('medicament_id', is_object($lot) ? $lot->medicament_id : ($lot['medicament_id'] ?? ''));
                                @endphp
                                @foreach($medicaments as $med)
                                    <option value="{{ $med->id }}" {{ $selectedMedId == $med->id ? 'selected' : '' }}>
                                        {{ $med->nom_commercial_fr }} ({{ $med->dci }})
                                    </option>
                                @endforeach
                            </select>
                            @error('medicament_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Quantité initiale -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Quantité initiale</label>
                            <input type="number" name="quantite_initial" class="form-control" 
                                   value="{{ old('quantite_initial', is_object($lot) ? $lot->quantite_initial : ($lot['quantite_initial'] ?? 0)) }}" 
                                   style="border-color: #e8e4da;" step="1" min="0">
                        </div>

                        <!-- Quantité actuelle -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Quantité actuelle</label>
                            <input type="number" name="quantite_actuelle" class="form-control" 
                                   value="{{ old('quantite_actuelle', is_object($lot) ? $lot->quantite_actuelle : ($lot['quantite_actuelle'] ?? 0)) }}" 
                                   style="border-color: #e8e4da;" step="1" min="0">
                        </div>

                        <!-- Statut -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Statut</label>
                            <select name="statut" class="form-select" style="border-color: #e8e4da;">
                                @php
                                    $currentStatut = old('statut', is_object($lot) ? $lot->statut : ($lot['statut'] ?? 'actif'));
                                @endphp
                                <option value="actif" {{ $currentStatut == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="epuise" {{ $currentStatut == 'epuise' ? 'selected' : '' }}>Épuisé</option>
                                <option value="perime" {{ $currentStatut == 'perime' ? 'selected' : '' }}>Périmé</option>
                                <option value="retire" {{ $currentStatut == 'retire' ? 'selected' : '' }}>Retiré</option>
                            </select>
                        </div>

                        <!-- Date fabrication -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Date de fabrication</label>
                            <input type="date" name="date_fabrication" class="form-control" 
                                   value="{{ old('date_fabrication', is_object($lot) ? ($lot->date_fabrication ? $lot->date_fabrication->format('Y-m-d') : '') : ($lot['date_fabrication'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Date péremption -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Date de péremption</label>
                            <input type="date" name="date_peremption" class="form-control" 
                                   value="{{ old('date_peremption', is_object($lot) ? ($lot->date_peremption ? $lot->date_peremption->format('Y-m-d') : '') : ($lot['date_peremption'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Fournisseur -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Fournisseur</label>
                            <input type="text" name="fournisseur" class="form-control" 
                                   value="{{ old('fournisseur', is_object($lot) ? $lot->fournisseur : ($lot['fournisseur'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Prix d'achat -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Prix d'achat (TND)</label>
                            <input type="number" step="0.001" name="prix_achat" class="form-control" 
                                   value="{{ old('prix_achat', is_object($lot) ? $lot->prix_achat : ($lot['prix_achat'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Prix de vente -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Prix de vente (TND)</label>
                            <input type="number" step="0.001" name="prix_vente" class="form-control" 
                                   value="{{ old('prix_vente', is_object($lot) ? $lot->prix_vente : ($lot['prix_vente'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Numéro de facture -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">N° de facture</label>
                            <input type="text" name="numero_facture" class="form-control" 
                                   value="{{ old('numero_facture', is_object($lot) ? $lot->numero_facture : ($lot['numero_facture'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Conditionnement -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Conditionnement</label>
                            <input type="text" name="conditionnement" class="form-control" 
                                   value="{{ old('conditionnement', is_object($lot) ? $lot->conditionnement : ($lot['conditionnement'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Emplacement -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Emplacement</label>
                            <input type="text" name="emplacement" class="form-control" 
                                   value="{{ old('emplacement', is_object($lot) ? $lot->emplacement : ($lot['emplacement'] ?? '')) }}" 
                                   style="border-color: #e8e4da;">
                        </div>

                        <!-- Observations -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Observations</label>
                            <textarea name="observations" class="form-control" rows="3" style="border-color: #e8e4da;">{{ old('observations', is_object($lot) ? $lot->observations : ($lot['observations'] ?? '')) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="d-flex justify-content-between gap-3 mb-4">
                    <a href="{{ route('lots.show', $lotId) }}" class="btn rounded-pill px-4 py-2" style="background: #9c8a78; color: white; border: none;">
                        <i class="bi bi-x-circle me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn rounded-pill px-4 py-2" style="background: #d4af37; color: white; border: none;">
                        <i class="bi bi-save me-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control, .form-select {
        border-radius: 0.5rem;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }
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
</style>
@endpush