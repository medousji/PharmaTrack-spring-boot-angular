package com.pharmatrack.chatbot.service;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.repository.UserRepository;
import com.pharmatrack.catalog.dto.StockSummary;
import com.pharmatrack.catalog.entity.LotStatut;
import com.pharmatrack.catalog.entity.Medicament;
import com.pharmatrack.catalog.entity.MedicamentStatut;
import com.pharmatrack.catalog.repository.AlerteRepository;
import com.pharmatrack.catalog.repository.MedicamentRepository;
import com.pharmatrack.chatbot.dto.ChatbotHistoryItem;
import com.pharmatrack.chatbot.dto.ChatbotResponse;
import com.pharmatrack.chatbot.entity.ChatbotConversation;
import com.pharmatrack.chatbot.repository.ChatbotConversationRepository;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.fournisseur.dto.CommandeResult;
import com.pharmatrack.fournisseur.entity.FournisseurMedicament;
import com.pharmatrack.fournisseur.repository.FournisseurMedicamentRepository;
import com.pharmatrack.fournisseur.service.CommandeFournisseurService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.text.Normalizer;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.Optional;
import java.util.UUID;
import java.util.regex.Pattern;
import java.util.stream.Collectors;

/**
 * Assistant Pharma IA — port of the legacy {@code ChatbotController} brain.
 * Unlike the legacy model (which faked an order by decrementing a stock column),
 * the command confirmation creates a real {@code CommandeFournisseur} via
 * {@link CommandeFournisseurService}: cheapest supplier, order lines, stock
 * decrement to the reserved minimum and supplier alerts.
 */
@Service
@Transactional
public class ChatbotService {

    private static final Logger log = LoggerFactory.getLogger(ChatbotService.class);
    private static final String SEP = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";

    private static final List<String> MOTS_OUI = List.of(
            "oui", "yes", "y", "ok", "daccord", "confirme", "valide", "bien", "accepte", "okay", "o");
    private static final List<String> MOTS_NON = List.of(
            "non", "no", "n", "annuler", "stop", "arreter", "pas", "annule");
    private static final List<String> MOTS_PARASITES = List.of(
            "commander", "commande", "acheter", "de", "le", "la", "les", "des",
            "pour", "avec", "veux", "prendre", "stock", "stocker");

    private final ChatbotConversationRepository conversationRepository;
    private final UserRepository userRepository;
    private final MedicamentRepository medicamentRepository;
    private final FournisseurMedicamentRepository fmRepository;
    private final AlerteRepository alerteRepository;
    private final CommandeFournisseurService commandeService;

    public ChatbotService(ChatbotConversationRepository conversationRepository,
                          UserRepository userRepository,
                          MedicamentRepository medicamentRepository,
                          FournisseurMedicamentRepository fmRepository,
                          AlerteRepository alerteRepository,
                          CommandeFournisseurService commandeService) {
        this.conversationRepository = conversationRepository;
        this.userRepository = userRepository;
        this.medicamentRepository = medicamentRepository;
        this.fmRepository = fmRepository;
        this.alerteRepository = alerteRepository;
        this.commandeService = commandeService;
    }

    public ChatbotResponse message(String raw, UUID userId) {
        if (userId == null) {
            throw new UnauthorizedException("Un utilisateur authentifié est requis.");
        }
        User user = userRepository.findById(userId)
                .orElseThrow(() -> new UnauthorizedException("Un utilisateur authentifié est requis."));

        String question = raw == null ? "" : raw.trim();
        Result result;
        try {
            result = analyser(question, user);
        } catch (Exception e) {
            log.error("Chatbot error [user={}]", userId, e);
            return new ChatbotResponse(false, "❌ Une erreur est survenue. Veuillez réessayer.", null, null);
        }

        ChatbotConversation conv = new ChatbotConversation();
        conv.setUserId(userId);
        conv.setQuestion(question);
        conv.setReponse(result.reponse());
        conv.setIntention(result.intention());
        conv.setDonnees(result.donnees());
        conversationRepository.save(conv);

        return new ChatbotResponse(true, result.reponse(), result.intention(), result.donnees());
    }

