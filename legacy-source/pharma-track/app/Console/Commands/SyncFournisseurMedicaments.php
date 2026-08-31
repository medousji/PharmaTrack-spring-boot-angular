<?php

namespace App\Console\Commands;

use App\Models\Fournisseur;
use App\Models\Medicament;
use App\Models\FournisseurMedicament;
use Illuminate\Console\Command;

class SyncFournisseurMedicaments extends Command
{
    protected $signature = 'fournisseur:sync-medicaments {fournisseurId?}';
    protected $description = 'Synchronise tous les médicaments pour un fournisseur';

    public function handle()
    {
        $fournisseurId = $this->argument('fournisseurId');
        
        if ($fournisseurId) {
            $fournisseurs = Fournisseur::where('id', $fournisseurId)->get();
        } else {
            $fournisseurs = Fournisseur::all();
        }
        
        $medicaments = Medicament::all();
        
        foreach ($fournisseurs as $fournisseur) {
            foreach ($medicaments as $medicament) {
                FournisseurMedicament::updateOrCreate(
                    [
                        'fournisseur_id' => $fournisseur->id,
                        'medicament_id' => $medicament->id
                    ],
                    [
                        'prix_achat' => rand(100, 1000) / 100,
                        'stock_disponible' => rand(50, 500),
                        'stock_minimum' => 10,
                        'seuil_reapprovisionnement' => 20,
                        'disponible' => true
                    ]
                );
            }
            $this->info("Médicaments synchronisés pour : {$fournisseur->raison_sociale}");
        }
        
        $this->info('Synchronisation terminée !');
    }
}