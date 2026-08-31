<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mouvement extends Model
{
    protected $fillable = [
        'lot_id',
        'pharmacie_id',
        'type',
        'quantite',
        'reference',
        'raison',
        'user_id',
        'scanned_at',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'scanned_at' => 'datetime',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function pharmacie(): BelongsTo
{
    return $this->belongsTo(Pharmacie::class);
}
}