<!-- resources/views/commandes/index.blade.php -->
@extends('layouts.app')

@section('title', 'Choisir un médicament')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card p-4 rounded-4">
                <h3 class="fw-bold mb-3">Choisir un médicament à commander</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Médicament</th><th>DCI</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @foreach($medicaments as $med)
                            <tr>
                                <td>{{ $med->nom_commercial_fr }}</td>
                                <td>{{ $med->dci }}</td>
                                <td><a href="{{ route('commandes.creer', $med->id) }}" class="btn btn-sm rounded-pill" style="background:#d4af37;color:white;">Commander</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection