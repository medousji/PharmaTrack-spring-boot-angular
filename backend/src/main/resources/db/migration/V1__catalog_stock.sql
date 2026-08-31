-- Epic 2: Catalog & Stock schema (normalized — no legacy dual fields).
-- Medicament, Lot, Mouvement (append-only ledger), Alerte.
-- Foreign keys to pharmacie/users are intentionally omitted at this stage;
-- they arrive with the auth (Epic 1) and procurement (Epic 4) modules.

CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ---------------------------------------------------------------------------
-- MEDICAMENTS
-- ---------------------------------------------------------------------------
CREATE TABLE medicaments (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    code_cip                    VARCHAR(64)    NOT NULL UNIQUE,
    nom_commercial_fr           VARCHAR(255),
    nom_commercial_ar           VARCHAR(255),
    dci                         VARCHAR(255),
    forme_pharmaceutique        VARCHAR(255),
    dosage                      VARCHAR(128),
    conditionnement             VARCHAR(128),
    ppv                         NUMERIC(12, 3),
    ph                          NUMERIC(12, 3),
    prix_br                     NUMERIC(12, 3),
    prix_public                 NUMERIC(12, 3),
    taux_remboursement          NUMERIC(5, 2),
    laboratoire                 VARCHAR(255),
    pays_origine                VARCHAR(128),
    stock_min                   INTEGER,
    stock_max                   INTEGER,
    seuil_alerte                INTEGER,
    classe_therapeutique        VARCHAR(255),
    voie_administration         VARCHAR(128),
    contre_indications          TEXT,
    effets_indesirables         TEXT,
    interactions_medicamenteuses TEXT,
    conditions_conservation     VARCHAR(255),
    code_atc                    VARCHAR(64),
    est_psychotrope             BOOLEAN      NOT NULL DEFAULT FALSE,
    est_ther_lourde             BOOLEAN      NOT NULL DEFAULT FALSE,
    est_renouvelable            BOOLEAN      NOT NULL DEFAULT TRUE,
    delai_renouvellement        INTEGER,
    code_barre                  VARCHAR(128),
    est_generique               BOOLEAN      NOT NULL DEFAULT FALSE,
    medicament_reference_id     UUID,
    statut                      VARCHAR(16)  NOT NULL DEFAULT 'actif',
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_medicament_reference
        FOREIGN KEY (medicament_reference_id) REFERENCES medicaments (id),
    CONSTRAINT ck_medicament_statut
        CHECK (statut IN ('actif', 'inactif', 'retire'))
);

CREATE INDEX idx_medicaments_nom_fr ON medicaments (nom_commercial_fr);
CREATE INDEX idx_medicaments_dci ON medicaments (dci);
CREATE INDEX idx_medicaments_classe ON medicaments (classe_therapeutique);
CREATE INDEX idx_medicaments_statut ON medicaments (statut);
CREATE INDEX idx_medicaments_code_barre ON medicaments (code_barre);

-- ---------------------------------------------------------------------------
-- LOTS
-- ---------------------------------------------------------------------------
CREATE TABLE lots (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    medicament_id       UUID         NOT NULL,
    numero_lot          VARCHAR(128) NOT NULL,
    date_fabrication    DATE,
    date_peremption     DATE         NOT NULL,
    quantite_initiale   INTEGER      NOT NULL,
    quantite_actuelle   INTEGER      NOT NULL,
    fournisseur_nom     VARCHAR(255),
    date_reception      DATE,
    statut              VARCHAR(16)  NOT NULL DEFAULT 'actif',
    prix_achat          NUMERIC(12, 3),
    prix_vente          NUMERIC(12, 3),
    numero_facture      VARCHAR(128),
    emplacement         VARCHAR(128),
    observations        TEXT,
    created_at          TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT fk_lot_medicament
        FOREIGN KEY (medicament_id) REFERENCES medicaments (id),
    CONSTRAINT ck_lot_statut
        CHECK (statut IN ('actif', 'epuise', 'perime', 'bloque'))
);

CREATE INDEX idx_lots_medicament ON lots (medicament_id);
CREATE INDEX idx_lots_peremption ON lots (date_peremption);
CREATE INDEX idx_lots_statut ON lots (statut);
CREATE INDEX idx_lots_numero ON lots (numero_lot);

-- ---------------------------------------------------------------------------
-- MOUVEMENTS (append-only stock ledger)
-- ---------------------------------------------------------------------------
CREATE TABLE mouvements (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    lot_id          UUID         NOT NULL,
    pharmacie_id    UUID,
    user_id         UUID,
    type            VARCHAR(16)  NOT NULL,
    quantite        INTEGER      NOT NULL,
    quantite_avant  INTEGER      NOT NULL,
    quantite_apres  INTEGER      NOT NULL,
    reference       VARCHAR(128),
    motif           VARCHAR(255),
    scanned_at      TIMESTAMPTZ,
    created_at      TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT fk_mouvement_lot
        FOREIGN KEY (lot_id) REFERENCES lots (id),
    CONSTRAINT ck_mouvement_type
        CHECK (type IN ('entree', 'sortie', 'ajustement'))
);

CREATE INDEX idx_mouvements_lot ON mouvements (lot_id);
CREATE INDEX idx_mouvements_pharmacie ON mouvements (pharmacie_id);
CREATE INDEX idx_mouvements_type ON mouvements (type);
CREATE INDEX idx_mouvements_created ON mouvements (created_at);

-- ---------------------------------------------------------------------------
-- ALERTES
-- ---------------------------------------------------------------------------
CREATE TABLE alertes (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    lot_id              UUID,
    type                VARCHAR(16)  NOT NULL,
    niveau              VARCHAR(16)  NOT NULL,
    message             VARCHAR(500) NOT NULL,
    donnees_concernees  JSONB        NOT NULL DEFAULT '{}'::jsonb,
    est_lue             BOOLEAN      NOT NULL DEFAULT FALSE,
    resolue_at          TIMESTAMPTZ,
    created_at          TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT fk_alerte_lot
        FOREIGN KEY (lot_id) REFERENCES lots (id),
    CONSTRAINT ck_alerte_type
        CHECK (type IN ('expiration', 'stock', 'rupture', 'qualite', 'autre')),
    CONSTRAINT ck_alerte_niveau
        CHECK (niveau IN ('faible', 'moyen', 'eleve', 'critique'))
);

CREATE INDEX idx_alertes_type ON alertes (type);
CREATE INDEX idx_alertes_niveau ON alertes (niveau);
CREATE INDEX idx_alertes_lue ON alertes (est_lue);
CREATE INDEX idx_alertes_lot ON alertes (lot_id);
-- De-duplication guard: at most one open (unresolved) alert per type+lot.
CREATE UNIQUE INDEX uq_alertes_type_lot_open
    ON alertes (lot_id, type)
    WHERE resolue_at IS NULL;
