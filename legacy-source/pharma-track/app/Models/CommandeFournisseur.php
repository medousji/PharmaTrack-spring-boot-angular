<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommandeFournisseur extends Model
{
    protected $table = 'commandes_fournisseurs';

    protected $fillable = [
        'numero_commande',
        'fournisseur_id',
        'pharmacie_id',
        'user_id',
        'date_commande',
        'date_livraison_prevue',
        'date_livraison_reelle',
        'statut',
        'total_ht',
        'total_ttc',
        'frais_livraison',
        'notes',
        'adresse_livraison'
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'date_livraison_reelle' => 'date',
        'total_ht' => 'decimal:3',
        'total_ttc' => 'decimal:3',
        'frais_livraison' => 'decimal:3'
    ];

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(CommandeFournisseurLigne::class, 'commande_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'commande_id');
    }
}