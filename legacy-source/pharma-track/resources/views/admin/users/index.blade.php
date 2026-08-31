@extends('layouts.app')

@section('title', 'Gestion des utilisateurs - Pharma Track')
@section('page-title', '') {{-- vide pour éviter le doublon --}}
@section('page-icon', 'bi-people')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #9c8a78;">Administration</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Utilisateurs</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec logo -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-people fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Gestion des utilisateurs</h1>
            <p class="text-muted mb-0" style="color: #9c8a78;">Administrez les comptes et les permissions</p>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    @php
        $total = $users->total();
        $admins = $users->where('role', 'admin')->count();
        $pharmaciens = $users->where('role', 'pharmacien')->count();
        $visiteurs = $users->where('role', 'visiteur')->count();
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Total</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $total }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-people fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Administrateurs</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $admins }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-shield-lock fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Pharmaciens</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $pharmaciens }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-capsule fs-1" style="color: #9caf88;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Visiteurs</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">{{ $visiteurs }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-person-walking fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn rounded-pill px-4 py-2" style="background: #d4af37; color: white;">
            <i class="bi bi-person-plus me-2"></i>Nouvel utilisateur
        </a>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">ID</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Nom</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Email</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Rôle</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Statut</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37;">Créé le</th>
                        <th style="color: #9c8a78; border-bottom: 2px solid #d4af37; text-align: center;">Actions</th>
                    </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="align-middle" style="border-bottom: 1px solid #f0ebe4;">
                        <td style="color: #5d4b38;">#{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2 bg-light d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold" style="color: #5d4b38;">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="badge ms-1" style="background: #d4af37; color: white;">Vous</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="color: #8b7355;">{{ $user->email }}</td>
                        <td>
                            @php
                                $roleBadge = [
                                    'admin' => 'Administrateur',
                                    'pharmacien' => 'Pharmacien',
                                    'visiteur' => 'Visiteur'
                                ][$user->role] ?? $user->role;
                                $roleColor = $user->role === 'admin' ? '#d4af37' : ($user->role === 'pharmacien' ? '#9caf88' : '#e6a57e');
                            @endphp
                            <span class="badge px-3 py-2" style="background: {{ $roleColor }}; color: white;">{{ $roleBadge }}</span>
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge px-3 py-2" style="background: #9caf88; color: white;">Actif</span>
                            @elseif($user->status === 'inactive')
                                <span class="badge px-3 py-2" style="background: #e6a57e; color: white;">Inactif</span>
                            @else
                                <span class="badge px-3 py-2" style="background: #9c8a78; color: white;">Suspendu</span>
                            @endif
                        </td>
                        <td style="color: #8b7355;">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm rounded-circle" style="width: 35px; height: 35px; background: #f5efe8; color: #d4af37; display: flex; align-items: center; justify-content: center;" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm rounded-circle" style="width: 35px; height: 35px; background: #f5efe8; color: #e6a57e; border: none; display: flex; align-items: center; justify-content: center;" title="Supprimer" onclick="return confirm('Supprimer cet utilisateur ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: #9c8a78;">
                            <i class="bi bi-people fs-1 mb-2 d-block"></i>
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small style="color: #9c8a78;">
                Affichage de {{ $users->firstItem() ?? 0 }} à {{ $users->lastItem() ?? 0 }} sur {{ $users->total() }} utilisateurs
            </small>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease;
        animation-fill-mode: both;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .stat-icon {
        transition: all 0.3s ease;
    }
    .stat-card:hover .stat-icon {
        transform: rotate(5deg) scale(1.1);
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .table tbody tr {
        transition: all 0.2s;
    }
    .table tbody tr:hover {
        background: #faf7f2 !important;
    }
    .btn.rounded-circle {
        transition: all 0.2s;
    }
    .btn.rounded-circle:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(139, 115, 85, 0.2);
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    /* Personnalisation de la pagination (comme les autres pages) */
    .pagination {
        gap: 5px;
    }
    .page-link {
        border: 1px solid #e8e4da;
        background: #ffffff;
        color: #8b7355;
        border-radius: 8px !important;
        margin: 0 2px;
        transition: all 0.3s ease;
    }
    .page-link:hover {
        background: #f5efe8;
        border-color: #d4af37;
        color: #5d4b38;
        transform: translateY(-2px);
    }
    .page-item.active .page-link {
        background: #d4af37;
        border-color: #d4af37;
        color: white;
    }
    .page-item.disabled .page-link {
        background: #f5efe8;
        border-color: #e8e4da;
        color: #9c8a78;
    }
</style>
@endpush