// database/seeders/AdminUserSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Pharma',
            'email' => 'admin@pharmatrack.tn',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);
        
        User::create([
            'name' => 'Pharmacie Test',
            'email' => 'pharmacie@test.tn',
            'password' => Hash::make('pharma123'),
            'role' => 'pharmacien'
        ]);
        
        $this->command->info('Utilisateurs créés avec succès!');
        $this->command->info('Admin: admin@pharmatrack.tn / admin123');
        $this->command->info('Pharmacien: pharmacie@test.tn / pharma123');
    }
}