<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de conformité ONP</title>
    <style>
        /* Style général pour le PDF */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #f8f5f0; /* beige clair */
            margin: 0;
            padding: 20px;
            color: #5d4b38;
        }
        .container {
            max-width: 100%;
            background-color: #ffffff;
            border: 1px solid #e8e4da;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(139, 115, 85, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            background-color: #f5efe8;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo i {
            font-size: 30px;
            color: #d4af37;
        }
        .title h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #5d4b38;
        }
        .title p {
            margin: 5px 0 0;
            color: #9c8a78;
            font-size: 12px;
        }
        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-item {
            flex: 1 1 200px;
            background-color: #f5efe8;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stat-label {
            font-size: 12px;
            color: #9c8a78;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #5d4b38;
            margin: 5px 0 0;
        }
        .stat-value.warning {
            color: #e6a57e;
        }
        .table-container {
            margin-top: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th {
            background-color: #d4af37;
            color: white;
            padding: 10px;
            font-weight: 600;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e8e4da;
            color: #5d4b38;
        }
        tr:nth-child(even) {
            background-color: #faf7f2;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-success {
            background-color: #9caf88;
            color: white;
        }
        .badge-danger {
            background-color: #e6a57e;
            color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9c8a78;
            border-top: 1px solid #e8e4da;
            padding-top: 15px;
        }
        .footer p {
            margin: 3px 0;
        }
        .date {
            text-align: right;
            color: #9c8a78;
            font-size: 11px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête avec logo -->
        <div class="header">
            <div class="logo">
                <i class="bi bi-file-text"></i>
            </div>
            <div class="title">
                <h1>Rapport de conformité ONP</h1>
                <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

        <!-- Statistiques en cartes -->
        <div class="stats">
            <div class="stat-item">
                <div class="stat-label">Total médicaments</div>
                <div class="stat-value">{{ $total_medicaments ?? $stats['total_medicaments'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Lots périmés</div>
                <div class="stat-value warning">{{ $lots_perimes ?? $stats['lots_perimes'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Lots proches expiration</div>
                <div class="stat-value warning">{{ $lots_proches ?? $stats['lots_proches'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Alertes non lues</div>
                <div class="stat-value warning">{{ $alertes_non_lues ?? $stats['alertes_non_lues'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Tableau des médicaments -->
        <div class="table-container">
            <h3 style="color: #5d4b38; margin-top: 0;">Liste des médicaments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Code CIP</th>
                        <th>Nom</th>
                        <th>DCI</th>
                        <th>Stock</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicaments ?? [] as $med)
                    <tr>
                        <td>{{ $med->code_cip ?? '—' }}</td>
                        <td>{{ $med->nom_commercial_fr ?? $med->nom }}</td>
                        <td>{{ $med->dci ?? '—' }}</td>
                        <td>{{ $med->stock_actuel ?? $med->quantite ?? 0 }}</td>
                        <td>
                            @if(($med->stock_actuel ?? $med->quantite ?? 0) > 0)
                                <span class="badge badge-success">En stock</span>
                            @else
                                <span class="badge badge-danger">Rupture</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucun médicament</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>Document généré automatiquement par <strong>Pharma Track</strong> - Système de gestion des stocks médicaux</p>
            <p>Conforme aux réglementations de l'Office National de Pharmacie (ONP) - Tunisie</p>
        </div>
    </div>

    <!-- Pour inclure les icônes Bootstrap Icons (si DomPDF supporte les polices externes, sinon on peut les remplacer par des caractères) -->
    <!-- On utilise une police simple, les icônes ne sont pas essentielles -->
</body>
</html>