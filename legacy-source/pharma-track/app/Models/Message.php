<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'expediteur_id',
        'destinataire_id',
        'commande_id',
        'message',
        'est_lu'
    ];

    protected $casts = [
        'est_lu' => 'boolean'
    ];

    public function expediteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(CommandeFournisseur::class);
    }
}