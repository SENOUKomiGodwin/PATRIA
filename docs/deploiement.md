# Déploiement PATRIA sur VM Ubuntu

Guide pas à pas pour héberger la plateforme sur une VM Ubuntu (Oracle Cloud Always Free ou équivalent, 1 OCPU / 1 Go RAM suffisent pour la démo ; 2 Go recommandés pour le build).

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
