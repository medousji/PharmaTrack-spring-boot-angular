-- Epic 5: Chat (order-linked + direct) & Chatbot (Assistant Pharma IA)
-- Normalized port of the legacy messages / chatbot_conversations tables.

-- ---------------------------------------------------------------------------
-- MESSAGES
-- ---------------------------------------------------------------------------
CREATE TABLE messages (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    expediteur_id  UUID NOT NULL,
    destinataire_id UUID NOT NULL,
    commande_id    UUID,
    message        TEXT NOT NULL,
    est_lu         BOOLEAN NOT NULL DEFAULT FALSE,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_message_expediteur
        FOREIGN KEY (expediteur_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_message_destinataire
        FOREIGN KEY (destinataire_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_message_commande
        FOREIGN KEY (commande_id) REFERENCES commandes_fournisseurs (id) ON DELETE CASCADE
);

CREATE INDEX idx_messages_expediteur ON messages (expediteur_id);
CREATE INDEX idx_messages_destinataire ON messages (destinataire_id);
CREATE INDEX idx_messages_commande ON messages (commande_id);
CREATE INDEX idx_messages_created ON messages (created_at);

-- ---------------------------------------------------------------------------
-- CHATBOT_CONVERSATIONS
-- ---------------------------------------------------------------------------
CREATE TABLE chatbot_conversations (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id     UUID NOT NULL,
    question    TEXT NOT NULL,
    reponse     TEXT NOT NULL,
    intention   VARCHAR(64),
    donnees     JSONB,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_chatbot_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE INDEX idx_chatbot_conversations_user ON chatbot_conversations (user_id);
CREATE INDEX idx_chatbot_conversations_created ON chatbot_conversations (created_at);

-- ---------------------------------------------------------------------------
-- Epic 5 demo seed: one order-linked thread (CommandeFournisseur + messages),
-- one direct conversation, and a couple of assistant exchanges so the Chat and
-- Chatbot pages render immediately. User ids match the standard seed namespace
-- (b000…001 Admin, b000…002 Dr Amine, b000…004 Société MedSupply).
-- ---------------------------------------------------------------------------
INSERT INTO commandes_fournisseurs (id, numero_commande, fournisseur_id, pharmacie_id, user_id,
                                    date_commande, statut, total_ht, total_ttc,
                                    frais_livraison, created_at, updated_at)
VALUES ('d0000000-0000-0000-0000-000000000001', 'CMD-SEED-0001',
        'e0000000-0000-0000-0000-000000000001',
        'a0000000-0000-0000-0000-000000000001',
        'b0000000-0000-0000-0000-000000000002',
        CURRENT_DATE, 'confirmee', 12.600, 14.600, 2.000, now(), now());

INSERT INTO commande_fournisseur_lignes (id, commande_id, medicament_id, quantite,
                                         quantite_demandee, stock_avant, prix_unitaire,
                                         total_ligne, created_at, updated_at)
VALUES ('d0000000-0000-0000-0000-000000000002', 'd0000000-0000-0000-0000-000000000001',
        'c0000000-0000-0000-0000-000000000001', 10, 10, 45, 1.260, 12.600, now(), now());

-- Order-linked thread: Dr Amine (pharmacien) poursuivait sa commande avec MedSupply
INSERT INTO messages (expediteur_id, destinataire_id, commande_id, message, est_lu, created_at)
VALUES
('b0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000004',
 'd0000000-0000-0000-0000-000000000001',
 'Bonjour, pouvez-vous confirmer la commande CMD-SEED-0001 pour 10 boîtes de Doliprane ?',
 FALSE, now() - interval '3 hours'),
('b0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000002',
 'd0000000-0000-0000-0000-000000000001',
 'Bonjour Docteur, votre commande est confirmée. L''expédition est prévue demain matin.',
 FALSE, now() - interval '2 hours');

-- Direct conversation: MedSupply <-> Admin Système
INSERT INTO messages (expediteur_id, destinataire_id, commande_id, message, est_lu, created_at)
VALUES
('b0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000001', NULL,
 'Bonjour, notre tarif sur l''Augmentin 1 g vient d''être mis à jour dans le catalogue.',
 FALSE, now() - interval '1 day'),
('b0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000004', NULL,
 'Merci, c''est noté. Nous vérifierons les marges dès demain.',
 FALSE, now() - interval '20 hours');

-- Assistant example exchanges (Dr Amine)
INSERT INTO chatbot_conversations (user_id, question, reponse, intention, donnees, created_at)
VALUES
('b0000000-0000-0000-0000-000000000002',
 'Quel est le stock de Doliprane ?',
 'Le Doliprane (paracétamol 1000 mg) dispose de 45 unités en stock. Le stock est bon, aucun risque de rupture.',
 'stock', '{"medicament":"Doliprane","stock":45,"statut":"ok"}'::jsonb,
 now() - interval '2 hours'),
('b0000000-0000-0000-0000-000000000002',
 'Quelles sont les alertes actuelles ?',
 'Vous avez 3 alertes de stock: Augmentin (stock insuffisant), Crestor (stock faible) et Ventoline (stock faible).',
 'alertes', '{"nbAlertes":3,"alertes":["Augmentin","Crestor","Ventoline"]}'::jsonb,
 now() - interval '1 hour');