<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historique des conversations</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .conversation { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .question { background: #f0f0f0; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .reponse { background: #e8f5e9; padding: 10px; border-radius: 5px; }
        .date { color: #666; font-size: 10px; text-align: right; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pharma Track - Assistant IA</h1>
        <p>Historique des conversations de {{ $user->name }}</p>
        <p>Généré le {{ $date }}</p>
    </div>
    
    @foreach($historique as $conv)
    <div class="conversation">
        <div class="question">
            <strong>❓ Vous :</strong> {{ $conv->question }}
            <div class="date">{{ $conv->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="reponse">
            <strong>🤖 Assistant :</strong> {{ $conv->reponse }}
        </div>
    </div>
    @endforeach
    
    <div class="footer">
        <p>Document généré automatiquement par Pharma Track</p>
    </div>
</body>
</html>