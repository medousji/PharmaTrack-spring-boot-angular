<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicaments', function (Blueprint $table) {
            // Liste des colonnes à ajouter
            $columns = [
                'forme_pharmaceutique' => ['type' => 'string', 'nullable' => true],
                'conditionnement' => ['type' => 'string', 'nullable' => true],
                'taux_remboursement' => ['type' => 'decimal', 'precision' => 10, 'scale' => 2, 'nullable' => true],
                'laboratoire' => ['type' => 'string', 'nullable' => true],
                'pays_origine' => ['type' => 'string', 'nullable' => true],
                'seuil_alerte' => ['type' => 'integer', 'default' => 30],
                'date_premption_alerte' => ['type' => 'date', 'nullable' => true],
                'classe_therapeutique' => ['type' => 'string', 'nullable' => true],
                'voie_administration' => ['type' => 'string', 'nullable' => true],
                'contre_indications' => ['type' => 'text', 'nullable' => true],
                'effets_indesirables' => ['type' => 'text', 'nullable' => true],
                'interactions_medicamenteuses' => ['type' => 'text', 'nullable' => true],
                'conditions_conservation' => ['type' => 'text', 'nullable' => true],
                'code_atc' => ['type' => 'string', 'nullable' => true],
                'est_psychotrope' => ['type' => 'boolean', 'default' => false],
                'est_ther_lourde' => ['type' => 'boolean', 'default' => false],
                'est_renouvelable' => ['type' => 'boolean', 'default' => true],
                'delai_renouvellement' => ['type' => 'integer', 'nullable' => true],
                'image_url' => ['type' => 'string', 'nullable' => true],
                'code_barre' => ['type' => 'string', 'nullable' => true],
                'est_generique' => ['type' => 'boolean', 'default' => false],
                'est_perime' => ['type' => 'boolean', 'default' => false],
                'date_peremption' => ['type' => 'date', 'nullable' => true],
                'statut' => ['type' => 'string', 'default' => 'actif'],
            ];

            foreach ($columns as $column => $config) {
                if (!Schema::hasColumn('medicaments', $column)) {
                    if ($config['type'] === 'string') {
                        $table->string($column)->nullable($config['nullable'] ?? true);
                    } elseif ($config['type'] === 'text') {
                        $table->text($column)->nullable($config['nullable'] ?? true);
                    } elseif ($config['type'] === 'integer') {
                        $table->integer($column)->default($config['default'] ?? 0);
                    } elseif ($config['type'] === 'decimal') {
                        $table->decimal($column, $config['precision'] ?? 10, $config['scale'] ?? 2)->nullable($config['nullable'] ?? true);
                    } elseif ($config['type'] === 'boolean') {
                        $table->boolean($column)->default($config['default'] ?? false);
                    } elseif ($config['type'] === 'date') {
                        $table->date($column)->nullable($config['nullable'] ?? true);
                    }
                }
            }

            // Ajouter les clés étrangères séparément
            if (!Schema::hasColumn('medicaments', 'medicament_reference_id')) {
                $table->foreignId('medicament_reference_id')->nullable()->constrained('medicaments');
            }
            
            if (!Schema::hasColumn('medicaments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users');
            }
            
            if (!Schema::hasColumn('medicaments', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicaments', function (Blueprint $table) {
            $columns = [
                'forme_pharmaceutique',
                'conditionnement',
                'taux_remboursement',
                'laboratoire',
                'pays_origine',
                'seuil_alerte',
                'date_premption_alerte',
                'classe_therapeutique',
                'voie_administration',
                'contre_indications',
                'effets_indesirables',
                'interactions_medicamenteuses',
                'conditions_conservation',
                'code_atc',
                'est_psychotrope',
                'est_ther_lourde',
                'est_renouvelable',
                'delai_renouvellement',
                'image_url',
                'code_barre',
                'est_generique',
                'medicament_reference_id',
                'est_perime',
                'date_peremption',
                'statut',
                'created_by',
                'updated_by',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('medicaments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};