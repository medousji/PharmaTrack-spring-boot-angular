<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Alerte;
use App\Models\ChatbotConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $historique = ChatbotConversation::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
        
        return view('chatbot.index', compact('historique'));
    }

    public function message(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500'
            ]);

            $message = trim($request->message);
            $user = auth()->user();

            $reponse = $this->getReponseUltraIntelligente($message, $user);

            ChatbotConversation::create([
                'user_id' => $user->id,
                'question' => $request->message,
                'reponse' => $reponse,
            ]);

            return response()->json([
                'success' => true,
                'reponse' => $reponse
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reponse' => '❌ Une erreur est survenue. Veuillez réessayer.'
            ], 500);
        }
    }

    private function getReponseUltraIntelligente($message, $user)
    {
        $messageOriginal = $message;
        $messageLower = strtolower($message);
        
        $derniereConv = ChatbotConversation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // ============================================
        // CONFIRMATION OUI - PRIORITÉ ABSOLUE
        // ============================================
        $msg = strtolower(trim($message));
        $msg = preg_replace('/[^a-z]/', '', $msg);
        
        // Vérifier si c'est OUI
        $estOui = in_array($msg, ['oui', 'yes', 'y', 'ok', 'daccord', 'confirme', 'valide', 'bien', 'accepte', 'okay', 'o']);
        
        if ($estOui) {
            if ($derniereConv && str_contains($derniereConv->reponse, 'Confirmez-vous cette commande')) {
                // Extraire le nom du médicament
                preg_match('/Commande de (.*?)\n/', $derniereConv->reponse, $medMatch);
                // Extraire la quantité
                preg_match('/Quantité : (\d+)/', $derniereConv->reponse, $qteMatch);
                
                if (isset($medMatch[1]) && isset($qteMatch[1])) {
                    $nomMed = trim($medMatch[1]);
                    $quantite = intval($qteMatch[1]);
                    
                    $medicament = Medicament::where('nom', $nomMed)->first();
                    if (!$medicament) {
                        $medicament = Medicament::where('nom_commercial_fr', $nomMed)->first();
                    }
                    
                    if ($medicament) {
                        $prix = $medicament->prix_vente ?? 0;
                        $total = $prix * $quantite;
                        $stockRestant = max(0, ($medicament->quantite ?? 0) - $quantite);
                        
                        $medicament->quantite = $stockRestant;
                        $medicament->save();
                        
                        return "✅ **COMMANDE CONFIRMÉE !**\n\n"
                            . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                            . "📦 **Médicament :** {$nomMed}\n"
                            . "🔢 **Quantité :** {$quantite} unités\n"
                            . "💰 **Total :** " . number_format($total, 3) . " TND\n"
                            . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
                            . "✅ Votre commande a été enregistrée avec succès !\n"
                            . "📊 Stock restant : {$stockRestant} unités\n\n"
                            . "🙏 Merci de votre confiance !\n\n"
                            . "💡 Souhaitez-vous autre chose ?";
                    }
                }
            }
        }
        
        // Vérifier si c'est NON
        $estNon = in_array($msg, ['non', 'no', 'n', 'annuler', 'stop', 'arreter', 'pas', 'annule']);
        
        if ($estNon) {
            if ($derniereConv && str_contains($derniereConv->reponse, 'Confirmez-vous cette commande')) {
                return "❌ **COMMANDE ANNULÉE**\n\n"
                    . "Pas de problème ! Votre commande a été annulée.\n\n"
                    . "👉 Souhaitez-vous autre chose ?\n\n"
                    . "💡 Tapez **'Aide'** pour voir ce que je peux faire.";
            }
        }
        
        // ============================================
        // COMMANDE AVEC QUANTITÉ "Commander 10 Grippex"
        // ============================================
        if (preg_match('/commander\s+(\d+)\s+(.+)/i', $messageOriginal, $matches)) {
            $quantite = intval($matches[1]);
            $nomRecherche = trim($matches[2]);
            
            $medicament = $this->chercherMedicamentParNom($nomRecherche);
            
            if ($medicament && $quantite > 0) {
                $nomMed = $medicament->nom_commercial_fr ?? $medicament->nom;
                $prix = $medicament->prix_vente ?? 0;
                $stock = $medicament->quantite ?? 0;
                $total = $prix * $quantite;
                
                if ($quantite > $stock && $stock > 0) {
                    return "⚠️ **Stock insuffisant !**\n\n"
                        . "📦 **{$nomMed}**\n"
                        . "Stock disponible : {$stock} unités\n"
                        . "👉 Voulez-vous commander {$stock} unités ? (Oui/Non)";
                }
                
                if ($stock <= 0) {
                    return "🔴 **RUPTURE DE STOCK !**\n\n📦 **{$nomMed}** n'est plus disponible.";
                }
                
                return "🛒 **Commande de {$nomMed}**\n\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "📦 Quantité : {$quantite} unités\n"
                    . "💰 Prix unitaire : " . number_format($prix, 3) . " TND\n"
                    . "💵 Total : " . number_format($total, 3) . " TND\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
                    . "✅ **Confirmez-vous cette commande ?** (Oui/Non)";
            }
        }
        
        // ============================================
        // COMMANDE SANS QUANTITÉ "Commander Grippex"
        // ============================================
        if (preg_match('/commander\s+([a-zA-Zàâäéèêëîïôöùûüç\s]+)/i', $messageOriginal, $matches) && !preg_match('/\d+/', $messageOriginal)) {
            $nomRecherche = trim($matches[1]);
            
            $medicament = $this->chercherMedicamentParNom($nomRecherche);
            
            if ($medicament) {
                $nomMed = $medicament->nom_commercial_fr ?? $medicament->nom;
                $prix = $medicament->prix_vente ?? 0;
                $stock = $medicament->quantite ?? 0;
                
                return "🛒 **Commande de {$nomMed}**\n\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "📦 Stock disponible : {$stock} unités\n"
                    . "💰 Prix unitaire : " . number_format($prix, 3) . " TND\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
                    . "👉 **Combien d'unités souhaitez-vous commander ?**\n"
                    . "💡 Exemple : \"10\" ou \"10 unités\"";
            }
        }
        
        // ============================================
        // PHRASE NATURELLE "je veux X unités de Y"
        // ============================================
        if (preg_match('/(\d+)\s*(unité|unités|boite|boîtes)?\s*(de|d\'|des)?\s*([a-zA-Zàâäéèêëîïôöùûüç\s]+)/i', $messageOriginal, $matches)) {
            $quantite = intval($matches[1]);
            $nomRecherche = trim($matches[4]);
            $nomRecherche = preg_replace('/[^a-zA-Zàâäéèêëîïôöùûüç\s]/', '', $nomRecherche);
            
            $medicament = $this->chercherMedicamentParNom($nomRecherche);
            
            if ($medicament && $quantite > 0) {
                $nomMed = $medicament->nom_commercial_fr ?? $medicament->nom;
                $prix = $medicament->prix_vente ?? 0;
                $stock = $medicament->quantite ?? 0;
                $total = $prix * $quantite;
                
                if ($quantite > $stock && $stock > 0) {
                    return "⚠️ **Stock insuffisant !**\n\n"
                        . "📦 **{$nomMed}**\n"
                        . "Stock disponible : {$stock} unités\n"
                        . "👉 Voulez-vous commander {$stock} unités ? (Oui/Non)";
                }
                
                if ($stock <= 0) {
                    return "🔴 **RUPTURE DE STOCK !**\n\n📦 **{$nomMed}** n'est plus disponible.";
                }
                
                return "🛒 **Commande de {$nomMed}**\n\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "📦 Quantité : {$quantite} unités\n"
                    . "💰 Prix unitaire : " . number_format($prix, 3) . " TND\n"
                    . "💵 Total : " . number_format($total, 3) . " TND\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
                    . "✅ **Confirmez-vous cette commande ?** (Oui/Non)";
            }
        }
        
        // ============================================
        // RÉPONSE À UNE QUANTITÉ (après demande)
        // ============================================
        if (preg_match('/^(\d+)\s*(unité|unités|boite|boîtes)?$/i', trim($messageOriginal), $matches)) {
            $quantite = intval($matches[1]);
            
            if ($derniereConv && str_contains($derniereConv->reponse, 'Combien d\'unités souhaitez-vous commander')) {
                if (preg_match('/\*\*([^*]+)\*\*/', $derniereConv->reponse, $medMatch)) {
                    $nomMed = trim($medMatch[1]);
                    $medicament = $this->chercherMedicamentParNom($nomMed);
                    
                    if ($medicament) {
                        $prix = $medicament->prix_vente ?? 0;
                        $total = $prix * $quantite;
                        return "🛒 **Commande de {$nomMed}**\n\n"
                            . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                            . "📦 Quantité : {$quantite} unités\n"
                            . "💰 Total : " . number_format($total, 3) . " TND\n"
                            . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
                            . "✅ **Confirmez-vous cette commande ?** (Oui/Non)";
                    }
                }
            }
        }
        
        // ============================================
        // STOCKS FAIBLES
        // ============================================
        if ($this->contientMot($messageLower, ['stock faible', 'stocks faibles', 'rupture', 'manque', 'épuisé', 'plus de stock', 'seuil'])) {
            return $this->getRecommandations();
        }
        
        // ============================================
        // RECHERCHE DE MÉDICAMENT
        // ============================================
        $medicament = $this->chercherMedicamentFouille($messageOriginal);
        
        if ($medicament) {
            $stock = $medicament->quantite ?? 0;
            $nomMed = $medicament->nom_commercial_fr ?? $medicament->nom ?? 'Médicament';
            $prix = $medicament->prix_vente ?? 0;
            $min = $medicament->stock_min ?? 10;
            
            $reponse = "📦 **{$nomMed}**\n";
            $reponse .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $reponse .= "📊 Stock actuel : **{$stock}** unités\n";
            if ($prix > 0) {
                $reponse .= "💰 Prix : " . number_format($prix, 3) . " TND\n";
            }
            $reponse .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            
            if ($stock <= 0) {
                $reponse .= "\n🔴 **RUPTURE DE STOCK !**";
            } elseif ($stock <= $min) {
                $reponse .= "\n⚠️ **STOCK FAIBLE !** Il ne reste que {$stock} unités.";
            } else {
                $reponse .= "\n✅ **Stock suffisant** pour le moment.";
            }
            $reponse .= "\n\n💡 Tapez *\"Commander {$nomMed}\"* pour passer commande.";
            return $reponse;
        }
        
        // ============================================
        // AIDE
        // ============================================
        if ($this->contientMot($messageLower, ['aide', 'help', 'que faire', '?'])) {
            return $this->getAide();
        }
        
        // ============================================
        // ALERTES
        // ============================================
        if ($this->contientMot($messageLower, ['alerte', 'notification', 'urgent'])) {
            $nbAlertes = Alerte::where('est_lue', false)->count();
            if ($nbAlertes > 0) {
                return "🚨 **VOUS AVEZ {$nbAlertes} ALERTE(S) NON LUE(S)**\n\n👉 Cliquez sur **'Alertes'** dans le menu.";
            }
            return "✅ **AUCUNE ALERTE NON LUE**";
        }
        
        // ============================================
        // STATISTIQUES
        // ============================================
        if ($this->contientMot($messageLower, ['statistique', 'stat', 'chiffre', 'total'])) {
            $totalMedicaments = Medicament::count();
            return "📊 **STATISTIQUES**\n\n🏥 Médicaments : {$totalMedicaments}";
        }
        
        // ============================================
        // RECOMMANDATIONS
        // ============================================
        if ($this->contientMot($messageLower, ['recommandation', 'suggestion', 'quoi commander'])) {
            return $this->getRecommandations();
        }
        
        // ============================================
        // BONJOUR
        // ============================================
        if ($this->contientMot($messageLower, ['bonjour', 'salut', 'coucou', 'hello'])) {
            $prenom = explode(' ', $user->name)[0];
            return "👋 **Bonjour {$prenom} !**\n\nJe suis votre assistant Pharma Track.\n\n👉 Comment puis-je vous aider ?";
        }
        
        // ============================================
        // MERCI
        // ============================================
        if ($this->contientMot($messageLower, ['merci', 'thanks'])) {
            return "🙏 **Avec plaisir !**";
        }
        
        // ============================================
        // AU REVOIR
        // ============================================
        if ($this->contientMot($messageLower, ['au revoir', 'bye'])) {
            return "👋 **Au revoir !** À bientôt !";
        }
        
        // ============================================
        // COMMANDE GÉNÉRIQUE
        // ============================================
        if ($this->contientMot($messageLower, ['commander', 'acheter', 'commande', 'je veux commander', 'passer commande'])) {
            return "🛒 **Passer une commande**\n\n"
                . "Exemples :\n"
                . "• \"Commander 10 Paracétamol\"\n"
                . "• \"Commander Grippex\" (puis la quantité)\n"
                . "• \"Je veux 2 unités de Antafen\"\n\n"
                . "👉 Quel médicament souhaitez-vous commander ?";
        }
        
        // ============================================
        // ANALYSE DES SYMPTÔMES
        // ============================================
        if ($this->contientMot($messageLower, ['mal', 'douleur', 'fièvre', 'toux', 'gorge', 'tête', 'ventre', 'nausée', 'diarrhée', 'fatigue', 'courbature', 'rhume', 'grippe', 'allergie'])) {
            return $this->analyserSymptomes($messageLower);
        }
        
        // ============================================
        // RÉPONSE PAR DÉFAUT
        // ============================================
        return "🤔 **Je n'ai pas bien compris votre demande.**\n\n"
            . "Voici ce que je peux faire :\n\n"
            . "📦 **Consulter un stock** → Tapez un nom de médicament\n"
            . "   Exemple : \"Paracétamol\" ou \"Antafen\" ou \"Grippex\"\n\n"
            . "🛒 **Passer une commande** :\n"
            . "   • \"Commander 10 Paracétamol\"\n"
            . "   • \"Commander Grippex\" (je demanderai la quantité)\n"
            . "   • \"Je veux 2 unités de Antafen\"\n\n"
            . "📋 **Stocks faibles** → \"Stocks faibles\"\n"
            . "🔍 **Analyser symptômes** → \"J'ai mal à la gorge\"\n"
            . "🚨 **Alertes** → \"Alertes\"\n"
            . "📊 **Statistiques** → \"Statistiques\"\n"
            . "💡 **Aide** → \"Aide\"\n\n"
            . "👉 **Que souhaitez-vous faire ?**";
    }

    private function analyserSymptomes($message)
    {
        if (str_contains($message, 'gorge') || str_contains($message, 'mal à la gorge')) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                . "📋 Symptômes détectés : Mal de gorge\n\n"
                . "💊 **Médicaments suggérés :**\n"
                . "• **Paracétamol 500mg** - Pour la douleur\n"
                . "• **Augmentin 1g** - Antibiotique (sur prescription)\n\n"
                . "⚠️ Consultez un médecin pour un diagnostic précis.\n\n"
                . "💡 Tapez le nom du médicament pour voir son stock.";
        }
        
        if (str_contains($message, 'tête') || str_contains($message, 'migraine')) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                . "📋 Symptômes détectés : Mal de tête\n\n"
                . "💊 **Médicaments suggérés :**\n"
                . "• **Paracétamol 500mg** - Antalgique\n"
                . "• **Ibuprofène 200mg** - Anti-inflammatoire\n\n"
                . "⚠️ Consultez un médecin si les symptômes persistent.";
        }
        
        if (str_contains($message, 'toux')) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                . "📋 Symptômes détectés : Toux\n\n"
                . "💊 **Médicaments suggérés :**\n"
                . "• **Tussidane 15mg** - Toux sèche\n"
                . "• **Exomuc 200mg** - Toux grasse\n\n"
                . "⚠️ Consultez un médecin pour un diagnostic précis.";
        }
        
        if (str_contains($message, 'fièvre') || str_contains($message, 'fievre')) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                . "📋 Symptômes détectés : Fièvre\n\n"
                . "💊 **Médicaments suggérés :**\n"
                . "• **Paracétamol 500mg** - Antipyrétique\n"
                . "• **Doliprane 1000mg** - Pour forte fièvre\n\n"
                . "⚠️ Consultez un médecin si la fièvre persiste.";
        }
        
        return "🔍 **Je n'ai pas détecté de symptômes spécifiques.**\n\n"
            . "👉 Pouvez-vous décrire plus précisément ce que vous ressentez ?";
    }

    private function getRecommandations()
    {
        $medicamentsFaibles = Medicament::where('quantite', '<=', 20)
            ->orderBy('quantite', 'asc')
            ->take(5)
            ->get();
        
        if ($medicamentsFaibles->count() > 0) {
            $reponse = "⚠️ **MÉDICAMENTS AVEC STOCK FAIBLE :**\n\n";
            foreach ($medicamentsFaibles as $med) {
                $stock = $med->quantite ?? 0;
                $icone = $stock <= 0 ? "🔴" : ($stock <= 5 ? "🟠" : "🟡");
                $reponse .= "$icone **{$med->nom_commercial_fr}**\n";
                $reponse .= "   📊 Stock restant : {$stock} unités\n\n";
            }
            $reponse .= "👉 Tapez *\"Commander [nom] [quantité]\"* pour passer commande.";
            return $reponse;
        }
        
        return "✅ **Aucun stock faible détecté !**\n\nTous les stocks sont à des niveaux corrects.";
    }

    private function getAide()
    {
        return "💡 **GUIDE D'UTILISATION**\n\n"
            . "📦 **Consulter un stock**\n"
            . "   → Tapez le nom d'un médicament\n"
            . "   Ex: \"Paracétamol\" ou \"Antafen\" ou \"Grippex\"\n\n"
            . "🛒 **Passer une commande**\n"
            . "   → Méthode 1: \"Commander 10 Paracétamol\"\n"
            . "   → Méthode 2: \"Commander Grippex\" (je demanderai la quantité)\n"
            . "   → Méthode 3: \"Je veux 2 unités de Antafen\"\n\n"
            . "⚠️ **Voir les stocks faibles**\n"
            . "   → \"Stocks faibles\"\n\n"
            . "🔍 **Analyser vos symptômes**\n"
            . "   → \"J'ai mal à la gorge\"\n\n"
            . "🚨 **Alertes**\n"
            . "   → \"Alertes\"\n\n"
            . "📊 **Statistiques**\n"
            . "   → \"Statistiques\"\n\n"
            . "👉 **Que voulez-vous faire ?**";
    }

    private function chercherMedicamentFouille($message)
    {
        $medicaments = Medicament::all();
        $messageClean = $this->nettoyerTexte($message);
        
        $motsParasites = ['commander', 'commande', 'acheter', 'de', 'le', 'la', 'les', 'des', 'pour', 'avec', 'veux', 'prendre', 'stock', 'stocker'];
        $messageClean = str_replace($motsParasites, '', $messageClean);
        $messageClean = trim($messageClean);
        
        if (empty($messageClean)) return null;
        
        foreach ($medicaments as $med) {
            $nom = $this->nettoyerTexte($med->nom_commercial_fr ?? $med->nom ?? '');
            
            if (str_contains($messageClean, $nom)) {
                return $med;
            }
            
            $motsMessage = explode(' ', $messageClean);
            foreach ($motsMessage as $motMsg) {
                if (strlen($motMsg) < 2) continue;
                if (str_contains($nom, $motMsg)) {
                    return $med;
                }
            }
        }
        return null;
    }
    
    private function chercherMedicamentParNom($nom)
    {
        $medicaments = Medicament::all();
        $nomClean = $this->nettoyerTexte($nom);
        $nomClean = str_replace(['commander', 'commande', 'acheter', 'de'], '', $nomClean);
        $nomClean = trim($nomClean);
        
        foreach ($medicaments as $med) {
            $nomMed = $this->nettoyerTexte($med->nom_commercial_fr ?? $med->nom ?? '');
            if (str_contains($nomMed, $nomClean) || str_contains($nomClean, $nomMed)) {
                return $med;
            }
        }
        return null;
    }

    private function contientMot($message, $mots)
    {
        foreach ($mots as $mot) {
            if (str_contains($message, $mot)) {
                return true;
            }
        }
        return false;
    }

    private function extraireQuantite($message)
    {
        preg_match('/(\d+)/', $message, $matches);
        return $matches[1] ?? null;
    }

    private function nettoyerTexte($texte)
    {
        $texte = strtolower($texte);
        $texte = preg_replace('/[éèêë]/', 'e', $texte);
        $texte = preg_replace('/[àâä]/', 'a', $texte);
        $texte = preg_replace('/[ùûü]/', 'u', $texte);
        $texte = preg_replace('/[îï]/', 'i', $texte);
        $texte = preg_replace('/[ôö]/', 'o', $texte);
        $texte = preg_replace('/[ç]/', 'c', $texte);
        $texte = preg_replace('/[^a-z0-9\s]/', '', $texte);
        return trim($texte);
    }
}
