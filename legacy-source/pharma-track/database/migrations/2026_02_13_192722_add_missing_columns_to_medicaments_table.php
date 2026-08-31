<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicaments', function (Blueprint $table) {
            // ✅ Ajout de toutes les colonnes manquantes une par une
            // (elles seront ignorées si elles existent déjà)
            
            if (!Schema::hasColumn('medicaments', 'code_cip')) {
                $table->string('code_cip')->nullable()->unique()->after('id');
            }
            
            if (!Schema::hasColumn('medicaments', 'nom_commercial_fr')) {
                $table->string('nom_commercial_fr')->nullable()->after('dci');
            }
            
            if (!Schema::hasColumn('medicaments', 'nom_commercial_ar')) {
                $table->string('nom_commercial_ar')->nullable()->after('nom_commercial_fr');
            }
            
            if (!Schema::hasColumn('medicaments', 'unite')) {
                $table->string('unite')->nullable()->after('dosage');
            }
            
            if (!Schema::hasColumn('medicaments', 'categorie')) {
                $table->string('categorie')->nullable()->after('forme');
            }
            
            if (!Schema::hasColumn('medicaments', 'prix_achat')) {
                $table->decimal('prix_achat', 10, 3)->nullable()->after('prix_public');
            }
            
            if (!Schema::hasColumn('medicaments', 'prix_vente')) {
                $table->decimal('prix_vente', 10, 3)->nullable()->after('prix_achat');
            }
            
            if (!Schema::hasColumn('medicaments', 'delai_appro')) {
                $table->integer('delai_appro')->default(7)->nullable()->after('prix_vente');
            }
            
            if (!Schema::hasColumn('medicaments', 'stock_min')) {
                $table->integer('stock_min')->default(10)->nullable()->after('delai_appro');
            }
            
            if (!Schema::hasColumn('medicaments', 'stock_max')) {
                $table->integer('stock_max')->default(100)->nullable()->after('stock_min');
            }
            
            if (!Schema::hasColumn('medicaments', 'est_essentiel')) {
                $table->boolean('est_essentiel')->default(false)->after('stock_max');
            }
            
            if (!Schema::hasColumn('medicaments', 'est_controle')) {
                $table->boolean('est_controle')->default(false)->after('est_essentiel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicaments', function (Blueprint $table) {
            // Liste des colonnes à supprimer en cas de rollback
            $columns = [
                'code_cip',
                'nom_commercial_fr',
                'nom_commercial_ar',
                'unite',
                'categorie',
                'prix_achat',
                'prix_vente',
                'delai_appro',
                'stock_min',
                'stock_max',
                'est_essentiel',
                'est_controle'
            ];
            
            // Ne supprime que les colonnes qui existent
            foreach ($columns as $column) {
                if (Schema::hasColumn('medicaments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};