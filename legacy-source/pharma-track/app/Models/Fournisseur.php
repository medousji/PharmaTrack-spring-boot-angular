<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    protected $fillable = [
        'user_id',
        'raison_sociale',
        'matricule_fiscal',
        'pays_origine',
        'specialite',
        'fax',
        'code_postal',
        'ville',
        'gouvernorat',
        'contact_poste',
        'adresse',
        'telephone',
        'email_pro',
        'contact_nom',
        'contact_telephone',
        'site_web',
        'delai_livraison_moyen',
        'frais_livraison',
        'note',
        'est_actif',
        'notes'
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'note' => 'decimal:2',
        'frais_livraison' => 'decimal:3'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicaments(): HasMany
    {
        return $this->hasMany(FournisseurMedicament::class);
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(CommandeFournisseur::class);
    }
}