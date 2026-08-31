<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    protected $fillable = [
        'medicament_id',
        'numero_lot',
        'date_fabrication',
        'date_peremption',
        'quantite_initial',
        'quantite_actuelle',
        'fournisseur',
        'date_reception',
        'statut',
        'prix_achat',
        'prix_vente',
        'numero_facture',
        'conditionnement',
        'emplacement',
        'observations',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_fabrication' => 'date',
        'date_peremption' => 'date',
        'date_reception' => 'date',
        'quantite_initial' => 'integer',
        'quantite_actuelle' => 'integer',
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];

    protected $attributes = [
        'statut' => 'actif',
        'quantite_actuelle' => 0,
    ];

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class);
    }

    public function getJoursAvantPeremptionAttribute(): int
    {
        if (!$this->date_peremption) return 999;
        return Carbon::now()->diffInDays($this->date_peremption, false);
    }

    public function getEstPerimeAttribute(): bool
    {
        return $this->date_peremption && $this->date_peremption <= now();
    }

    public function getEstActifAttribute(): bool
    {
        return $this->statut === 'actif' && $this->quantite_actuelle > 0 && !$this->est_perime;
    }

    public function modifierQuantite(int $quantite, string $type = 'ajustement', string $motif = ''): bool
    {
        $ancienne = $this->quantite_actuelle;
        $this->quantite_actuelle += ($type === 'sortie') ? -$quantite : $quantite;
        if ($this->quantite_actuelle < 0) $this->quantite_actuelle = 0;
        
        $saved = $this->save();
        
        if ($saved && class_exists(Mouvement::class)) {
            Mouvement::create([
                'lot_id' => $this->id,
                'type' => $type,
                'quantite' => $quantite,
                'quantite_avant' => $ancienne,
                'quantite_apres' => $this->quantite_actuelle,
                'motif' => $motif,
                'user_id' => auth()->id(),
            ]);
        }
        
        return $saved;
    }
}