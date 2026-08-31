-- Epic 1: Auth & Users (normalized schema — no legacy dual fields).
-- Pharmacie, User (JWT auth + admin approval), RefreshToken (rotation/blacklist).

-- ---------------------------------------------------------------------------
-- PHARMACIES
-- ---------------------------------------------------------------------------
CREATE TABLE pharmacies (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom            VARCHAR(255) NOT NULL,
    adresse        VARCHAR(255),
    telephone      VARCHAR(64),
    email          VARCHAR(255),
    licence_number VARCHAR(128),
    responsable    VARCHAR(255),
    est_active     BOOLEAN NOT NULL DEFAULT TRUE,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ---------------------------------------------------------------------------
-- USERS
-- ---------------------------------------------------------------------------
CREATE TABLE users (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pharmacie_id   UUID,
    name           VARCHAR(255) NOT NULL,
    email          VARCHAR(255) NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NOT NULL,
    role           VARCHAR(32)  NOT NULL DEFAULT 'visiteur',
    status         VARCHAR(16)  NOT NULL DEFAULT 'active',
    is_approved    BOOLEAN      NOT NULL DEFAULT FALSE,
    approved_at    TIMESTAMPTZ,
    last_login_at  TIMESTAMPTZ,
    last_login_ip  VARCHAR(64),
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_user_pharmacie
        FOREIGN KEY (pharmacie_id) REFERENCES pharmacies (id),
    CONSTRAINT ck_user_role
        CHECK (role IN ('admin', 'pharmacien', 'fournisseur', 'visiteur')),
    CONSTRAINT ck_user_status
        CHECK (status IN ('active', 'inactive', 'suspended'))
);

CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_status ON users (status);
CREATE INDEX idx_users_role ON users (role);

-- ---------------------------------------------------------------------------
-- REFRESH TOKENS (persisted for rotation + revocation/blacklist)
-- jti = JWT ID claim; used to validate, rotate and revoke refresh tokens.
-- ---------------------------------------------------------------------------
CREATE TABLE refresh_tokens (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id     UUID NOT NULL,
    jti         UUID NOT NULL UNIQUE,
    token_hash  VARCHAR(128) NOT NULL,
    expires_at  TIMESTAMPTZ NOT NULL,
    revoked     BOOLEAN NOT NULL DEFAULT FALSE,
    revoked_at  TIMESTAMPTZ,
    replaced_by UUID,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_refresh_token_user
        FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE INDEX idx_refresh_tokens_user ON refresh_tokens (user_id);
CREATE INDEX idx_refresh_tokens_expires ON refresh_tokens (expires_at);
