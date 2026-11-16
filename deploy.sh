#!/usr/bin/env bash
#
# deploy.sh — Déploiement PATRIA sur VM Ubuntu (Oracle Cloud Always Free ou équivalent)
#
# Usage :
#   git clone <url-du-repo> patria && cd patria
#   cp .env.example .env   # puis éditer les secrets (voir docs/deploiement.md)
#   bash deploy.sh
#
# Ou directement depuis n'importe quel dossier :
#   bash deploy.sh <url-du-repo> [dossier]   (clone puis déploie)
#
set -euo pipefail

# Mode clone : bash deploy.sh https://github.com/org/PATRIA.git [dossier]
if [ $# -ge 1 ]; then
    REPO_URL="$1"
    TARGET_DIR="${2:-patria}"
    if [ ! -d "$TARGET_DIR/.git" ]; then
        git clone "$REPO_URL" "$TARGET_DIR"
    fi
    cd "$TARGET_DIR"
fi

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$REPO_DIR"

echo "==> [1/5] Vérification de Docker"
if ! command -v docker >/dev/null 2>&1; then
    echo "Docker absent : installation..."
    sudo apt-get update
    sudo apt-get install -y ca-certificates curl gnupg
    sudo install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
    sudo chmod a+r /etc/apt/keyrings/docker.gpg
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
        | sudo tee /etc/apt/sources.list.d/docker.list >/dev/null
    sudo apt-get update
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
    sudo usermod -aG docker "$USER"
    echo "Docker installé. Reconnectez-vous (ou 'newgrp docker') puis relancez ce script."
    exit 0
fi

echo "==> [2/5] Fichier .env"
if [ ! -f .env ]; then
    cp .env.example .env
    echo "!! .env créé depuis .env.example : éditez-le AVANT de continuer (secrets !)" >&2
    echo "   nano .env" >&2
    exit 1
fi

echo "==> [3/5] Génération des secrets si placeholders"
if grep -q "change_this" .env; then
    JWT_SECRET="$(openssl rand -hex 32)"
    PG_PASS="$(openssl rand -hex 24)"
    FE_KEY="base64:$(openssl rand -base64 32)"
    sed -i "s|change_this_jwt_secret_in_production_minimum_32_characters|$JWT_SECRET|" .env
    sed -i "s|change_this_password_in_production|$PG_PASS|" .env
    sed -i "s|^FRONTEND_APP_KEY=|FRONTEND_APP_KEY=$FE_KEY|" .env
    echo "Secrets générés dans .env"
fi

echo "==> [4/5] Build et démarrage des conteneurs"
docker compose up -d --build

echo "==> [5/5] Initialisation des bases (migrations + seeders démo)"
sleep 10
for svc in auth-service asset-service movement-service inventory-service maintenance-service reporting-service frontend; do
    docker compose exec -T "$svc" php artisan migrate --force || true
done
for svc in auth-service asset-service movement-service inventory-service maintenance-service reporting-service; do
    docker compose exec -T "$svc" php artisan db:seed --force || \
        echo "  ⚠ seed $svc ignoré (à lancer manuellement si besoin)"
done

echo ""
echo "Terminé. L'application est disponible sur http://$(hostname -I | awk '{print $1}')"
echo "Voir docs/deploiement.md pour le port 80, le domaine (DuckDNS) et HTTPS."
