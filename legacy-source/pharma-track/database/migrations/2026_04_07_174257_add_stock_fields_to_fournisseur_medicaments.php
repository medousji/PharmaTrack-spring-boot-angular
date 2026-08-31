<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fournisseur_medicaments', function (Blueprint $table) {
            $table->integer('stock_minimum')->default(10)->after('stock_disponible');
            $table->integer('stock_maximum')->nullable()->after('stock_minimum');
            $table->integer('seuil_reapprovisionnement')->default(20)->after('stock_maximum');
        });
    }

    public function down()
    {
        Schema::table('fournisseur_medicaments', function (Blueprint $table) {
            $table->dropColumn(['stock_minimum', 'stock_maximum', 'seuil_reapprovisionnement']);
        });
    }
};