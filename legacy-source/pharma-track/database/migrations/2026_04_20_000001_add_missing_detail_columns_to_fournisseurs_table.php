<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->string('pays_origine')->nullable();
            $table->string('specialite')->nullable();
            $table->string('fax')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->string('gouvernorat')->nullable();
            $table->string('contact_poste')->nullable();
        });
    }

    public function down()
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->dropColumn([
                'pays_origine', 'specialite', 'fax', 'code_postal',
                'ville', 'gouvernorat', 'contact_poste',
            ]);
        });
    }
};