<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin Pharma',
            'email' => 'admin@pharmatrack.tn',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        // Pharmacien
        User::create([
            'name' => 'Pharmacien Test',
            'email' => 'pharmacien@pharmatrack.tn',
            'password' => Hash::make('pharma123'),
            'role' => 'pharmacien',
            'status' => 'active'
        ]);

        // Fournisseur
        User::create([
            'name' => 'Fournisseur Test',
            'email' => 'fournisseur@pharmatrack.tn',
            'password' => Hash::make('fourni123'),
            'role' => 'fournisseur',
            'status' => 'active'
        ]);

        // Visiteur
        User::create([
            'name' => 'Visiteur Test',
            'email' => 'visiteur@pharmatrack.tn',
            'password' => Hash::make('visiteur123'),
            'role' => 'visiteur',
            'status' => 'active'
        ]);
    }
}