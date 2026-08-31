<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('commande_fournisseur_lignes', function (Blueprint $table) {
            $table->integer('quantite_demandee')->nullable()->after('quantite');
        });
    }

    public function down()
    {
        Schema::table('commande_fournisseur_lignes', function (Blueprint $table) {
            $table->dropColumn('quantite_demandee');
        });
    }
};