<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('raison_sociale');
            $table->string('matricule_fiscal')->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email_pro')->nullable();
            $table->string('contact_nom')->nullable();
            $table->string('contact_telephone')->nullable();
            $table->string('site_web')->nullable();
            $table->integer('delai_livraison_moyen')->default(7);
            $table->decimal('frais_livraison', 10, 3)->default(0);
            $table->decimal('note', 3, 2)->nullable();
            $table->boolean('est_actif')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fournisseurs');
    }
};