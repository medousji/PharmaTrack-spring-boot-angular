<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->boolean('relance_active')->default(true)->after('est_actif');
            $table->timestamp('derniere_relance')->nullable()->after('relance_active');
            $table->integer('nb_relances')->default(0)->after('derniere_relance');
        });
    }

    public function down()
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->dropColumn(['relance_active', 'derniere_relance', 'nb_relances']);
        });
    }
};