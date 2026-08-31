@extends('layouts.app')

@section('title', 'Mon Profil - Pharma Track')
@section('page-title', '') {{-- vide pour éviter le doublon --}}
@section('page-icon', 'bi-person-circle')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Mon Profil</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec logo -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-person-circle fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Mon Profil</h1>
            <p class="text-muted mb-0" style="color: #9c8a78;">Gérez vos informations personnelles</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Colonne gauche : informations personnelles -->
        <div class="col-md-6">
            <div class="card p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-person-vcard me-2" style="color: #d4af37;"></i>Informations personnelles
                </h5>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required style="border-color: #e8e4da;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required style="border-color: #e8e4da;">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Rôle</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly disabled style="background: #f5efe8; border-color: #e8e4da;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="color: #5d4b38;">Statut</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->status) }}" readonly disabled style="background: #f5efe8; border-color: #e8e4da;">
                        </div>
                    </div>

                    <div class="alert alert-info rounded-3 py-2 mb-3" style="background: #f0f4fa; border: none; color: #2c5282;">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Contactez un administrateur pour modifier votre rôle.</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn rounded-pill py-2" style="background: #d4af37; color: white;">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Colonne droite : sécurité et informations système -->
        <div class="col-md-6">
            <!-- Sécurité (changement de mot de passe) -->
            <div class="card p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-shield-lock me-2" style="color: #d4af37;"></i>Sécurité
                </h5>
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Mot de passe actuel <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required style="border-color: #e8e4da;">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Nouveau mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required style="border-color: #e8e4da;">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 8 caractères</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required style="border-color: #e8e4da;">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn rounded-pill py-2" style="background: #9caf88; color: white;">
                            <i class="bi bi-key me-2"></i>Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>

            <!-- Information système -->
            <div class="card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-info-circle me-2" style="color: #d4af37;"></i>Information système
                </h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">Identifiant</span>
                        <span class="fw-semibold" style="color: #8b7355;">#{{ $user->id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">Dernière connexion</span>
                        <span style="color: #8b7355;">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->format('d/m/Y à H:i') }}
                            @else
                                Jamais
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">IP</span>
                        <span style="color: #8b7355;">{{ $user->last_login_ip ?? 'Non enregistrée' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">Compte créé</span>
                        <span style="color: #8b7355;">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">Compte vérifié</span>
                        <span style="color: #8b7355;">
                            @if($user->email_verified_at)
                                Oui ({{ $user->email_verified_at->format('d/m/Y') }})
                            @else
                                Non
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">Dernière mise à jour</span>
                        <span style="color: #8b7355;">{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0" style="background: transparent; border-color: #e8e4da;">
                        <span style="color: #5d4b38;">Statut</span>
                        <span>
                            @if($user->status === 'active')
                                <span class="badge px-3 py-2" style="background: #9caf88; color: white;">Actif</span>
                            @elseif($user->status === 'inactive')
                                <span class="badge px-3 py-2" style="background: #e6a57e; color: white;">Inactif</span>
                            @else
                                <span class="badge px-3 py-2" style="background: #9c8a78; color: white;">Suspendu</span>
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .btn {
        transition: all 0.2s;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    .form-control:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
    }
</style>
@endpush