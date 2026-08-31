-- ---------------------------------------------------------------------------
-- Approval-flow notifications: the alertes table now also carries admin
-- "inscription" (new pending account) and user "approbation" (account
-- approved) notices. The open (unresolved) unique guard ignores them since
-- lot_id stays NULL (Postgres treats NULLs as distinct in unique indexes).
-- ---------------------------------------------------------------------------

ALTER TABLE alertes
    DROP CONSTRAINT ck_alerte_type;

ALTER TABLE alertes
    ADD CONSTRAINT ck_alerte_type
        CHECK (type IN ('expiration', 'stock', 'rupture', 'qualite', 'autre',
                        'inscription', 'approbation'));