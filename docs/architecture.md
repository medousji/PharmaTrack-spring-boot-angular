# Architecture

Vue d'ensemble de l'architecture applicative de PharmaTrack.

## Vue d'ensemble

```
                        ┌───────────────┐
                        │   Navigateur  │
                        │   (Angular)   │
                        └──────┬────────┘
                               │ HTTP (proxy /api)
                               ▼
                        ┌───────────────┐
                        │ Spring Boot   │  localhost:8080
                        │   (backend)   │
                        └──────┬────────┘
                               │ JPA / JDBC
                               ▼
                        ┌───────────────┐
                        │ PostgreSQL 16 │  localhost:5432 / 5433
                        └───────────────┘
```

## Backend (Spring Boot 3.3.5)

Organisé par domaine (structure de packages) :

| Package | Responsabilité |
| --- | --- |
| `auth` | Authentification JWT, inscription, `/auth/register`, `/login`, `/refresh`, `/logout`, `/me` ; gestion admin des utilisateurs (`admin/users`) |
| `catalog` | `Medicament`, `Lot`, `Mouvement`, `Alerte`, dashboard de statistiques |
| `fournisseur` | `Fournisseur`, `CommandeFournisseur`, disponibilité, prix |
| `chat` | Conversations et messagerie entre billan / fournisseurs |
| `chatbot` | Assistant intégré (`/assistant`), historique |
| `common` | `PagedResponse`, utilitaires transverses (sécurité, exceptions) |

Points notables :
- **Sécurité** : JWT HS256 (secret en variable d'env `JWT_SECRET`), `@PreAuthorize`
  sur les écritures (`ADMIN`, `PHARMACIEN`), endpoints publics pour `/auth/**`.
- **Migrations** : Flyway `V1..V6` (schéma + seed). Le `V3_1__seed_base_data.sql`
  fournit les données de base requises par le seed fournisseurs `V4` sur une base
  vide.
- **API** : JSON, pagination (`PagedResponse`), Swagger/OpenAPI.

## Frontend (Angular 21)

Structure :

| Emplacement | Rôle |
| --- | --- |
| `src/app/layout` | Layout applicatif, header, sidebar de navigation |
| `src/app/features` | Pages métier : catalogue, lots, scan, prédictions, conformité, alertes, fournisseur, chat, admin |
| `src/app/core` | `AuthService`, interceptor HTTP (injection du token), guards (`authGuard`, gardes de rôles), constantes (`STORAGE_KEYS`, `ROLES`) |
| `src/app/core/models` | DTOs / types (`AuthUser`, `UserRole`, etc.) |

Points notables :
- **Proxy** : dev-server redirige `/api` vers `http://localhost:8080`.
- **Session** : jetons `pharmatrack_access` / `pharmatrack_refresh` + utilisateur
  `pharmatrack_user` dans `localStorage` (voir `docs/roles-permissions.md`).
- **Guards** : redirection vers `/login` si non authentifié ; `/unauthorized`
  pour les rôles insuffisants.
- Génération du client API possible depuis OpenAPI (via un script de build).

## Déploiement

`docker-compose.yml` orchestre `db` (postgres:16), `backend` et `frontend` (nginx).
Voir le `README.md` pour les instructions.
