<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FournisseurMedicament extends Model
{
    protected $table = 'fournisseur_medicaments';

    protected $fillable = [
        'fournisseur_id',
        'medicament_id',
        'reference_fournisseur',
        'prix_achat',
        'prix_public',
        'stock_disponible',
        'delai_livraison',
        'disponible',
        'derniere_mise_a_jour'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:3',
        'prix_public' => 'decimal:3',
        'stock_disponible' => 'integer',
        'disponible' => 'boolean',
        'derniere_mise_a_jour' => 'date'
    ];

    protected $attributes = [
        'disponible' => true,
        'stock_disponible' => 0
    ];

    /**
     * Relation avec le fournisseur
     */
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    /**
     * Relation avec le médicament
     */
    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    /**
     * Vérifier si le produit est disponible
     */
    public function estDisponible(): bool
    {
        return $this->disponible && $this->stock_disponible > 0;
    }

    /**
     * Calculer le prix total pour une quantité
     */
    public function prixTotal(int $quantite): float
    {
        return $this->prix_achat * $quantite;
    }

    /**
     * Mettre à jour le stock
     */
    public function reduireStock(int $quantite): bool
    {
        if ($this->stock_disponible < $quantite) {
            return false;
        }
        
        $this->stock_disponible -= $quantite;
        return $this->save();
    }
}