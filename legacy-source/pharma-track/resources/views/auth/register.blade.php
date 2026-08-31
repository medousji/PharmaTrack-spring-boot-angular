{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - Pharma Track</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f5f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .register-card {
            max-width: 500px;
            width: 100%;
            background: #ffffff;
            border: 1px solid #e8e4da;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(139, 115, 85, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        .register-header {
            background: linear-gradient(135deg, #5d4b38 0%, #8b7355 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        .register-header .icon-circle {
            background: rgba(255,255,255,0.15);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .register-header .icon-circle i {
            font-size: 3rem;
            color: #d4af37;
        }
        .register-header h1 {
            font-weight: 700;
            margin-bottom: 0.25rem;
            font-size: 2rem;
        }
        .register-header p {
            opacity: 0.9;
            font-size: 0.95rem;
            margin-bottom: 0;
        }
        .register-body {
            padding: 2rem;
        }
        .form-label {
            font-weight: 600;
            color: #5d4b38;
            margin-bottom: 0.25rem;
        }
        .input-group-text {
            background: #f5efe8;
            border: 1px solid #e8e4da;
            border-right: none;
            color: #d4af37;
        }
        .form-control {
            border: 1px solid #e8e4da;
            border-left: none;
            padding: 0.75rem 0.5rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.1);
        }
        .btn-register {
            background: #d4af37;
            color: white;
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 50px;
            border: none;
            width: 100%;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        .btn-register:hover {
            background: #c09c2e;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
        }
        .footer-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        .footer-links a {
            color: #8b7355;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: #d4af37;
        }
        .alert {
            border-radius: 15px;
            border: none;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .password-hint {
            font-size: 0.85rem;
            color: #9c8a78;
            margin-top: 0.25rem;
        }
        .info-message {
            background: #e8f4f8;
            border-left: 4px solid #d4af37;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        .info-message i {
            color: #d4af37;
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .info-message .text {
            color: #5d4b38;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <div class="icon-circle">
                <i class="bi bi-capsule-pill"></i>
            </div>
            <h1>PHARMA TRACK</h1>
            <p>Créer votre compte</p>
        </div>
        <div class="register-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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

            <!-- Message d'information sur l'approbation admin -->
            <div class="info-message">
                <i class="bi bi-info-circle-fill"></i>
                <span class="text">
                    <strong>📝 Important :</strong> Après inscription, votre compte devra être <strong>approuvé par l'administrateur</strong> avant de pouvoir vous connecter. Vous recevrez une notification une fois votre compte validé.
                </span>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nom complet -->
                <div class="mb-4">
                    <label class="form-label">Nom complet</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Votre nom" required autofocus>
                    </div>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Adresse Email -->
                <div class="mb-4">
                    <label class="form-label">Adresse Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="votre@email.com" required>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="mb-4">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="password-hint">
                        <i class="bi bi-info-circle"></i> Minimum 8 caractères
                    </div>
                </div>

                <!-- Confirmation mot de passe -->
                <div class="mb-4">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Retapez votre mot de passe" required>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="bi bi-person-plus me-2"></i>Créer mon compte
                </button>
            </form>

            <div class="footer-links">
                <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Déjà un compte ? Se connecter</a>
                <span class="mx-2">|</span>
                <a href="{{ route('home') }}"><i class="bi bi-arrow-left me-1"></i>Retour à l'accueil</a>
            </div>
            <div class="text-center mt-3 small text-muted">
                <i class="bi bi-shield-check" style="color: #d4af37;"></i> Inscription sécurisée - Approbation admin requise
            </div>
        </div>
    </div>
</body>
</html>