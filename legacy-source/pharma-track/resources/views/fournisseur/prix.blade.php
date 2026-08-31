@extends('layouts.app')

@section('title', 'Gestion des prix - Pharma Track')
@section('page-title', 'Gestion des prix')
@section('page-icon', 'bi-tag')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #9c8a78;">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('fournisseur.dashboard') }}" style="color: #9c8a78;">Dashboard</a></li>
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Gestion des prix</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-tag me-2" style="color: #d4af37;"></i>Gestion des prix
                        </h2>
                        <p class="mb-0" style="color: #9c8a78;">
                            Mettez à jour vos prix et disponibilités
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('fournisseur.dashboard') }}" class="btn px-4 py-2 rounded-pill" 
                           style="background: #f0ebe4; color: #8b7355; border: 1px solid #e8e4da;">
                            <i class="bi bi-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de mise à jour des prix -->
    <div class="row">
        <div class="col-12">
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da; box-shadow: 0 4px 12px rgba(139, 115, 85, 0.05);">
                <form action="{{ route('fournisseur.prix.update') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background: #f5efe8;">
                                <tr>
                                    <th style="color: #5d4b38;">Médicament</th>
                                    <th style="color: #5d4b38;">DCI</th>
                                    <th style="color: #5d4b38;">Dosage</th>
                                    <th style="color: #5d4b38;">Prix actuel (TND)</th>
                                    <th style="color: #5d4b38;">Nouveau prix (TND)</th>
                                    <th style="color: #5d4b38;">Stock</th>
                                    <th style="color: #5d4b38;">Disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prix as $item)
                                <tr>
                                    <td style="color: #8b7355;">
                                        <strong>{{ $item->medicament->nom_commercial_fr ?? 'N/A' }}</strong>
                                        <input type="hidden" name="prix[{{ $item->id }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td style="color: #8b7355;">{{ $item->medicament->dci ?? 'N/A' }}</td>
                                    <td style="color: #8b7355;">{{ $item->medicament->dosage ?? '' }} {{ $item->medicament->unite ?? '' }}</td>
                                    <td style="color: #d4af37; font-weight: bold;">{{ number_format($item->prix_achat, 3) }}</td>
                                    <td>
                                        <input type="number" step="0.001" 
                                               name="prix[{{ $item->id }}][prix_achat]" 
                                               class="form-control" 
                                               value="{{ $item->prix_achat }}"
                                               style="width: 120px; border-color: #e8e4da;">
                                     </td>
                                    <td>
                                        <input type="number" step="1" 
                                               name="prix[{{ $item->id }}][stock_disponible]" 
                                               class="form-control" 
                                               value="{{ $item->stock_disponible }}"
                                               style="width: 100px; border-color: #e8e4da;">
                                     </td>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="prix[{{ $item->id }}][disponible]" 
                                                   class="form-check-input" 
                                                   value="1"
                                                   {{ $item->disponible ? 'checked' : '' }}
                                                   style="cursor: pointer;">
                                        </div>
                                     </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($prix->count() > 0)
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn rounded-pill px-5 py-2" 
                                style="background: #d4af37; color: white; border: none;">
                            <i class="bi bi-save me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                    @else
                    <div class="text-center py-5" style="color: #9c8a78;">
                        <i class="bi bi-box-seam fs-1 mb-3 d-block"></i>
                        <p>Aucun médicament associé à votre compte fournisseur.</p>
                        <a href="{{ route('fournisseur.dashboard') }}" class="btn rounded-pill px-4 py-2" 
                           style="background: #d4af37; color: white;">
                            Retour au dashboard
                        </a>
                    </div>
                    @endif
                </form>

                <!-- Pagination -->
                @if($prix->count() > 0)
                <div class="mt-4">
                    {{ $prix->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-light, .welcome-card {
        transition: all 0.3s ease;
    }
    .card-light:hover, .welcome-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .form-control:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
    }
    .form-check-input:checked {
        background-color: #d4af37;
        border-color: #d4af37;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(212, 175, 55, 0.05);
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
</style>
@endpush