    @Transactional(readOnly = true)
    public List<ChatbotHistoryItem> historique(UUID userId) {
        return conversationRepository.findTop20ByUserIdOrderByCreatedAtDesc(userId).stream()
                .map(c -> new ChatbotHistoryItem(c.getId(), c.getQuestion(), c.getReponse(),
                        c.getIntention(), c.getDonnees(), c.getCreatedAt()))
                .toList();
    }

    // ------------------------------------------------------------------
    // brain
    // ------------------------------------------------------------------

    private Result analyser(String message, User user) {
        String messageLower = nettoyer(message);
        Optional<ChatbotConversation> derniere =
                conversationRepository.findFirstByUserIdOrderByCreatedAtDesc(user.getId());

        String msg = messageLower.replaceAll("[^a-zA-Z]", "");
        boolean estOui = MOTS_OUI.contains(msg);
        boolean estNon = MOTS_NON.contains(msg);

        // ---- CONFIRMATION OUI (priorité absolue) ----
        if (estOui) {
            Map<String, Object> donnees = donneesDe(derniere);
            if ("confirmation".equals(donnees.get("action"))) {
                return confirmerCommande(donnees, user.getId());
            }
        }

        // ---- ANNULATION NON ----
        if (estNon) {
            Map<String, Object> donnees = donneesDe(derniere);
            if ("confirmation".equals(donnees.get("action"))) {
                return new Result(
                        "❌ **COMMANDE ANNULÉE**\n\n"
                                + "Pas de problème ! Votre commande a été annulée.\n\n"
                                + "👉 Souhaitez-vous autre chose ?\n\n"
                                + "💡 Tapez **'Aide'** pour voir ce que je peux faire.",
                        "commande_annulee", null);
            }
        }

        // ---- "commander N X" ----
        Pattern pAvecQte = Pattern.compile("commander\\s+(\\d+)\\s+(.+)", Pattern.CASE_INSENSITIVE);
        java.util.regex.Matcher m = pAvecQte.matcher(message);
        if (m.find()) {
            int quantite = Integer.parseInt(m.group(1).trim());
            String nomRecherche = m.group(2).trim();
            Optional<Medicament> med = chercherMedicamentParNom(nomRecherche);
            if (med.isPresent() && quantite > 0) {
                return commander(med.get(), quantite);
            }
        }

        // ---- "commander X" (sans quantité) ----
        if (!Pattern.compile("\\d").matcher(message).find()) {
            Pattern pSansQte = Pattern.compile(
                    "commander\\s+([a-zA-Zàâäéèêëîïôöùûüç\\s]+)", Pattern.CASE_INSENSITIVE);
            java.util.regex.Matcher m2 = pSansQte.matcher(message);
            if (m2.find()) {
                String nomRecherche = m2.group(1).trim();
                Optional<Medicament> med = chercherMedicamentParNom(nomRecherche);
                if (med.isPresent()) {
                    return demanderQuantite(med.get());
                }
            }
        }

        // ---- phrase naturelle "je veux N unités de X" ----
        Pattern pNaturel = Pattern.compile(
                "(\\d+)\\s*(unit\\u00e9|unit\\u00e9s|boite|bo\\u00eetes)?\\s*(de|d'|des)?\\s*"
                        + "([a-zA-Zàâäéèêëîïôöùûüç\\s]+)", Pattern.CASE_INSENSITIVE);
        java.util.regex.Matcher m3 = pNaturel.matcher(message);
        if (m3.find()) {
            int quantite = Integer.parseInt(m3.group(1).trim());
            String nomRecherche = m3.group(4).trim()
                    .replaceAll("[^a-zA-Zàâäéèêëîïôöùûüç\\s]", "").trim();
            Optional<Medicament> med = chercherMedicamentParNom(nomRecherche);
            if (med.isPresent() && quantite > 0) {
                return commander(med.get(), quantite);
            }
        }

        // ---- réponse à une demande de quantité ("10" ou "10 unités") ----
        Pattern pQuantite = Pattern.compile("^(\\d+)\\s*(unit\\u00e9|unit\\u00e9s|boite|bo\\u00eetes)?$",
                Pattern.CASE_INSENSITIVE);
        java.util.regex.Matcher m4 = pQuantite.matcher(message.trim());
        if (m4.find()) {
            Map<String, Object> donnees = donneesDe(derniere);
            if ("attente_quantite".equals(donnees.get("action"))) {
                int quantite = Integer.parseInt(m4.group(1).trim());
                UUID fmId = UUID.fromString(String.valueOf(donnees.get("fmId")));
                Optional<FournisseurMedicament> fm = fmRepository.findById(fmId);
                if (fm.isPresent()) {
                    return commander(fm.get(), quantite);
                }
            }
        }

        // ---- stocks faibles / rupture ----
        if (contientMot(messageLower, "stock faible", "stocks faibles", "rupture", "manque",
                "epuise", "plus de stock", "seuil")) {
            return new Result(getRecommandations(), "recommandations", null);
        }

        // ---- recherche médicament ----
        Optional<Medicament> med = chercherMedicamentFouille(message);
        if (med.isPresent()) {
            return new Result(stockDuMedicament(med.get()), "stock", null);
        }

        // ---- aide ----
        if (contientMot(messageLower, "aide", "help", "que faire", "?")) {
            return new Result(getAide(), "aide", null);
        }

        // ---- alertes ----
        if (contientMot(messageLower, "alerte", "notification", "urgent")) {
            long nbAlertes = alerteRepository.countByEstLueFalse();
            if (nbAlertes > 0) {
                return new Result("🚨 **VOUS AVEZ " + nbAlertes + " ALERTE(S) NON LUE(S)**\n\n"
                        + "👉 Cliquez sur **'Alertes'** dans le menu.", "alertes", null);
            }
            return new Result("✅ **AUCUNE ALERTE NON LUE**", "alertes", null);
        }

        // ---- statistiques ----
        if (contientMot(messageLower, "statistique", "stat", "chiffre", "total")) {
            long totalMedicaments = medicamentRepository.count();
            return new Result("📊 **STATISTIQUES**\n\n🏥 Médicaments : " + totalMedicaments,
                    "statistiques", null);
        }

        // ---- recommandations ----
        if (contientMot(messageLower, "recommandation", "suggestion", "quoi commander")) {
            return new Result(getRecommandations(), "recommandations", null);
        }

        // ---- bonjour ----
        if (contientMot(messageLower, "bonjour", "salut", "coucou", "hello")) {
            String prenom = user.getName().trim().split("\\s+")[0];
            return new Result("👋 **Bonjour " + prenom + " !**\n\nJe suis votre assistant Pharma Track.\n\n"
                    + "👉 Comment puis-je vous aider ?", "salutation", null);
        }

        // ---- merci ----
        if (contientMot(messageLower, "merci", "thanks")) {
            return new Result("🙏 **Avec plaisir !**", "remerciement", null);
        }

        // ---- au revoir ----
        if (contientMot(messageLower, "au revoir", "bye")) {
            return new Result("👋 **Au revoir !** À bientôt !", "au_revoir", null);
        }

        // ---- commande générique ----
        if (contientMot(messageLower, "commander", "acheter", "commande", "je veux commander",
                "passer commande")) {
            return new Result(
                    "🛒 **Passer une commande**\n\n"
                            + "Exemples :\n"
                            + "• \"Commander 10 Paracétamol\"\n"
                            + "• \"Commander Grippex\" (puis la quantité)\n"
                            + "• \"Je veux 2 unités de Antafen\"\n\n"
                            + "👉 Quel médicament souhaitez-vous commander ?",
                    "commande_generique", null);
        }

        // ---- analyse des symptômes ----
        if (contientMot(messageLower, "mal", "douleur", "fievre", "toux", "gorge", "tete", "ventre",
                "nausee", "diarrhee", "fatigue", "courbature", "rhume", "grippe", "allergie")) {
            return new Result(analyserSymptomes(messageLower), "symptomes", null);
        }

        // ---- réponse par défaut ----
        return new Result(
                "🤔 **Je n'ai pas bien compris votre demande.**\n\n"
                        + "Voici ce que je peux faire :\n\n"
                        + "📦 **Consulter un stock** → Tapez un nom de médicament\n"
                        + "   Exemple : \"Paracétamol\" ou \"Antafen\" ou \"Grippex\"\n\n"
                        + "🛒 **Passer une commande** :\n"
                        + "   • \"Commander 10 Paracétamol\"\n"
                        + "   • \"Commander Grippex\" (je demanderai la quantité)\n"
                        + "   • \"Je veux 2 unités de Antafen\"\n\n"
                        + "📋 **Stocks faibles** → \"Stocks faibles\"\n"
                        + "🔍 **Analyser symptômes** → \"J'ai mal à la gorge\"\n"
                        + "🚨 **Alertes** → \"Alertes\"\n"
                        + "📊 **Statistiques** → \"Statistiques\"\n"
                        + "💡 **Aide** → \"Aide\"\n\n"
                        + "👉 **Que souhaitez-vous faire ?**",
                "inconnu", null);
    }

