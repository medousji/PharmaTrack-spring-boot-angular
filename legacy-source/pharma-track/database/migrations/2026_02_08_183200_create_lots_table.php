<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicament_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Informations de base du lot
            $table->string('numero_lot', 50);
            $table->date('date_fabrication');
            $table->date('date_peremption');
            
            // Quantités
            $table->integer('quantite_initial')
                  ->unsigned()
                  ->default(0);
            $table->integer('quantite_actuelle')
                  ->unsigned()
                  ->default(0);
            
            // Prix (optionnels pour commencer)
            $table->decimal('prix_achat', 10, 3)
                  ->nullable();
            $table->decimal('prix_vente', 10, 3)
                  ->nullable();
            
            // Fournisseur et réception
            $table->string('fournisseur', 255);
            $table->string('numero_facture', 100)
                  ->nullable();
            $table->date('date_reception');
            
            // Statut
            $table->enum('statut', [
                'actif',    // En stock et utilisable
                'perime',   // Date de péremption dépassée
                'epuise',   // Quantité épuisée
                'rappele',  // Rappelé par le fabricant
            ])->default('actif');
            
            // Emplacement (optionnel)
            $table->string('emplacement', 100)
                  ->nullable()
                  ->comment('Emplacement dans la pharmacie');
            
            // Observations (optionnel)
            $table->text('observations')
                  ->nullable();
            
            // Index pour les recherches fréquentes
            $table->index('numero_lot');
            $table->index('date_peremption');
            $table->index('statut');
            $table->index(['medicament_id', 'statut']);
            
            // Contrainte d'unicité
            $table->unique(['medicament_id', 'numero_lot']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
