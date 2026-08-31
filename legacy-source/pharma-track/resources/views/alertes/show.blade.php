@extends('layouts.app')

@section('title', 'Détail de l\'alerte - Pharma Track')
@section('page-title', '')
@section('page-icon', 'bi-bell')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('alertes.index') }}" style="color: #9c8a78;">Alertes</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Détail alerte #{{ $alerte['id'] }}</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Titre avec logo -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #f5efe8;">
            <i class="bi bi-bell fs-1" style="color: #d4af37;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #5d4b38;">Détail de l'alerte #{{ $alerte['id'] }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-info-circle me-2" style="color: #d4af37;"></i>Informations
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <td style="color: #5d4b38; width: 30%;">Type</td>
                        <td style="color: #8b7355;">{{ $alerte['type'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Message</td>
                        <td style="color: #8b7355;">{{ $alerte['message'] ?? 'Sans message' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Niveau</td>
                        <td>
                            @php
                                $niveau = $alerte['niveau'] ?? 'faible';
                                $color = $niveau === 'Élevé' || $niveau === 'critique' ? '#e6a57e' : ($niveau === 'Moyen' ? '#d4af37' : '#9caf88');
                            @endphp
                            <span class="badge px-3 py-2" style="background: {{ $color }}; color: white;">
                                {{ ucfirst($niveau) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Statut</td>
                        <td>
                            @if($alerte['est_lue'] ?? false)
                                <span class="badge px-3 py-2" style="background: #9caf88; color: white;">Lue</span>
                            @else
                                <span class="badge px-3 py-2" style="background: #e6a57e; color: white;">Non lue</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Date création</td>
                        <td style="color: #8b7355;">{{ $alerte['date_creation'] ?? 'Non spécifiée' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #5d4b38;">Lot concerné</td>
                        <td style="color: #8b7355;">{{ $alerte['lot'] ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Actions -->
            <div class="card p-4 rounded-4 mb-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-lightning-charge me-2" style="color: #d4af37;"></i>Actions
                </h5>
                <div class="d-flex flex-column gap-2">
                    @if(!($alerte['est_lue'] ?? false))
                        <form action="{{ route('alertes.marquer-lue', $alerte['id']) }}" method="POST" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="btn w-100 rounded-pill" style="background: #d4af37; color: white;">
                                <i class="bi bi-check-circle me-2"></i>Marquer comme lue
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('alertes.index') }}" class="btn w-100 rounded-pill" style="background: #9caf88; color: white;">
                        <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                    </a>
                </div>
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
</style>
@endpush