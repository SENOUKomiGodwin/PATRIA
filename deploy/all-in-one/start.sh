#!/bin/sh
# Démarrage de la pile PATRIA tout-en-un (Render).
#
# IMPORTANT : `php artisan serve` ne transmet pas toutes les variables
# d'environnement au processus `php -S` enfant. On écrit donc un .env
# COMPLET par application au démarrage (depuis l'env du conteneur +
# les surcharges propres à chaque app). Aucun secret n'est commité :
# les valeurs viennent des variables d'environnement Render.
#
# phpdotenv conserve la PREMIÈRE occurrence d'une clé : les fichiers de
# surcharge (DB_DATABASE, URLs inter-services) sont écrits en premier.

set -u

# APP_KEY / JWT_SECRET sont obligatoires (chiffrement cookies/sessions,
# signature JWT). Laravel exige "base64:<16|24|32 octets>" ou une clef
# brute de 16/24/32 octets. Cas fréquent : clé base64 collée sans le
# préfixe "base64:" -> on le rajoute si le contenu décode en 32 octets.
case "${APP_KEY:-}" in
    base64:*) ;;
    *)
        if [ "$(printf '%s' "$APP_KEY" | base64 -d 2>/dev/null | wc -c)" = "32" ]; then
            APP_KEY="base64:$APP_KEY"
            echo "==> APP_KEY préfixée base64: (format corrigé)"
        fi
        ;;
esac

app_key_len() {
    case "$1" in
        base64:*) printf '%s' "${1#base64:}" | base64 -d 2>/dev/null | wc -c ;;
        *) printf '%s' "$1" | wc -c ;;
    esac
}
kl=$(app_key_len "${APP_KEY:-}")
case "$kl" in
    16|24|32) ;;
    *)
        APP_KEY="base64:$(head -c 32 /dev/urandom | base64 | tr -d '\n')"
        echo "!! APP_KEY invalide (${kl} octets) : clé éphémère générée"
        ;;
esac
export APP_KEY

if [ -z "${JWT_SECRET:-}" ]; then
    JWT_SECRET="$(head -c 48 /dev/urandom | base64 | tr -d '\n')"
    export JWT_SECRET
    echo "!! JWT_SECRET absent : secret éphémère généré (tokens perdus au redémarrage)"
fi

DEPLOY_DIR=/app/deploy/all-in-one
# Chemins réels dans l'image : les 6 API sous services/, le frontend à la racine
APPS="services/auth-service services/asset-service services/movement-service services/inventory-service services/maintenance-service services/reporting-service frontend"
SEED_APPS="services/auth-service services/asset-service services/movement-service services/inventory-service services/maintenance-service services/reporting-service"

write_env() {
    app=$1
    name=${app##*/}
    out="/app/$app/.env"

    # 1) Surcharges propres à l'application (DB_DATABASE, URLs inter-services)
    : > "$out"
    if [ -f "$DEPLOY_DIR/env/$name.env" ]; then
        cat "$DEPLOY_DIR/env/$name.env" >> "$out"
    fi

    # 2) Variables communes (conteneur) — ajoutées ensuite, n'écrasent rien
    cat >> "$out" <<EOF
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY:-}
APP_URL=${APP_URL:-http://localhost}
API_URL=${API_URL:-http://127.0.0.1:10000}
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
LOG_CHANNEL=stderr
LOG_LEVEL=warning
DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-5432}
DB_USERNAME=${DB_USERNAME:-}
DB_PASSWORD=${DB_PASSWORD:-}
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database
JWT_SECRET=${JWT_SECRET:-}
JWT_TTL=${JWT_TTL:-480}
CORS_ALLOWED_ORIGINS=${CORS_ALLOWED_ORIGINS:-}
EOF
}

for app in $APPS; do
    write_env "$app"
done

# Migrations systématiques (idempotentes) pour les 7 applications
for app in $APPS; do
    echo "==> migrate $app"
    (cd "/app/$app" && php artisan migrate --force) \
        || echo "!! migrate $app a échoué (vérifier les logs)"
done

# Seeders démo : APIs uniquement (le frontend n'a pas de données à semer
# et son factory requiert faker, absent du build --no-dev)
if [ "${RUN_SEEDERS:-true}" = "true" ]; then
    for app in $SEED_APPS; do
        echo "==> seed $app"
        (cd "/app/$app" && php artisan db:seed --force) \
            || echo "!! seed $app a échoué (doublons possibles si relancé)"
    done
fi

# Serveurs PHP — un processus par application, accessibles uniquement en local
PORTS="services/auth-service:8001 services/asset-service:8002 services/movement-service:8003 services/inventory-service:8004 services/maintenance-service:8005 services/reporting-service:8006 frontend:8007"
for entry in $PORTS; do
    app=${entry%%:*}
    port=${entry##*:}
    echo "==> serve $app sur 127.0.0.1:$port"
    # Logs streamés sur stdout (visibles dans Render) avec préfixe par app
    (cd "/app/$app" && php artisan serve --host=127.0.0.1 --port="$port" 2>&1 | sed "s/^/[${app##*/}] /") &
done

# Attend que le frontend réponde réellement avant de lancer nginx.
# 000 = connexion refusée ; tout code HTTP (même 500) prouve que PHP tourne.
i=0
code=000
until [ "$code" != "000" ] || [ "$i" -ge 90 ]; do
    code=$(curl -s --max-time 5 -o /dev/null -w '%{http_code}' http://127.0.0.1:8007/login 2>/dev/null || echo 000)
    if [ "$code" = "000" ]; then
        i=$((i+1))
        sleep 2
    fi
done
echo "==> frontend /login -> HTTP $code après ~$((i*2))s d'attente"

# nginx en avant-plan (PID 1) — seul processus exposé sur le port 10000
exec nginx -g 'daemon off;'
