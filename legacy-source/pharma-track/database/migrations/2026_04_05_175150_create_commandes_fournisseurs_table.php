<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('commandes_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('numero_commande')->unique();
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->foreignId('pharmacie_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_reelle')->nullable();
            $table->enum('statut', ['en_attente', 'confirmee', 'preparation', 'expediee', 'livree', 'annulee'])->default('en_attente');
            $table->decimal('total_ht', 12, 3)->default(0);
            $table->decimal('total_ttc', 12, 3)->default(0);
            $table->decimal('frais_livraison', 10, 3)->default(0);
            $table->text('notes')->nullable();
            $table->string('adresse_livraison')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commandes_fournisseurs');
    }
};