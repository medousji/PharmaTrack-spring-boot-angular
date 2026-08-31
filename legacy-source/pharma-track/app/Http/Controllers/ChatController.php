<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\CommandeFournisseur;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher toutes les conversations
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les conversations liées aux commandes AVEC les médicaments
        if ($user->role === 'fournisseur') {
            $fournisseur = Fournisseur::where('user_id', $user->id)->first();
            $commandes = CommandeFournisseur::where('fournisseur_id', $fournisseur->id ?? 0)
                ->with(['messages', 'lignes.medicament'])  // ← AJOUTÉ : lignes.medicament
                ->latest()
                ->get();
        } else {
            $commandes = CommandeFournisseur::with(['messages', 'lignes.medicament'])  // ← AJOUTÉ : lignes.medicament
                ->latest()
                ->get();
        }
        
        // Récupérer les conversations directes
        $conversations = collect();
        
        if ($user->role === 'fournisseur') {
            // Le fournisseur voit les conversations avec les admins et pharmaciens
            $adminsAndPharmaciens = User::whereIn('role', ['admin', 'pharmacien'])->get();
            
            foreach ($adminsAndPharmaciens as $destinataire) {
                $dernierMessage = Message::where(function($q) use ($user, $destinataire) {
                    $q->where('expediteur_id', $user->id)->where('destinataire_id', $destinataire->id);
                })->orWhere(function($q) use ($user, $destinataire) {
                    $q->where('expediteur_id', $destinataire->id)->where('destinataire_id', $user->id);
                })->latest()->first();
                
                $nonLus = Message::where('expediteur_id', $destinataire->id)
                    ->where('destinataire_id', $user->id)
                    ->where('est_lu', false)
                    ->count();
                
                if ($dernierMessage) {
                    $conversations->push([
                        'id' => $destinataire->id,
                        'nom' => $destinataire->name,
                        'role' => $destinataire->role,
                        'dernier_message' => $dernierMessage->message,
                        'date' => $dernierMessage->created_at,
                        'non_lus' => $nonLus
                    ]);
                }
            }
        } else {
            // Admin ou pharmacien voit les conversations avec les fournisseurs
            $fournisseurs = Fournisseur::with('user')->where('est_actif', true)->get();
            
            foreach ($fournisseurs as $fournisseur) {
                if ($fournisseur->user) {
                    $dernierMessage = Message::where(function($q) use ($user, $fournisseur) {
                        $q->where('expediteur_id', $user->id)->where('destinataire_id', $fournisseur->user_id);
                    })->orWhere(function($q) use ($user, $fournisseur) {
                        $q->where('expediteur_id', $fournisseur->user_id)->where('destinataire_id', $user->id);
                    })->latest()->first();
                    
                    $nonLus = Message::where('expediteur_id', $fournisseur->user_id)
                        ->where('destinataire_id', $user->id)
                        ->where('est_lu', false)
                        ->count();
                    
                    if ($dernierMessage) {
                        $conversations->push([
                            'id' => $fournisseur->user_id,
                            'nom' => $fournisseur->raison_sociale,
                            'role' => 'fournisseur',
                            'dernier_message' => $dernierMessage->message,
                            'date' => $dernierMessage->created_at,
                            'non_lus' => $nonLus
                        ]);
                    }
                }
            }
        }
        
        $conversations = $conversations->sortByDesc('date');
        
        return view('chat.index', compact('commandes', 'conversations'));
    }

    /**
     * Afficher une conversation directe
     */
    public function conversation($userId)
    {
        $destinataire = User::findOrFail($userId);
        
        // Marquer les messages comme lus
        Message::where('expediteur_id', $destinataire->id)
            ->where('destinataire_id', Auth::id())
            ->update(['est_lu' => true]);
        
        $messages = Message::where(function($q) use ($destinataire) {
            $q->where('expediteur_id', Auth::id())->where('destinataire_id', $destinataire->id);
        })->orWhere(function($q) use ($destinataire) {
            $q->where('expediteur_id', $destinataire->id)->where('destinataire_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();
        
        return view('chat.conversation', compact('destinataire', 'messages'));
    }

    /**
     * Envoyer un message lié à une commande
     */
    public function envoyer(Request $request, $commandeId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);
        
        $user = Auth::user();
        $commande = CommandeFournisseur::findOrFail($commandeId);
        
        // Déterminer le destinataire
        if ($user->role === 'fournisseur') {
            $destinataireId = $commande->user_id;
        } else {
            $fournisseur = Fournisseur::find($commande->fournisseur_id);
            $destinataireId = $fournisseur ? $fournisseur->user_id : null;
        }
        
        if (!$destinataireId) {
            return redirect()->back()->with('error', 'Destinataire non trouvé.');
        }
        
        Message::create([
            'expediteur_id' => $user->id,
            'destinataire_id' => $destinataireId,
            'commande_id' => $commandeId,
            'message' => $request->message,
            'est_lu' => false
        ]);
        
        return redirect()->back()->with('success', 'Message envoyé !');
    }

    /**
     * Envoyer un message direct (sans commande)
     */
    public function envoyerDirect(Request $request)
    {
        $request->validate([
            'destinataire_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);
        
        $destinataire = User::find($request->destinataire_id);
        
        if (!$destinataire) {
            return redirect()->back()->with('error', 'Destinataire non trouvé.');
        }
        
        Message::create([
            'expediteur_id' => Auth::id(),
            'destinataire_id' => $request->destinataire_id,
            'message' => $request->message,
            'est_lu' => false
        ]);
        
        return redirect()->back()->with('success', 'Message envoyé à ' . $destinataire->name . ' !');
    }

    /**
     * Afficher les messages d'une commande AVEC les médicaments
     */
    public function show($commandeId)
    {
        // Chargez aussi les messages et les médicaments
        $commande = CommandeFournisseur::with([
            'lignes.medicament',  // ← Pour les médicaments
            'messages'            // ← Pour les messages existants
        ])->findOrFail($commandeId);
        
        $user = Auth::user();
        
        // Vérifier l'accès
        if ($user->role === 'fournisseur') {
            $fournisseur = Fournisseur::where('user_id', $user->id)->first();
            if (!$fournisseur || $commande->fournisseur_id != $fournisseur->id) {
                abort(403);
            }
        }
        
        // Marquer les messages comme lus
        Message::where('commande_id', $commandeId)
            ->where('destinataire_id', $user->id)
            ->update(['est_lu' => true]);
        
        return view('chat.show', compact('commande'));
    }
}