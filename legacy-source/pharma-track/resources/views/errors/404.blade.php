<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <!-- Bootstrap Icons (pour l'icône) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f8f5f0;
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .error-card {
            background: #ffffff;
            border: 1px solid #e8e4da;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(139, 115, 85, 0.1);
            max-width: 500px;
            width: 100%;
            padding: 3rem 2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .error-card:hover {
            transform: translateY(-5px);
        }
        .icon-circle {
            background: #f5efe8;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-circle i {
            font-size: 4rem;
            color: #d4af37;
        }
        h1 {
            font-size: 5rem;
            font-weight: 800;
            margin: 0;
            line-height: 1;
            color: #5d4b38;
        }
        h1 span {
            color: #d4af37;
        }
        p {
            font-size: 1.2rem;
            color: #8b7355;
            margin: 1rem 0 2rem;
        }
        .btn-home {
            display: inline-block;
            background: #d4af37;
            color: white;
            text-decoration: none;
            padding: 0.9rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }
        .btn-home:hover {
            background: #c09c2e;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }
        .btn-home:active {
            transform: translateY(0);
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #9c8a78;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-circle">
            <i class="bi bi-emoji-frown"></i>
        </div>
        <h1>4<span>0</span>4</h1>
        <p>Oups ! La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <a href="{{ route('dashboard') }}" class="btn-home">
            <i class="bi bi-house-door me-2"></i>Retour au tableau de bord
        </a>
        <div class="footer">
            <i class="bi bi-capsule-pill me-1" style="color: #d4af37;"></i> Pharma Track
        </div>
    </div>
</body>
</html>