@extends('layouts.app')

@section('title', 'Conversation avec ' . $destinataire->name)
@section('page-title', 'Conversation avec ' . $destinataire->name)
@section('page-icon', 'bi-chat-dots')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-light p-4 rounded-4 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $destinataire->name }}</h5>
                        <small class="text-muted">{{ $destinataire->role }}</small>
                    </div>
                    <a href="{{ route('chat.index') }}" class="btn btn-sm rounded-pill" style="background: #f0ebe4; color: #8b7355;">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            
            <div class="card-light p-4 rounded-4 mb-3" style="height: 450px; overflow-y: auto; background: #ffffff;">
                @foreach($messages as $message)
                <div class="mb-3 {{ $message->expediteur_id == auth()->id() ? 'text-end' : '' }}">
                    <div class="d-inline-block p-3 rounded-3" style="max-width: 70%; {{ $message->expediteur_id == auth()->id() ? 'background: #d4af37; color: white;' : 'background: #f5efe8; color: #5d4b38;' }}">
                        <strong>{{ $message->expediteur->name }}</strong>
                        <p class="mb-0">{{ $message->message }}</p>
                        <small class="{{ $message->expediteur_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="card-light p-4 rounded-4">
                <form action="{{ route('chat.envoyer.direct') }}" method="POST">
                    @csrf
                    <input type="hidden" name="destinataire_id" value="{{ $destinataire->id }}">
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
@endsectionphp artisan route:clear
