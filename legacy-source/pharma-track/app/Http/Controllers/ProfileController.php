<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Afficher la page de profil
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ajouter des propriétés supplémentaires pour la vue
        $user->roleName = ucfirst($user->role);
        $user->statusName = $user->is_active ? 'Actif' : 'Inactif';
        
        // Statistiques pour le profil
        $stats = [
            'totalMedicaments' => \App\Models\Medicament::count(),
            'alertesNonLues' => \App\Models\Alerte::where('est_lue', false)->count(),
            'lotsProchesExpiration' => \App\Models\Lot::where('date_peremption', '<=', now()->addDays(30))->count(),
        ];
        
        return view('profile.index', compact('user', 'stats'));
    }
    
    /**
     * Mettre à jour les informations personnelles
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Le nom complet est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('profile.index')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Mise à jour des informations
        $user->name = $request->name;
        $user->email = $request->email;
        
        // Si l'email a changé, réinitialiser la vérification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->save();
        
        return redirect()->route('profile.index')
            ->with('success', 'Vos informations personnelles ont été mises à jour avec succès.');
    }
    
    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // Validation
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Le mot de passe actuel est incorrect.');
                }
            }],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Le mot de passe actuel est requis.',
            'password.required' => 'Le nouveau mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('profile.index')
                ->withErrors($validator)
                ->with('active_tab', 'password')
                ->withInput();
        }
        
        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Déconnecter l'utilisateur des autres appareils (optionnel)
        Auth::logoutOtherDevices($request->password);
        
        return redirect()->route('profile.index')
            ->with('password_success', 'Votre mot de passe a été changé avec succès.');
    }
    
    /**
     * Télécharger la photo de profil
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = Auth::user();
        
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo && file_exists(public_path('storage/' . $user->photo))) {
                unlink(public_path('storage/' . $user->photo));
            }
            
            // Enregistrer la nouvelle photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $path;
            $user->save();
            
            return redirect()->route('profile.index')
                ->with('success', 'Photo de profil mise à jour avec succès.');
        }
        
        return redirect()->route('profile.index')
            ->with('error', 'Erreur lors du téléchargement de la photo.');
    }
    
    /**
     * Supprimer la photo de profil
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        
        if ($user->photo && file_exists(public_path('storage/' . $user->photo))) {
            unlink(public_path('storage/' . $user->photo));
        }
        
        $user->photo = null;
        $user->save();
        
        return redirect()->route('profile.index')
            ->with('success', 'Photo de profil supprimée avec succès.');
    }
    
    /**
     * Afficher l'historique des activités
     */
    public function activityLog()
    {
        $user = Auth::user();
        $activities = \App\Models\ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('profile.activity', compact('user', 'activities'));
    }
    
    /**
     * Exporter les données personnelles
     */
    public function exportData()
    {
        $user = Auth::user();
        
        $data = [
            'informations_personnelles' => [
                'nom' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'statut' => $user->is_active ? 'Actif' : 'Inactif',
                'date_inscription' => $user->created_at->format('d/m/Y H:i:s'),
                'derniere_connexion' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : null,
            ],
            'statistiques' => [
                'medicaments_geres' => \App\Models\Medicament::count(),
                'alertes_creees' => \App\Models\Alerte::where('user_id', $user->id)->count(),
                'lots_ajoutes' => \App\Models\Lot::where('user_id', $user->id)->count(),
            ]
        ];
        
        $filename = 'donnees-personnelles-' . $user->id . '-' . date('Y-m-d') . '.json';
        
        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}