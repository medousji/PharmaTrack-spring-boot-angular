-- Epic 4: Supplier (fournisseur) & purchase order (commande) flow
-- Normalized port of the legacy fournisseurs / fournisseur_medicaments /
-- commandes_fournisseurs / commande_fournisseur_lignes tables.

-- ---------------------------------------------------------------------------
-- FOURNISSEURS
-- ---------------------------------------------------------------------------
CREATE TABLE fournisseurs (
    id                     UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id                UUID,
    raison_sociale         VARCHAR(255) NOT NULL,
    matricule_fiscal       VARCHAR(128),
    pays_origine           VARCHAR(128),
    specialite             VARCHAR(255),
    fax                    VARCHAR(64),
    code_postal            VARCHAR(32),
    ville                  VARCHAR(128),
    gouvernorat            VARCHAR(128),
    contact_poste          VARCHAR(255),
    adresse                VARCHAR(255),
    telephone              VARCHAR(64),
    email_pro              VARCHAR(255),
    contact_nom            VARCHAR(255),
    contact_telephone      VARCHAR(64),
    site_web               VARCHAR(255),
    delai_livraison_moyen  INTEGER NOT NULL DEFAULT 7,
    frais_livraison        NUMERIC(10, 3) NOT NULL DEFAULT 0,
    note                   NUMERIC(3, 2),
    est_actif              BOOLEAN NOT NULL DEFAULT TRUE,
    relance_active         BOOLEAN NOT NULL DEFAULT TRUE,
    derniere_relance       TIMESTAMPTZ,
    nb_relances            INTEGER NOT NULL DEFAULT 0,
    notes                  TEXT,
    created_at             TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at             TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_fournisseur_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
);

CREATE INDEX idx_fournisseurs_user ON fournisseurs (user_id);

-- ---------------------------------------------------------------------------
-- FOURNISSEUR_MEDICAMENTS
-- ---------------------------------------------------------------------------
CREATE TABLE fournisseur_medicaments (
    id                       UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    fournisseur_id           UUID NOT NULL,
    medicament_id            UUID NOT NULL,
    reference_fournisseur    VARCHAR(128),
    prix_achat               NUMERIC(10, 3) NOT NULL,
    prix_public              NUMERIC(10, 3),
    stock_disponible         INTEGER NOT NULL DEFAULT 0,
    stock_minimum            INTEGER NOT NULL DEFAULT 10,
    stock_maximum            INTEGER,
    seuil_reapprovisionnement INTEGER NOT NULL DEFAULT 20,
    delai_livraison          INTEGER,
    disponible               BOOLEAN NOT NULL DEFAULT TRUE,
    derniere_mise_a_jour     DATE,
    created_at               TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at               TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_fm_fournisseur
        FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs (id) ON DELETE CASCADE,
    CONSTRAINT fk_fm_medicament
        FOREIGN KEY (medicament_id) REFERENCES medicaments (id) ON DELETE CASCADE,
    CONSTRAINT uq_fm_fournisseur_medicament UNIQUE (fournisseur_id, medicament_id)
);

CREATE INDEX idx_fm_fournisseur ON fournisseur_medicaments (fournisseur_id);
CREATE INDEX idx_fm_medicament ON fournisseur_medicaments (medicament_id);
CREATE INDEX idx_fm_disponible ON fournisseur_medicaments (disponible);

-- ---------------------------------------------------------------------------
-- COMMANDES_FOURNISSEURS
-- ---------------------------------------------------------------------------
CREATE TABLE commandes_fournisseurs (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    numero_commande         VARCHAR(64) NOT NULL UNIQUE,
    fournisseur_id          UUID NOT NULL,
    pharmacie_id            UUID,
    user_id                 UUID,
    date_commande           DATE NOT NULL,
    date_livraison_prevue   DATE,
    date_livraison_reelle   DATE,
    statut                  VARCHAR(16) NOT NULL DEFAULT 'en_attente',
    total_ht                NUMERIC(12, 3) NOT NULL DEFAULT 0,
    total_ttc               NUMERIC(12, 3) NOT NULL DEFAULT 0,
    frais_livraison         NUMERIC(10, 3) NOT NULL DEFAULT 0,
    notes                   TEXT,
    adresse_livraison       VARCHAR(255),
    created_at              TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at              TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_commande_fournisseur
        FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs (id) ON DELETE CASCADE,
    CONSTRAINT fk_commande_pharmacie
        FOREIGN KEY (pharmacie_id) REFERENCES pharmacies (id) ON DELETE SET NULL,
    CONSTRAINT fk_commande_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT ck_commande_statut
        CHECK (statut IN ('en_attente', 'confirmee', 'preparation', 'partiel',
                          'expediee', 'livree', 'annulee'))
);

CREATE INDEX idx_commandes_fournisseur ON commandes_fournisseurs (fournisseur_id);
CREATE INDEX idx_commandes_statut ON commandes_fournisseurs (statut);
CREATE INDEX idx_commandes_date ON commandes_fournisseurs (date_commande);
CREATE INDEX idx_commandes_created ON commandes_fournisseurs (created_at);

-- ---------------------------------------------------------------------------
-- COMMANDE_FOURNISSEUR_LIGNES
-- ---------------------------------------------------------------------------
CREATE TABLE commande_fournisseur_lignes (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    commande_id         UUID NOT NULL,
    medicament_id       UUID NOT NULL,
    quantite            INTEGER NOT NULL,
    quantite_demandee   INTEGER NOT NULL DEFAULT 0,
    stock_avant         INTEGER NOT NULL DEFAULT 0,
    prix_unitaire       NUMERIC(10, 3) NOT NULL,
    total_ligne         NUMERIC(12, 3) NOT NULL,
    notes               TEXT,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_ligne_commande
        FOREIGN KEY (commande_id) REFERENCES commandes_fournisseurs (id) ON DELETE CASCADE,
    CONSTRAINT fk_ligne_medicament
        FOREIGN KEY (medicament_id) REFERENCES medicaments (id) ON DELETE CASCADE
);

CREATE INDEX idx_lignes_commande ON commande_fournisseur_lignes (commande_id);
CREATE INDEX idx_lignes_medicament ON commande_fournisseur_lignes (medicament_id);