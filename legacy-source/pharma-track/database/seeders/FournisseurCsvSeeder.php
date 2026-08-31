<?php

namespace Database\Seeders;

use App\Models\Fournisseur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FournisseurCsvSeeder extends Seeder
{
    public function run()
    {
        $csvFile = database_path('seeders/fournisseurs.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("❌ Fichier CSV non trouvé : $csvFile");
            return;
        }
        
        Schema::disableForeignKeyConstraints();
        Fournisseur::truncate();
        Schema::enableForeignKeyConstraints();
        
        // Lire le contenu du fichier
        $content = file_get_contents($csvFile);
        
        // Convertir en tableau de lignes
        $lines = explode("\n", trim($content));
        
        // Première ligne = en-têtes
        $headers = str_getcsv(array_shift($lines));
        
        $count = 0;
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            // Parser la ligne en gérant les guillemets
            $row = str_getcsv($line);
            
            // Si la ligne a moins de colonnes, on la corrige
            while (count($row) < count($headers)) {
                $row[] = null;
            }
            
            // Si la ligne a plus de colonnes, on la tronque
            if (count($row) > count($headers)) {
                $row = array_slice($row, 0, count($headers));
            }
            
            $data = array_combine($headers, $row);
            
            // Ignorer la ligne si pas de raison sociale
            if (empty($data['raison_sociale']) || $data['raison_sociale'] == 'id') {
                continue;
            }
            
            Fournisseur::updateOrCreate(
                ['raison_sociale' => $data['raison_sociale']],
                [
                    'pays_origine' => $data['pays_origine'] ?? null,
                    'specialite' => $data['specialite'] ?? null,
                    'email_pro' => $data['email'] ?? null,
                    'telephone' => $data['telephone'] ?? null,
                    'fax' => $data['fax'] ?? null,
                    'adresse' => $data['adresse'] ?? null,
                    'code_postal' => $data['code_postal'] ?? null,
                    'ville' => $data['ville'] ?? null,
                    'gouvernorat' => $data['gouvernorat'] ?? null,
                    'site_web' => $data['site_web'] ?? null,
                    'contact_nom' => $data['contact_nom'] ?? null,
                    'contact_poste' => $data['contact_poste'] ?? null,
                    'est_actif' => ($data['est_actif'] ?? 1) == 1,
                ]
            );
            
            $count++;
        }
        
        $this->command->info("✅ $count fournisseurs importés avec succès !");
    }
}