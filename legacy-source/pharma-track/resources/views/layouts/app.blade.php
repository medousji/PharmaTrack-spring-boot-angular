<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pharma Track')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Html5 QrCode Scanner -->
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Hover.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hover.css/2.3.1/css/hover-min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --primary-soft: rgba(13, 110, 253, 0.1);
            --success-soft: rgba(25, 135, 84, 0.1);
            --warning-soft: rgba(255, 193, 7, 0.1);
            --beige-bg: #f8f5f0;
            --beige-border: #e8e4da;
            --beige-text: #5d4b38;
            --beige-accent: #d4af37;
            --beige-light: #9c8a78;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--beige-bg);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--beige-accent) !important;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #5d4b38 0%, #8b7355 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(212, 175, 55, 0.3);
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.2);
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        .alert-badge-pulse {
            animation: pulse-red 1.5s infinite;
            display: inline-block;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            transform: translateY(-5px);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            animation: slideInDown 0.5s ease;
        }

        .page-title {
            font-weight: 700;
            color: var(--beige-text);
            margin-bottom: 0;
        }

        .stat-card {
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease;
            animation-fill-mode: both;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 1rem 2rem rgba(0,0,0,0.2) !important;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            transition: all 0.3s ease;
        }

        .badge:hover {
            transform: scale(1.1);
        }

        .btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled,
        .disabled {
            opacity: 0.65;
            cursor: not-allowed !important;
            pointer-events: none;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffecb5;
            color: #664d03;
        }

        .table th {
            font-weight: 600;
            color: var(--beige-text);
            border-top: none;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.05);
            transform: translateX(5px);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-30px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse-animation {
            animation: pulse 2s ease infinite;
        }

        #qr-reader {
            border: none !important;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        #qr-reader:hover {
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        #qr-reader video {
            border-radius: 10px;
        }
        
        #qr-reader__scan_region {
            background: #f8f5f0;
        }
        
        #qr-reader__dashboard {
            padding: 10px !important;
        }
        
        #qr-reader__status_span {
            background: var(--beige-accent) !important;
            color: white !important;
            border-radius: 5px !important;
            padding: 5px 15px !important;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .icon-circle:hover {
            transform: rotate(360deg);
        }

        .bg-primary-soft {
            background-color: var(--primary-soft);
        }

        .bg-success-soft {
            background-color: var(--success-soft);
        }

        .bg-warning-soft {
            background-color: var(--warning-soft);
        }

        .breadcrumb-item a {
            color: #9c8a78;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .breadcrumb-item a:hover {
            color: #d4af37;
        }
        
        .breadcrumb-item.active {
            color: #d4af37;
        }
    </style>

    @stack('styles')