    // ------------------------------------------------------------------
    // command flow (real CommandeFournisseur on confirmation)
    // ------------------------------------------------------------------

    /**
     * Build the confirmation bubble for {@code quantity} unités of a medicament
     * (transient — stored in {@code donnees} until the user replies Oui/Non).
     */
    private Result commander(Medicament med, int quantite) {
        return fmRepository.findByMedicamentIdAndDisponibleTrueOrderByPrixAchatAsc(med.getId()).stream()
                .findFirst()
                .map(fm -> commander(fm, quantite))
                .orElseGet(() -> new Result(
                        "🔴 **RUPTURE DE STOCK !**\n\n📦 **" + nom(med) + "** n'est plus disponible "
                                + "auprès des fournisseurs partenaires.",
                        "commande_rupture", null));
    }

    private Result commander(FournisseurMedicament fm, int quantite) {
        String nom = nom(fm.getMedicament());
        BigDecimal prix = fm.getPrixAchat();
        int stockDispo = fm.getStockDisponible();
        int stockMin = fm.getStockMinimum();
        int disponibleVente = stockDispo - stockMin;

        if (quantite <= 0) {
            return new Result("⚠️ La quantité doit être supérieure à zéro.", "commande_invalide", null);
        }
        if (disponibleVente <= 0) {
            return new Result("🔴 **STOCK INSUFFISANT !**\n\n📦 **" + nom + "**\n"
                    + "Stock disponible : " + stockDispo + " unités (minimum réservé : " + stockMin + ")",
                    "commande_rupture", null);
        }
        if (quantite > disponibleVente) {
            return new Result("⚠️ **Stock insuffisant !**\n\n📦 **" + nom + "**\n"
                    + "Stock disponible : " + stockDispo + " unités\n"
                    + "👉 Voulez-vous commander " + disponibleVente + " unités ? (Oui/Non)",
                    "commande_confirmation",
                    donneesCommande(fm, nom, disponibleVente, prix));
        }

        return new Result(confirmationTexte(nom, quantite, prix.multiply(BigDecimal.valueOf(quantite))),
                "commande_confirmation",
                donneesCommande(fm, nom, quantite, prix));
    }

