<?php

namespace Database\Seeders;

use App\Models\Medicament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportMedicamentsCsvSeeder extends Seeder
{
    public function run()
    {
        // Désactiver les contraintes pour éviter les erreurs de clé étrangère
        Schema::disableForeignKeyConstraints();
        
        // Vider la table avant import (optionnel, commentez si vous voulez conserver les données existantes)
        DB::table('medicaments')->truncate();

        $csvFile = database_path('seeders/medicaments_complet.csv');
        if (!file_exists($csvFile)) {
            $this->command->error("Fichier CSV introuvable : {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        $headers = fgetcsv($file); // Lire l'en-tête

        // Récupérer la liste réelle des colonnes de la table
        $tableColumns = Schema::getColumnListing('medicaments');

        $rowCount = 0;
        $errors = [];

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);

            // 1. Supprimer les colonnes qui n'existent pas dans la table
            $data = array_intersect_key($data, array_flip($tableColumns));

            // 1bis. Ne jamais forcer la clé primaire depuis le CSV (autoincrement)
            unset($data['id']);

            // 2. Nettoyer la colonne taux_remboursement (enlever le %)
            if (isset($data['taux_remboursement'])) {
                $data['taux_remboursement'] = (int) str_replace('%', '', $data['taux_remboursement']);
            }

            // 3. Convertir les champs numériques (éviter les chaînes vides)
            $numericFields = [
                'quantite', 'stock_min', 'stock_max', 'delai_appro',
                'prix_achat', 'prix_vente', 'prix_br', 'ppv', 'ph', 'seuil_alerte',
                'est_essentiel', 'est_controle', 'est_generique', 'est_perime',
                'est_psychotrope', 'est_ther_lourde', 'est_renouvelable'
            ];
            foreach ($numericFields as $field) {
                if (isset($data[$field]) && $data[$field] !== '') {
                    if (in_array($field, ['est_essentiel', 'est_controle', 'est_generique', 'est_perime', 'est_psychotrope', 'est_ther_lourde', 'est_renouvelable'])) {
                        $data[$field] = (int) $data[$field];
                    } else {
                        $data[$field] = (float) $data[$field];
                    }
                } else {
                    $data[$field] = null;
                }
            }

            // 4. Remplacer les chaînes vides par null pour tous les champs
            foreach ($data as $key => $value) {
                if ($value === '') {
                    $data[$key] = null;
                }
            }

            // 5. Insérer
            try {
                Medicament::create($data);
                $rowCount++;
            } catch (\Exception $e) {
                $errors[] = "Ligne " . ($rowCount + 2) . " : " . $e->getMessage();
            }
        }

        fclose($file);
        Schema::enableForeignKeyConstraints();

        $this->command->info("✅ {$rowCount} médicaments importés avec succès.");
        if (!empty($errors)) {
            $this->command->warn("⚠️ Erreurs rencontrées :");
            foreach ($errors as $err) {
                $this->command->error($err);
            }
        }
    }
}