</head>
<body>
    @if(Auth::check())
    @php
        // Alertes pour admin/pharmacien (exclure ruptures fournisseurs)
        $alertesNonLues = \App\Models\Alerte::whereNotIn('type', ['inscription', 'approbation', 'rupture', 'stock_faible'])
            ->where('est_lue', false)
            ->count();
        
        $userRole = Auth::user()->role;
        
        // Récupérer les commandes non traitées pour le fournisseur
        $commandesNonTraitees = 0;
        $alertesFournisseur = 0;
        $fournisseurId = null;
        
        if ($userRole === 'fournisseur') {
            $fournisseurId = \App\Models\Fournisseur::where('user_id', Auth::id())->value('id');
            if ($fournisseurId) {
                $commandesNonTraitees = \App\Models\CommandeFournisseur::where('fournisseur_id', $fournisseurId)
                    ->whereIn('statut', ['en_attente', 'confirmee', 'preparation'])
                    ->count();
                $alertesFournisseur = \App\Models\Alerte::where('est_lue', false)
                    ->where('donnees_concernees->fournisseur_id', $fournisseurId)
                    ->count();
            }
        }
        
        // Récupérer les messages non lus
        $messagesNonLus = \App\Models\Message::where('destinataire_id', Auth::id())->where('est_lu', false)->count();
        
        // Récupérer les inscriptions en attente (pour admin)
        $pendingApprovals = 0;
        if ($userRole === 'admin') {
            $pendingApprovals = \App\Models\User::where('is_approved', false)
                ->where('role', 'visiteur')
                ->count();
        }
        
        // Dernier ID de commande pour les notifications
        $lastCommandeId = \App\Models\CommandeFournisseur::max('id') ?? 0;
    @endphp
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar p-0">
                <div class="position-sticky pt-3">
                    <div class="p-3">
                        <a class="navbar-brand text-white hvr-grow" href="{{ route('dashboard') }}">
                            <i class="bi bi-capsule-pill"></i> PHARMA TRACK
                        </a>
                        <hr class="text-white-50">
                        <div class="text-white small mb-3 animate__animated animate__fadeIn">
                            <i class="bi bi-person-circle"></i> 
                            <strong>{{ Auth::user()->name }}</strong>
                            <br>
                            <span class="badge bg-light text-dark mt-1">
                                @if($userRole === 'admin')
                                    Administrateur
                                @elseif($userRole === 'pharmacien')
                                    Pharmacien
                                @elseif($userRole === 'fournisseur')
                                    Fournisseur
                                @else
                                    Visiteur
                                @endif
                            </span>
                        </div>
                    </div>

                    <ul class="nav flex-column">
                        <!-- Tableau de Bord -->
                        <li class="nav-item">
                            <div class="text-white-50 small px-3 mb-2">TABLEAU DE BORD</div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} hvr-sweep-to-right" 
                               href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Tableau de Bord
                            </a>
                        </li>

                        <!-- Messages (Chat) - visible pour tous -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" 
                               href="{{ route('chat.index') }}">
                                <i class="bi bi-chat-dots"></i> Messages
                                @if($messagesNonLus > 0)
                                    <span class="badge bg-danger float-end pulse-animation" style="background: #dc3545 !important;">
                                        {{ $messagesNonLus }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <!-- ⭐⭐⭐ ASSISTANT IA (visible pour admin et pharmacien) ⭐⭐⭐ -->
                        @if($userRole === 'admin' || $userRole === 'pharmacien')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('chatbot.*') ? 'active' : '' }}" 
                               href="{{ route('chatbot.index') }}">
                                <i class="bi bi-robot"></i> Assistant IA
                            </a>
                        </li>
                        @endif

                        <!-- Gestion (visible pour admin et pharmacien uniquement) -->
                        @if($userRole === 'admin' || $userRole === 'pharmacien')
                        <li class="nav-item mt-3">
                            <div class="text-white-50 small px-3 mb-2">GESTION</div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('medicaments.*') ? 'active' : '' }}" 
                               href="{{ route('medicaments.index') }}">
                                <i class="bi bi-capsule"></i> Médicaments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lots.*') ? 'active' : '' }}" 
                               href="{{ route('lots.index') }}">
                                <i class="bi bi-box"></i> Lots
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('commandes.selection') ? 'active' : '' }}" 
                               href="{{ route('commandes.selection') }}">
                                <i class="bi bi-cart"></i> Commander
                            </a>
                        </li>
                        @endif
                        
                        <!-- Scan Code (visible pour tous sauf visiteur) -->
                        @if($userRole !== 'visiteur')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('scan.*') ? 'active' : '' }}" 
                               href="{{ route('scan.index') }}">
                                <i class="bi bi-upc-scan"></i> Scan Code
                            </a>
                        </li>
                        @endif
                        
                        <!-- Prédictions IA (visible pour admin et pharmacien) -->
                        @if($userRole === 'admin' || $userRole === 'pharmacien')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('predictions.*') ? 'active' : '' }}" 
                               href="{{ route('predictions.index') }}">
                                <i class="bi bi-robot"></i> Prédictions IA
                            </a>
                        </li>
                        @endif
                        
                        <!-- Conformité ONP (visible pour admin et pharmacien) -->
                        @if($userRole === 'admin' || $userRole === 'pharmacien')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('conformite.*') ? 'active' : '' }}" 
                               href="{{ route('conformite.index') }}">
                                <i class="bi bi-shield-check"></i> Conformité ONP
                            </a>
                        </li>
                        @endif
                        
                        <!-- Alertes (visible pour admin et pharmacien) -->
                        @if($userRole === 'admin' || $userRole === 'pharmacien')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('alertes.*') ? 'active' : '' }}" 
                               href="{{ route('alertes.index') }}">
                                <i class="bi bi-bell"></i> Alertes
                                @if($alertesNonLues > 0)
                                    <span class="badge bg-danger float-end alert-badge-pulse" style="background: #dc3545 !important;">
                                        {{ $alertesNonLues }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                        
                        <!-- ESPACE FOURNISSEUR (visible uniquement pour fournisseur) -->
                        @if($userRole === 'fournisseur')
                        <li class="nav-item mt-3">
                            <div class="text-white-50 small px-3 mb-2">ESPACE FOURNISSEUR</div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('fournisseur.dashboard') ? 'active' : '' }}" 
                               href="{{ route('fournisseur.dashboard') }}">
                                <i class="bi bi-building"></i> Dashboard Fournisseur
                                @if($commandesNonTraitees > 0)
                                    <span class="badge bg-danger float-end pulse-animation" style="background: #dc3545 !important;">
                                        {{ $commandesNonTraitees }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('alertes.index') ? 'active' : '' }}" 
                               href="{{ route('alertes.index') }}">
                                <i class="bi bi-bell"></i> Mes Alertes
                                @if($alertesFournisseur > 0)
                                    <span class="badge bg-danger float-end pulse-animation" style="background: #dc3545 !important;">
                                        {{ $alertesFournisseur }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                        
                        <!-- Administration (visible uniquement pour admin) -->
                        @if($userRole === 'admin')
                        <li class="nav-item mt-3">
                            <div class="text-white-50 small px-3 mb-2">ADMINISTRATION</div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-gear"></i> Administration
                            </a>
                        </li>
                        <!-- Approbations des inscriptions -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.pending') ? 'active' : '' }}" 
                               href="{{ route('admin.users.pending') }}">
                                <i class="bi bi-person-check"></i> Approbations
                                @if($pendingApprovals > 0)
                                    <span class="badge bg-warning float-end pulse-animation" style="background: #e6a57e !important;">
                                        {{ $pendingApprovals }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                        
                        <!-- COMPTE (visible pour tous) -->
                        <li class="nav-item mt-3">
                            <div class="text-white-50 small px-3 mb-2">COMPTE</div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" 
                               href="{{ route('profile.index') }}">
                                <i class="bi bi-person"></i> Mon Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link border-0 bg-transparent text-start w-100" 
                                        style="color: rgba(255, 255, 255, 0.8);">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <!-- Page Header -->
                <div class="page-header pt-4">
                    <div>
                        @hasSection('page-title')
                        <h1 class="page-title">
                            <i class="bi @yield('page-icon', 'bi-house') me-2" style="color: #d4af37;"></i>
                            @yield('page-title')
                        </h1>
                        @endif
                        
                        <!-- Breadcrumb -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">
                                        <i class="bi bi-house-door me-1"></i>Accueil
                                    </a>
                                </li>
                                @if(View::hasSection('breadcrumb'))
                                    @yield('breadcrumb')
                                @elseif(View::hasSection('page-title'))
                                    <li class="breadcrumb-item active" aria-current="page">
                                        @yield('page-title')
                                    </li>
                                @endif
                            </ol>
                        </nav>
                    </div>
                    
                    @if($userRole === 'visiteur')
                    <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Mode visiteur :</strong> Accès en lecture seule.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                </div>

                <!-- Messages Flash -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('password_success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('password_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Content Section -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast de notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header" style="background: #e6a57e; color: white;">
                <i class="bi bi-bell-fill me-2"></i>
                <strong class="me-auto">Pharma Track</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <div id="notificationMessage"></div>
                <a href="{{ route('fournisseur.commandes') }}" class="btn btn-sm mt-2 w-100" style="background: #d4af37; color: white;">
                    Voir les commandes
                </a>
            </div>
        </div>
    </div>

    <script>
        // Vérifier les nouvelles commandes toutes les 30 secondes (pour les fournisseurs)
        let lastCommandeId = {{ $lastCommandeId }};
        
        setInterval(function() {
            @if($userRole === 'fournisseur')
            fetch('/api/fournisseur/nouvelles-commandes')
                .then(response => response.json())
                .then(data => {
                    if (data.nouvelle_commande && data.commande_id > lastCommandeId) {
                        lastCommandeId = data.commande_id;
                        const toast = new bootstrap.Toast(document.getElementById('notificationToast'));
                        document.getElementById('notificationMessage').innerHTML = 
                            '<i class="bi bi-exclamation-triangle-fill me-2" style="color: #e6a57e;"></i>' +
                            '<strong>Nouvelle commande reçue !</strong><br>' +
                            'Commande #' + data.numero_commande + ' de ' + data.total + ' TND';
                        toast.show();
                        
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    }
                })
                .catch(error => console.error('Erreur:', error));
            @endif
        }, 30000);
    </script>

    @else
    <!-- Layout pour les pages non authentifiées -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm animate__animated animate__fadeInDown">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}" style="color: #d4af37;">
                <i class="bi bi-capsule-pill"></i> PHARMA TRACK
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link px-3" href="{{ route('login') }}" style="color: #5d4b38;">
                    <i class="bi bi-box-arrow-in-right"></i> Connexion
                </a>
                <a class="nav-link px-3" href="{{ route('register') }}" style="color: #5d4b38;">
                    <i class="bi bi-person-plus"></i> Inscription
                </a>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
    @endif

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS (Animate On Scroll) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Initialisation des animations -->
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
            easing: 'ease-in-out'
        });
    </script>

    <!-- Scripts spécifiques aux pages -->
    @stack('scripts')

    <!-- Script de vérification de rôle -->
    <script>
    async function checkUserRole() {
        try {
            const response = await fetch('/api/user-role');
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erreur:', error);
            return { role: null, isVisitor: true };
        }
    }

    function disableVisitorElements() {
        document.querySelectorAll('a[href*="medicaments/create"], a[href*="medicaments/edit"], a[href*="medicaments/destroy"], a[href*="medicaments/delete"]').forEach(el => {
            el.classList.add('disabled');
            el.style.pointerEvents = 'none';
            el.style.opacity = '0.65';
            el.title = 'Mode visiteur : Action non autorisée';
        });
        
        document.querySelectorAll('a[href*="lots/create"], a[href*="lots/edit"], a[href*="lots/destroy"], a[href*="lots/delete"]').forEach(el => {
            el.classList.add('disabled');
            el.style.pointerEvents = 'none';
            el.style.opacity = '0.65';
            el.title = 'Mode visiteur : Action non autorisée';
        });
        
        document.querySelectorAll('a[href*="alertes/create"], a[href*="alertes/edit"], a[href*="alertes/destroy"], a[href*="alertes/delete"]').forEach(el => {
            el.classList.add('disabled');
            el.style.pointerEvents = 'none';
            el.style.opacity = '0.65';
            el.title = 'Mode visiteur : Action non autorisée';
        });
        
        document.querySelectorAll('.btn-danger').forEach(el => {
            if (el.closest('form[action*="logout"]')) return;
            if (window.location.pathname.includes('/profile')) return;
            
            if (el.tagName === 'BUTTON') {
                el.disabled = true;
                el.title = 'Mode visiteur : Action non autorisée';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        checkUserRole().then(userData => {
            if (userData.isVisitor) {
                disableVisitorElements();
            }
        });

        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                try {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } catch(e) {}
            });
        }, 5000);
    });
    </script>
</body>
</html>