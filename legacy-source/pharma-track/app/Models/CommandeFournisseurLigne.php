<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeFournisseurLigne extends Model
{
    protected $table = 'commande_fournisseur_lignes';

    protected $fillable = [
        'commande_id',
        'medicament_id',
        'quantite',
        'quantite_demandee',
        'stock_avant',
        'prix_unitaire',
        'total_ligne',
        'notes'
    ];

    protected $casts = [
        'quantite' => 'integer',
        'quantite_demandee' => 'integer',
        'stock_avant' => 'integer',
        'prix_unitaire' => 'decimal:3',
        'total_ligne' => 'decimal:3'
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(CommandeFournisseur::class, 'commande_id');
    }

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }
}