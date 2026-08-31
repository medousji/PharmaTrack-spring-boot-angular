package com.pharmatrack.chat.service;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.auth.entity.UserRole;
import com.pharmatrack.auth.repository.UserRepository;
import com.pharmatrack.chat.dto.ChatOverviewResponse;
import com.pharmatrack.chat.dto.CommandeChatResponse;
import com.pharmatrack.chat.dto.CommandeThreadResponse;
import com.pharmatrack.chat.dto.ConversationResponse;
import com.pharmatrack.chat.dto.ConversationThreadResponse;
import com.pharmatrack.chat.dto.EnvoyerMessageRequest;
import com.pharmatrack.chat.dto.MessageResponse;
import com.pharmatrack.chat.entity.Message;
import com.pharmatrack.chat.mapper.ChatMapper;
import com.pharmatrack.chat.repository.MessageRepository;
import com.pharmatrack.common.error.ForbiddenException;
import com.pharmatrack.common.error.ResourceNotFoundException;
import com.pharmatrack.common.error.UnauthorizedException;
import com.pharmatrack.fournisseur.dto.CommandeResponse;
import com.pharmatrack.fournisseur.entity.CommandeFournisseur;
import com.pharmatrack.fournisseur.entity.Fournisseur;
import com.pharmatrack.fournisseur.entity.FournisseurMedicament;
import com.pharmatrack.fournisseur.mapper.FournisseurMapper;
import com.pharmatrack.fournisseur.repository.CommandeFournisseurRepository;
import com.pharmatrack.fournisseur.repository.FournisseurMedicamentRepository;
import com.pharmatrack.fournisseur.repository.FournisseurRepository;
import org.springframework.data.domain.PageRequest;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.UUID;
import java.util.function.Function;
import java.util.stream.Collectors;

/**
 * Chat feature: order-linked threads and direct conversations. Access model
 * mirrors the legacy {@code ChatController}: a fournisseur only sees his own
 * commandes and talks to admins/pharmaciens; admins & pharmaciens see every
 * commande and talk to active (user-linked) fournisseurs.
 */
@Service
@Transactional
public class ChatService {

    private final MessageRepository messageRepository;
    private final UserRepository userRepository;
    private final FournisseurRepository fournisseurRepository;
    private final CommandeFournisseurRepository commandeRepository;
    private final FournisseurMedicamentRepository fournisseurMedicamentRepository;
    private final FournisseurMapper fournisseurMapper;
    private final ChatMapper mapper;

    public ChatService(MessageRepository messageRepository,
                       UserRepository userRepository,
                       FournisseurRepository fournisseurRepository,
                       CommandeFournisseurRepository commandeRepository,
                       FournisseurMedicamentRepository fournisseurMedicamentRepository,
                       FournisseurMapper fournisseurMapper,
                       ChatMapper mapper) {
        this.messageRepository = messageRepository;
        this.userRepository = userRepository;
        this.fournisseurRepository = fournisseurRepository;
        this.commandeRepository = commandeRepository;
        this.fournisseurMedicamentRepository = fournisseurMedicamentRepository;
        this.fournisseurMapper = fournisseurMapper;
        this.mapper = mapper;
    }

    @Transactional(readOnly = true)
    public ChatOverviewResponse overview(UUID userId) {
        User user = user(userId);
        boolean fournisseur = user.getRole() == UserRole.fournisseur;

        List<CommandeFournisseur> commandes = fournisseur
                ? commandesDuFournisseur(userId)
                : commandeRepository.findAllByOrderByCreatedAtDesc();

        List<CommandeChatResponse> commandesDto = commandes.stream()
                .map(c -> toCommandeChat(c, userId))
                .toList();

        List<ConversationResponse> conversations = contacts(userId, user)
                .stream().map(u -> toConversation(u, userId))
                .filter(java.util.Objects::nonNull)
                .toList();

        long totalNonLus = commandesDto.stream().mapToLong(CommandeChatResponse::nonLus).sum()
                + conversations.stream().mapToLong(ConversationResponse::nonLus).sum();

        return new ChatOverviewResponse(commandesDto, conversations, totalNonLus);
    }

