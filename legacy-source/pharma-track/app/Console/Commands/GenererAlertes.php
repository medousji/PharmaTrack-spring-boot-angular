<?php

namespace App\Console\Commands;

use App\Models\Alerte;
use Illuminate\Console\Command;

class GenererAlertes extends Command
{
    protected $signature = 'alertes:generer';
    protected $description = 'Génère automatiquement les alertes';

    public function handle()
    {
        $this->info('🔍 Vérification des alertes en cours...');
        
        $resultats = Alerte::verifierToutesLesAlertes();
        
        $this->info("✅ {$resultats['ruptures']} alertes de rupture créées");
        $this->info("✅ {$resultats['expirations']} alertes d'expiration créées");
        $this->info("✅ {$resultats['stocks_faibles']} alertes de stock faible créées");
        $this->info("📊 Total: {$resultats['total']} alertes créées");
        
        // Nettoyage
        $supprimees = Alerte::nettoyerAnciennesAlertes();
        $this->info("🧹 {$supprimees} anciennes alertes nettoyées");
        
        return Command::SUCCESS;
    }
}