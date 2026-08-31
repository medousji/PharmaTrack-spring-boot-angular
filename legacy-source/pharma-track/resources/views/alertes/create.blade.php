@extends('layouts.app')

@section('title', 'Créer une alerte - Pharma Track')
@section('page-title', '') {{-- vide pour éviter le doublon --}}
@section('page-icon', 'bi-bell')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('alertes.index') }}" style="color: #9c8a78;">Alertes</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Créer une alerte</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec logo -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-bell fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Créer une alerte</h1>
            <p class="text-muted mb-0" style="color: #9c8a78;">Ajouter une nouvelle alerte manuelle</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <form action="{{ route('alertes.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Type d'alerte</label>
                        <select name="type" class="form-select rounded-pill @error('type') is-invalid @enderror" style="border-color: #e8e4da;">
                            <option value="expiration" {{ old('type') == 'expiration' ? 'selected' : '' }}>Expiration</option>
                            <option value="stock" {{ old('type') == 'stock' ? 'selected' : '' }}>Stock faible</option>
                            <option value="qualite" {{ old('type') == 'qualite' ? 'selected' : '' }}>Qualité</option>
                            <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Niveau / Priorité</label>
                        <select name="niveau" class="form-select rounded-pill @error('niveau') is-invalid @enderror" style="border-color: #e8e4da;">
                            <option value="faible" {{ old('niveau') == 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="moyen" {{ old('niveau') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="eleve" {{ old('niveau') == 'eleve' ? 'selected' : '' }}>Élevé</option>
                            <option value="critique" {{ old('niveau') == 'critique' ? 'selected' : '' }}>Critique</option>
                        </select>
                        @error('niveau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Message</label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="3" style="border-color: #e8e4da;">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">ID du lot (optionnel)</label>
                        <input type="text" name="lot_id" class="form-control @error('lot_id') is-invalid @enderror" value="{{ old('lot_id') }}" style="border-color: #e8e4da;">
                        @error('lot_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">ID du médicament (optionnel)</label>
                        <input type="text" name="medicament_id" class="form-control @error('medicament_id') is-invalid @enderror" value="{{ old('medicament_id') }}" style="border-color: #e8e4da;">
                        @error('medicament_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('alertes.index') }}" class="btn rounded-pill px-4 py-2" style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn rounded-pill px-4 py-2" style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-check-circle me-2"></i>Créer l'alerte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
    }
</style>
@endpush