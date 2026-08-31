<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medicament;
use App\Models\Lot;
use App\Models\Alerte;
use App\Models\Pharmacie;
use App\Models\Fournisseur;
use App\Models\FournisseurMedicament;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * ✅ CONSTRUCTEUR - VÉRIFICATION ADMIN
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('dashboard')
                    ->with('error', '❌ Accès réservé aux administrateurs.');
            }
            
            return $next($request);
        });
    }

    /**
     * Tableau de bord admin avec graphiques et statistiques.
     */
    public function dashboard()
    {
        $totalMedicaments = Medicament::count();
        $ruptures = Lot::where('quantite_actuelle', 0)->count();
        $alertesNonLues = Alerte::where('est_lue', false)->count();
        
        $lotsProches = Lot::where('statut', 'actif')
            ->where('date_peremption', '<=', now()->addDays(30))
            ->where('quantite_actuelle', '>', 0)
            ->count();
        
        $categories = Medicament::select('categorie')
            ->selectRaw('count(*) as total')
            ->whereNotNull('categorie')
            ->groupBy('categorie')
            ->pluck('total', 'categorie')
            ->toArray();
        
        $topMedicaments = Medicament::withSum('lots as stock_total', 'quantite_actuelle')
            ->orderBy('stock_total', 'desc')
            ->limit(5)
            ->get();
        
        $evolution = Medicament::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
        
        $lotsExpiration = Lot::selectRaw('DATE(date_peremption) as date, COUNT(*) as total')
            ->where('statut', 'actif')
            ->where('date_peremption', '<=', now()->addDays(30))
            ->where('date_peremption', '>=', now())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
        
        $alertesRecentes = Alerte::where('est_lue', false)
            ->latest()
            ->take(5)
            ->get();
        
        $stats = [
            'users' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'pharmaciens' => User::where('role', 'pharmacien')->count(),
                'fournisseurs' => User::where('role', 'fournisseur')->count(),
                'visiteurs' => User::where('role', 'visiteur')->count(),
            ],
            'medicaments' => [
                'total' => $totalMedicaments,
                'en_rupture' => $ruptures,
            ],
            'lots' => [
                'total' => Lot::count(),
                'actifs' => Lot::where('statut', 'actif')->count(),
                'perimes' => Lot::where('statut', 'perime')->count(),
            ],
            'alertes' => [
                'total' => Alerte::count(),
                'non_lues' => $alertesNonLues,
            ],
            'pharmacies' => [
                'total' => Pharmacie::count(),
            ]
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_medicaments = Medicament::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_users',
            'recent_medicaments',
            'totalMedicaments',
            'ruptures',
            'alertesNonLues',
            'lotsProches',
            'categories',
            'topMedicaments',
            'evolution',
            'lotsExpiration',
            'alertesRecentes'
        ));
    }

    /**
     * Liste des utilisateurs.
     */
    public function users(Request $request)
    {
        $users = User::latest()->paginate(10);
        $stats = [
            'admin' => User::where('role', 'admin')->count(),
            'pharmacien' => User::where('role', 'pharmacien')->count(),
            'fournisseur' => User::where('role', 'fournisseur')->count(),
            'visiteur' => User::where('role', 'visiteur')->count(),
        ];
        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Demandes d'inscription en attente d'approbation.
     */
    public function pendingUsers()
    {
        $users = User::where('is_approved', false)
            ->where('role', 'visiteur')
            ->latest()
            ->paginate(15);
        
        $totalUsers = User::count();
        $approvedUsers = User::where('is_approved', true)->count();
        
        return view('admin.users.pending', compact('users', 'totalUsers', 'approvedUsers'));
    }

    /**
     * Approuver un utilisateur.
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
        
        // Créer une notification pour l'utilisateur
        Alerte::create([
            'type' => 'approbation',
            'niveau' => 'info',
            'message' => "✅ Votre compte a été approuvé. Vous pouvez maintenant vous connecter.",
            'est_lue' => false,
            'donnees_concernees' => json_encode(['user_id' => $user->id])
        ]);
        
        return redirect()->route('admin.users.pending')
            ->with('success', "✅ Compte de {$user->name} approuvé avec succès.");
    }

    /**
     * Rejeter un utilisateur (le supprimer).
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->forceDelete();
        
        return redirect()->route('admin.users.pending')
            ->with('success', "❌ Compte de {$userName} rejeté et supprimé.");
    }

    /**
     * Formulaire de création d'utilisateur.
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,pharmacien,fournisseur,visiteur'],
            'is_approved' => ['sometimes', 'boolean'],
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => 'active',
                'is_approved' => $request->has('is_approved') ? true : false,
                'email_verified_at' => now(),
            ]);

            if ($validated['role'] === 'fournisseur' && $request->filled('raison_sociale')) {
                Fournisseur::create([
                    'user_id' => $user->id,
                    'raison_sociale' => $request->raison_sociale,
                    'est_actif' => true,
                ]);
            }

            return redirect()->route('admin.users')
                ->with('success', "✅ Utilisateur {$user->name} créé avec succès.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erreur lors de la création : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher le formulaire d'édition d'un utilisateur.
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,pharmacien,fournisseur,visiteur'],
            'is_approved' => ['sometimes', 'boolean'],
        ]);

        if ($user->role === 'admin' && $validated['role'] !== 'admin' && 
            User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()
                ->with('error', '❌ Impossible de modifier le rôle du dernier administrateur.');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_approved' => $request->has('is_approved') ? true : false,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['min:8', 'confirmed'],
            ]);
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('admin.users')
            ->with('success', "✅ Utilisateur {$user->name} mis à jour avec succès.");
    }

    /**
     * Mettre à jour le rôle d'un utilisateur.
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()
                ->with('error', '❌ Impossible de modifier le rôle du dernier administrateur.');
        }

        $request->validate([
            'role' => ['required', 'in:admin,pharmacien,fournisseur,visiteur'],
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return redirect()->back()
            ->with('success', "✅ Rôle de {$user->name} mis à jour avec succès.");
    }

    /**
     * Supprimer un utilisateur (SUPPRESSION DÉFINITIVE)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()
                ->with('error', '❌ Impossible de supprimer le dernier administrateur.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', '❌ Vous ne pouvez pas supprimer votre propre compte.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        
        $user->forceDelete();

        return redirect()->route('admin.users')
            ->with('success', "✅ Utilisateur {$userName} ({$userEmail}) supprimé définitivement.");
    }

    /**
     * Statistiques détaillées.
     */
    public function stats()
    {
        $stats = [
            'users_by_role' => [
                'admin' => User::where('role', 'admin')->count(),
                'pharmacien' => User::where('role', 'pharmacien')->count(),
                'fournisseur' => User::where('role', 'fournisseur')->count(),
                'visiteur' => User::where('role', 'visiteur')->count(),
            ],
            'users_by_status' => [
                'active' => User::where('status', 'active')->count(),
                'inactive' => User::where('status', 'inactive')->count(),
                'suspended' => User::where('status', 'suspended')->count(),
            ],
            'total_medicaments' => Medicament::count(),
            'total_lots' => Lot::count(),
            'total_pharmacies' => Pharmacie::count(),
        ];
        
        return view('admin.stats', compact('stats'));
    }

    /**
     * Paramètres système.
     */
    public function settings()
    {
        $settings = [
            'app_name' => config('app.name', 'Pharma Track'),
            'app_env' => config('app.env'),
            'db_connection' => config('database.default'),
            'timezone' => config('app.timezone'),
            'version' => '1.0.0',
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Vider le cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('optimize:clear');

            return redirect()->back()
                ->with('success', '✅ Caches vidés avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erreur lors du vidage des caches : ' . $e->getMessage());
        }
    }

    /**
     * Envoyer une relance manuelle à un fournisseur.
     */
    public function relancerFournisseur($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        $produitsCritiques = FournisseurMedicament::where('fournisseur_id', $fournisseur->id)
            ->where(function($q) {
                $q->where('stock_disponible', '<=', 0)
                  ->orWhereRaw('stock_disponible <= stock_minimum');
            })
            ->with('medicament')
            ->get();
        
        if ($produitsCritiques->isEmpty()) {
            return redirect()->back()->with('info', 'Aucun produit nécessitant une relance pour ce fournisseur.');
        }
        
        $admin = auth()->user();
        
        $message = "🔄 **RELANCE MANUELLE**\n\n";
        $message .= "Bonjour,\n\n";
        $message .= "Voici la liste des produits nécessitant un réapprovisionnement :\n\n";
        
        foreach ($produitsCritiques as $produit) {
            $nomMedicament = $produit->medicament->nom_commercial_fr ?? 'Médicament';
            $stockActuel = $produit->stock_disponible;
            $stockMinimum = $produit->stock_minimum ?? 10;
            
            if ($stockActuel <= 0) {
                $message .= "• 🔴 **RUPTURE** : {$nomMedicament} - Stock: 0 unité\n";
            } else {
                $message .= "• ⚠️ **STOCK FAIBLE** : {$nomMedicament} - Stock: {$stockActuel}/{$stockMinimum} unités\n";
            }
        }
        
        $message .= "\nMerci de bien vouloir procéder au réapprovisionnement dès que possible.\n\n";
        $message .= "Cordialement,\nL'équipe Pharma Track";
        
        Message::create([
            'expediteur_id' => $admin->id,
            'destinataire_id' => $fournisseur->user_id,
            'message' => $message,
            'est_lu' => false
        ]);
        
        $fournisseur->update([
            'derniere_relance' => now(),
            'nb_relances' => ($fournisseur->nb_relances ?? 0) + 1
        ]);
        
        Alerte::create([
            'type' => 'relance',
            'niveau' => 'moyen',
            'message' => "📨 Relance manuelle envoyée. {$produitsCritiques->count()} produit(s) nécessitent votre attention.",
            'est_lue' => false,
            'donnees_concernees' => json_encode([
                'fournisseur_id' => $fournisseur->id,
                'nb_produits' => $produitsCritiques->count()
            ])
        ]);
        
        return redirect()->back()->with('success', "Relance envoyée à {$fournisseur->raison_sociale}.");
    }
}