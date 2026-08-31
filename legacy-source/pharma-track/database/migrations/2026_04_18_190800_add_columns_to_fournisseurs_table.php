<?php

use App\Models\Fournisseur;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Fournisseur::truncate();
        Schema::enableForeignKeyConstraints();

        $fournisseurs = [
            [
                'raison_sociale' => 'Laboratoire Hikma',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques, antibiotiques, anti-infectieux',
                'email_pro' => 'contact@hikma.tn',
                'telephone' => '71 123 456',
                'adresse' => 'Zone Industrielle Ben Arous - Ben Arous - 2013 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Opalia',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques, antibiotiques',
                'email_pro' => 'contact@opalia.tn',
                'telephone' => '71 800 100',
                'adresse' => 'Zone Industrielle Mghira 2 - Fouchana - 2082 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Adwya',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Médicaments génériques',
                'email_pro' => 'contact@adwya.com.tn',
                'telephone' => '71 430 000',
                'adresse' => 'Rue de la Pharmacie - Sidi Thabet - 2020 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Unimed',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques, cardiologie',
                'email_pro' => 'contact@unimed.tn',
                'telephone' => '71 850 200',
                'adresse' => 'Zone Industrielle Kalaa Kebira - Kalaa Kebira - 4060 Sousse Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Teriak',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Médicaments génériques',
                'email_pro' => 'contact@teriak.com.tn',
                'telephone' => '71 560 300',
                'adresse' => 'Avenue Hédi Khefacha - Montplaisir - 1073 Tunis Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Medis',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques, oncologie',
                'email_pro' => 'contact@medis.tn',
                'telephone' => '71 945 000',
                'adresse' => 'Zone Industrielle Borj Cédria - Borj Cédria - 2083 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Pantheon',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@pantheon.tn',
                'telephone' => '71 567 890',
                'adresse' => 'Zone Industrielle La Charguia - La Charguia - 2035 Tunis Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Pharmascience',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Médicaments génériques',
                'email_pro' => 'contact@pharmascience.tn',
                'telephone' => '71 234 567',
                'adresse' => 'Rue des Entrepreneurs - Charguia - 2035 Tunis Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Servipharm',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@servipharm.com.tn',
                'telephone' => '71 876 543',
                'adresse' => 'Zone Industrielle Kalaa Kebira - Kalaa Kebira - 4060 Sousse Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Sotapharm',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@sotapharm.tn',
                'telephone' => '71 345 678',
                'adresse' => 'Rue de la Pharmacie - Sidi Thabet - 2020 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire North Africa',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@northafrica.tn',
                'telephone' => '71 987 654',
                'adresse' => 'Zone Industrielle Ben Arous - Ben Arous - 2013 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Labopharm',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@labopharm.tn',
                'telephone' => '71 456 789',
                'adresse' => 'Rue des Légumes - Tunis Centre - 1000 Tunis Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Alfa Pharma',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@alfapharma.tn',
                'telephone' => '71 654 321',
                'adresse' => 'Zone Industrielle Mghira - Mghira - 2082 Fouchana Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Biotech',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Biosimilaires',
                'email_pro' => 'contact@biotech.tn',
                'telephone' => '71 789 012',
                'adresse' => 'Zone Industrielle Borj Cédria - Borj Cédria - 2083 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Medipharm',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@medipharm.tn',
                'telephone' => '71 210 987',
                'adresse' => 'Rue de la Pharmacie - Ariana - 2080 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Europharm',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@eupharm.tn',
                'telephone' => '71 543 210',
                'adresse' => 'Zone Industrielle La Soukra - La Soukra - 2073 Ariana Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Générique Pharma',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@generiquepharma.tn',
                'telephone' => '71 876 123',
                'adresse' => 'Rue des Entrepreneurs - Charguia - 2035 Tunis Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Tunisienne de Pharmacie',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Médicaments divers',
                'email_pro' => 'contact@tunipharm.tn',
                'telephone' => '71 432 109',
                'adresse' => 'Zone Industrielle Megrine - Megrine - 2033 Ben Arous Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Atlas Pharma',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@atlaspharma.tn',
                'telephone' => '71 109 876',
                'adresse' => 'Zone Industrielle Sfax - Sfax - 3000 Tunisie',
                'est_actif' => true,
            ],
            [
                'raison_sociale' => 'Laboratoire Sud Pharma',
                'pays_origine' => 'Tunisie',
                'specialite' => 'Génériques',
                'email_pro' => 'contact@sudpharma.tn',
                'telephone' => '71 654 098',
                'adresse' => 'Zone Industrielle Gabès - Gabès - 6000 Tunisie',
                'est_actif' => true,
            ],
        ];

        foreach ($fournisseurs as $data) {
            Fournisseur::updateOrCreate(
                ['raison_sociale' => $data['raison_sociale']],
                $data
            );
        }
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Fournisseur::where('pays_origine', 'Tunisie')->delete();
        Schema::enableForeignKeyConstraints();
    }
};