    private Result demanderQuantite(Medicament med) {
        Optional<FournisseurMedicament> opt = fmRepository
                .findByMedicamentIdAndDisponibleTrueOrderByPrixAchatAsc(med.getId()).stream().findFirst();
        if (opt.isEmpty()) {
            return new Result("🔴 **RUPTURE DE STOCK !**\n\n📦 **" + nom(med)
                    + "** n'est plus disponible auprès des fournisseurs partenaires.",
                    "commande_rupture", null);
        }
        FournisseurMedicament fm = opt.get();
        String nom = nom(med);
        Map<String, Object> donnees = new LinkedHashMap<>();
        donnees.put("action", "attente_quantite");
        donnees.put("fmId", fm.getId().toString());
        donnees.put("medicamentId", med.getId().toString());
        donnees.put("nom", nom);
        donnees.put("prixUnitaire", fm.getPrixAchat());
        donnees.put("stockDisponible", fm.getStockDisponible());

        return new Result(
                "🛒 **Commande de " + nom + "**\n\n"
                        + SEP + "\n"
                        + "📦 Stock disponible : " + fm.getStockDisponible() + " unités\n"
                        + "💰 Prix unitaire : " + format(fm.getPrixAchat()) + " TND\n"
                        + SEP + "\n\n"
                        + "👉 **Combien d'unités souhaitez-vous commander ?**\n"
                        + "💡 Exemple : \"10\" ou \"10 unités\"",
                "commande_attente_quantite", donnees);
    }

