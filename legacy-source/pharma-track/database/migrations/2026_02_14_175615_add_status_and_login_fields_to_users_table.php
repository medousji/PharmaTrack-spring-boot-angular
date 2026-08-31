<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Ajouter le champ status s'il n'existe pas
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive', 'suspended'])
                      ->default('active')
                      ->after('password');
            }
            
            // ✅ Ajouter le champ role s'il n'existe pas (déjà existant probablement)
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'pharmacien', 'visiteur'])
                      ->default('visiteur')
                      ->after('email');
            }
            
            // ✅ Ajouter les champs de suivi de connexion
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'last_login_at',
                'last_login_ip'
            ]);
        });
    }
};