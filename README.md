# PharmaTrack

Application full-stack de gestion de stocks pharmaceutiques : catalogue de médicaments, gestion des lots, lecture de codes, prédictions IA, conformité réglementaire (ONP), commandes fournisseurs, messagerie et alertes.

## Aperçu

PharmaTrack est un projet **Epic** construit par itérations, couvrant un back-office pharmaceutique complet :

- **Catalogue médicaments** — fiche détaillée (princeps/génériques, posologie, conditionnement, prix, seuils, contre-indications, compatibilités), recherche/filtres, marquage des psychotropes.
- **Gestion des lots** — réception de lot, ajustement de stock, statuts (`actif`, `epuise`, `perime`), pérémption proche.
- **Scan** — lecture et recherche par code-barres / CIP.
- **Suggestions IA** — prédictions de rupture et de pérémption.
- **Conformité ONP** — contrôle réglementaire et listes contrôlées.
- **Fournisseurs & commandes** — vérifier la disponibilité, passer une commande, expédier, gestion des prix.
- **Messagerie & chatbot** — conversations avec les fournisseurs, assistance intégrée.
- **Alertes** — boîte des alertes (stock bas, expiration, péremption) avec lecture/résolution.
- **Administration** — gestion des utilisateurs, approbation des visiteurs.

## Stack technique

| Couche | Technologie |
| --- | --- |
| **Backend** | Java 21 (JDK 26 OK), Spring Boot **3.3.5**, Spring Web, Spring Data JPA, Spring Security + JWT, Flyway, Swagger/OpenAPI |
| **Frontend** | Angular **21**, RxJS, Angular Router (guards de rôles), TypeScript 5.9 |
| **Base de données** | PostgreSQL 16 (Hibernate JPA ; Flyway pour les migrations) |
| **Outillage** | Maven (wrapper), npm, Docker Compose (db + backend + frontend) |

## Rôles & permissions

| Rôle | Droits principaux | Accès |
| --- | --- | --- |
| **admin** | Tout : gestion des utilisateurs, approbation des visiteurs, écriture catalogue/stock, alertes, conformité | Admin |
| **pharmacien** | Écriture catalogue/stock (lots), prédictions, conformité, alertes, commandes | Utilisateur connecté |
| **fournisseur** | Dashboard fournisseur, commandes (expédier), prix | Utilisateur connecté |
| **visiteur** | Consultation seule ; **doit être approuvé** par un admin avant de se connecter | Restreint |

## Comptes de démonstration (données initiales)

> Mots de passe valides (BCrypt dans le seed) : `Admin@123`, `Pharma@123`, `Supply@123`, `Visitor@123`.

| Rôle | Email | Mot de passe | Statut |
| --- | --- | --- | --- |
| Admin | `admin@pharmatrack.ma` | `Admin@123` | actif |
| Pharmacien | `amine@pharmatrack.ma` | `Pharma@123` | actif |
| Pharmacien | `salma@pharmatrack.ma` | `Pharma@123` | actif |
| Fournisseur | `medsupply@pharmatrack.ma` | `Supply@123` | actif |
| Visiteur (approuvé) | `nadia@pharmatrack.ma` | `Visitor@123` | inactif |
| Visiteur (à approuver) | `karim@pharmatrack.ma` | `Visitor@123` | **non approuvé** (connexion refusée jusqu'à approbation) |

## Démarrage

### Prérequis
- JDK 21+ (le build a été validé avec JDK 26), Maven (via `mvnw.cmd`)
- Node.js + npm (Angular CLI)
- PostgreSQL 16 (local ou Docker)

### 1. Base de données
Deux options équivalentes :

- **Docker** : `docker compose up db -d` (port 5432, base `pharmatrack`).
- **Postgres local** : créer une base `pharmatrack` avec l'utilisateur `pharmatrack`/`pharmatrack`.

Les migrations **Flyway** (`src/main/resources/db/migration/V1..V6`) créent le schéma **et** les données initiales sur une base vide (le `V3_1__seed_base_data.sql` fournit les données de base requises par le seed fournisseurs `V4`).

### 2. Backend (Spring Boot)

```bash
cd backend
mvnw.cmd package -DskipTests
java -jar target\pharmatrack-backend-0.1.0.jar
```

Variables d'environnement (défauts dans `.env.example` / `application.yml`) :
- `DB_URL` : `jdbc:postgresql://localhost:5432/pharmatrack` (port 5433 si votre cluster local est sur 5433)
- `DB_USERNAME` / `DB_PASSWORD` : `pharmatrack` / `pharmatrack`
- `JWT_SECRET` : clé HS256 base64 (64+ octets) — à changer hors dev
- `CORS_ALLOWED_ORIGINS` : `http://localhost:4200`

L'API démarre sur `http://localhost:8080` (Swagger sur `/swagger-ui.html`).

> Astuce : si vous lancez depuis IntelliJ, pensez à passer `-Dspring.datasource.url=jdbc:postgresql://localhost:5433/pharmatrack` quand votre Postgres local est sur 5433 (le défaut est 5432).

### 3. Frontend (Angular)

```bash
cd frontend
npm install
ng serve -o
```

L'application est servie sur `http://localhost:4200`. Le frontend proxie `/api` vers le backend (port 8080).

### 4. Tout-en-un (Docker)

```bash
docker compose up --build
```

- Frontend (nginx) : `http://localhost:4200`
- Backend : `http://localhost:8080`
- PostgreSQL : `localhost:5432`

## Captures d'écran

Voir le dossier [`screenshots/`](screenshots) : connexion, tableau de bord admin, catalogue, fiche médicament, lots, réception de lot, scan, suggestions IA, conformité ONP, alertes, profil, gestion des utilisateurs, approbations, tableau de bord pharmacien.

## Documentation

- [`docs/`](docs) — ADR et documentation d'architecture, notes rôles/permissions.

## Structure du dépôt

```
pharma-track/
├── backend/          # Spring Boot (catalogue, lots, auth, fournisseur, chat, alertes)
├── frontend/         # Angular (pages, layout, guards de rôles, interceptor HTTP)
├── docs/             # ADR / documentation
├── screenshots/      # Captures d'écran de l'application
├── legacy-source/    # Sources héritées (style beige Laravel de référence)
├── docker-compose.yml
├── .env.example
└── .gitignore
```