    private Result confirmerCommande(Map<String, Object> donnees, UUID userId) {
        UUID fmId = UUID.fromString(String.valueOf(donnees.get("fmId")));
        int quantite = ((Number) donnees.get("quantite")).intValue();
        String nom = String.valueOf(donnees.get("nom"));

        CommandeResult result = commandeService.passerCommande(fmId, quantite, userId);
        if (result.success() && result.commande() != null) {
            Map<String, Object> infos = new LinkedHashMap<>();
            infos.put("action", "commande");
            infos.put("commandeId", result.commande().id().toString());
            infos.put("numeroCommande", result.commande().numeroCommande());
            infos.put("nom", nom);
            infos.put("quantite", result.quantiteCommandee());
            infos.put("stockApres", result.stockApres());

            String partial = result.quantiteManquante() > 0
                    ? "📦 Commande partielle — " + result.quantiteManquante() + " unité(s) en attente.\n"
                    : "";
            return new Result(
                    "✅ **COMMANDE CONFIRMÉE !**\n\n"
                            + SEP + "\n"
                            + "📦 **Médicament :** " + nom + "\n"
                            + "🔢 **Quantité :** " + result.quantiteCommandee() + " unités\n"
                            + "💰 **Total :** " + formatTtc(result.commande().totalHt()) + " TND\n"
                            + "🆔 **N° commande :** " + result.commande().numeroCommande() + "\n"
                            + SEP + "\n\n"
                            + partial
                            + "✅ Votre commande a été enregistrée avec succès !\n"
                            + "📊 Stock libéré : " + result.stockApres() + " unités\n\n"
                            + "🙏 Merci de votre confiance !\n\n"
                            + "💡 Souhaitez-vous autre chose ?",
                    "commande_confirmee", infos);
        }

        Map<String, Object> echec = new LinkedHashMap<>();
        echec.put("action", "commande_echec");
        echec.put("message", result.message());
        return new Result("⚠️ **Commande non confirmée**\n\n" + (result.message() == null
                ? "Le stock ne permet pas de passer cette commande."
                : result.message())
                + "\n\n👉 Souhaitez-vous réessayer ?", "commande_echec", echec);
    }

    private Map<String, Object> donneesCommande(FournisseurMedicament fm, String nom,
                                                int quantite, BigDecimal prix) {
        Map<String, Object> donnees = new LinkedHashMap<>();
        donnees.put("action", "confirmation");
        donnees.put("fmId", fm.getId().toString());
        donnees.put("medicamentId", fm.getMedicament().getId().toString());
        donnees.put("nom", nom);
        donnees.put("quantite", quantite);
        donnees.put("prixUnitaire", prix);
        donnees.put("total", prix.multiply(BigDecimal.valueOf(quantite)));
        return donnees;
    }

    private String confirmationTexte(String nom, int quantite, BigDecimal total) {
        return "🛒 **Commande de " + nom + "**\n\n"
                + SEP + "\n"
                + "📦 Quantité : " + quantite + " unités\n"
                + "💰 Prix unitaire : à confirmer par le fournisseur\n"
                + "💵 Total estimé : " + format(total) + " TND\n"
                + SEP + "\n\n"
                + "✅ **Confirmez-vous cette commande ?** (Oui/Non)";
    }

    // ------------------------------------------------------------------
    // stock / info branches
    // ------------------------------------------------------------------

    private String stockDuMedicament(Medicament med) {
        String nom = nom(med);
        long stock = stockActif(med.getId());
        long min = med.getStockMin() == null ? 0 : med.getStockMin();

        String reponse = "📦 **" + nom + "**\n"
                + SEP + "\n"
                + "📊 Stock actuel : **" + stock + "** unités\n"
                + SEP + "\n";
        if (stock <= 0) {
            reponse += "\n🔴 **RUPTURE DE STOCK !**";
        } else if (stock <= min) {
            reponse += "\n⚠️ **STOCK FAIBLE !** Il ne reste que " + stock + " unités.";
        } else {
            reponse += "\n✅ **Stock suffisant** pour le moment.";
        }
        reponse += "\n\n💡 Tapez *\"Commander " + nom + "\"* pour passer commande.";
        return reponse;
    }

