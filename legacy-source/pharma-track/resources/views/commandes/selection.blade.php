@extends('layouts.app')

@section('title', 'Choisir un médicament - Pharma Track')
@section('page-title', 'Choisir un médicament')
@section('page-icon', 'bi-cart')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff;">
                <h2 class="fw-bold mb-0" style="color: #5d4b38;">
                    <i class="bi bi-cart me-2" style="color: #d4af37;"></i>
                    Commander un médicament
                </h2>
                <p class="mb-0 mt-2">Sélectionnez le médicament que vous souhaitez commander</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card-light p-4 rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: #f5efe8;">
                            <tr>
                                <th>Médicament</th>
                                <th>DCI</th>
                                <th>Forme</th>
                                <th>Dosage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicaments as $med)
                            <tr>
                                <td><strong>{{ $med->nom_commercial_fr }}</strong></td>
                                <td>{{ $med->dci ?? 'N/A' }}</td>
                                <td>{{ $med->forme ?? 'N/A' }}</td>
                                <td>{{ $med->dosage ?? '' }} {{ $med->unite ?? '' }}</td>
                                <td>
                                    <a href="{{ route('commandes.creer', $med->id) }}" 
                                       class="btn btn-sm rounded-pill" 
                                       style="background: #d4af37; color: white;">
                                        <i class="bi bi-cart"></i> Commander
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination sans les flèches -->
                <div class="mt-4">
                    @if($medicaments->lastPage() > 1)
                        <nav>
                            <ul class="pagination justify-content-center">
                                {{-- Seulement les numéros de page, pas de Previous/Next --}}
                                @for($i = 1; $i <= $medicaments->lastPage(); $i++)
                                    <li class="page-item {{ $i == $medicaments->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $medicaments->url($i) }}" style="color: #5d4b38; border-color: #e8e4da; margin: 0 3px; border-radius: 8px;">
                                            {{ $i }}
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                        </nav>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .page-item.active .page-link {
        background-color: #d4af37 !important;
        border-color: #d4af37 !important;
        color: white !important;
    }
    
    .page-link:hover {
        background-color: #f5efe8 !important;
        color: #d4af37 !important;
    }
</style>
@endpush