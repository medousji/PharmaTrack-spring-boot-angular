<?php

namespace App\Console\Commands;

use App\Models\Lot;
use App\Models\Alerte;
use Illuminate\Console\Command;
use Carbon\Carbon;

class VerifierPeremptions extends Command
{
    protected $signature = 'peremptions:verifier';
    protected $description = 'Vérifie les lots proches de péremption et crée des alertes';

    public function handle()
    {
        $lots = Lot::whereNotNull('date_peremption')->get();
        $count = 0;

        foreach ($lots as $lot) {
            $jours = $lot->jours_avant_peremption;
            if ($jours !== null && $jours > 0 && $jours <= 30) {
                // Vérifier si une alerte existe déjà pour ce lot
                $existe = Alerte::where('type', 'expiration')
                    ->where('est_lue', false)
                    ->whereRaw('JSON_EXTRACT(donnees_concernees, "$.lot_id") = ?', [$lot->id])
                    ->exists();

                if (!$existe) {
                    $niveau = $jours <= 7 ? 'critique' : ($jours <= 15 ? 'eleve' : 'moyen');
                    Alerte::create([
                        'type' => 'expiration',
                        'niveau' => $niveau,
                        'message' => "Le lot {$lot->numero_lot} expire dans {$jours} jours.",
                        'donnees_concernees' => [
                            'lot_id' => $lot->id,
                            'jours_restants' => $jours,
                        ],
                        // Pas de colonne lot_id, on ne met que dans donnees_concernees
                    ]);
                    $count++;
                }
            }
        }

        $this->info("$count alertes d'expiration créées.");
        return Command::SUCCESS;
    }
}