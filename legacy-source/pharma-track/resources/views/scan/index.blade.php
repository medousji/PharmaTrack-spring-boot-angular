@extends('layouts.app')

@section('title', 'Scan - Pharma Track')
@section('page-title', 'Scanner Code-barres / QR')
@section('page-icon', 'bi-upc-scan')

@section('content')
<div class="container-fluid px-4 py-4" style="background: #f8f5f0; min-height: 100vh;">
    <!-- Breadcrumb personnalisé -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" style="color: #9c8a78; text-decoration: none;">
                            <i class="bi bi-house-door me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #d4af37;" aria-current="page">
                        Scanner Code-barres / QR
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff; border: 1px solid #e8e4da;">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0" style="color: #5d4b38;">
                        <i class="bi bi-upc-scan me-2" style="color: #d4af37;"></i>Scanner un code
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert" style="background: #d4edda; border: none; color: #155724;">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" style="background: #f8d7da; border: none; color: #721c24;">
                            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Scanner vidéo -->
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0" style="color: #5d4b38;">
                                <i class="bi bi-camera-video me-2" style="color: #d4af37;"></i>Scan par caméra
                            </h6>
                            <div>
                                <button type="button" class="btn btn-sm rounded-pill px-3" id="startScannerBtn" 
                                        style="background: #d4af37; color: white; border: none; display: none;">
                                    <i class="bi bi-play-circle me-1"></i>Démarrer
                                </button>
                                <button type="button" class="btn btn-sm rounded-pill px-3" id="stopScannerBtn" 
                                        style="background: #e6a57e; color: white; border: none; display: none;">
                                    <i class="bi bi-stop-circle me-1"></i>Arrêter
                                </button>
                            </div>
                        </div>
                        <div id="qr-reader" style="width: 100%; border-radius: 12px; overflow: hidden; min-height: 300px; background: #f5efe8; border: 2px dashed #e8e4da;"></div>
                        <div id="qr-reader-results" class="mt-3"></div>
                    </div>

                    <hr style="border-color: #e8e4da;">

                    <!-- Saisie manuelle -->
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3" style="color: #5d4b38;">
                            <i class="bi bi-keyboard me-2" style="color: #d4af37;"></i>Ou saisissez manuellement
                        </h6>
                        <form action="{{ route('scan.traiter') }}" method="POST" id="scanForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0" style="border-color: #e8e4da;">
                                            <i class="bi bi-upc-scan" style="color: #d4af37;"></i>
                                        </span>
                                        <input type="text" 
                                               name="code" 
                                               id="codeInput" 
                                               class="form-control border-start-0 ps-0" 
                                               placeholder="Code CIP ou numéro de lot"
                                               style="border-color: #e8e4da; background: transparent;">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn w-100 py-2 rounded-pill" 
                                            style="background: #d4af37; color: white; border: none;">
                                        <i class="bi bi-search me-2"></i>Rechercher
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr style="border-color: #e8e4da;">

                    <!-- Simulateur de scan (test) -->
                    <div class="mt-4 text-center">
                        <h6 class="fw-semibold mb-3" style="color: #5d4b38;">
                            <i class="bi bi-joystick me-2" style="color: #d4af37;"></i>Simulateur de scan (test)
                        </h6>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 test-scan" 
                                    data-code="LOT-001"
                                    style="border-color: #9c8a78; color: #5d4b38;">
                                <i class="bi bi-box me-1"></i>LOT-001
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 test-scan" 
                                    data-code="3400931234567"
                                    style="border-color: #9c8a78; color: #5d4b38;">
                                <i class="bi bi-capsule me-1"></i>CIP 3400931234567
                            </button>
                            @php
                                $premierLot = App\Models\Lot::first();
                                $premierMedicament = App\Models\Medicament::first();
                            @endphp
                            @if($premierLot)
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 test-scan" 
                                    data-code="{{ $premierLot->numero_lot }}"
                                    style="border-color: #9c8a78; color: #5d4b38;">
                                <i class="bi bi-box me-1"></i>Premier lot
                            </button>
                            @endif
                            @if($premierMedicament && $premierMedicament->code_cip)
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 test-scan" 
                                    data-code="{{ $premierMedicament->code_cip }}"
                                    style="border-color: #9c8a78; color: #5d4b38;">
                                <i class="bi bi-capsule me-1"></i>Premier médicament
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styles pour le scanner et les animations */
    #qr-reader {
        border: none !important;
    }
    #qr-reader video {
        border-radius: 12px;
        width: 100%;
    }
    #qr-reader__scan_region {
        background: #f5efe8;
    }
    #qr-reader__dashboard {
        padding: 10px !important;
    }
    #qr-reader__status_span {
        background: #d4af37 !important;
        color: white !important;
        border-radius: 5px !important;
        padding: 5px 15px !important;
    }
    .test-scan {
        transition: all 0.3s ease;
    }
    .test-scan:hover {
        background: #d4af37 !important;
        color: white !important;
        border-color: #d4af37 !important;
        transform: translateY(-2px);
    }
    .btn-outline-secondary:focus {
        box-shadow: none;
    }
    .form-control:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('codeInput');
    const scanForm = document.getElementById('scanForm');
    const startBtn = document.getElementById('startScannerBtn');
    const stopBtn = document.getElementById('stopScannerBtn');
    const readerElement = document.getElementById('qr-reader');
    const resultsDiv = document.getElementById('qr-reader-results');

    let html5QrCode = null;
    let isScanning = false;

    // Vérifier si la bibliothèque est chargée
    if (typeof Html5Qrcode !== 'undefined') {
        console.log('Html5Qrcode chargé');
        startBtn.style.display = 'inline-block';
        
        try {
            html5QrCode = new Html5Qrcode("qr-reader");
            
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            };

            const onScanSuccess = (decodedText, decodedResult) => {
                resultsDiv.innerHTML = `
                    <div class="alert alert-success rounded-3" style="background: #d4edda; border: none; color: #155724;">
                        <i class="bi bi-check-circle me-2"></i>Code détecté : ${decodedText}
                    </div>
                `;
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(() => {
                        isScanning = false;
                        startBtn.style.display = 'inline-block';
                        stopBtn.style.display = 'none';
                    }).catch(err => console.log(err));
                }
                codeInput.value = decodedText;
                setTimeout(() => scanForm.submit(), 1000);
            };

            startBtn.addEventListener('click', function() {
                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    (errorMessage) => { /* ignore */ }
                ).then(() => {
                    isScanning = true;
                    startBtn.style.display = 'none';
                    stopBtn.style.display = 'inline-block';
                }).catch(err => {
                    readerElement.innerHTML = `
                        <div class="alert alert-danger rounded-3 m-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Impossible d'accéder à la caméra : ${err.message}
                        </div>
                    `;
                });
            });

            stopBtn.addEventListener('click', function() {
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(() => {
                        isScanning = false;
                        startBtn.style.display = 'inline-block';
                        stopBtn.style.display = 'none';
                        resultsDiv.innerHTML = '';
                    }).catch(err => console.log(err));
                }
            });
        } catch (e) {
            readerElement.innerHTML = `
                <div class="alert alert-warning rounded-3 m-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Scanner non disponible. Utilisez la saisie manuelle.
                </div>
            `;
        }
    } else {
        readerElement.innerHTML = `
            <div class="alert alert-warning rounded-3 m-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Scanner non disponible. Utilisez la saisie manuelle.
            </div>
        `;
    }

    // Boutons de test
    document.querySelectorAll('.test-scan').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            codeInput.value = this.dataset.code;
            scanForm.submit();
        });
    });
});
</script>
@endpush