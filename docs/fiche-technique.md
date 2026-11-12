# Fiche Technique - Plateforme de Gestion du Patrimoine de l'État

## 1. Présentation du Projet

### 1.1 Objectif
Développer une plateforme de gestion du patrimoine de l'État en architecture micro-services pour assurer la traçabilité, la maintenance et l'inventaire des biens publics.

### 1.2 Contexte
Le système doit permettre la gestion complète du cycle de vie des actifs : acquisition, affectation, transfert, maintenance, inventaire et réforme.

## 2. Architecture Technique

### 2.1 Stack Technologique
- **Backend** : Laravel 11 (PHP 8.3)
- **Base de données** : PostgreSQL 16
- **Orchestration** : Docker / Docker Compose
- **API Gateway** : Nginx
- **Frontend** : À définir (suggestion : Vue.js 3 ou React)

### 2.2 Architecture Micro-Services
Le système est composé de 6 services indépendants :

1. **auth-service** : Gestion de l'authentification et des utilisateurs
2. **asset-service** : Gestion des actifs, catégories et localisations
3. **movement-service** : Gestion des mouvements (affectation, transfert, cession, réforme)
4. **inventory-service** : Gestion des campagnes d'inventaire
5. **maintenance-service** : Gestion des interventions et plans de maintenance
6. **reporting-service** : Génération de rapports et exports

### 2.3 Pattern Base de Données
- **Database per Service** : 1 conteneur PostgreSQL avec 6 bases de données séparées
- **Pas de clés étrangères SQL entre services** : Communication via UUID et appels REST
- **Isolation des données** : Chaque service a sa propre base de données

## 3. Sécurité et Authentification

### 3.1 Authentification JWT
- **Package** : tymon/jwt-auth
- **JWT Secret partagé** : Tous les services utilisent le même JWT_SECRET
- **Validation locale** : Chaque service valide le token lui-même via middleware
- **Pas d'appel à auth-service** pour la validation (évite le bottleneck)

### 3.2 Claims du Token
```json
{
  "sub": "user_id",
  "role": "ADMIN|GESTIONNAIRE|AGENT|AUDITEUR",
  "service_id": "service_id"
}
```

### 3.3 Rôles et Permissions
- **ADMIN** : Tous les droits (CRUD complet sur toutes les ressources)
- **GESTIONNAIRE** : Gestion des actifs, mouvements, maintenance
- **AGENT** : Lecture et déclaration de maintenance
- **AUDITEUR** : Lecture seule (consultation des rapports et inventaires)

## 4. Communication Inter-Services

### 4.1 Protocole
- Communication via API REST JSON
- Transmission du JWT utilisateur dans l'en-tête Authorization
- URLs injectées par variables d'environnement

### 4.2 Variables d'Environnement
Chaque service consommateur dispose des URLs des services nécessaires :
- `ASSET_SERVICE_URL`
- `MOVEMENT_SERVICE_URL`
- `INVENTORY_SERVICE_URL`
- `MAINTENANCE_SERVICE_URL`
- `AUTH_SERVICE_URL`

### 4.3 Routage API Gateway
Le gateway Nginx route les requêtes vers les services appropriés :

