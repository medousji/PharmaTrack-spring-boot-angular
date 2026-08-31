@extends('layouts.app')

@section('title', 'Ajouter un médicament - Pharma Track')
@section('page-title', '')  {{-- Vidé pour éviter le doublon --}}
@section('page-icon', 'bi-plus-circle-fill')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-plus-circle-fill me-2" style="color: #d4af37;"></i>Ajouter un nouveau médicament
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('home') }}" style="color: #9c8a78; text-decoration: none;">Accueil</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('medicaments.index') }}" style="color: #9c8a78; text-decoration: none;">Médicaments</a>
                                </li>
                                <li class="breadcrumb-item active" style="color: #d4af37;">Ajouter</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('medicaments.index') }}" class="btn px-4 py-2 rounded-pill" 
                           style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="form-card p-5 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                
                <!-- Messages de succès/erreur -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" style="background: #d4edda; border: none; color: #155724;">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" style="background: #f8d7da; border: none; color: #721c24;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs ci-dessous
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('medicaments.store') }}" method="POST">
                    @csrf
                    
                    <!-- Informations de base -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-info-circle-fill me-2" style="color: #d4af37;"></i>Informations de base
                        </h5>
                        
                        <div class="row g-3">
                            <!-- Code CIP -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Code CIP <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-upc-scan" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="text" 
                                           name="code_cip" 
                                           class="form-control border-start-0 ps-0 @error('code_cip') is-invalid @enderror" 
                                           value="{{ old('code_cip') }}" 
                                           placeholder="Ex: 3400931234567"
                                           style="border-color: #e8e4da;">
                                </div>
                                @error('code_cip')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Code unique à 13 chiffres</small>
                            </div>

                            <!-- DCI -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    DCI <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-droplet" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="text" 
                                           name="dci" 
                                           class="form-control border-start-0 ps-0 @error('dci') is-invalid @enderror" 
                                           value="{{ old('dci') }}" 
                                           placeholder="Ex: Paracetamol"
                                           style="border-color: #e8e4da;">
                                </div>
                                @error('dci')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Noms commerciaux -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-translate me-2" style="color: #d4af37;"></i>Noms commerciaux
                        </h5>
                        
                        <div class="row g-3">
                            <!-- Nom FR -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Nom commercial (FR) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-translate" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="text" 
                                           name="nom_commercial_fr" 
                                           class="form-control border-start-0 ps-0 @error('nom_commercial_fr') is-invalid @enderror" 
                                           value="{{ old('nom_commercial_fr') }}" 
                                           placeholder="Ex: Doliprane 1000mg"
                                           style="border-color: #e8e4da;">
                                </div>
                                @error('nom_commercial_fr')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nom AR -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Nom commercial (AR)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-globe2" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="text" 
                                           name="nom_commercial_ar" 
                                           class="form-control border-start-0 ps-0 @error('nom_commercial_ar') is-invalid @enderror" 
                                           value="{{ old('nom_commercial_ar') }}" 
                                           placeholder="Ex: دوليبران 1000 ملغ"
                                           style="border-color: #e8e4da; direction: rtl;">
                                </div>
                                @error('nom_commercial_ar')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Caractéristiques -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-capsule-pill me-2" style="color: #d4af37;"></i>Caractéristiques
                        </h5>
                        
                        <div class="row g-3">
                            <!-- Forme -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Forme <span class="text-danger">*</span>
                                </label>
                                <select name="forme" class="form-select @error('forme') is-invalid @enderror" style="border-color: #e8e4da;">
                                    <option value="">Sélectionner</option>
                                    <option value="comprimé" {{ old('forme') == 'comprimé' ? 'selected' : '' }}>Comprimé</option>
                                    <option value="gélule" {{ old('forme') == 'gélule' ? 'selected' : '' }}>Gélule</option>
                                    <option value="sirop" {{ old('forme') == 'sirop' ? 'selected' : '' }}>Sirop</option>
                                    <option value="injectable" {{ old('forme') == 'injectable' ? 'selected' : '' }}>Injectable</option>
                                    <option value="pommade" {{ old('forme') == 'pommade' ? 'selected' : '' }}>Pommade</option>
                                    <option value="sachet" {{ old('forme') == 'sachet' ? 'selected' : '' }}>Sachet</option>
                                    <option value="ampoule" {{ old('forme') == 'ampoule' ? 'selected' : '' }}>Ampoule</option>
                                    <option value="collyre" {{ old('forme') == 'collyre' ? 'selected' : '' }}>Collyre</option>
                                    <option value="spray" {{ old('forme') == 'spray' ? 'selected' : '' }}>Spray</option>
                                    <option value="creme" {{ old('forme') == 'creme' ? 'selected' : '' }}>Crème</option>
                                    <option value="suppositoire" {{ old('forme') == 'suppositoire' ? 'selected' : '' }}>Suppositoire</option>
                                    <option value="poudre" {{ old('forme') == 'poudre' ? 'selected' : '' }}>Poudre</option>
                                </select>
                                @error('forme')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dosage -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Dosage <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="dosage" 
                                       class="form-control @error('dosage') is-invalid @enderror" 
                                       value="{{ old('dosage') }}" 
                                       placeholder="Ex: 1000"
                                       style="border-color: #e8e4da;">
                                @error('dosage')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Unité -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Unité <span class="text-danger">*</span>
                                </label>
                                <select name="unite" class="form-select @error('unite') is-invalid @enderror" style="border-color: #e8e4da;">
                                    <option value="">Sélectionner</option>
                                    <option value="mg" {{ old('unite') == 'mg' ? 'selected' : '' }}>mg</option>
                                    <option value="g" {{ old('unite') == 'g' ? 'selected' : '' }}>g</option>
                                    <option value="ml" {{ old('unite') == 'ml' ? 'selected' : '' }}>ml</option>
                                    <option value="UI" {{ old('unite') == 'UI' ? 'selected' : '' }}>UI</option>
                                    <option value="%" {{ old('unite') == '%' ? 'selected' : '' }}>%</option>
                                    <option value="µg" {{ old('unite') == 'µg' ? 'selected' : '' }}>µg</option>
                                </select>
                                @error('unite')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Catégorie -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Catégorie <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="categorie" 
                                       class="form-control @error('categorie') is-invalid @enderror" 
                                       value="{{ old('categorie') }}" 
                                       placeholder="Ex: Antalgique"
                                       style="border-color: #e8e4da;">
                                @error('categorie')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Laboratoire -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Laboratoire
                                </label>
                                <input type="text" name="laboratoire" 
                                       class="form-control @error('laboratoire') is-invalid @enderror" 
                                       value="{{ old('laboratoire') }}" 
                                       placeholder="Ex: Sanofi"
                                       style="border-color: #e8e4da;">
                                @error('laboratoire')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Prix et stocks -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-cash-stack me-2" style="color: #d4af37;"></i>Prix et stocks
                        </h5>
                        
                        <div class="row g-3">
                            <!-- Prix achat -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Prix d'achat (TND)
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.001" name="prix_achat" 
                                           class="form-control @error('prix_achat') is-invalid @enderror" 
                                           value="{{ old('prix_achat') }}" 
                                           placeholder="0.000"
                                           style="border-color: #e8e4da;">
                                    <span class="input-group-text" style="background: #f5efe8; border-color: #e8e4da; color: #8b7355;">TND</span>
                                </div>
                                @error('prix_achat')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Prix vente -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Prix de vente (TND)
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.001" name="prix_vente" 
                                           class="form-control @error('prix_vente') is-invalid @enderror" 
                                           value="{{ old('prix_vente') }}" 
                                           placeholder="0.000"
                                           style="border-color: #e8e4da;">
                                    <span class="input-group-text" style="background: #f5efe8; border-color: #e8e4da; color: #8b7355;">TND</span>
                                </div>
                                @error('prix_vente')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Stock min -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Stock minimum
                                </label>
                                <input type="number" name="stock_min" 
                                       class="form-control @error('stock_min') is-invalid @enderror" 
                                       value="{{ old('stock_min', 10) }}"
                                       style="border-color: #e8e4da;">
                                @error('stock_min')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Stock max -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Stock maximum
                                </label>
                                <input type="number" name="stock_max" 
                                       class="form-control @error('stock_max') is-invalid @enderror" 
                                       value="{{ old('stock_max', 100) }}"
                                       style="border-color: #e8e4da;">
                                @error('stock_max')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Délai appro -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Délai d'approvisionnement (jours)
                                </label>
                                <input type="number" name="delai_appro" 
                                       class="form-control @error('delai_appro') is-invalid @enderror" 
                                       value="{{ old('delai_appro', 7) }}"
                                       style="border-color: #e8e4da;">
                                @error('delai_appro')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-gear me-2" style="color: #d4af37;"></i>Options
                        </h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="est_essentiel" class="form-check-input" id="est_essentiel" value="1" {{ old('est_essentiel') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="est_essentiel" style="color: #5d4b38;">
                                        Médicament essentiel
                                    </label>
                                    <small class="d-block text-muted">Selon la liste OMS adaptée pour la Tunisie</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="est_controle" class="form-check-input" id="est_controle" value="1" {{ old('est_controle') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="est_controle" style="color: #5d4b38;">
                                        Substance contrôlée
                                    </label>
                                    <small class="d-block text-muted">Nécessite une traçabilité renforcée</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <a href="{{ route('medicaments.index') }}" class="btn px-5 py-2 rounded-pill" 
                           style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn px-5 py-2 rounded-pill" 
                                style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer
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
    /* Animation du formulaire */
    .form-card {
        transition: all 0.3s ease;
    }
    
    .form-card:hover {
        box-shadow: 0 10px 30px rgba(139, 115, 85, 0.1) !important;
    }
    
    /* Style des inputs */
    .form-control, .form-select {
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
    }
    
    /* Animation des checkboxes */
    .form-check-input {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: #d4af37;
        border-color: #d4af37;
    }
    
    .form-check-input:hover {
        transform: scale(1.1);
    }
    
    /* Style des boutons */
    .btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn:hover {
        transform: translateY(-2px);
    }
    
    .btn[style*="background: #d4af37"]:hover {
        filter: brightness(1.1);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }
    
    .btn[style*="background: #f0ebe4"]:hover {
        background: #e8e0d5 !important;
    }
    
    /* Animation de l'en-tête */
    .welcome-card {
        transition: all 0.3s ease;
    }
    
    .welcome-card:hover {
        box-shadow: 0 10px 30px rgba(139, 115, 85, 0.1) !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem !important;
        }
        
        .btn {
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
        }
    }
</style>
@endpush