<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Medicament;
use App\Models\Pharmacie;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin Pharma',
            'email' => 'admin@pharmatrack.tn',
            'password' => Hash::make('admin123'),
        ]);

        // Créer une pharmacie
        Pharmacie::create([
            'nom' => 'Pharmacie Centrale',
            'region' => 'Tunis',
            'type' => 'urbain',
            'contact_telephone' => '12345678',
            'contact_email' => 'pharmacie@test.tn',
            'est_pilote' => true,
        ]);

        // Créer quelques médicaments de test
        $medicaments = [
            [
                'code_cip' => '3400931234567',
                'dci' => 'Paracetamol',
                'nom_commercial_fr' => 'Doliprane 1000mg',
                'nom_commercial_ar' => 'دوليبران 1000ملغ',
                'forme' => 'Comprimé',
                'dosage' => 1000,
                'unite' => 'mg',
                'categorie' => 'Antalgique',
                'est_controle' => false,
                'est_essentiel' => true,
                'prix_achat' => 5.0,
                'prix_vente' => 8.0,
                'stock_min' => 50,
                'stock_max' => 200,
                'delai_appro' => 7,
            ],
            [
                'code_cip' => '3400937654321',
                'dci' => 'Amoxicilline',
                'nom_commercial_fr' => 'Clamoxyl 500mg',
                'nom_commercial_ar' => 'كلاموكسيل 500ملغ',
                'forme' => 'Comprimé',
                'dosage' => 500,
                'unite' => 'mg',
                'categorie' => 'Antibiotique',
                'est_controle' => false,
                'est_essentiel' => true,
                'prix_achat' => 12.0,
                'prix_vente' => 18.0,
                'stock_min' => 30,
                'stock_max' => 100,
                'delai_appro' => 10,
            ],
            [
                'code_cip' => '3400939876543',
                'dci' => 'Ibuprofene',
                'nom_commercial_fr' => 'Advil 400mg',
                'nom_commercial_ar' => 'أدفيل 400ملغ',
                'forme' => 'Comprimé',
                'dosage' => 400,
                'unite' => 'mg',
                'categorie' => 'Anti-inflammatoire',
                'est_controle' => false,
                'est_essentiel' => false,
                'prix_achat' => 6.0,
                'prix_vente' => 10.0,
                'stock_min' => 20,
                'stock_max' => 80,
                'delai_appro' => 5,
            ]
        ];

        foreach ($medicaments as $medicament) {
            Medicament::create($medicament);
        }

        $this->command->info('✅ Base de données peuplée avec succès!');
        $this->command->info('👤 Admin: admin@pharmatrack.tn / admin123');
        $this->command->info('🏥 Pharmacie: Pharmacie Centrale créée');
        $this->command->info('💊 3 médicaments créés');
    }
}