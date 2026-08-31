<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modifier l'enum pour ajouter 'fournisseur'
        if (DB::connection()->getDriverName() === 'pgsql') {
            // PostgreSQL: les enums schema builder sont des contraintes CHECK, on les retire simplement
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pharmacien', 'visiteur', 'fournisseur') NOT NULL DEFAULT 'visiteur'");
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pharmacien', 'visiteur') NOT NULL DEFAULT 'visiteur'");
        }
    }
};