# ADR-001 — Base de données cible

**Statut** : Accepté
**Date** : 2026-08-31
**Contexte** : choix de la base de données relationnelle pour PharmaTrack.

## Décision

PostgreSQL est la base de données cible, avec Hibernate (JPA) pour le mapping
objet-relationnel et **Flyway** pour la gestion des migrations de schéma.

## Justification

- **Relationnel adapté au domaine** : catalogue médicaments, lots, mouvements,
  alertes, commandes fournisseurs, utilisateurs. Le modèle est naturellement
  relationnel (clés étrangères, contraintes d'unicité partielles sur les alertes,
  transactions pour les mouvements de stock).
- **Fiabilité du schéma** : Flyway applique les migrations versionnées (`V1..V6`)
  de manière déterministe et idempotente, y compris un jeu de données initial
  (seed) requis par le `V4` (fournisseurs).
- **Parité dev/prod** : le `docker-compose.yml` utilise `postgres:16-alpine`,
  identique en local et en production.

## Conséquences

- Toute modification de schéma est versionnée dans `db/migration`.
- Le seed de base (`V3_1__seed_base_data.sql`) est nécessaire pour qu'une base
  **vide** démarre : il crée pharmacies, utilisateurs, médicaments, lots,
  mouvements et alertes avant le seed fournisseurs.
- Options de déploiement : Postgres local (cluster dédié, ex. sur 5433) ou via
  Docker (5432).
