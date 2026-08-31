@extends('layouts.app')

@section('title', 'Prédictions de la demande - Pharma Track')
@section('page-title', '')
@section('page-icon', 'bi-robot')

@section('breadcrumb')
<li class="breadcrumb-item active" style="color: #2E7D64;" aria-current="page">Prédictions de la demande</li>
@endsection

@section('content')
<div class="container-fluid px-4 py-4" style="background: #F5F2EB; min-height: 100vh;">
    <!-- Titre avec icône robot -->
    <div class="d-flex align-items-center mb-4">
        <div class="rounded-circle p-3 me-3" style="background: #E8F3F0;">
            <i class="bi bi-robot fs-1" style="color: #2E7D64;"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0" style="color: #1B5E4A;">Prédictions de la demande</h1>
            <p class="mb-0" style="color: #6B7280;">Anticipez vos besoins en stock grâce à l'intelligence artificielle</p>
        </div>
        <div class="ms-auto">
            <span id="apiStatus" class="badge" style="background: #10B981; color: white; padding: 8px 15px;">
                <i class="bi bi-check-circle me-1"></i> Chargement...
            </span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #FFFFFF; border: 1px solid #E8E4DA; box-shadow: 0 4px 12px rgba(46, 125, 100, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #6B7280;">Stock total</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #1B5E4A;" id="totalStock">0</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #E8F3F0;">
                        <i class="bi bi-box-seam fs-1" style="color: #2E7D64;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #FFFFFF; border: 1px solid #E8E4DA; box-shadow: 0 4px 12px rgba(46, 125, 100, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #6B7280;">Prédiction 7j</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #F59E0B;" id="prediction7j">0</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #E8F3F0;">
                        <i class="bi bi-calendar-week fs-1" style="color: #F59E0B;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #FFFFFF; border: 1px solid #E8E4DA; box-shadow: 0 4px 12px rgba(46, 125, 100, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #6B7280;">Prédiction 30j</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #2E7D64;" id="prediction30j">0</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #E8F3F0;">
                        <i class="bi bi-calendar-month fs-1" style="color: #2E7D64;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 rounded-4" style="background: #FFFFFF; border: 1px solid #E8E4DA; box-shadow: 0 4px 12px rgba(46, 125, 100, 0.05);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label text-uppercase small fw-bold" style="color: #6B7280;">À commander</span>
                        <h2 class="stat-value fw-bold mb-0" style="color: #DC2626;" id="aCommander">0</h2>
                    </div>
                    <div class="stat-icon p-3 rounded-3" style="background: #E8F3F0;">
                        <i class="bi bi-cart fs-1" style="color: #DC2626;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique - Version Jauges inversée -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card p-4 rounded-4" style="background: #FFFFFF; border: 1px solid #E8E4DA; box-shadow: 0 4px 12px rgba(46, 125, 100, 0.05);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: #1B5E4A;">
                        <i class="bi bi-bar-chart-steps me-2" style="color: #2E7D64;"></i>Comparaison Prédiction vs Stock
                    </h5>
                    <button class="btn btn-sm rounded-pill" style="background: #2E7D64; color: white;" onclick="refreshData()">
                        <i class="bi bi-arrow-repeat me-1"></i> Actualiser
                    </button>
                </div>
                <div id="gaugeContainer" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Tableau des prédictions -->
    <div class="row">
        <div class="col-12">
            <div class="card p-4 rounded-4" style="background: #FFFFFF; border: 1px solid #E8E4DA; box-shadow: 0 4px 12px rgba(46, 125, 100, 0.05);">
                <h5 class="fw-bold mb-3" style="color: #1B5E4A;">
                    <i class="bi bi-table me-2" style="color: #2E7D64;"></i>Détail des prédictions
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="color: #6B7280; border-bottom: 2px solid #2E7D64;">Médicament</th>
                                <th style="color: #6B7280; border-bottom: 2px solid #2E7D64;">Stock actuel</th>
                                <th style="color: #6B7280; border-bottom: 2px solid #2E7D64;">Prédiction 7j</th>
                                <th style="color: #6B7280; border-bottom: 2px solid #2E7D64;">Prédiction 30j</th>
                                <th style="color: #6B7280; border-bottom: 2px solid #2E7D64;">Recommandation</th>
                                <th style="color: #6B7280; border-bottom: 2px solid #2E7D64;">Statut</th>
                            </tr>
                        </thead>
                        <tbody id="predictionsTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="loading-spinner"></div>
                                    <p class="mt-2 text-muted">Chargement des prédictions...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.loading-spinner {
    display: inline-block;
    width: 30px;
    height: 30px;
    border: 3px solid #E8F3F0;
    border-top-color: #2E7D64;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
.stat-card {
    transition: all 0.3s ease;
    animation: fadeInUp 0.5s ease;
    animation-fill-mode: both;
}
.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(46, 125, 100, 0.1) !important;
}
.stat-icon {
    transition: all 0.3s ease;
}
.stat-card:hover .stat-icon {
    transform: rotate(5deg) scale(1.1);
}
.card {
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 15px 30px rgba(46, 125, 100, 0.1) !important;
}
.table tbody tr {
    transition: all 0.3s ease;
}
.table tbody tr:hover {
    background: #E8F3F0 !important;
    transform: translateX(5px);
}
.badge-critique { background: #DC2626; color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; }
.badge-attention { background: #F59E0B; color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; }
.badge-normal { background: #10B981; color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; }
.gauge-item {
    transition: all 0.3s ease;
}
.gauge-item:hover {
    transform: translateX(5px);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection

@push('scripts')
<script>
let currentMaxValue = 5000; // Valeur max fixe - ne bouge jamais

document.addEventListener('DOMContentLoaded', function() {
    loadPredictions();
});

async function loadPredictions() {
    try {
        const response = await fetch('/api/predictions');
        const data = await response.json();
        
        if (data.success) {
            updateStats(data.statistiques);
            updateTable(data.medicaments);
            updateChart(data.medicaments);
            updateApiStatus(true);
        } else {
            updateApiStatus(false, data.message);
            console.error('API Error:', data);
        }
    } catch (error) {
        console.error('Erreur:', error);
        updateApiStatus(false, error.message);
        document.getElementById('predictionsTableBody').innerHTML = `
            <tr><td colspan="6" class="text-center py-5 text-danger">
                <i class="bi bi-exclamation-triangle fs-2 d-block mb-2"></i>
                Erreur de connexion à l'API<br>
                <small>Vérifiez que l'API Python est lancée (python app.py)</small>
            \n     </td>
            </tr>
        `;
    }
}

function updateStats(stats) {
    document.getElementById('totalStock').innerText = (stats.total_stock || 0).toLocaleString();
    document.getElementById('prediction7j').innerText = (stats.prediction_7j || 0).toLocaleString();
    document.getElementById('prediction30j').innerText = (stats.prediction_30j || 0).toLocaleString();
    document.getElementById('aCommander').innerText = (stats.a_commander || 0).toLocaleString();
}

function updateTable(medicaments) {
    const tbody = document.getElementById('predictionsTableBody');
    
    if (!medicaments || medicaments.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">Aucun médicament trouvé</td></tr>`;
        return;
    }
    
    tbody.innerHTML = medicaments.map(med => {
        let statutClass = 'badge-normal';
        let statutText = '✅ Normal';
        
        if (med.rupture_risque === 'rupture_immediate') {
            statutClass = 'badge-critique';
            statutText = '🔴 Rupture imminente';
        } else if (med.rupture_risque === 'critique') {
            statutClass = 'badge-critique';
            statutText = '🔴 Critique';
        } else if (med.rupture_risque === 'attention') {
            statutClass = 'badge-attention';
            statutText = '🟡 Attention';
        }
        
        return `
            <tr>
                <td>
                    <div class="d-flex flex-column">
                        <span class="fw-semibold" style="color: #1B5E4A;">${med.nom || 'Médicament'}</span>
                        <small style="color: #6B7280;">${med.dci || ''}</small>
                    </div>
                </td>
                <td style="color: #1B5E4A; font-weight: bold;">${(med.stock_actuel || 0).toLocaleString()}</td>
                <td style="color: #F59E0B;">${(med.prediction_7j || 0).toLocaleString()}</td>
                <td style="color: #2E7D64;">${(med.prediction_30j || 0).toLocaleString()}</td>
                <td>
                    ${med.quantite_recommandee > 0 ? 
                        `<span class="badge" style="background: #DC2626; color: white; padding: 5px 12px;">Acheter ${Math.ceil(med.quantite_recommandee)}</span>` : 
                        `<span class="badge" style="background: #10B981; color: white; padding: 5px 12px;">Stock suffisant</span>`
                    }
                </td>
                <td><span class="${statutClass}">${statutText}</span></td>
            </tr>
        `;
    }).join('');
}

function updateChart(medicaments) {
    if (!medicaments || medicaments.length === 0) {
        document.getElementById('gaugeContainer').innerHTML = '<div class="text-center py-5 text-muted">Aucune donnée disponible</div>';
        return;
    }
    
    // Prendre les 10 premiers médicaments
    const topMeds = medicaments.slice(0, 10);
    
    let html = '';
    
    for (const med of topMeds) {
        const stock = med.stock_actuel || 0;
        const pred = med.prediction_30j || 0;
        
        // Calculer les largeurs en pourcentage (base fixe sur currentMaxValue)
        const predWidth = Math.min((pred / currentMaxValue) * 100, 100);
        const stockWidth = Math.min((stock / currentMaxValue) * 100, 100);
        
        // Déterminer la classe de statut
        let statutColor = '#10B981';
        if (med.rupture_risque === 'critique' || med.rupture_risque === 'rupture_immediate') {
            statutColor = '#DC2626';
        } else if (med.rupture_risque === 'attention') {
            statutColor = '#F59E0B';
        }
        
        html += `
            <div class="gauge-item mb-4 p-2 rounded-3" style="border-left: 3px solid ${statutColor};">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle me-2" style="width: 10px; height: 10px; background-color: ${statutColor};"></div>
                        <span class="fw-semibold" style="color: #1B5E4A;">${med.nom || 'Médicament'}</span>
                        <small class="text-muted ms-2">${med.dci || ''}</small>
                    </div>
                    <div>
                        <span class="badge me-2" style="background: #2E7D64; color: white;">📈 Prédiction: ${pred.toLocaleString()}</span>
                        <span class="badge" style="background: #F59E0B; color: white;">📦 Stock: ${stock.toLocaleString()}</span>
                    </div>
                </div>
                <div class="progress" style="height: 35px; background-color: #E8F3F0; border-radius: 10px;">
                    <div class="progress-bar d-flex align-items-center justify-content-end pe-2" 
                         role="progressbar" 
                         style="width: ${predWidth}%; background-color: #2E7D64; border-radius: 10px 0 0 10px;"
                         aria-valuenow="${pred}" aria-valuemin="0" aria-valuemax="${currentMaxValue}">
                        ${predWidth > 15 ? `<span class="fw-bold">📈 ${pred.toLocaleString()}</span>` : ''}
                    </div>
                    <div class="progress-bar d-flex align-items-center justify-content-start ps-2" 
                         role="progressbar" 
                         style="width: ${stockWidth}%; background-color: #F59E0B;"
                         aria-valuenow="${stock}" aria-valuemin="0" aria-valuemax="${currentMaxValue}">
                        ${stockWidth > 15 ? `<span class="fw-bold">📦 ${stock.toLocaleString()}</span>` : ''}
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">0</small>
                    <small class="text-muted">${Math.round(currentMaxValue / 4).toLocaleString()}</small>
                    <small class="text-muted">${Math.round(currentMaxValue / 2).toLocaleString()}</small>
                    <small class="text-muted">${Math.round(currentMaxValue * 3 / 4).toLocaleString()}</small>
                    <small class="text-muted">${currentMaxValue.toLocaleString()}</small>
                </div>
                ${med.quantite_recommandee > 0 ? `
                    <div class="mt-2 text-end">
                        <span class="badge" style="background: #DC2626; color: white;">
                            ⚠️ Recommandation: Acheter ${Math.ceil(med.quantite_recommandee)} unités
                        </span>
                    </div>
                ` : ''}
            </div>
        `;
    }
    
    document.getElementById('gaugeContainer').innerHTML = html;
}

function updateApiStatus(isConnected, errorMessage = '') {
    const statusBadge = document.getElementById('apiStatus');
    if (isConnected) {
        statusBadge.style.background = '#10B981';
        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i> API Connectée';
    } else {
        statusBadge.style.background = '#DC2626';
        statusBadge.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> API Déconnectée';
        if (errorMessage) console.error('API Error:', errorMessage);
    }
}

function refreshData() {
    document.getElementById('predictionsTableBody').innerHTML = `
        <tr><td colspan="6" class="text-center py-5">
            <div class="loading-spinner"></div>
            <p class="mt-2 text-muted">Actualisation...</p>
        \n     </td>
        </tr>
    `;
    document.getElementById('gaugeContainer').innerHTML = `
        <div class="text-center py-5">
            <div class="loading-spinner"></div>
            <p class="mt-2 text-muted">Chargement du graphique...</p>
        </div>
    `;
    loadPredictions();
}
</script>
@endpush