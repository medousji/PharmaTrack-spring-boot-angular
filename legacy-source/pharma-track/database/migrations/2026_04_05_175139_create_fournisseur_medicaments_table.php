<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fournisseur_medicaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicament_id')->constrained()->onDelete('cascade');
            $table->string('reference_fournisseur')->nullable();
            $table->decimal('prix_achat', 10, 3);
            $table->decimal('prix_public', 10, 3)->nullable();
            $table->integer('stock_disponible')->default(0);
            $table->integer('delai_livraison')->nullable();
            $table->boolean('disponible')->default(true);
            $table->date('derniere_mise_a_jour')->nullable();
            $table->timestamps();
            
            $table->unique(['fournisseur_id', 'medicament_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fournisseur_medicaments');
    }
};