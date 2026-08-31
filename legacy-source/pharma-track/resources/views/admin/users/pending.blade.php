@extends('layouts.app')

@section('title', 'Inscriptions en attente - Pharma Track')
@section('page-title', 'Inscriptions en attente')
@section('page-icon', 'bi-person-check')

@php
    // Forcer le navigateur à ne pas mettre en cache cette page
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
@endphp

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <h2 class="fw-bold mb-0" style="color: #5d4b38;">
                    <i class="bi bi-person-check me-2" style="color: #d4af37;"></i>
                    Demandes d'inscription en attente
                </h2>
                <p class="mb-0 mt-2" style="color: #9c8a78;">Validez ou rejetez les comptes en attente d'approbation</p>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">En attente</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #e6a57e;" id="pendingCount">{{ $users->count() }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-clock-history fs-1" style="color: #e6a57e;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Total inscrits</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #9caf88;">{{ $totalUsers ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-people fs-1" style="color: #9caf88;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Approuvés</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #d4af37;">{{ $approvedUsers ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-check-circle fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" id="successAlert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-list-check me-2" style="color: #d4af37;"></i>
                    Demandes en attente
                </h5>

                @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background: #f5efe8;">
                            <tr>
                                <th style="color: #5d4b38;">Nom</th>
                                <th style="color: #5d4b38;">Email</th>
                                <th style="color: #5d4b38;">Date d'inscription</th>
                                <th style="color: #5d4b38;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr id="user-row-{{ $user->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: #f5efe8;">
                                            <i class="bi bi-person" style="color: #d4af37;"></i>
                                        </div>
                                        <strong style="color: #5d4b38;">{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td style="color: #9c8a78;">{{ $user->email }}</td>
                                <td style="color: #9c8a78;">
                                    <i class="bi bi-calendar3 me-1" style="color: #d4af37;"></i>
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        {{-- FORMULAIRE POUR APPROUVER --}}
                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm rounded-pill" style="background: #9caf88; color: white; border: none; padding: 6px 15px;">
                                                <i class="bi bi-check-circle"></i> Approuver
                                            </button>
                                        </form>
                                        
                                        {{-- FORMULAIRE POUR REJETER --}}
                                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm rounded-pill" style="background: #e6a57e; color: white; border: none; padding: 6px 15px;" onclick="return confirm('Rejeter définitivement {{ $user->name }} ?')">
                                                <i class="bi bi-x-circle"></i> Rejeter
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <div style="background: #f5efe8; width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <i class="bi bi-check-circle fs-1" style="color: #9caf88;"></i>
                    </div>
                    <h5 style="color: #5d4b38;">Aucune inscription en attente</h5>
                    <p style="color: #9c8a78;">Toutes les demandes ont été traitées</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease;
        animation-fill-mode: both;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
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
    .card-light, .welcome-card {
        transition: all 0.3s ease;
    }
    .card-light:hover, .welcome-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .btn {
        transition: all 0.2s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    .table tbody tr {
        transition: all 0.3s ease;
    }
    .table tbody tr:hover {
        background: #faf7f2 !important;
        transform: translateX(5px);
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@push('scripts')
<script>
    // Recharger automatiquement la page après une approbation ou un rejet
    document.addEventListener('DOMContentLoaded', function() {
        // Si un message de succès est présent, recharger la page après 1 seconde
        @if(session('success'))
            console.log('Rechargement dans 1 seconde...');
            setTimeout(function() {
                window.location.href = window.location.href;
            }, 1000);
        @endif
        
        // Désactiver les boutons après clic pour éviter double soumission
        document.querySelectorAll('form[action*="approve"] button, form[action*="reject"] button').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('form');
                if (form) {
                    setTimeout(() => {
                        form.querySelector('button').disabled = true;
                        form.querySelector('button').innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement...';
                    }, 100);
                }
            });
        });
    });
</script>
@endpush