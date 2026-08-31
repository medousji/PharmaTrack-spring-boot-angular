# Rôles & cycle de vie des permissions

Ce document décrit les rôles utilisateurs, leurs permissions et le cycle de vie
(inscription → approbation → activation) dans PharmaTrack.

## Rôles

| Rôle | Périmètre | Droits d'écriture | Accès |
| --- | --- | --- | --- |
| `admin` | Tout le back-office | Gestion des utilisateurs, approbation des visiteurs, écriture catalogue/stock | `/admin/**` |
| `pharmacien` | Exploitation de la pharmacie | Catalogue, lots, ajustements de stock, prédictions, conformité, commandes | Routage standard |
| `fournisseur` | Portail fournisseur | Commandes (expédition), prix | `/fournisseur/**` |
| `visiteur` | Consultation seule | Aucune écriture | Lecture seule |

## Cycle de vie d'un utilisateur

1. **Inscription** (`POST /api/v1/auth/register`) — un visiteur crée un compte.
   Le compte est créé avec `is_approved = false`.
2. **Approbation** — un admin approuve le visiteur via
   `POST /api/v1/admin/users/{id}/approve` (ou le rejette via `/reject`).
   Tant que `is_approved = false`, la connexion (`/auth/login`) est **refusée**.
3. **Activation** — statut du compte (`active` / `inactive`). Le seed initiale
   crée par exemple Nadia (visiteur vrai, approuvé) et Karim (visiteur non
   approuvé, pour illustrer le flux d'approbation).
4. **Fin de vie** — un admin peut désactiver / supprimer un utilisateur
   (`PUT /admin/users/{id}`, `DELETE /admin/users/{id}`).

## Point d'entrée côté frontend

- `AuthService` conserve la session dans `localStorage` (jetons `pharmatrack_access`
  / `pharmatrack_refresh`, utilisateur `pharmatrack_user`).
- Les routes sont protégées par `authGuard` (`isAuthenticated()`), avec des gardes
  de rôle (`hasRole`) pour `/admin/**` et `/fournisseur/**`.
- `admin/users` liste les utilisateurs, `admin/users/pending` liste ceux en
  attente d'approbation.

## Notes d'implémentation (bugs corrigés)

- **Persistance de session** : `setSession()` n'écrivait pas la clé
  `pharmatrack_user` dans `localStorage`, ce qui déconnectait tout utilisateur
  à chaque rechargement de page (raison : `isAuthenticated()` exige un utilisateur
  courant non-null, rechargé depuis `localStorage`). Corrigé en persistant
  l'utilisateur dans `setSession()` et `me()`.
