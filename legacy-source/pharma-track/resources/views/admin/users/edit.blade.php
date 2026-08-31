@extends('layouts.app')

@section('title', 'Modifier un utilisateur - Pharma Track')
@section('page-title', '')
@section('page-icon', 'bi-person-gear')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-person-gear me-2" style="color: #d4af37;"></i>Modifier l'utilisateur
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('home') }}" style="color: #9c8a78; text-decoration: none;">Accueil</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}" style="color: #9c8a78; text-decoration: none;">Administration</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.users') }}" style="color: #9c8a78; text-decoration: none;">Utilisateurs</a>
                                </li>
                                <li class="breadcrumb-item active" style="color: #d4af37;">Modifier</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.users') }}" class="btn px-4 py-2 rounded-pill" 
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
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" style="background: #f8d7da; border: none; color: #721c24;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs ci-dessous
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Informations principales -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-info-circle-fill me-2" style="color: #d4af37;"></i>Informations principales
                        </h5>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-person" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" 
                                           style="border-color: #e8e4da;"
                                           required>
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-envelope" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" 
                                           style="border-color: #e8e4da;"
                                           required>
                                </div>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">Rôle</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" style="border-color: #e8e4da;"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    <option value="pharmacien" {{ old('role', $user->role) == 'pharmacien' ? 'selected' : '' }}>Pharmacien</option>
                                    <option value="fournisseur" {{ old('role', $user->role) == 'fournisseur' ? 'selected' : '' }}>Fournisseur</option>
                                    <option value="visiteur" {{ old('role', $user->role) == 'visiteur' ? 'selected' : '' }}>Visiteur</option>
                                </select>
                                @if($user->id === auth()->id())
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <small class="text-warning d-block mt-1">
                                        <i class="bi bi-exclamation-triangle"></i> Vous ne pouvez pas modifier votre propre rôle.
                                    </small>
                                @endif
                                @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">Statut</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" style="border-color: #e8e4da;">
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                </select>
                                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                <small class="text-muted">Les utilisateurs inactifs ne peuvent pas se connecter</small>
                            </div>
                        </div>
                    </div>

                    <!-- Mot de passe avec bouton afficher/masquer -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-key me-2" style="color: #d4af37;"></i>Changer le mot de passe
                        </h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">Nouveau mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-lock" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="password" 
                                           name="password" 
                                           id="password"
                                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                           style="border-color: #e8e4da;">
                                    <button type="button" 
                                            class="btn btn-outline-secondary border-start-0" 
                                            style="border-color: #e8e4da; background: white;"
                                            onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #5d4b38;">Confirmer le mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                        <i class="bi bi-lock-fill" style="color: #d4af37;"></i>
                                    </span>
                                    <input type="password" 
                                           name="password_confirmation" 
                                           id="password_confirmation"
                                           class="form-control border-start-0 ps-0" 
                                           style="border-color: #e8e4da;">
                                    <button type="button" 
                                            class="btn btn-outline-secondary border-start-0" 
                                            style="border-color: #e8e4da; background: white;"
                                            onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="togglePasswordConfirmationIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations système -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #5d4b38; border-bottom: 2px solid #d4af37; padding-bottom: 0.5rem;">
                            <i class="bi bi-server me-2" style="color: #d4af37;"></i>Informations système
                        </h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card p-3 rounded-3" style="background: #f5efe8;">
                                    <small class="text-muted d-block">Compte créé le</small>
                                    <span class="fw-semibold" style="color: #5d4b38;">
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y à H:i') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card p-3 rounded-3" style="background: #f5efe8;">
                                    <small class="text-muted d-block">Dernière connexion</small>
                                    <span class="fw-semibold" style="color: #5d4b38;">
                                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y à H:i') : 'Jamais' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-card p-3 rounded-3" style="background: #f5efe8;">
                                    <small class="text-muted d-block">Dernière IP</small>
                                    <span class="fw-semibold" style="color: #5d4b38;">
                                        {{ $user->last_login_ip ?? 'Non enregistrée' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.users') }}" class="btn px-4 py-2 rounded-pill" 
                           style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn px-4 py-2 rounded-pill" 
                                style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-check-circle me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>

                @if($user->id !== auth()->id())
                <hr class="my-4" style="border-color: #e8e4da;">
                
                <!-- Zone dangereuse -->
                <div class="danger-zone">
                    <div class="alert border rounded-3 p-4" style="background: #fff5f0; border-color: #e6a57e !important;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-exclamation-triangle-fill fs-2" style="color: #e6a57e;"></i>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1" style="color: #e6a57e;">Zone dangereuse</h6>
                                <p class="mb-2 small" style="color: #8b7355;">Cette action est irréversible. La suppression définitive de l'utilisateur entraînera la perte de toutes ses données associées.</p>
                                <form action="{{ route('admin.users.delete', $user) }}" 
                                      method="POST" 
                                      id="deleteForm"
                                      onsubmit="return confirm('⚠️ Êtes-vous absolument sûr de vouloir supprimer définitivement cet utilisateur ? Cette action ne peut pas être annulée.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm rounded-pill px-4 py-2" 
                                            style="background: #e6a57e; color: white; border: none;">
                                        <i class="bi bi-trash me-2"></i>Supprimer l'utilisateur
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-card {
        transition: all 0.3s ease;
    }
    .form-card:hover {
        box-shadow: 0 10px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .form-control, .form-select {
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
    }
    .info-card {
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateX(5px);
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .btn[style*="background: #d4af37"]:hover {
        filter: brightness(1.1);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }
    .btn[style*="background: #e6a57e"]:hover {
        filter: brightness(1.05);
        box-shadow: 0 5px 15px rgba(230, 165, 126, 0.3);
    }
    .welcome-card {
        transition: all 0.3s ease;
    }
    .welcome-card:hover {
        box-shadow: 0 10px 30px rgba(139, 115, 85, 0.1) !important;
    }
    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem !important;
        }
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        .btn {
            width: 100%;
        }
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