    private String getRecommandations() {
        List<Medicament> faibles = recommanderFaibles();
        if (!faibles.isEmpty()) {
            StringBuilder reponse = new StringBuilder("⚠️ **MÉDICAMENTS AVEC STOCK FAIBLE :**\n\n");
            for (Medicament med : faibles) {
                long stock = stockActif(med.getId());
                String icone = stock <= 0 ? "🔴" : (stock <= 5 ? "🟠" : "🟡");
                reponse.append(icone).append(" **").append(nom(med)).append("**\n");
                reponse.append("   📊 Stock restant : ").append(stock).append(" unités\n\n");
            }
            reponse.append("👉 Tapez *\"Commander [nom] [quantité]\"* pour passer commande.");
            return reponse.toString();
        }
        return "✅ **Aucun stock faible détecté !**\n\nTous les stocks sont à des niveaux corrects.";
    }

    private List<Medicament> recommanderFaibles() {
        Map<UUID, StockSummary> stocks = medicamentRepository
                .aggregateStockAll(LotStatut.actif, LotStatut.perime).stream()
                .collect(Collectors.toMap(StockSummary::medicamentId, s -> s, (a, b) -> a));

        List<Medicament> faibles = new ArrayList<>();
        for (Medicament med : medicamentRepository.findAllNonRetired(MedicamentStatut.retire)) {
            long stock = stocks.containsKey(med.getId())
                    ? stocks.get(med.getId()).stockActif() : 0;
            long min = med.getStockMin() == null ? 0 : med.getStockMin();
            long seuil = med.getSeuilAlerte() == null ? 20 : med.getSeuilAlerte();
            if (stock <= Math.max(seuil, min)) {
                faibles.add(med);
            }
        }
        faibles.sort((a, b) -> Long.compare(stockActif(a.getId()), stockActif(b.getId())));
        return faibles.stream().limit(5).toList();
    }