    @Transactional
    public CommandeThreadResponse commandeThread(UUID commandeId, UUID userId) {
        User user = user(userId);
        CommandeFournisseur commande = commandeRepository.findById(commandeId)
                .orElseThrow(() -> new ResourceNotFoundException("commande", commandeId));

        if (user.getRole() == UserRole.fournisseur) {
            Fournisseur fournisseur = fournisseurRepository.findByUserId(userId).orElse(null);
            if (fournisseur == null || !commande.getFournisseur().getId().equals(fournisseur.getId())) {
                throw new ForbiddenException("Accès à cette commande refusé.");
            }
        }

        messageRepository.markLu(userId, commandeId);

        List<MessageResponse> messages = messageRepository.findByCommandeIdOrderByCreatedAtAsc(commandeId).stream()
                .map(this::toMessage)
                .toList();

        Function<UUID, Optional<FournisseurMedicament>> stockLookup =
                medicamentId -> fournisseurMedicamentRepository
                        .findByFournisseurIdAndMedicamentId(commande.getFournisseur().getId(), medicamentId);
        CommandeResponse dto = fournisseurMapper.toCommande(commande, stockLookup);

        return new CommandeThreadResponse(dto, messages);
    }

    @Transactional
    public ConversationThreadResponse conversationThread(UUID contactId, UUID userId) {
        User contact = user(contactId);
        messageRepository.markLu(userId, null);

        List<MessageResponse> messages = messageRepository.findThread(userId, contactId).stream()
                .map(this::toMessage)
                .toList();

        return new ConversationThreadResponse(contact.getId(), contact.getName(),
                contact.getRole().name(), messages);
    }

    public MessageResponse envoyer(EnvoyerMessageRequest request, UUID userId) {
        User expediteur = user(userId);

        UUID commandeId = request.commandeId();
        CommandeFournisseur commande = null;
        if (commandeId != null) {
            commande = commandeRepository.findById(commandeId)
                    .orElseThrow(() -> new ResourceNotFoundException("commande", commandeId));
            if (expediteur.getRole() == UserRole.fournisseur) {
                Fournisseur fournisseur = fournisseurRepository.findByUserId(userId).orElse(null);
                if (fournisseur == null || !commande.getFournisseur().getId().equals(fournisseur.getId())) {
                    throw new ForbiddenException("Accès à cette commande refusé.");
                }
            }
        }

        User destinataire;
        if (request.destinataireId() != null) {
            destinataire = user(request.destinataireId());
            if (commande == null && expediteur.getRole() == UserRole.fournisseur
                    && destinataire.getRole() != UserRole.admin
                    && destinataire.getRole() != UserRole.pharmacien) {
                throw new ForbiddenException(
                        "Un fournisseur ne converse qu'avec les administrateurs et les pharmaciens.");
            }
        } else if (commande != null) {
            destinataire = destinatairePourCommande(expediteur, commande);
        } else {
            throw new UnauthorizedException("Un destinataire est requis.");
        }

        Message message = new Message();
        message.setExpediteurId(userId);
        message.setDestinataireId(destinataire.getId());
        message.setCommandeId(commandeId);
        message.setMessage(request.message().trim());
        message.setEstLu(false);
        messageRepository.save(message);

        return mapper.toMessage(message, Optional.of(expediteur), Optional.of(destinataire));
    }

    // ------------------------------------------------------------------
    // helpers
    // ------------------------------------------------------------------

    private List<CommandeFournisseur> commandesDuFournisseur(UUID userId) {
        return fournisseurRepository.findByUserId(userId)
                .map(f -> commandeRepository.findByFournisseurIdOrderByCreatedAtDesc(f.getId()))
                .orElse(List.of());
    }

