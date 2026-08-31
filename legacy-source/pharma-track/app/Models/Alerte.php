<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Lot; // ← Import du modèle Lot

class Alerte extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type',
        'niveau',
        'message',
        'donnees_concernees',
        'est_lue',
        'resolue_at',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'donnees_concernees' => 'array',
        'est_lue' => 'boolean',
        'resolue_at' => 'datetime',
    ];

    /**
     * Les attributs avec des valeurs par défaut.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'est_lue' => false,
        'niveau' => 'moyen',
    ];

    /**
     * Types d'alertes disponibles.
     *
     * @var array
     */
    const TYPES = [
        'expiration' => 'Expiration',
        'stock' => 'Stock faible',
        'rupture' => 'Rupture',
        'qualite' => 'Qualité',
        'autre' => 'Autre',
    ];

    /**
     * Niveaux d'alerte disponibles.
     *
     * @var array
     */
    const NIVEAUX = [
        'faible' => 'Faible',
        'moyen' => 'Moyen',
        'eleve' => 'Élevé',
        'critique' => 'Critique',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    /**
     * Relation avec le lot concerné (si applicable).
     * La table alertes doit avoir une colonne lot_id.
     */
    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope pour les alertes non lues.
     */
    public function scopeNonLues($query)
    {
        return $query->where('est_lue', false);
    }

    /**
     * Scope pour les alertes lues.
     */
    public function scopeLues($query)
    {
        return $query->where('est_lue', true);
    }

    /**
     * Scope pour les alertes critiques.
     */
    public function scopeCritiques($query)
    {
        return $query->whereIn('niveau', ['eleve', 'critique']);
    }

    /**
     * Scope pour les alertes par type.
     */
    public function scopeParType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les alertes récentes (7 derniers jours).
     */
    public function scopeRecentes($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Scope pour les alertes non résolues.
     */
    public function scopeNonResolues($query)
    {
        return $query->whereNull('resolue_at');
    }

    // ============================================
    // MÉTHODES D'INSTANCE
    // ============================================

    /**
     * Marquer l'alerte comme lue.
     */
    public function marquerCommeLue(): bool
    {
        $this->est_lue = true;
        return $this->save();
    }

    /**
     * Marquer l'alerte comme non lue.
     */
    public function marquerCommeNonLue(): bool
    {
        $this->est_lue = false;
        return $this->save();
    }

    /**
     * Résoudre l'alerte.
     */
    public function resoudre(): bool
    {
        $this->resolue_at = now();
        $this->est_lue = true;
        return $this->save();
    }

    /**
     * Vérifier si l'alerte est résolue.
     */
    public function getEstResolueAttribute(): bool
    {
        return !is_null($this->resolue_at);
    }

    /**
     * Obtenir le libellé du type.
     */
    public function getTypeLibelleAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtenir le libellé du niveau.
     */
    public function getNiveauLibelleAttribute(): string
    {
        return self::NIVEAUX[$this->niveau] ?? $this->niveau;
    }

    /**
     * Obtenir la couleur CSS pour le niveau.
     */
    public function getNiveauCouleurAttribute(): string
    {
        return match($this->niveau) {
            'faible' => 'info',
            'moyen' => 'warning',
            'eleve' => 'danger',
            'critique' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Obtenir l'icône pour le type.
     */
    public function getTypeIconeAttribute(): string
    {
        return match($this->type) {
            'expiration' => 'calendar-exclamation',
            'stock', 'rupture' => 'box-seam',
            'qualite' => 'exclamation-triangle',
            default => 'bell'
        };
    }

    // ============================================
    // MÉTHODES STATISTIQUES
    // ============================================

    /**
     * Vérifier automatiquement toutes les alertes
     */
    public static function verifierToutesLesAlertes(): array
    {
        $ruptures = self::verifierRuptures();
        $expirations = self::verifierExpirations();
        $stocksFaibles = self::verifierStocksFaibles();
        
        return [
            'ruptures' => $ruptures,
            'expirations' => $expirations,
            'stocks_faibles' => $stocksFaibles,
            'total' => $ruptures + $expirations + $stocksFaibles
        ];
    }

    /**
     * Vérifier les ruptures de stock
     */
    public static function verifierRuptures(): int
    {
        $medicaments = Medicament::with('lots')->get();
        $count = 0;
        
        foreach ($medicaments as $medicament) {
            $stockActuel = $medicament->lots->where('statut', 'actif')->sum('quantite_actuelle');
            
            if ($stockActuel == 0) {
                // Vérifier si une alerte existe déjà
                $existe = self::where('type', 'rupture')
                    ->where('est_lue', false)
                    ->get()
                    ->contains(function($alerte) use ($medicament) {
                        $donnees = $alerte->donnees_concernees ?? [];
                        return ($donnees['medicament_id'] ?? null) == $medicament->id;
                    });
                
                if (!$existe) {
                    self::create([
                        'type' => 'rupture',
                        'niveau' => 'critique',
                        'message' => "Rupture de stock : {$medicament->nom_commercial_fr}",
                        'donnees_concernees' => [
                            'medicament_id' => $medicament->id,
                            'nom_medicament' => $medicament->nom_commercial_fr,
                        ],
                    ]);
                    $count++;
                }
            }
        }
        
        return $count;
    }

    /**
     * Vérifier les stocks faibles
     */
    public static function verifierStocksFaibles(): int
    {
        $medicaments = Medicament::with('lots')->get();
        $count = 0;
        
        foreach ($medicaments as $medicament) {
            $stockActuel = $medicament->lots->where('statut', 'actif')->sum('quantite_actuelle');
            
            if ($stockActuel > 0 && $stockActuel < $medicament->stock_min) {
                $existe = self::where('type', 'stock')
                    ->where('est_lue', false)
                    ->get()
                    ->contains(function($alerte) use ($medicament) {
                        $donnees = $alerte->donnees_concernees ?? [];
                        return ($donnees['medicament_id'] ?? null) == $medicament->id;
                    });
                
                if (!$existe) {
                    self::create([
                        'type' => 'stock',
                        'niveau' => 'eleve',
                        'message' => "Stock faible pour {$medicament->nom_commercial_fr} : {$stockActuel} / {$medicament->stock_min}",
                        'donnees_concernees' => [
                            'medicament_id' => $medicament->id,
                            'nom_medicament' => $medicament->nom_commercial_fr,
                            'stock_actuel' => $stockActuel,
                            'stock_min' => $medicament->stock_min,
                        ],
                    ]);
                    $count++;
                }
            }
        }
        
        return $count;
    }

    /**
     * Vérifier les lots proches d'expiration
     */
    public static function verifierExpirations(): int
    {
        $lots = Lot::where('statut', 'actif')
            ->where('date_peremption', '<=', now()->addDays(30))
            ->where('quantite_actuelle', '>', 0)
            ->get();
        
        $count = 0;
        
        foreach ($lots as $lot) {
            $joursRestants = now()->diffInDays($lot->date_peremption);
            
            $existe = self::where('type', 'expiration')
                ->where('est_lue', false)
                ->get()
                ->contains(function($alerte) use ($lot) {
                    $donnees = $alerte->donnees_concernees ?? [];
                    return ($donnees['lot_id'] ?? null) == $lot->id;
                });
            
            if (!$existe) {
                self::create([
                    'type' => 'expiration',
                    'niveau' => $joursRestants <= 7 ? 'critique' : 'eleve',
                    'message' => "Le lot {$lot->numero_lot} expire dans {$joursRestants} jours",
                    'donnees_concernees' => [
                        'lot_id' => $lot->id,
                        'medicament_id' => $lot->medicament_id,
                        'numero_lot' => $lot->numero_lot,
                        'date_peremption' => $lot->date_peremption->format('Y-m-d'),
                        'jours_restants' => $joursRestants,
                    ],
                ]);
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Nettoyer les anciennes alertes
     */
    public static function nettoyerAnciennesAlertes(): int
    {
        return self::where('created_at', '<', now()->subDays(30))
            ->where('est_lue', true)
            ->delete();
    }

    /**
     * Obtenir les statistiques des alertes
     */
    public static function getStatistiques(): array
    {
        return [
            'non_lues' => self::nonLues()->count(),
            'critiques' => self::critiques()->nonLues()->count(),
            'par_type' => [
                'expiration' => self::parType('expiration')->nonLues()->count(),
                'stock' => self::parType('stock')->nonLues()->count(),
                'rupture' => self::parType('rupture')->nonLues()->count(),
                'autre' => self::whereNotIn('type', ['expiration', 'stock', 'rupture'])->nonLues()->count(),
            ],
        ];
    }
}