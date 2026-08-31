<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicaments', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('dci')->nullable();
            $table->string('dosage')->nullable();
            $table->string('forme')->nullable();
            $table->string('presentation')->nullable();
            $table->decimal('ppv', 10, 2)->nullable();
            $table->decimal('ph', 10, 2)->nullable();
            $table->decimal('prix_br', 10, 2)->nullable();
            $table->decimal('prix_public', 10, 2)->nullable();
            $table->integer('quantite')->default(0);
            $table->string('conditionnement')->nullable();
            $table->text('remarque')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};