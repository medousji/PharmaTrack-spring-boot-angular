@extends('layouts.app')

@section('title', 'Messages - Pharma Track')
@section('page-title', 'Messages')
@section('page-icon', 'bi-chat-dots')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <h2 class="fw-bold mb-0" style="color: #5d4b38;">
                    <i class="bi bi-chat-dots me-2" style="color: #d4af37;"></i>Conversations
                </h2>
                <p class="mb-0 mt-2" style="color: #9c8a78;">Discutez avec les pharmaciens, administrateurs et fournisseurs</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card-light p-4 rounded-4">

                <!-- Formulaire pour envoyer un nouveau message direct -->
                <div class="card mb-4 p-3" style="background: #f5efe8; border-radius: 15px;">
                    <h6 class="fw-bold mb-2" style="color: #5d4b38;">
                        <i class="bi bi-plus-circle me-1" style="color: #d4af37;"></i> Nouveau message
                    </h6>
                    <form action="{{ route('chat.envoyer.direct') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <select name="destinataire_id" class="form-select" style="border-color: #e8e4da;" required>
                                <option value="">-- Choisir un destinataire --</option>
                                @if(auth()->user()->role === 'fournisseur')
                                    @php
                                        $destinataires = \App\Models\User::whereIn('role', ['admin', 'pharmacien'])->get();
                                    @endphp
                                    @foreach($destinataires as $dest)
                                        <option value="{{ $dest->id }}">{{ $dest->name }} ({{ $dest->role }})</option>
                                    @endforeach
                                @else
                                    @php
                                        $destinataires = \App\Models\Fournisseur::with('user')->where('est_actif', true)->get();
                                    @endphp
                                    @foreach($destinataires as $fournisseur)
                                        @if($fournisseur->user)
                                            <option value="{{ $fournisseur->user_id }}">{{ $fournisseur->raison_sociale }} (Fournisseur)</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="input-group">
                            <textarea name="message" class="form-control" rows="2" placeholder="Votre message..." required style="border-color: #e8e4da;"></textarea>
                            <button type="submit" class="btn rounded-pill px-4" style="background: #d4af37; color: white;">
                                <i class="bi bi-send"></i> Envoyer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Conversations liées aux commandes -->
                <h6 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-cart me-1"></i> Conversations liées aux commandes
                </h6>
                @forelse($commandes as $commande)
                <a href="{{ route('chat.show', $commande->id) }}" class="text-decoration-none">
                    <div class="d-flex align-items-start p-3 mb-2 rounded-3" style="background: #f5efe8; transition: all 0.3s ease;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 50px; height: 50px; background: #d4af37;">
                            <i class="bi bi-chat-dots fs-4 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="color: #5d4b38;">Commande #{{ $commande->numero_commande }}</strong>
                            
                            <!-- ⭐ AFFICHAGE DES MÉDICAMENTS AJOUTÉ ⭐ -->
                            @if($commande->lignes && $commande->lignes->count() > 0)
                                <div class="mt-1 mb-2">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($commande->lignes->take(3) as $ligne)
                                            <span class="badge px-2 py-1" style="background: #e8e4da; color: #5d4b38; font-weight: normal;">
                                                💊 {{ $ligne->medicament->nom ?? 'Médicament' }}
                                                @if($ligne->medicament->dosage)
                                                    {{ $ligne->medicament->dosage }}
                                                @endif
                                                x{{ $ligne->quantite }}
                                            </span>
                                        @endforeach
                                        @if($commande->lignes->count() > 3)
                                            <span class="badge px-2 py-1" style="background: #d4af37; color: white;">
                                                +{{ $commande->lignes->count() - 3 }} autres
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="small text-warning mt-1 mb-1">
                                    <i class="bi bi-exclamation-triangle"></i> Aucun médicament
                                </div>
                            @endif
                            
                            <p class="small text-muted mb-0">
                                {{ $commande->messages->last()->message ?? 'Aucun message' }}
                            </p>
                            <small style="color: #9c8a78;">
                                {{ $commande->messages->last() ? $commande->messages->last()->created_at->diffForHumans() : '' }}
                            </small>
                        </div>
                        @php
                            $nonLus = $commande->messages->where('destinataire_id', auth()->id())->where('est_lu', false)->count();
                        @endphp
                        @if($nonLus > 0)
                            <span class="badge bg-danger rounded-pill">{{ $nonLus }}</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="text-center py-3" style="color: #9c8a78;">
                    <small>Aucune conversation liée aux commandes</small>
                </div>
                @endforelse

                <!-- Conversations directes -->
                <h6 class="fw-bold mb-3 mt-4" style="color: #5d4b38;">
                    <i class="bi bi-chat-dots me-1"></i> Conversations directes
                </h6>
                @forelse($conversations as $conv)
                <a href="{{ route('chat.conversation', $conv['id']) }}" class="text-decoration-none">
                    <div class="d-flex align-items-center p-3 mb-2 rounded-3" style="background: #f5efe8; transition: all 0.3s ease;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: #9caf88;">
                            <i class="bi bi-person fs-4 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="color: #5d4b38;">{{ $conv['nom'] }} <small class="text-muted">({{ $conv['role'] }})</small></strong>
                            <p class="small text-muted mb-0">
                                {{ $conv['dernier_message'] ?? 'Aucun message' }}
                            </p>
                            <small style="color: #9c8a78;">
                                {{ $conv['date'] ? $conv['date']->diffForHumans() : '' }}
                            </small>
                        </div>
                        @if($conv['non_lus'] > 0)
                            <span class="badge bg-danger rounded-pill">{{ $conv['non_lus'] }}</span>
                        @endif
                    </div>
                </a>
                @empty
                <div class="text-center py-3" style="color: #9c8a78;">
                    <small>Aucune conversation directe</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection