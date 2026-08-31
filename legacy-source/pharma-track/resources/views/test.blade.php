@extends('layouts.app')

@section('title', 'Test - Pharma Track')

@section('page-title', 'Page de test')
@section('page-icon', 'bi-check-circle')

@section('content')
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-check-circle"></i> Test réussi !</h5>
    </div>
    <div class="card-body text-center py-5">
        <i class="bi bi-check-circle display-1 text-success mb-4"></i>
        <h3 class="mb-3">Le système fonctionne correctement</h3>
        <p class="text-muted mb-4">
            Pharma Track est opérationnel et connecté à la base de données.
        </p>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="{{ route('home') }}" class="btn btn-primary w-100">
                    <i class="bi bi-house"></i> Page d'accueil
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="{{ route('dashboard') }}" class="btn btn-success w-100">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="{{ route('medicaments.index') }}" class="btn btn-info w-100">
                    <i class="bi bi-capsule"></i> Médicaments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection