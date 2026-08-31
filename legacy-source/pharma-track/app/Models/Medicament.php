<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicament extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code_cip',
        'nom_commercial_fr',
        'nom_commercial_ar',
        'dci',
        'forme_pharmaceutique',
        'dosage',
        'conditionnement',
        'ppv',
        'ph',
        'prix_br',
        'prix_public',
        'taux_remboursement',
        'laboratoire',
        'pays_origine',
        'stock_min',
        'stock_max',
        'seuil_alerte',
        'date_premption_alerte',
        'classe_therapeutique',
        'voie_administration',
        'contre_indications',
        'effets_indesirables',
        'interactions_medicamenteuses',
        'conditions_conservation',
        'code_atc',
        'est_psychotrope',
        'est_ther_lourde',
        'est_renouvelable',
        'delai_renouvellement',
        'image_url',
        'code_barre',
        'est_generique',
        'medicament_reference_id',
        'est_perime',
        'date_peremption',
        'statut',
        'created_by',
        'updated_by',
        // Anciens champs (pour compatibilité)
        'nom',
        'forme',
        'presentation',
        'quantite',
        'remarque',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ppv' => 'decimal:2',
        'ph' => 'decimal:2',
        'prix_br' => 'decimal:2',
        'prix_public' => 'decimal:2',
        'taux_remboursement' => 'decimal:2',
        'stock_min' => 'integer',
        'stock_max' => 'integer',
        'seuil_alerte' => 'integer',
        'est_psychotrope' => 'boolean',
        'est_ther_lourde' => 'boolean',
        'est_renouvelable' => 'boolean',
        'est_generique' => 'boolean',
        'est_perime' => 'boolean',
        'date_peremption' => 'date',
        'date_premption_alerte' => 'date',
        // Anciens champs
        'quantite' => 'integer',
    ];

    /**
     * Les attributs avec des valeurs par défaut.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'stock_min' => 10,
        'stock_max' => 100,
        'seuil_alerte' => 30,
        'est_psychotrope' => false,
        'est_ther_lourde' => false,
        'est_renouvelable' => true,
        'est_generique' => false,
        'est_perime' => false,
        'statut' => 'actif',
        // Anciens champs
        'quantite' => 0,
    ];

    /**
     * Relation avec les lots
     */
    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    /**
     * Relation avec les mouvements de stock
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class);
    }

    /**
     * Relation avec les alertes
     */
    public function alertes(): HasMany
    {
        return $this->hasMany(AlerteStock::class);
    }

    /**
     * Relation avec le médicament de référence (pour les génériques)
     */
    public function medicamentReference()
    {
        return $this->belongsTo(Medicament::class, 'medicament_reference_id');
    }

    /**
     * Relation avec les génériques (inverse)
     */
    public function generiques(): HasMany
    {
        return $this->hasMany(Medicament::class, 'medicament_reference_id');
    }

    /**
     * Calculer le stock total actif
     */
    public function getStockActifAttribute(): int
    {
        // Si pas de relation lots, utiliser l'ancien champ quantite
        if (!method_exists($this, 'lots')) {
            return $this->quantite ?? 0;
        }
        
        return $this->lots()
            ->where('statut', 'actif')
            ->where('date_peremption', '>', now())
            ->sum('quantite_actuelle');
    }

    /**
     * Calculer le stock total (tous statuts)
     */
    public function getStockTotalAttribute(): int
    {
        if (!method_exists($this, 'lots')) {
            return $this->quantite ?? 0;
        }
        
        return $this->lots()->sum('quantite_actuelle');
    }

    /**
     * Vérifier si en rupture de stock
     */
    public function getEstEnRuptureAttribute(): bool
    {
        $stockMin = $this->stock_min ?? 0;
        return $this->stock_actif < $stockMin;
    }

    /**
     * Vérifier si proche de la péremption
     */
    public function getEstProchePeremptionAttribute(): bool
    {
        if (!method_exists($this, 'lots')) {
            return false;
        }
        
        $lotsProches = $this->lots()
            ->where('statut', 'actif')
            ->where('date_peremption', '<=', now()->addDays(30))
            ->where('date_peremption', '>', now())
            ->exists();
            
        return $lotsProches && $this->stock_actif > 0;
    }

    /**
     * Vérifier si périmé
     */
    public function getEstPerimeAttribute(): bool
    {
        if (!method_exists($this, 'lots')) {
            return $this->getAttribute('est_perime') ?? false;
        }
        
        return $this->lots()
            ->where('statut', 'actif')
            ->where('date_peremption', '<=', now())
            ->exists();
    }

    /**
     * Obtenir la date de péremption la plus proche
     */
    public function getDatePeremptionProcheAttribute()
    {
        if (!method_exists($this, 'lots')) {
            return $this->date_peremption;
        }
        
        return $this->lots()
            ->where('statut', 'actif')
            ->where('date_peremption', '>', now())
            ->orderBy('date_peremption', 'asc')
            ->value('date_peremption');
    }

    /**
     * Obtenir la valeur totale du stock
     */
    public function getValeurStockAttribute(): float
    {
        if (!method_exists($this, 'lots')) {
            return ($this->quantite ?? 0) * ($this->prix_br ?? 0);
        }
        
        return $this->lots()
            ->where('statut', 'actif')
            ->where('date_peremption', '>', now())
            ->sum(\DB::raw('quantite_actuelle * prix_achat'));
    }

    /**
     * Obtenir la marge totale
     */
    public function getMargeTotaleAttribute(): float
    {
        if (!method_exists($this, 'lots')) {
            $prixVente = $this->prix_public ?? $this->ppv ?? 0;
            $prixAchat = $this->prix_br ?? 0;
            return ($prixVente - $prixAchat) * ($this->quantite ?? 0);
        }
        
        return $this->lots()
            ->where('statut', 'actif')
            ->where('date_peremption', '>', now())
            ->sum(\DB::raw('quantite_actuelle * (prix_vente - prix_achat)'));
    }

    /**
     * Scope pour les médicaments en rupture
     */
    public function scopeEnRupture($query)
    {
        return $query->whereHas('lots', function($q) {
            $q->where('statut', 'actif')
              ->where('date_peremption', '>', now())
              ->groupBy('medicament_id')
              ->havingRaw('SUM(quantite_actuelle) < medicaments.stock_min');
        })->orWhere('quantite', '<', \DB::raw('stock_min'));
    }

    /**
     * Scope pour les médicaments proches de péremption
     */
    public function scopeProchePeremption($query, $jours = 30)
    {
        return $query->whereHas('lots', function($q) use ($jours) {
            $q->where('statut', 'actif')
              ->whereBetween('date_peremption', [now(), now()->addDays($jours)]);
        });
    }

    /**
     * Scope pour les médicaments périmés
     */
    public function scopePerimes($query)
    {
        return $query->whereHas('lots', function($q) {
            $q->where('statut', 'actif')
              ->where('date_peremption', '<=', now());
        })->orWhere('est_perime', true);
    }

    /**
     * Scope pour les médicaments psychotropes
     */
    public function scopePsychotropes($query)
    {
        return $query->where('est_psychotrope', true);
    }

    /**
     * Scope pour les médicaments à thérapie lourde
     */
    public function scopeTherLourde($query)
    {
        return $query->where('est_ther_lourde', true);
    }

    /**
     * Scope pour les génériques
     */
    public function scopeGeneriques($query)
    {
        return $query->where('est_generique', true);
    }

    /**
     * Scope pour les médicaments de référence
     */
    public function scopeReferences($query)
    {
        return $query->where('est_generique', false)->whereNull('medicament_reference_id');
    }

    /**
     * Formater le prix PPV
     */
    public function getPpvFormateAttribute(): string
    {
        return $this->ppv ? number_format($this->ppv, 2) . ' DH' : '-';
    }

    /**
     * Formater le prix PH
     */
    public function getPhFormateAttribute(): string
    {
        return $this->ph ? number_format($this->ph, 2) . ' DH' : '-';
    }

    /**
     * Obtenir le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        $nom = $this->nom_commercial_fr ?? $this->nom ?? '';
        
        if ($this->nom_commercial_ar) {
            $nom .= ' / ' . $this->nom_commercial_ar;
        }
        
        if ($this->dci) {
            $nom .= ' (' . $this->dci . ')';
        }
        
        return $nom;
    }

    /**
     * Obtenir le statut CSS pour l'affichage
     */
    public function getStatutCssAttribute(): string
    {
        if ($this->est_en_rupture) {
            return 'danger';
        } elseif ($this->est_proche_peremption) {
            return 'warning';
        } elseif ($this->est_perime) {
            return 'dark';
        } elseif ($this->stock_actif >= $this->stock_min) {
            return 'success';
        } else {
            return 'secondary';
        }
    }

    /**
     * Obtenir l'icône du statut
     */
    public function getStatutIconeAttribute(): string
    {
        if ($this->est_en_rupture) {
            return 'exclamation-triangle';
        } elseif ($this->est_proche_peremption) {
            return 'clock';
        } elseif ($this->est_perime) {
            return 'exclamation-octagon';
        } elseif ($this->stock_actif >= $this->stock_min) {
            return 'check-circle';
        } else {
            return 'dash-circle';
        }
    }
}