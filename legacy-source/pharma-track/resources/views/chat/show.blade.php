@extends('layouts.app')

@section('title', 'Chat - Commande #' . $commande->numero_commande)
@section('page-title', 'Chat - Commande #' . $commande->numero_commande)
@section('page-icon', 'bi-chat-dots')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: #5d4b38;">
                            <i class="bi bi-chat-dots me-2" style="color: #d4af37;"></i>Commande #{{ $commande->numero_commande }}
                        </h2>
                        <p class="mb-0 mt-2" style="color: #9c8a78;">
                            Total: {{ number_format($commande->total_ttc, 3) }} TND | 
                            Statut: <span class="badge" style="background: #d4af37;">{{ ucfirst($commande->statut) }}</span>
                        </p>
                        <p class="mb-0 mt-1 small" style="color: #9c8a78;">
                            Fournisseur: <strong style="color: #5d4b38;">{{ $commande->fournisseur->raison_sociale ?? 'N/A' }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('chat.index') }}" class="btn rounded-pill px-4 py-2" style="background: #f0ebe4; color: #8b7355;">
                        <i class="bi bi-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Carte des médicaments -->
            <div class="card-light p-4 rounded-4 mb-3" style="background: #f5efe8;">
                <h6 class="fw-bold mb-3" style="color: #5d4b38;">
                    <i class="bi bi-capsule me-2" style="color: #d4af37;"></i>Médicaments commandés
                </h6>
                @if($commande->lignes && $commande->lignes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="color: #8b7355;">Médicament</th>
                                    <th style="color: #8b7355;">Dosage</th>
                                    <th style="color: #8b7355;">Laboratoire</th>
                                    <th style="color: #8b7355;">Quantité</th>
                                    <th style="color: #8b7355;">Prix unitaire</th>
                                    <th style="color: #8b7355;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commande->lignes as $ligne)
                                    <tr>
                                        <td style="color: #5d4b38;">
                                            <strong>{{ $ligne->medicament->nom ?? 'Médicament' }}</strong>
                                        </td>
                                        <td style="color: #9c8a78;">
                                            {{ $ligne->medicament->dosage ?? '-' }}
                                        </td>
                                        <td style="color: #9c8a78;">
                                            {{ $ligne->medicament->laboratoire ?? '-' }}
                                        </td>
                                        <td style="color: #5d4b38;">
                                            x{{ $ligne->quantite }}
                                        </td>
                                        <td style="color: #9c8a78;">
                                            {{ number_format($ligne->prix_unitaire ?? 0, 3) }} TND
                                        </td>
                                        <td style="color: #d4af37; font-weight: bold;">
                                            {{ number_format(($ligne->prix_unitaire ?? 0) * $ligne->quantite, 3) }} TND
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="border-top: 2px solid #e8e4da;">
                                    <td colspan="5" class="text-end fw-bold" style="color: #5d4b38;">Total :</td>
                                    <td class="fw-bold" style="color: #d4af37;">
                                        {{ number_format($commande->total_ttc ?? 0, 3) }} TND
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3" style="color: #9c8a78;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Aucun médicament associé à cette commande
                    </div>
                @endif
            </div>

            <!-- Zone des messages -->
            <div class="card-light p-4 rounded-4 mb-3" style="height: 450px; overflow-y: auto; background: #ffffff;">
                @if($commande->messages && $commande->messages->count() > 0)
                    @foreach($commande->messages as $message)
                    <div class="mb-3 {{ $message->expediteur_id == auth()->id() ? 'text-end' : '' }}">
                        <div class="d-inline-block p-3 rounded-3" style="max-width: 70%; {{ $message->expediteur_id == auth()->id() ? 'background: #d4af37; color: white;' : 'background: #f5efe8; color: #5d4b38;' }}">
                            <strong>{{ $message->expediteur->name ?? 'Inconnu' }}</strong>
                            <p class="mb-0">{{ $message->message }}</p>
                            <small class="{{ $message->expediteur_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                {{ $message->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-5" style="color: #9c8a78;">
                        <i class="bi bi-chat-dots fs-1 mb-3 d-block" style="color: #e8e4da;"></i>
                        <p>Aucun message pour cette commande</p>
                        <small>Soyez le premier à envoyer un message</small>
                    </div>
                @endif
            </div>

            <!-- Formulaire d'envoi de message -->
            <div class="card-light p-4 rounded-4">
                <form action="{{ route('chat.envoyer', $commande->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <textarea name="message" class="form-control" rows="2" placeholder="Votre message..." required style="border-color: #e8e4da;"></textarea>
                        <button type="submit" class="btn rounded-pill px-4" style="background: #d4af37; color: white;">
                            <i class="bi bi-send"></i> Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection