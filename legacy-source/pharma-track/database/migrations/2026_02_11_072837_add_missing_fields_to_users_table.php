<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifier et ajouter chaque champ manquant
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('visiteur');
            }
            
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable();
            }
            
            // email_verified_at devrait déjà exister avec Laravel
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les colonnes si rollback
            $columns = ['role', 'status', 'last_login_at', 'last_login_ip'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};