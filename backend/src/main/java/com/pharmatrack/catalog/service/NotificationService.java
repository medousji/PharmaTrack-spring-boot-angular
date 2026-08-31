package com.pharmatrack.catalog.service;

import com.pharmatrack.auth.entity.User;
import com.pharmatrack.catalog.entity.Alerte;
import com.pharmatrack.catalog.entity.AlerteNiveau;
import com.pharmatrack.catalog.entity.AlerteType;
import com.pharmatrack.catalog.repository.AlerteRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.LinkedHashMap;
import java.util.Map;

/**
 * Writes the approval-flow notifications into the {@code alertes} feed:
 * <ul>
 *   <li><b>inscription</b> — a new self-registered account is awaiting an
 *       admin's approval (one alert per admin);</li>
 *   <li><b>approbation</b> — an admin approved a pending account (the alert
 *       lands in the requesting user's own feed once they log in).</li>
 * </ul>
 *
 * <p>Both share the null-{@code lot} layout of the stock/rupture alerts, so
 * the {@code uq_alertes_type_lot_open} guard never deduplicates them.
 */
@Service
public class NotificationService {

    private final AlerteRepository alerteRepository;

    public NotificationService(AlerteRepository alerteRepository) {
        this.alerteRepository = alerteRepository;
    }

    @Transactional
    public void nouveauCompteEnAttente(User user) {
        Map<String, Object> donnees = new LinkedHashMap<>();
        donnees.put("user_id", user.getId().toString());
        donnees.put("nom", user.getName());
        donnees.put("email", user.getEmail());
        alerteRepository.save(newAlerte(AlerteType.inscription, AlerteNiveau.moyen,
                "Nouvelle inscription en attente d'approbation : " + user.getName()
                        + " (" + user.getEmail() + ")",
                donnees));
    }

    @Transactional
    public void compteApprouve(User user) {
        Map<String, Object> donnees = new LinkedHashMap<>();
        donnees.put("user_id", user.getId().toString());
        donnees.put("nom", user.getName());
        donnees.put("email", user.getEmail());
        alerteRepository.save(newAlerte(AlerteType.approbation, AlerteNiveau.faible,
                "Votre compte a été approuvé. Vous pouvez maintenant vous connecter.",
                donnees));
    }

    private Alerte newAlerte(AlerteType type, AlerteNiveau niveau, String message,
                             Map<String, Object> donnees) {
        Alerte alerte = new Alerte();
        alerte.setType(type);
        alerte.setNiveau(niveau);
        alerte.setMessage(message);
        alerte.setLot(null);
        alerte.setDonneesConcernees(donnees);
        alerte.setEstLue(false);
        return alerte;
    }
}