    private String analyserSymptomes(String message) {
        if (message.contains("gorge")) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                    + "📋 Symptômes détectés : Mal de gorge\n\n"
                    + "💊 **Médicaments suggérés :**\n"
                    + "• **Paracétamol** - Pour la douleur\n"
                    + "• **Augmentin** - Antibiotique (sur prescription)\n\n"
                    + "⚠️ Consultez un médecin pour un diagnostic précis.\n\n"
                    + "💡 Tapez le nom du médicament pour voir son stock.";
        }
        if (message.contains("tete") || message.contains("migraine")) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                    + "📋 Symptômes détectés : Mal de tête\n\n"
                    + "💊 **Médicaments suggérés :**\n"
                    + "• **Paracétamol** - Antalgique\n"
                    + "• **Ibuprofène** - Anti-inflammatoire\n\n"
                    + "⚠️ Consultez un médecin si les symptômes persistent.";
        }
        if (message.contains("toux")) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                    + "📋 Symptômes détectés : Toux\n\n"
                    + "💊 **Médicaments suggérés :**\n"
                    + "• **Tussidane** - Toux sèche\n"
                    + "• **Exomuc** - Toux grasse\n\n"
                    + "⚠️ Consultez un médecin pour un diagnostic précis.";
        }
        if (message.contains("fievre")) {
            return "🔍 **ANALYSE DE VOS SYMPTÔMES**\n\n"
                    + "📋 Symptômes détectés : Fièvre\n\n"
                    + "💊 **Médicaments suggérés :**\n"
                    + "• **Paracétamol** - Antipyrétique\n"
                    + "• **Doliprane** - Pour forte fièvre\n\n"
                    + "⚠️ Consultez un médecin si la fièvre persiste.";
        }
        return "🔍 **Je n'ai pas détecté de symptômes spécifiques.**\n\n"
                + "👉 Pouvez-vous décrire plus précisément ce que vous ressentez ?";
    }

    private String getAide() {
        return "💡 **GUIDE D'UTILISATION**\n\n"
                + "📦 **Consulter un stock**\n"
                + "   → Tapez le nom d'un médicament\n"
                + "   Ex: \"Paracétamol\" ou \"Antafen\" ou \"Grippex\"\n\n"
                + "🛒 **Passer une commande**\n"
                + "   → Méthode 1: \"Commander 10 Paracétamol\"\n"
                + "   → Méthode 2: \"Commander Grippex\" (je demanderai la quantité)\n"
                + "   → Méthode 3: \"Je veux 2 unités de Antafen\"\n\n"
                + "⚠️ **Voir les stocks faibles**\n"
                + "   → \"Stocks faibles\"\n\n"
                + "🔍 **Analyser vos symptômes**\n"
                + "   → \"J'ai mal à la gorge\"\n\n"
                + "🚨 **Alertes**\n"
                + "   → \"Alertes\"\n\n"
                + "📊 **Statistiques**\n"
                + "   → \"Statistiques\"\n\n"
                + "👉 **Que voulez-vous faire ?**";
    }

    // ------------------------------------------------------------------
    // medicament matching
    // ------------------------------------------------------------------

    private Optional<Medicament> chercherMedicamentParNom(String nom) {
        String nomClean = nettoyer(nom);
        for (String parasite : MOTS_PARASITES) {
            nomClean = nomClean.replace(parasite, "");
        }
        nomClean = nomClean.trim();
        if (nomClean.isEmpty()) {
            return Optional.empty();
        }
        for (Medicament med : medicamentRepository.findAllNonRetired(MedicamentStatut.retire)) {
            String nomMed = nettoyer(nom(med));
            if (!nomMed.isEmpty() && (nomMed.contains(nomClean) || nomClean.contains(nomMed))) {
                return Optional.of(med);
            }
        }
        return Optional.empty();
    }

    private Optional<Medicament> chercherMedicamentFouille(String message) {
        String messageClean = nettoyer(message);
        for (String parasite : MOTS_PARASITES) {
            messageClean = messageClean.replace(parasite, "");
        }
        messageClean = messageClean.trim();
        if (messageClean.isEmpty()) {
            return Optional.empty();
        }
        for (Medicament med : medicamentRepository.findAllNonRetired(MedicamentStatut.retire)) {
            String nomMed = nettoyer(nom(med));
            if (nomMed.isEmpty()) {
                continue;
            }
            if (messageClean.contains(nomMed)) {
                return Optional.of(med);
            }
            for (String mot : messageClean.split("\\s+")) {
                if (mot.length() < 2) {
                    continue;
                }
                if (nomMed.contains(mot)) {
                    return Optional.of(med);
                }
            }
        }
        return Optional.empty();
    }

    private long stockActif(UUID medicamentId) {
        return medicamentRepository.aggregateStock(List.of(medicamentId),
                        LotStatut.actif, LotStatut.perime).stream()
                .findFirst().map(StockSummary::stockActif).orElse(0L);
    }

    // ------------------------------------------------------------------
    // helpers
    // ------------------------------------------------------------------

    private record Result(String reponse, String intention, Map<String, Object> donnees) {
    }

    private Map<String, Object> donneesDe(Optional<ChatbotConversation> derniere) {
        if (derniere.isEmpty() || derniere.get().getDonnees() == null) {
            return Map.of();
        }
        return derniere.get().getDonnees();
    }

    private boolean contientMot(String message, String... mots) {
        for (String mot : mots) {
            if (message.contains(mot)) {
                return true;
            }
        }
        return false;
    }

    private String nom(Medicament med) {
        return med.getNomCommercialFr() != null ? med.getNomCommercialFr() : "Médicament";
    }

    private String format(BigDecimal montant) {
        return String.format(Locale.FRANCE, "%.3f", montant);
    }

    private String formatTtc(BigDecimal montant) {
        return String.format(Locale.FRANCE, "%.3f", montant);
    }

    private String nettoyer(String texte) {
        String s = texte == null ? "" : texte;
        s = Normalizer.normalize(s, Normalizer.Form.NFD)
                .replaceAll("\\p{M}", "")
                .toLowerCase(Locale.FRENCH)
                .replaceAll("[^a-z0-9\\s]", "");
        return s.trim();
    }
}