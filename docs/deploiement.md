# Déploiement PATRIA

Deux options : **Render + Neon** (recommandée, sans serveur à gérer) ou **VM Ubuntu** (section plus bas).

---

## Option A — Render (applications) + Neon (bases de données)

Architecture : **un seul service Render** (`type: web`) dont l'image
`deploy/all-in-one/Dockerfile` embarque nginx + les 6 microservices Laravel +
le frontend. nginx écoute sur le port 10000 (port attendu par Render) et
répartit le trafic exactement comme `gateway/nginx.conf` en local.
Les **7 bases PostgreSQL** sont sur Neon (projet `patria`, région
`aws-eu-central-1` — même région que le service Render `frankfurt`).

Coût indicatif : plan Render **gratuit** (le service s'endort après
inactivité, cold start ~1 min) + Neon gratuit (hors dépassement). Si le
conteneur redémarre en boucle par manque de RAM (7 processus PHP +
nginx dans 512 Mo), passer `plan` à `0.5c-512mb` (~7 $/mois) ou
`1c-2g` (25 $/mois) dans `render.yaml`.

### A.1. État Neon (déjà provisionné)

- Projet : `patria` (id `silent-sun-13116919`), branche `main`
- Hôte : `ep-autumn-mode-b98ogbj7.c-7.eu-central-1.aws.neon.tech` (port 5432)
- Rôle applicatif : `patria_app`
- Bases : `auth_db`, `asset_db`, `movement_db`, `inventory_db`,
  `maintenance_db`, `reporting_db`, `frontend_db`

Le mot de passe du rôle `patria_app` a été communiqué une seule fois :
le conserver dans un gestionnaire de mots de passe. Pour le régénérer :
Neon Console → Project `patria` → Roles → `patria_app` → Reset password.

### A.2. Déployer sur Render (~10 min)

