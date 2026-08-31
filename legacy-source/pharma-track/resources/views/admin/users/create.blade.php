@extends('layouts.app')

@section('title', 'Créer un utilisateur - Pharma Track')
@section('page-title', '')
@section('page-icon', 'bi-person-plus')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #9c8a78;">Administration</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.users') }}" style="color: #9c8a78;">Utilisateurs</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Créer</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec logo -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-person-plus fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Créer un nouvel utilisateur</h1>
            <p class="text-muted mb-0" style="color: #9c8a78;">Ajouter un compte à la plateforme</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                
                <!-- Affichage des erreurs -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <!-- Nom complet -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Nom complet <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                <i class="bi bi-person" style="color: #d4af37;"></i>
                            </span>
                            <input type="text" name="name" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nom complet" required style="border-color: #e8e4da;">
                        </div>
                        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Adresse Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                <i class="bi bi-envelope" style="color: #d4af37;"></i>
                            </span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="exemple@email.com" required style="border-color: #e8e4da;">
                        </div>
                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Mot de passe <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                <i class="bi bi-lock" style="color: #d4af37;"></i>
                            </span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••" required style="border-color: #e8e4da;">
                            <button type="button" class="btn btn-outline-secondary border-start-0" style="border-color: #e8e4da; background: white;" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        <div class="password-hint mt-1" style="font-size: 0.85rem; color: #9c8a78;">
                            <i class="bi bi-info-circle"></i> Minimum 8 caractères
                        </div>
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                <i class="bi bi-lock-fill" style="color: #d4af37;"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 ps-0" placeholder="Retapez le mot de passe" required style="border-color: #e8e4da;">
                            <button type="button" class="btn btn-outline-secondary border-start-0" style="border-color: #e8e4da; background: white;" onclick="togglePassword('password_confirmation')">
                                <i class="bi bi-eye" id="togglePasswordConfirmationIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Rôle -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Rôle <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" style="border-color: #e8e4da;">
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                            <option value="pharmacien" {{ old('role') == 'pharmacien' ? 'selected' : '' }}>Pharmacien</option>
                            <option value="fournisseur" {{ old('role') == 'fournisseur' ? 'selected' : '' }}>Fournisseur</option>
                            <option value="visiteur" {{ old('role') == 'visiteur' ? 'selected' : '' }}>Visiteur</option>
                        </select>
                        @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <!-- Statut (AJOUTÉ) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Statut <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" style="border-color: #e8e4da;">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Les utilisateurs inactifs ne peuvent pas se connecter</small>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.users') }}" class="btn rounded-pill px-4 py-2" style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn rounded-pill px-4 py-2" style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-check-circle me-2"></i>Créer l'utilisateur
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
    .btn {
        transition: all 0.2s;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = fieldId === 'password' ? 
        document.getElementById('togglePasswordIcon') : 
        document.getElementById('togglePasswordConfirmationIcon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush