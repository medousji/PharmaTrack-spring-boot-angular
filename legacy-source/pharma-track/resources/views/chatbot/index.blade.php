@extends('layouts.app')

@section('title', 'Assistant Pharma - Chatbot IA')
@section('page-title', 'Assistant Pharma')
@section('page-icon', 'bi-robot')

@section('breadcrumb')
<li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">Assistant IA</li>
@endsection

@section('content')
<style>
    .chat-container {
        height: 500px;
        overflow-y: auto;
        background: #f8f5f0;
        border-radius: 1rem;
        padding: 1rem;
    }
    .message {
        margin-bottom: 1rem;
        display: flex;
        animation: fadeInUp 0.3s ease;
    }
    .message.user {
        justify-content: flex-end;
    }
    .message.bot {
        justify-content: flex-start;
    }
    .message-bubble {
        max-width: 80%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        font-size: 0.9rem;
        line-height: 1.4;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .message.user .message-bubble {
        background: #d4af37;
        color: white;
        border-radius: 1rem 1rem 0.25rem 1rem;
    }
    .message.bot .message-bubble {
        background: white;
        color: #5d4b38;
        border: 1px solid #e8e4da;
        border-radius: 1rem 1rem 1rem 0.25rem;
    }
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 0.75rem 1rem;
        background: white;
        border: 1px solid #e8e4da;
        border-radius: 1rem;
        width: 60px;
    }
    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #9c8a78;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-10px); opacity: 1; }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .suggestion-chip {
        background: #f5efe8;
        border: 1px solid #e8e4da;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        color: #5d4b38;
        transition: all 0.2s;
        cursor: pointer;
    }
    .suggestion-chip:hover {
        background: #d4af37;
        color: white;
        transform: translateY(-2px);
        border-color: #d4af37;
    }
    .chat-header {
        background: linear-gradient(135deg, #5d4b38 0%, #8b7355 100%);
    }
    .btn-send {
        background: #d4af37;
        color: white;
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        transition: all 0.2s;
    }
    .btn-send:hover {
        background: #c4a030;
        transform: translateY(-2px);
    }
    .btn-send:disabled {
        background: #9c8a78;
        transform: none;
    }
    .btn-export {
        background: #9caf88;
        color: white;
        border-radius: 50px;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }
    .btn-export:hover {
        background: #7a9a6e;
        transform: translateY(-2px);
    }
</style>

<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-light p-0 rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #e8e4da;">
                
                <!-- En-tête -->
                <div class="chat-header p-4 text-white">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white p-2 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-robot fs-2" style="color: #d4af37;"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Assistant Pharma</h4>
                            <p class="mb-0 opacity-75 small">Votre assistant IA pour la gestion des médicaments</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('chatbot.export-pdf') }}" class="btn btn-export btn-sm" title="Exporter l'historique en PDF">
                                <i class="bi bi-download me-1"></i> PDF
                            </a>
                            <span class="badge ms-2" style="background: #9caf88; color: white;">
                                <i class="bi bi-check-circle-fill me-1"></i> En ligne
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Zone des messages -->
                <div id="chatContainer" class="chat-container">
                    <div class="message bot">
                        <div class="message-bubble">
                            👋 <strong>Bonjour !</strong><br><br>
                            Je suis votre assistant Pharma Track. Je peux vous aider à :<br><br>
                            📦 Consulter un stock<br>
                            🛒 Passer une commande<br>
                            📋 Voir les recommandations<br>
                            🚨 Voir les alertes<br>
                            📊 Afficher les statistiques<br><br>
                            <strong>Exemples :</strong><br>
                            • "Stock de Paracétamol"<br>
                            • "Commander 100 Amoxicilline"<br>
                            • "Recommandations"<br>
                            • "Alertes"<br>
                            • "Aide"<br><br>
                            Comment puis-je vous aider aujourd'hui ?
                        </div>
                    </div>
                </div>

                <!-- Suggestions - MODIFIÉES -->
                <div class="p-3 border-top" style="background: #ffffff;">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button class="suggestion-chip" onclick="envoyerSuggestion('Stock de Paracétamol')">📦 Stock</button>
                        <button class="suggestion-chip" onclick="envoyerSuggestion('Stocks faibles')">⚠️ Stocks faibles</button>
                        <button class="suggestion-chip" onclick="envoyerSuggestion('Recommandations')">📋 Recommandations</button>
                        <button class="suggestion-chip" onclick="envoyerSuggestion('Je veux commander')">🛒 Commander</button>
                        <button class="suggestion-chip" onclick="envoyerSuggestion('Alertes')">🚨 Alertes</button>
                        <button class="suggestion-chip" onclick="envoyerSuggestion('Statistiques')">📊 Statistiques</button>
                        <button class="suggestion-chip" onclick="envoyerSuggestion('Aide')">💡 Aide</button>
                    </div>
                    
                    <!-- Zone de saisie -->
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control" placeholder="Tapez votre message..." 
                               style="border-color: #e8e4da; border-radius: 50px 0 0 50px;" 
                               onkeypress="handleKeyPress(event)">
                        <button class="btn btn-send" id="sendBtn" onclick="envoyerMessage()">
                            <i class="bi bi-send"></i> Envoyer
                        </button>
                    </div>
                </div>

                <!-- Historique des conversations -->
                @if($historique->count() > 0)
                <div class="p-3 border-top" style="background: #faf7f2;">
                    <details>
                        <summary class="text-muted small" style="cursor: pointer;">
                            📜 Historique des conversations ({{ $historique->count() }})
                        </summary>
                        <div class="mt-2">
                            @foreach($historique as $conv)
                            <div class="small text-muted mb-2 p-2 rounded-2" style="background: #f5efe8;">
                                <div><strong>Vous :</strong> {{ \Str::limit($conv->question, 50) }}</div>
                                <div><strong>Assistant :</strong> {{ \Str::limit($conv->reponse, 80) }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ $conv->created_at->diffForHumans() }}</div>
                            </div>
                            @endforeach
                        </div>
                    </details>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const chatContainer = document.getElementById('chatContainer');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    let isTyping = false;

    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            envoyerMessage();
        }
    }

    function envoyerSuggestion(message) {
        messageInput.value = message;
        envoyerMessage();
    }

    function envoyerMessage() {
        const message = messageInput.value.trim();
        if (!message) return;

        ajouterMessage(message, 'user');
        messageInput.value = '';
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Envoi...';

        ajouterTypingIndicator();

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/assistant/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            retirerTypingIndicator();
            if (data.success) {
                ajouterMessage(data.reponse, 'bot');
            } else {
                ajouterMessage("❌ Désolé, une erreur s'est produite. Veuillez réessayer.", 'bot');
            }
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="bi bi-send"></i> Envoyer';
        })
        .catch(error => {
            console.error('Erreur:', error);
            retirerTypingIndicator();
            ajouterMessage("❌ Erreur de connexion. Vérifiez que le serveur est en cours d'exécution.", 'bot');
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="bi bi-send"></i> Envoyer';
        });
    }

    function ajouterMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.innerHTML = `<div class="message-bubble">${formatMessage(message)}</div>`;
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function formatMessage(message) {
        message = message.replace(/\n/g, '<br>');
        message = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        message = message.replace(/•/g, '•');
        return message;
    }

    function ajouterTypingIndicator() {
        isTyping = true;
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
        `;
        chatContainer.appendChild(typingDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function retirerTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
        isTyping = false;
    }

    messageInput.focus();

    window.addEventListener('load', function() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
@endsection

@push('styles')
<style>
    .card-light {
        transition: all 0.3s ease;
    }
    .card-light:hover {
        box-shadow: 0 15px 30px rgba(139, 115, 85, 0.1) !important;
    }
    .form-control:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }
    details summary {
        transition: all 0.2s;
    }
    details summary:hover {
        color: #d4af37;
    }
</style>
@endpush