1. Pousser la branche contenant `render.yaml` sur GitHub (PR #6 → `develop`).
2. [Render Dashboard](https://dashboard.render.com) → **New → Blueprint**.
3. Sélectionner le dépôt `SENOUKomiGodwin/PATRIA` et la branche.
4. Render lit `render.yaml` et demande les secrets marqués `sync: false` :
   - `DB_PASSWORD` → mot de passe du rôle Neon `patria_app`.
   (`APP_KEY` et `JWT_SECRET` sont générés automatiquement.)
5. Cliquer **Apply** : le build Docker prend ~10-15 min (7 applications
   Laravel + assets Vite).

### A.3. Après le premier déploiement

- L'URL publique est `https://patria.onrender.com` (ou le sous-domaine
  attribué par Render si le nom est pris).
- Si le sous-domaine diffère, mettre à jour dans Render → service `patria`
  → Environment : `APP_URL` et `CORS_ALLOWED_ORIGINS`, puis
  **Manual Deploy → Deploy latest commit** (ou Edit → Save pour recharger).
- Une fois les migrations + seeders exécutés avec succès (logs Render),
  passer `RUN_SEEDERS` à `"false"` pour éviter les doublons de données de
  démo à chaque redémarrage.
- Comptes de démonstration (mot de passe `Password123!`) :
  `admin@patria.gov` (ADMIN), `gestionnaire@patria.gov` (GESTIONNAIRE),
  `agent@patria.gov` (AGENT), `auditeur@patria.gov` (AUDITEUR).

### A.4. Vérifier

```bash
curl -I https://patria.onrender.com/login      # 200
curl -X POST https://patria.onrender.com/api/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@patria.gov","password":"Password123!"}'   # 200 + token
BASE=https://patria.onrender.com python e2e_parcours.py         # parcours complet
```

### A.5. Limites de l'option Render

- `php artisan serve` mono-processus par application : correct pour une
  démo/pilote, pas pour un fort trafic.
- Le système de fichiers est éphémère : pas de disque persistant (sessions
  et cache sont en base Neon, OK).
- Le cold start HTTP n'existe pas (service payant always-on), mais le
  compute Neon gratuit se met en veille après inactivité : la première
  requête SQL peut prendre quelques secondes (resume automatique).

---

## Option B — VM Ubuntu (Oracle Cloud Always Free ou équivalent)

## 1. Créer la VM

- Oracle Cloud : *Compute → Instances → Create instance* — image **Ubuntu 22.04/24.04**, shape **VM.Standard.E2.1.Micro** (Always Free).
- Ouvrir le port **80** dans le Security List (et 443 si HTTPS) : *Instance → Subnet → Security List → Add Ingress Rules* — source `0.0.0.0/0`, port TCP 80.

## 2. Se connecter et cloner

```bash
ssh ubuntu@<IP-PUBLIQUE-DE-LA-VM>
git clone <URL-DU-DEPOT> patria
cd patria
```

## 3. Configurer les secrets

```bash
cp .env.example .env
nano .env
```

Renseigner obligatoirement :

| Variable | Rôle |
|---|---|
| `POSTGRES_PASSWORD` | mot de passe PostgreSQL (jamais commité) |
| `JWT_SECRET` | secret JWT **partagé par les 6 services** (min. 32 caractères) |
| `FRONTEND_APP_KEY` | `base64:...` — générer : `openssl rand -base64 32` |
| `APP_URL` / `CORS_ALLOWED_ORIGINS` | `http://<IP-ou-domaine>` |
| `DEMO_MODE` | `false` en production |
| `GATEWAY_PORT` / `FRONTEND_PORT` | `80` / `3000` par défaut |

`deploy.sh` peut aussi générer les secrets automatiquement s'ils contiennent encore `change_this_*`.

## 4. Lancer le déploiement

```bash
bash deploy.sh
```

Le script :
1. installe Docker + Docker Compose s'il est absent,
2. vérifie `.env`,
3. génère les secrets manquants,
4. `docker compose up -d --build` (7 images : postgres, gateway nginx, 6 services Laravel, frontend),
5. applique les migrations et les seeders de démonstration.

Comptes de démonstration créés par les seeders (mot de passe `Password123!`) :

- `admin@patria.gov` — ADMIN
- `gestionnaire@patria.gov` — GESTIONNAIRE
- `agent@patria.gov` — AGENT
- `auditeur@patria.gov` — AUDITEUR

## 5. Vérifier

```bash
docker compose ps            # les 9 conteneurs doivent être Up
docker compose logs -f gateway
curl -I http://localhost/    # via la passerelle nginx
```

Depuis Internet : `http://<IP-PUBLIQUE>` — le port 80 de la VM doit être ouvert (étape 1).

## 6. Domaine gratuit + HTTPS (option)

### DuckDNS (domaine gratuit)

1. Créer un compte sur https://www.duckdns.org et ajouter un sous-domaine (ex. `patria-chef.et.duckdns.org`) pointant vers l'IP publique de la VM.
2. Dans `.env` : `APP_URL=http://patria-chef.et.duckdns.org` et `CORS_ALLOWED_ORIGINS=http://patria-chef.et.duckdns.org`, puis `docker compose up -d`.

### HTTPS avec Caddy (recommandé, automatique)

Sur la VM :

```bash
sudo apt install -y caddy
sudo tee /etc/caddy/Caddyfile <<'EOF'
patria-chef.et.duckdns.org {
    reverse_proxy localhost:80
}
EOF
sudo systemctl reload caddy
```

Caddy obtient et renouvelle le certificat Let's Encrypt automatiquement. Penser à ouvrir aussi le port **443** dans la Security List Oracle.

Alternative : `certbot --nginx` si vous préférez garder nginx comme frontal TLS.

## 7. Maintenance courante

```bash
docker compose pull        # images de base
docker compose up -d --build   # redéployer après git pull
docker compose logs -f <service>
docker compose exec postgres sh -c 'pg_dumpall -U "$POSTGRES_USER"' > backup_$(date +%F).sql   # toutes les bases
```

## Limites connues

- `php artisan serve` est mono-processus par service : suffisant pour une démo/pilote, pas pour la production à fort trafic (prévoir PHP-FPM + nginx par service ou FrankenPHP).
- Les seeders sont des données de démonstration : ne pas les relancer en production sans les adapter.
- Sauvegarder le volume `patria_patria-postgres-data` régulièrement.
