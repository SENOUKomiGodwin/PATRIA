# Plateforme de Gestion du Patrimoine de l'État

Plateforme de gestion du patrimoine de l'État en architecture micro-services pour assurer la traçabilité, la maintenance et l'inventaire des biens publics.

## 🏗️ Architecture

### Nommage PATRIA
Pour une cohérence dans l'infrastructure, tous les conteneurs et images utilisent le préfixe `patria` :
- Nom du projet Docker Compose : `patria`
- Images : `patria/<service-name>`
- Conteneurs : `patria-<service-name>`
- Volume PostgreSQL : `patria-postgres-data`
- Network : `patria-network`

### Stack Technologique
- **Backend** : Laravel 12 (PHP 8.3)
- **Base de données** : PostgreSQL 16
- **Orchestration** : Docker / Docker Compose
- **API Gateway** : Nginx
- **Frontend** : Laravel 12

### Micro-Services
Le système est composé de 6 services indépendants :

1. **auth-service** : Gestion de l'authentification et des utilisateurs
2. **asset-service** : Gestion des actifs, catégories et localisations
3. **movement-service** : Gestion des mouvements (affectation, transfert, cession, réforme)
4. **inventory-service** : Gestion des campagnes d'inventaire
5. **maintenance-service** : Gestion des interventions et plans de maintenance
6. **reporting-service** : Génération de rapports et exports

### Pattern Base de Données
- **Database per Service** : 1 conteneur PostgreSQL avec 6 bases de données séparées
- **Pas de clés étrangères SQL entre services** : Communication via UUID et appels REST
- **Isolation des données** : Chaque service a sa propre base de données

## 🚀 Démarrage Rapide

### Prérequis
- Docker et Docker Compose
- Git
- Navigateur web moderne

### Installation

1. **Cloner le repository**
```bash
git clone <repository-url>
cd Gestion_Patrimoine
```

2. **Configurer l'environnement**
```bash
cp .env.example .env
# Éditer .env et modifier les valeurs sensibles (POSTGRES_PASSWORD, JWT_SECRET)
```

3. **Lancer les services**
```bash
docker compose up -d --build
```

4. **Accéder à l'application**
- Frontend : http://localhost
- API Gateway : http://localhost/api

5. **Vérifier que les services fonctionnent**
```bash
# Vérifier les health endpoints
curl http://localhost/api/auth/health
curl http://localhost/api/assets/health
curl http://localhost/api/movements/health
curl http://localhost/api/campaigns/health
curl http://localhost/api/interventions/health
curl http://localhost/api/reports/health
```

### Commandes Utiles

```bash
# Lancer tous les services
docker compose up -d

# Arrêter tous les services
docker compose down

# Voir les logs d'un service
docker compose logs -f [service]

# Reconstruire un service
docker compose build [service]

# Redémarrer un service
docker compose restart [service]
```

## 📁 Structure du Projet

```
patrimoine-etat/
├── docker-compose.yml          # Orchestration Docker (nom: patria)
├── .env.example                # Variables d'environnement exemple
├── docker/
│   └── init-db.sql            # Script d'initialisation des 6 bases de données
├── gateway/
│   └── nginx.conf             # Configuration Nginx Gateway
├── services/
│   ├── auth-service/          # Service d'authentification (patria/auth-service)
│   ├── asset-service/         # Service de gestion des actifs (patria/asset-service)
│   ├── movement-service/      # Service de gestion des mouvements (patria/movement-service)
│   ├── inventory-service/     # Service d'inventaire (patria/inventory-service)
│   ├── maintenance-service/   # Service de maintenance (patria/maintenance-service)
│   │   └── entrypoint.sh     # Script pour scheduler Laravel
│   └── reporting-service/     # Service de reporting (patria/reporting-service)
├── frontend/                   # Application frontend (patria/frontend)
├── docs/
│   ├── uml/
│   │   └── patrimoine-uml.drawio  # Diagramme UML
│   ├── fiche-technique.md     # Documentation technique
│   └── proposition-financiere.md  # Proposition financière
└── README.md                   # Ce fichier
```

## 🔐 Sécurité et Authentification

### Authentification JWT
- **Package** : tymon/jwt-auth
- **JWT Secret partagé** : Tous les services utilisent le même JWT_SECRET
- **Validation locale** : Chaque service valide le token lui-même via middleware
- **Pas d'appel à auth-service** pour la validation (évite le bottleneck)

