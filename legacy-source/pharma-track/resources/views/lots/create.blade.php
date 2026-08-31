@extends('layouts.app')

@section('title', 'Ajouter un lot - Pharma Track')
@section('page-title', 'Ajouter un nouveau lot')
@section('page-icon', 'bi-box')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('lots.index') }}">Lots</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-box"></i> Nouveau lot</h5>
    </div>
    
    <div class="card-body">
        {{-- Afficher les erreurs de validation --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lots.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Numéro de lot</label>
                    <input type="text" name="numero_lot" 
                           class="form-control @error('numero_lot') is-invalid @enderror"
                           value="{{ old('numero_lot') }}" 
                           placeholder="Ex: LOT-2024-001"
                           required>
                    @error('numero_lot')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Médicament</label>
                    <select name="medicament" 
                            class="form-control @error('medicament') is-invalid @enderror"
                            required>
                        <option value="">Sélectionnez un médicament</option>
                        <option value="Paracétamol" {{ old('medicament') == 'Paracétamol' ? 'selected' : '' }}>Paracétamol</option>
                        <option value="Ibuprofène" {{ old('medicament') == 'Ibuprofène' ? 'selected' : '' }}>Ibuprofène</option>
                        <option value="Amoxicilline" {{ old('medicament') == 'Amoxicilline' ? 'selected' : '' }}>Amoxicilline</option>
                        <option value="Oméprazole" {{ old('medicament') == 'Oméprazole' ? 'selected' : '' }}>Oméprazole</option>
                        <option value="Metformine" {{ old('medicament') == 'Metformine' ? 'selected' : '' }}>Metformine</option>
                    </select>
                    @error('medicament')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Quantité</label>
                    <input type="number" name="quantite" 
                           class="form-control @error('quantite') is-invalid @enderror"
                           value="{{ old('quantite', 100) }}" 
                           min="1"
                           required>
                    @error('quantite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Date d'expiration</label>
                    <input type="date" name="date_expiration" 
                           class="form-control @error('date_expiration') is-invalid @enderror"
                           value="{{ old('date_expiration') }}" 
                           required>
                    @error('date_expiration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de fabrication</label>
                    <input type="date" name="date_fabrication" 
                           class="form-control @error('date_fabrication') is-invalid @enderror"
                           value="{{ old('date_fabrication') }}">
                    @error('date_fabrication')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fournisseur</label>
                    <input type="text" name="fournisseur" 
                           class="form-control @error('fournisseur') is-invalid @enderror"
                           value="{{ old('fournisseur') }}" 
                           placeholder="Ex: Pharmacie Centrale">
                    @error('fournisseur')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prix d'achat unitaire (TND)</label>
                    <input type="number" step="0.001" name="prix_achat" 
                           class="form-control @error('prix_achat') is-invalid @enderror"
                           value="{{ old('prix_achat') }}" 
                           placeholder="Ex: 15.500">
                    @error('prix_achat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prix de vente unitaire (TND)</label>
                    <input type="number" step="0.001" name="prix_vente" 
                           class="form-control @error('prix_vente') is-invalid @enderror"
                           value="{{ old('prix_vente') }}" 
                           placeholder="Ex: 22.500">
                    @error('prix_vente')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label">Emplacement</label>
                    <input type="text" name="emplacement" 
                           class="form-control @error('emplacement') is-invalid @enderror"
                           value="{{ old('emplacement') }}" 
                           placeholder="Ex: A-12-3">
                    @error('emplacement')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="conditions_particulieres" 
                               class="form-check-input" id="conditions_particulieres"
                               {{ old('conditions_particulieres') ? 'checked' : '' }}>
                        <label class="form-check-label" for="conditions_particulieres">
                            Conditions de conservation particulières (réfrigération, etc.)
                        </label>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('lots.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Enregistrer le lot
                </button>
            </div>
        </form>
    </div>
</div>
@endsection