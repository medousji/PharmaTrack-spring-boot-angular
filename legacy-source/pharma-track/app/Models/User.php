<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
        'last_login_ip',
        'is_approved',
        'approved_at',
    ];

    /**
     * Les attributs qui doivent être cachés pour les sérialisations.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'approved_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
    ];

    /**
     * Les attributs avec des valeurs par défaut.
     */
    protected $attributes = [
        'role' => 'visiteur',
        'status' => 'active',
        'is_approved' => false,
    ];

    /**
     * Rôles disponibles dans l'application.
     */
    public static $roles = [
        'admin' => 'Administrateur',
        'pharmacien' => 'Pharmacien',
        'fournisseur' => 'Fournisseur',
        'visiteur' => 'Visiteur',
    ];

    /**
     * Statuts disponibles.
     */
    public static $statuses = [
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'suspended' => 'Suspendu',
    ];

    /**
     * Vérifier si l'utilisateur est administrateur.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifier si l'utilisateur est pharmacien.
     */
    public function isPharmacien()
    {
        return $this->role === 'pharmacien';
    }

    /**
     * Vérifier si l'utilisateur est fournisseur.
     */
    public function isFournisseur()
    {
        return $this->role === 'fournisseur';
    }

    /**
     * Vérifier si l'utilisateur est visiteur.
     */
    public function isVisiteur()
    {
        return $this->role === 'visiteur';
    }

    /**
     * Vérifier si l'utilisateur est actif.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Vérifier si l'utilisateur est approuvé.
     */
    public function isApproved()
    {
        return $this->is_approved === true;
    }

    /**
     * Obtenir le nom du rôle.
     */
    public function getRoleNameAttribute()
    {
        return self::$roles[$this->role] ?? $this->role;
    }

    /**
     * Obtenir le nom du statut.
     */
    public function getStatusNameAttribute()
    {
        return self::$statuses[$this->status] ?? $this->status;
    }

    /**
     * Scope pour les administrateurs.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope pour les pharmaciens.
     */
    public function scopePharmaciens($query)
    {
        return $query->where('role', 'pharmacien');
    }

    /**
     * Scope pour les fournisseurs.
     */
    public function scopeFournisseurs($query)
    {
        return $query->where('role', 'fournisseur');
    }

    /**
     * Scope pour les visiteurs.
     */
    public function scopeVisiteurs($query)
    {
        return $query->where('role', 'visiteur');
    }

    /**
     * Scope pour les utilisateurs actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les utilisateurs approuvés.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope pour les utilisateurs non approuvés.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Enregistrer la connexion de l'utilisateur.
     */
    public function recordLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * Relation avec les médicaments créés.
     */
    public function medicamentsCreated()
    {
        return $this->hasMany(Medicament::class, 'created_by');
    }

    /**
     * Relation avec les médicaments modifiés.
     */
    public function medicamentsUpdated()
    {
        return $this->hasMany(Medicament::class, 'updated_by');
    }

    /**
     * Relation avec la pharmacie.
     */
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    /**
     * Relation avec les lots créés.
     */
    public function lotsCreated()
    {
        return $this->hasMany(Lot::class, 'created_by');
    }

    /**
     * Relation avec les lots modifiés.
     */
    public function lotsUpdated()
    {
        return $this->hasMany(Lot::class, 'updated_by');
    }

    /**
     * Relation avec les mouvements.
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class);
    }
}