<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alerte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    /**
     * Afficher le formulaire d'inscription.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.register');
    }

    /**
     * Traiter la connexion.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Vérifier si le compte est actif
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
                ])->onlyInput('email');
            }
            
            // Vérifier si le compte est approuvé
            if (!$user->is_approved) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte est en attente d\'approbation. Vous serez notifié dès que l\'administrateur validera votre compte.',
                ])->onlyInput('email');
            }
            
            // Enregistrer la connexion
            try {
                $user->recordLogin();
            } catch (\Exception $e) {
                // Ignorer si la méthode n'existe pas
            }
            
            // Message de bienvenue
            $message = 'Connexion réussie ! Bienvenue ' . $user->name . ' !';
            
            // Rediriger selon le rôle
            return $this->redirectByRole($user, $message);
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Traiter l'inscription avec approbation admin.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'visiteur',
                'status' => 'active',
                'is_approved' => false,
                'email_verified_at' => now(),
            ]);

            // Notifier les administrateurs
            $this->notifierAdmin($user);

            return redirect()->route('login')
                ->with('success', '✅ Votre compte a été créé ! Un administrateur doit approuver votre compte avant de pouvoir vous connecter.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erreur lors de la création du compte : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Notifier tous les administrateurs d'une nouvelle inscription.
     */
    private function notifierAdmin($user)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            Alerte::create([
                'type' => 'inscription',
                'niveau' => 'moyen',
                'message' => "Nouvelle inscription en attente d'approbation : {$user->name} ({$user->email})",
                'est_lue' => false,
                'donnees_concernees' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email
                ]
            ]);
        }
    }

    /**
     * Déconnecter l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', '✅ Vous avez été déconnecté avec succès.');
    }

    /**
     * Rediriger selon le rôle de l'utilisateur.
     */
    private function redirectByRole($user = null, $message = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        
        $message = $message ?? 'Connexion réussie !';
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', $message);
        }
        
        if ($user->role === 'fournisseur') {
            return redirect()->route('fournisseur.dashboard')->with('success', $message);
        }
        
        if ($user->role === 'pharmacien') {
            return redirect()->route('dashboard')->with('success', $message);
        }
        
        return redirect()->route('dashboard')->with('success', $message);
    }
}