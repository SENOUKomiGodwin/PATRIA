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

echo "=== DEBUG ENV ==="
echo "DB_HOST=${DB_HOST:-NOT_SET}"
echo "DB_PORT=${DB_PORT:-NOT_SET}"
echo "DB_USERNAME=${DB_USERNAME:-NOT_SET}"
echo "DB_PASSWORD=${DB_PASSWORD:+SET}"
echo "APP_KEY=${APP_KEY:+SET}"
echo "JWT_SECRET=${JWT_SECRET:+SET}"
echo "=== END DEBUG ==="

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
LOG_CHANNEL=stack
LOG_STACK=single
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
    (cd "/app/$app" && php artisan serve --host=127.0.0.1 --port="$port" >"/tmp/serve-${app##*/}.log" 2>&1) &
done

# Laisse les serveurs monter, puis nginx en avant-plan (PID 1)
sleep 3
exec nginx -g 'daemon off;'