| Chemin Gateway | Service Destination | Chemin Service |
|----------------|-------------------|----------------|
| /api/auth/* | auth-service | /api/* (préfixe retiré) |
| /api/assets | asset-service | /api/assets |
| /api/categories | asset-service | /api/categories |
| /api/locations | asset-service | /api/locations |
| /api/movements | movement-service | /api/movements |
| /api/campaigns | inventory-service | /api/campaigns |
| /api/interventions | maintenance-service | /api/interventions |
| /api/maintenance-plans | maintenance-service | /api/maintenance-plans |
| /api/reports | reporting-service | /api/reports |
| / | frontend | / |

## 5. Contrat d'API

### 5.1 Auth Service
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | /api/login | Connexion utilisateur |
| GET | /api/me | Informations utilisateur connecté |
| GET | /api/users | Liste des utilisateurs (ADMIN) |
| POST | /api/users | Créer un utilisateur (ADMIN) |
| GET | /api/users/{id} | Détails utilisateur (ADMIN) |
| PUT | /api/users/{id} | Modifier utilisateur (ADMIN) |
| DELETE | /api/users/{id} | Supprimer utilisateur (ADMIN) |

### 5.2 Asset Service
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | /api/assets | Liste des actifs |
| POST | /api/assets | Créer un actif |
| GET | /api/assets/{id} | Détails d'un actif |
| PUT | /api/assets/{id} | Modifier un actif |
| DELETE | /api/assets/{id} | Supprimer un actif |
| GET | /api/assets/{id}/qrcode | Générer QR code |
| PATCH | /api/assets/{id}/assignment | Affecter à un service/localisation |
| PATCH | /api/assets/{id}/status | Changer le statut |
| GET | /api/assets/{id}/depreciation | Tableau d'amortissement |
| GET | /api/assets/stats | Statistiques globales |
| CRUD | /api/categories | Gestion des catégories |
| CRUD | /api/locations | Gestion des localisations |

### 5.3 Movement Service
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | /api/movements | Créer un mouvement |
| GET | /api/movements | Liste des mouvements (filtres : asset_id, type) |
| GET | /api/movements/{id} | Détails d'un mouvement |

Types de mouvement : AFFECTATION, TRANSFERT, CESSION, RÉFORME

### 5.4 Inventory Service
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | /api/campaigns | Créer une campagne d'inventaire |
| GET | /api/campaigns | Liste des campagnes |
| GET | /api/campaigns/{id} | Détails d'une campagne |
| POST | /api/campaigns/{id}/lines | Ajouter une ligne (scan QR code) |
| POST | /api/campaigns/{id}/close | Clôturer la campagne |
| GET | /api/campaigns/{id}/discrepancies | Écarts de l'inventaire |

### 5.5 Maintenance Service
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | /api/interventions | Déclarer une panne |
| GET | /api/interventions | Liste des interventions (filtres : asset_id, status, type) |
| GET | /api/interventions/{id} | Détails d'une intervention |
| PATCH | /api/interventions/{id}/start | Démarrer l'intervention |
| PATCH | /api/interventions/{id}/close | Clôturer l'intervention (coût, rapport) |
| PATCH | /api/interventions/{id}/cancel | Annuler l'intervention |
| GET | /api/interventions/stats | Statistiques de maintenance |
| CRUD | /api/maintenance-plans | Gestion des plans de maintenance |

### 5.6 Reporting Service
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | /api/reports/dashboard | Dashboard principal |
| GET | /api/reports/depreciation?year= | Rapport d'amortissement |
| GET | /api/reports/exports/{type}?format= | Export (PDF/XLSX) |

Types d'export : assets, movements, inventory, maintenance

## 6. Standards de Développement

### 6.1 API REST
- Préfixe /api pour tous les endpoints
- Codes HTTP corrects (200, 201, 400, 401, 403, 404, 422, 500)
- Validation via FormRequest
- Réponses via API Resources
- Format d'erreur standard : `{"message": "...", "errors": {...}}`

### 6.2 Code Quality
- Code commenté en français quand utile
- Un commit par fonctionnalité
- Respect des conventions Laravel

### 6.3 Git Workflow
- Branches : main (stable) <- develop (intégration) <- feature/* (tâches)
- Commits conventionnels avec scope du service : `feat(asset): ...`
- Pull Request vers develop (jamais vers main)
- Pas de force-push sur les branches poussées

## 7. Modèle de Données

Le modèle de données est défini dans le diagramme UML (voir `docs/uml/patrimoine-uml.drawio`).

### 7.1 Entités Principales
- **User** : Utilisateurs du système
- **Asset** : Actifs du patrimoine
- **Category** : Catégories d'actifs
- **Location** : Localisations physiques
- **Movement** : Mouvements d'actifs
- **InventoryCampaign** : Campagnes d'inventaire
- **InventoryLine** : Lignes d'inventaire
- **Intervention** : Interventions de maintenance
- **MaintenancePlan** : Plans de maintenance préventive

### 7.2 Identifiants
- Tous les identifiants sont des UUID
- Pas de clés étrangères SQL entre services
- Références via UUID stockés dans les tables

## 8. Déploiement

### 8.1 Environnement Requis
- Docker et Docker Compose
- Git
- Navigateur web moderne

### 8.2 Commandes de Base
```bash
# Copier l'environnement
cp .env.example .env

# Lancer tous les services
docker-compose up -d

# Arrêter tous les services
docker-compose down

# Voir les logs
docker-compose logs -f [service]
```

### 8.3 Ports par Défaut
- Gateway : 80
- Frontend : 3000
- PostgreSQL : 5432

## 9. Tests

### 9.1 Types de Tests
- Tests unitaires pour chaque service
- Tests d'intégration pour les appels inter-services
- Tests E2E pour les flux utilisateurs principaux

### 9.2 Couverture
Objectif : minimum 80% de couverture de code pour les services critiques

## 10. Documentation

### 10.1 Documentation Technique
- Diagramme UML : `docs/uml/patrimoine-uml.drawio`
- Fiche technique : `docs/fiche-technique.md`
- Proposition financière : `docs/proposition-financiere.md`
- README principal : `README.md`

### 10.2 Documentation API
À ajouter : OpenAPI/Swagger pour chaque service

## 11. Maintenance et Support

### 11.1 Logs
- Logs des services Docker Compose
- Logs Nginx Gateway
- Logs PostgreSQL

### 11.2 Monitoring
À définir : Solution de monitoring (Prometheus, Grafana, etc.)

### 11.3 Sauvegardes
- Sauvegardes PostgreSQL régulières
- Sauvegardes des fichiers de configuration

## 12. Roadmap

### Phase 1 (MVP)
- Infrastructure Docker
- Auth Service
- Asset Service
- Frontend basique

### Phase 2
- Movement Service
- Inventory Service
- Maintenance Service

### Phase 3
- Reporting Service
- Frontend complet
- Tests et documentation

### Phase 4
- Monitoring avancé
- Optimisations performances
- Fonctionnalités avancées
