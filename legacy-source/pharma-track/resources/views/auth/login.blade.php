{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - Pharma Track</title>
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
        .login-card {
            max-width: 450px;
            width: 100%;
            background: #ffffff;
            border: 1px solid #e8e4da;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(139, 115, 85, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        .login-header {
            background: linear-gradient(135deg, #5d4b38 0%, #8b7355 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        .login-header .icon-circle {
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
        .login-header .icon-circle i {
            font-size: 3rem;
            color: #d4af37;
        }
        .login-header h1 {
            font-weight: 700;
            margin-bottom: 0.25rem;
            font-size: 2rem;
        }
        .login-header p {
            opacity: 0.9;
            font-size: 0.95rem;
            margin-bottom: 0;
        }
        .login-body {
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
        .btn-login {
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
        .btn-login:hover {
            background: #c09c2e;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
        }
        .demo-accounts {
            background: #faf7f2;
            border-radius: 15px;
            padding: 1rem;
            margin: 1.5rem 0 1rem;
            border: 1px dashed #d4af37;
        }
        .demo-accounts h6 {
            color: #5d4b38;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .demo-accounts .badge {
            background: white;
            color: #5d4b38;
            border: 1px solid #e8e4da;
            padding: 0.5rem 0.75rem;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
        }
        .demo-accounts .badge:hover {
            background: #d4af37;
            color: white;
            border-color: #d4af37;
        }
        .demo-accounts .password-hint {
            font-size: 0.9rem;
            color: #8b7355;
            margin-top: 0.75rem;
            text-align: center;
        }
        .form-check-input:checked {
            background-color: #d4af37;
            border-color: #d4af37;
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
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="icon-circle">
                <i class="bi bi-capsule-pill"></i>
            </div>
            <h1>PHARMA TRACK</h1>
            <p>Système de gestion des stocks médicaux</p>
        </div>
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Adresse Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="exemple@email.com" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember" style="color: #5d4b38;">Se souvenir de moi</label>
                </div>

                <!-- Comptes de démonstration -->
                <div class="demo-accounts">
                    <h6><i class="bi bi-clock-history" style="color: #d4af37;"></i> Comptes de démonstration</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge" onclick="document.querySelector('input[name=email]').value='admin@pharmatrack.tn'">admin@pharmatrack.tn</span>
                        <span class="badge" onclick="document.querySelector('input[name=email]').value='sonninour@gmail.com'">sonninour@gmail.com</span>
                        <span class="badge" onclick="document.querySelector('input[name=email]').value='test@test.com'">test@test.com</span>
                    </div>
                    <div class="password-hint">
                        <i class="bi bi-key"></i> Mot de passe pour tous : <strong>password123</strong>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                </button>
            </form>

            <div class="footer-links">
                <a href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i>Créer un nouveau compte</a>
                <span class="mx-2">|</span>
                <a href="{{ route('home') }}"><i class="bi bi-arrow-left me-1"></i>Retour à l'accueil</a>
            </div>
            <div class="text-center mt-3 small text-muted">
                <i class="bi bi-shield-check" style="color: #d4af37;"></i> Connexion sécurisée
            </div>
        </div>
    </div>

    <!-- Script optionnel pour remplir l'email au clic sur les badges -->
    <script>
        // (optionnel) les badges ont déjà un onclick inline
    </script>
</body>
</html>