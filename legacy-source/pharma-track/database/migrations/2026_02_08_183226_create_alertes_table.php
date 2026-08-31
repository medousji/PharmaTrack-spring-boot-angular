<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // peremption, rupture, rappel, epidemie
            $table->string('niveau'); // bas, moyen, critique
            $table->text('message');
            $table->json('donnees_concernees')->nullable();
            $table->boolean('est_lue')->default(false);
            $table->timestamp('resolue_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};