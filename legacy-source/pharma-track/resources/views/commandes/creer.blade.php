@extends('layouts.app')

@section('title', 'Commander - ' . $medicament->nom_commercial_fr)
@section('page-title', 'Commander : ' . $medicament->nom_commercial_fr)
@section('page-icon', 'bi-cart')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card-light p-4 rounded-4" style="background: #ffffff; border: 1px solid #e8e4da;">
                <h3 class="fw-bold mb-3" style="color: #5d4b38;">Commander : {{ $medicament->nom_commercial_fr }}</h3>
                
                <!-- Informations médicament -->
                <div class="mb-4 p-3 rounded-3" style="background: #f5efe8;">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Médicament</small>
                            <p class="fw-bold mb-0" style="color: #5d4b38;">{{ $medicament->nom_commercial_fr }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">DCI</small>
                            <p class="mb-0" style="color: #9c8a78;">{{ $medicament->dci ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                
                <form id="commandeForm" method="POST" action="{{ route('commandes.passer') }}">
                    @csrf
                    <input type="hidden" name="medicament_id" value="{{ $medicament->id }}">
                    
                    <!-- Sélection du fournisseur - MENU DÉROULANT -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2" style="color: #5d4b38;">
                            <i class="bi bi-building me-1" style="color: #d4af37;"></i>
                            Choisir un fournisseur
                        </label>
                        
                        @if($fournisseurs->count() > 0)
                            <select name="fournisseur_medicament_id" id="fournisseurSelect" class="form-select" style="border-color: #e8e4da;" required>
                                <option value="">-- Sélectionnez un fournisseur --</option>
                                @foreach($fournisseurs as $fm)
                                    <option value="{{ $fm->id }}" 
                                            data-prix="{{ $fm->prix_achat }}"
                                            data-stock="{{ $fm->stock_disponible }}"
                                            data-delai="{{ $fm->delai_livraison ?? 7 }}"
                                            data-fournisseur="{{ $fm->fournisseur->raison_sociale ?? 'Fournisseur' }}">
                                        {{ $fm->fournisseur->raison_sociale }} - 
                                        {{ number_format($fm->prix_achat, 3) }} TND 
                                        (Stock: {{ $fm->stock_disponible }} unités)
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Choisissez le fournisseur pour cette commande</small>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Aucun fournisseur n'est associé à ce médicament.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Quantité -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #5d4b38;">Quantité</label>
                        <div class="input-group" style="max-width: 150px;">
                            <button type="button" class="btn" style="background: #f5efe8;" onclick="changerQuantite(-1)">-</button>
                            <input type="number" name="quantite" id="quantite" class="form-control text-center" value="1" min="1">
                            <button type="button" class="btn" style="background: #f5efe8;" onclick="changerQuantite(1)">+</button>
                        </div>
                    </div>
                    
                    <!-- Message de disponibilité -->
                    <div id="disponibiliteMessage" class="alert d-none"></div>
                    
                    <!-- Récapitulatif -->
                    <div class="mb-4 p-3 rounded-3" style="background: #f5efe8;">
                        <h6 class="fw-bold mb-3" style="color: #5d4b38;">Récapitulatif</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Fournisseur :</span>
                            <span class="fw-bold" id="fournisseurNom">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Prix unitaire :</span>
                            <span class="fw-bold" id="prixUnitaireSpan">0,000 TND</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Quantité :</span>
                            <span id="quantiteSpan">1</span>
                        </div>
                        <hr style="border-color: #e8e4da;">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total TTC :</span>
                            <span class="fw-bold" style="color: #d4af37; font-size: 1.2rem;" id="totalSpan">0,000 TND</span>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnCommander" class="btn w-100 rounded-pill" style="background: #d4af37; color: white;" disabled>
                        <i class="bi bi-check-circle me-2"></i>Passer commande
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const fournisseurSelect = document.getElementById('fournisseurSelect');
    const quantiteInput = document.getElementById('quantite');
    const prixSpan = document.getElementById('prixUnitaireSpan');
    const quantiteSpan = document.getElementById('quantiteSpan');
    const totalSpan = document.getElementById('totalSpan');
    const fournisseurNomSpan = document.getElementById('fournisseurNom');
    const btnCommander = document.getElementById('btnCommander');
    const messageDiv = document.getElementById('disponibiliteMessage');
    
    function updateFournisseurInfo() {
        const selectedOption = fournisseurSelect.options[fournisseurSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const prix = selectedOption.dataset.prix;
            const fournisseur = selectedOption.dataset.fournisseur;
            
            if (prix) {
                prixSpan.innerText = parseFloat(prix).toLocaleString() + ' TND';
                fournisseurNomSpan.innerText = fournisseur;
                updateTotal();
                verifierDisponibilite();
                btnCommander.disabled = false;
            }
        } else {
            prixSpan.innerText = '0,000 TND';
            fournisseurNomSpan.innerText = '-';
            totalSpan.innerText = '0,000 TND';
            btnCommander.disabled = true;
            messageDiv.classList.add('d-none');
        }
    }
    
    function updateTotal() {
        const selectedOption = fournisseurSelect.options[fournisseurSelect.selectedIndex];
        const quantite = parseInt(quantiteInput.value) || 1;
        
        if (selectedOption && selectedOption.value && selectedOption.dataset.prix) {
            const prix = parseFloat(selectedOption.dataset.prix);
            const total = prix * quantite;
            totalSpan.innerText = total.toLocaleString() + ' TND';
        }
    }
    
    function changerQuantite(delta) {
        let quantite = parseInt(quantiteInput.value) || 1;
        quantite = Math.max(1, quantite + delta);
        quantiteInput.value = quantite;
        quantiteSpan.innerText = quantite;
        updateTotal();
        verifierDisponibilite();
    }
    
    function verifierDisponibilite() {
        const selectedOption = fournisseurSelect.options[fournisseurSelect.selectedIndex];
        const quantite = parseInt(quantiteInput.value) || 1;
        
        if (!selectedOption || !selectedOption.value) {
            return;
        }
        
        const fournisseurMedicamentId = selectedOption.value;
        const stockDispo = parseInt(selectedOption.dataset.stock) || 0;
        
        if (quantite <= stockDispo) {
            messageDiv.className = 'alert alert-success rounded-3';
            messageDiv.innerHTML = '✅ Stock suffisant. Commande possible.';
            messageDiv.classList.remove('d-none');
            btnCommander.disabled = false;
        } else {
            messageDiv.className = 'alert alert-danger rounded-3';
            messageDiv.innerHTML = `❌ Stock insuffisant ! Seulement ${stockDispo} unités disponibles.`;
            messageDiv.classList.remove('d-none');
            btnCommander.disabled = true;
        }
    }
    
    // Écouteurs d'événements
    fournisseurSelect.addEventListener('change', updateFournisseurInfo);
    quantiteInput.addEventListener('input', function() {
        quantiteSpan.innerText = this.value;
        updateTotal();
        verifierDisponibilite();
    });
    
    // Initialisation
    quantiteSpan.innerText = quantiteInput.value;
    if (fournisseurSelect.options.length > 1) {
        fournisseurSelect.selectedIndex = 1;
        updateFournisseurInfo();
    }
</script>
@endsection