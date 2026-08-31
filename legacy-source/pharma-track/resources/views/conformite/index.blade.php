@extends('layouts.app')

@section('title', 'Conformité ONP - Pharma Track')
@section('page-title', '')  {{-- Vide pour éviter le doublon --}}
@section('page-icon', 'bi-shield-check')

@section('breadcrumb')
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Conformité ONP</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec icône bouclier -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-shield-check fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Conformité ONP</h1>
            <p class="text-muted mb-0" style="color: #9c8a78;">Respect des réglementations pharmaceutiques tunisiennes</p>
        </div>
    </div>

    <!-- Statistiques de conformité (exemple) -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Réglementations</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">4/4</h2>
                        <small style="color: #9c8a78;">Toutes conformes</small>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-file-earmark-text fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #9c8a78;">Certifications</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #5d4b38;">3/3</h2>
                        <small style="color: #9c8a78;">À jour</small>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #f5efe8;">
                        <i class="bi bi-award fs-1" style="color: #d4af37;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de contenu -->
    <div class="row g-4">
        <!-- Conformité réglementaire -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                        <i class="bi bi-check-circle-fill me-2" style="color: #9caf88;"></i>Conformité réglementaire
                    </h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #f0ebe4;">
                            <span style="color: #5d4b38;">Loi 85-87 relative à la pharmacie</span>
                            <span class="badge" style="background: #9caf88; color: white;">Conforme</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #f0ebe4;">
                            <span style="color: #5d4b38;">Décret 91-356 relatif aux médicaments</span>
                            <span class="badge" style="background: #9caf88; color: white;">Conforme</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #f0ebe4;">
                            <span style="color: #5d4b38;">Arrêté du 22 janvier 2015</span>
                            <span class="badge" style="background: #9caf88; color: white;">Conforme</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #f0ebe4;">
                            <span style="color: #5d4b38;">Normes ISO 9001</span>
                            <span class="badge" style="background: #9caf88; color: white;">Conforme</span>
                        </li>
                    </ul>
                    <div class="mt-3 text-end">
                        <a href="#" class="btn btn-sm rounded-pill px-3" style="background: #f0ebe4; color: #8b7355;">
                            Voir détails <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents ONP -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                        <i class="bi bi-file-earmark-pdf-fill me-2" style="color: #e6a57e;"></i>Documents ONP
                    </h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #f0ebe4;">
                            <span style="color: #5d4b38;">Enregistrement ONP</span>
                            <span class="badge" style="background: #9caf88; color: white;">Valide</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #f0ebe4;">
                            <span style="color: #5d4b38;">Licence d'exploitation</span>
                            <span class="badge" style="background: #d4af37; color: white;">N° PH-2024-0123</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: #f0ebe4;">
                            <span style="color: #5d4b38;">Agrément du pharmacien</span>
                            <span class="badge" style="background: #d4af37; color: white;">N° 12345</span>
                        </li>
                    </ul>
                    <div class="mt-3 text-end">
                        <a href="#" class="btn btn-sm rounded-pill px-3" style="background: #f0ebe4; color: #8b7355;">
                            Télécharger <i class="bi bi-download ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="quick-actions p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-file-earmark-text me-2" style="color: #d4af37;"></i>Rapports ONP
                </h5>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('conformite.rapport') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #9caf88; color: white; border: none;">
                        <i class="bi bi-file-earmark-text me-2"></i>Générer rapport
                    </a>
                    <a href="{{ route('conformite.pdf') }}" class="btn rounded-pill px-4 py-2" 
                       style="background: #e6a57e; color: white; border: none;">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Exporter PDF
                    </a>
                </div>
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
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .list-group-item {
        transition: all 0.3s ease;
    }
    .list-group-item:hover {
        background: #faf7f2 !important;
        transform: translateX(5px);
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
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
</style>
@endpush