### Rôles et Permissions
- **ADMIN** : Tous les droits (CRUD complet sur toutes les ressources)
- **GESTIONNAIRE** : Gestion des actifs, mouvements, maintenance
- **AGENT** : Lecture et déclaration de maintenance
- **AUDITEUR** : Lecture seule (consultation des rapports et inventaires)

## 🌐 API Gateway

Le gateway Nginx route les requêtes vers les services appropriés :

| Chemin Gateway | Service Destination |
|----------------|-------------------|
| /api/auth/* | auth-service |
| /api/assets | asset-service |
| /api/categories | asset-service |
| /api/locations | asset-service |
| /api/movements | movement-service |
| /api/campaigns | inventory-service |
| /api/interventions | maintenance-service |
| /api/maintenance-plans | maintenance-service |
| /api/reports | reporting-service |
| / | frontend |

## 📚 Documentation

- **Fiche technique** : [docs/fiche-technique.md](docs/fiche-technique.md)
- **Proposition financière** : [docs/proposition-financiere.md](docs/proposition-financiere.md)
- **Diagramme UML** : [docs/uml/patrimoine-uml.drawio](docs/uml/patrimoine-uml.drawio)

## 🛠️ Développement

### Git Workflow
- Branches : `main` (stable) <- `develop` (intégration) <- `feature/*` (tâches)
- Commits conventionnels avec scope du service : `feat(asset): ...`
- Pull Request vers `develop` (jamais vers `main`)
- Pas de force-push sur les branches poussées

### Standards de Code
- Code commenté en français quand utile
- Un commit par fonctionnalité
- Respect des conventions Laravel
- API REST avec codes HTTP corrects
- Validation via FormRequest
- Réponses via API Resources

### Tests
- Tests unitaires pour chaque service
- Tests d'intégration pour les appels inter-services
- Tests E2E pour les flux utilisateurs principaux

## 📊 Base de Données

Le script `docker/init-db.sql` crée automatiquement les 6 bases de données à l'initialisation (database per service pattern) :
- `auth_db` : Base de données du service d'authentification
- `asset_db` : Base de données du service de gestion des actifs
- `movement_db` : Base de données du service de gestion des mouvements
- `inventory_db` : Base de données du service d'inventaire
- `maintenance_db` : Base de données du service de maintenance
- `reporting_db` : Base de données du service de reporting

**Note** : Aucune clé étrangère SQL entre services. Communication via UUID et appels REST.

## 🔧 Configuration

### Variables d'Environnement Principales

```env
# Configuration de l'environnement
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# PostgreSQL
POSTGRES_USER=patrimoine_user
POSTGRES_PASSWORD=change_this_password_in_production
POSTGRES_PORT=5432

# JWT Secret (PARTAGÉ ENTRE TOUS LES SERVICES)
JWT_SECRET=change_this_jwt_secret_in_production_minimum_32_characters

# Ports
GATEWAY_PORT=80
FRONTEND_PORT=3000
```

## 🐳 Docker

### Dockerfiles
Chaque service Laravel utilise un Dockerfile basé sur `php:8.3-cli` avec :
- Extensions PHP : pdo, pdo_pgsql, mbstring, exif, pcntl, bcmath, gd
- Composer installé
- Commande de démarrage : `php artisan migrate --force --seed && php artisan serve --host=0.0.0.0 --port=8000`

**Services spécifiques** :
- **maintenance-service** : Utilise un `entrypoint.sh` pour lancer le scheduler Laravel en arrière-plan
- **reporting-service** : Ajoute les extensions PHP `zip` et `gd` pour les exports PDF/Excel

### Health Endpoints
Chaque service expose un endpoint de santé :
- `GET /api/health` -> `{"service": "<service-name>", "status": "ok"}`

Exemples :
- http://localhost/api/auth/health
- http://localhost/api/assets/health
- http://localhost/api/movements/health
- http://localhost/api/campaigns/health
- http://localhost/api/interventions/health
- http://localhost/api/reports/health

## 🤝 Contribution

Pour contribuer à ce projet :

1. Forker le repository
2. Créer une branche `feature/nom-de-la-fonctionnalité`
3. Commiter vos changements avec des messages conventionnels
4. Pusher vers la branche
5. Ouvrir une Pull Request vers `develop`

## 📄 Licence

À définir

## 👥 Équipe

À définir

## 📞 Support

Pour toute question ou problème, merci de contacter l'équipe de développement.

---

**Développé avec ❤️ pour la gestion du patrimoine de l'État**
