<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacie extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'email',
        'licence_number',
        'responsable',
        'est_active'
    ];

    protected $casts = [
        'est_active' => 'boolean',
    ];

    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}