    private CommandeChatResponse toCommandeChat(CommandeFournisseur c, UUID userId) {
        Optional<Message> dernier = messageRepository.findFirstByCommandeIdOrderByCreatedAtDesc(c.getId());
        long nonLus = messageRepository.countByCommandeIdAndDestinataireIdAndEstLuFalse(c.getId(), userId);
        return mapper.toCommandeChat(c,
                dernier.map(Message::getMessage).orElse(null),
                dernier.map(Message::getCreatedAt).orElse(null),
                (int) nonLus);
    }

    /**
     * Potential dialogue partners for the current user. A fournisseur talks to
     * admins & pharmaciens; admins & pharmaciens talk to active fournisseurs
     * that have a linked user account.
     */
    private List<User> contacts(UUID userId, User user) {
        if (user.getRole() == UserRole.fournisseur) {
            return userRepository.findByRoleIn(List.of(UserRole.admin, UserRole.pharmacien));
        }
        List<Fournisseur> actifs = fournisseurRepository.findByEstActifTrue();
        List<UUID> userIds = actifs.stream()
                .map(Fournisseur::getUserId)
                .filter(java.util.Objects::nonNull)
                .toList();
        if (userIds.isEmpty()) {
            return List.of();
        }
        Map<UUID, User> byId = userRepository.findAllById(userIds).stream()
                .collect(Collectors.toMap(User::getId, u -> u));
        List<User> contacts = new ArrayList<>();
        for (UUID id : userIds) {
            User u = byId.get(id);
            if (u != null) {
                contacts.add(u);
            }
        }
        return contacts;
    }

    private ConversationResponse toConversation(User contact, UUID userId) {
        List<Message> latest = messageRepository.findLatest(contact.getId(), userId, PageRequest.of(0, 1));
        if (latest.isEmpty()) {
            return null;
        }
        long nonLus = messageRepository.countByExpediteurIdAndDestinataireIdAndEstLuFalse(contact.getId(), userId);
        return mapper.toConversation(contact, latest.get(0), (int) nonLus);
    }

    private MessageResponse toMessage(Message m) {
        return mapper.toMessage(m,
                userRepository.findById(m.getExpediteurId()),
                userRepository.findById(m.getDestinataireId()));
    }

    /**
     * Natural counterpart on an order thread. A fournisseur talks to an
     * admin/pharmacien already taking part in the thread (or the first one
     * available); an admin/pharmacien talks to the linked user of the
     * order's fournisseur.
     */
    private User destinatairePourCommande(User expediteur, CommandeFournisseur commande) {
        if (expediteur.getRole() == UserRole.fournisseur) {
            return messageRepository.findByCommandeIdOrderByCreatedAtAsc(commande.getId()).stream()
                    .map(Message::getExpediteurId)
                    .filter(id -> !id.equals(expediteur.getId()))
                    .map(userRepository::findById)
                    .flatMap(Optional::stream)
                    .filter(u -> u.getRole() == UserRole.admin || u.getRole() == UserRole.pharmacien)
                    .findFirst()
                    .orElseGet(() -> userRepository.findByRoleIn(List.of(UserRole.admin, UserRole.pharmacien))
                            .stream().filter(u -> !u.getId().equals(expediteur.getId())).findFirst()
                            .orElseThrow(() -> new UnauthorizedException(
                                    "Aucun interlocuteur (administrateur/pharmacien) disponible.")));
        }
        Fournisseur fournisseur = commande.getFournisseur();
        if (fournisseur.getUserId() == null) {
            throw new UnauthorizedException(
                    "Le fournisseur de cette commande n'a pas de compte de messagerie.");
        }
        return user(fournisseur.getUserId());
    }

    private User user(UUID id) {
        if (id == null) {
            throw new UnauthorizedException("Un utilisateur authentifié est requis.");
        }
        return userRepository.findById(id)
                .orElseThrow(() -> new UnauthorizedException("Un utilisateur authentifié est requis."));
    }
}