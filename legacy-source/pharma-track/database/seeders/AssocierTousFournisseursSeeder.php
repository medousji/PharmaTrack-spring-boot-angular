<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssocierTousFournisseursSeeder extends Seeder
{
    public function run()
    {
        // Récupérer tous les médicaments et fournisseurs
        $medicaments = DB::table('medicaments')->pluck('id');
        $fournisseurs = DB::table('fournisseurs')->pluck('id');
        
        foreach ($medicaments as $medicamentId) {
            foreach ($fournisseurs as $fournisseurId) {
                // Vérifier si l'association existe déjà
                $exists = DB::table('fournisseur_medicaments')
                    ->where('medicament_id', $medicamentId)
                    ->where('fournisseur_id', $fournisseurId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('fournisseur_medicaments')->insert([
                        'fournisseur_id' => $fournisseurId,
                        'medicament_id' => $medicamentId,
                        'prix_achat' => rand(100, 1000) / 100,
                        'stock_disponible' => rand(50, 500),
                        'delai_livraison' => rand(3, 10),
                        'disponible' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        $this->command->info('✅ Tous les médicaments ont été associés à tous les fournisseurs !');
    }
}