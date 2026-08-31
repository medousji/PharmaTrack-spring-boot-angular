<?php

namespace Database\Seeders;

use App\Models\Medicament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UpdatePrixMedicaments extends Seeder
{
    public function run()
    {
        // Chemin vers votre fichier CSV
        $csvFile = database_path('seeders/medicaments_complet.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("❌ Fichier CSV non trouvé : {$csvFile}");
            return;
        }
        
        $this->command->info("📂 Lecture du fichier CSV...");
        
        // Lire tout le fichier
        $content = file_get_contents($csvFile);
        $lines = explode("\n", $content);
        
        if (empty($lines)) {
            $this->command->error("❌ Fichier vide");
            return;
        }
        
        // Déterminer le séparateur (; ou ,)
        $firstLine = $lines[0];
        $separator = str_contains($firstLine, ';') ? ';' : ',';
        $this->command->info("📋 Séparateur détecté : '{$separator}'");
        
        // Lire les en-têtes
        $headers = str_getcsv($firstLine, $separator);
        $this->command->info("📋 Colonnes : " . implode(', ', $headers));
        
        // Trouver les index
        $nomIndex = null;
        $nomCommercialIndex = null;
        $prixIndex = null;
        $quantiteIndex = null;
        
        foreach ($headers as $index => $colonne) {
            $colonne = trim($colonne);
            if ($colonne == 'nom') $nomIndex = $index;
            if ($colonne == 'nom_commercial_fr') $nomCommercialIndex = $index;
            if ($colonne == 'prix_vente') $prixIndex = $index;
            if ($colonne == 'quantite') $quantiteIndex = $index;
        }
        
        $updated = 0;
        $notFound = 0;
        
        // Parcourir les lignes (sauter la première ligne d'en-tête)
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;
            
            $row = str_getcsv($line, $separator);
            
            // S'assurer que la ligne a assez de colonnes
            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), null);
            }
            
            $nom = $nomIndex !== null ? ($row[$nomIndex] ?? null) : null;
            $nomCommercial = $nomCommercialIndex !== null ? ($row[$nomCommercialIndex] ?? null) : null;
            $prix = $prixIndex !== null ? floatval(str_replace(',', '.', $row[$prixIndex] ?? 0)) : null;
            $quantite = $quantiteIndex !== null ? intval($row[$quantiteIndex] ?? 0) : null;
            
            // Chercher le médicament
            $medicament = null;
            
            if ($nom) {
                $medicament = Medicament::where('nom', $nom)->first();
            }
            
            if (!$medicament && $nomCommercial) {
                $medicament = Medicament::where('nom_commercial_fr', $nomCommercial)->first();
            }
            
            if ($medicament) {
                if ($prix !== null && $prix > 0) {
                    $medicament->prix_vente = $prix;
                    $medicament->save();
                    $this->command->line("✅ {$medicament->nom} : prix = {$prix} TND");
                }
                if ($quantite !== null && $quantite > 0) {
                    $medicament->quantite = $quantite;
                    $medicament->save();
                    $this->command->line("✅ {$medicament->nom} : stock = {$quantite}");
                }
                $updated++;
            } else {
                $this->command->warn("❌ Non trouvé : {$nom} / {$nomCommercial}");
                $notFound++;
            }
        }
        
        $this->command->info("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("🎉 MISE À JOUR TERMINÉE !");
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("✅ Médicaments mis à jour : {$updated}");
        if ($notFound > 0) {
            $this->command->warn("⚠️ Médicaments non trouvés : {$notFound}");
        }
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    }
}