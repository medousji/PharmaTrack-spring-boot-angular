<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modifier l'enum pour ajouter 'partiel' (déjà géré par 013351 sur PostgreSQL)
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE commandes_fournisseurs DROP CONSTRAINT IF EXISTS commandes_fournisseurs_statut_check');
        } else {
            DB::statement("ALTER TABLE commandes_fournisseurs MODIFY COLUMN statut ENUM('en_attente', 'confirmee', 'preparation', 'expediee', 'livree', 'annulee', 'partiel') NOT NULL DEFAULT 'en_attente'");
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE commandes_fournisseurs DROP CONSTRAINT IF EXISTS commandes_fournisseurs_statut_check');
        } else {
            DB::statement("ALTER TABLE commandes_fournisseurs MODIFY COLUMN statut ENUM('en_attente', 'confirmee', 'preparation', 'expediee', 'livree', 'annulee') NOT NULL DEFAULT 'en_attente'");
        }
    }
};