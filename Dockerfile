# syntax=docker/dockerfile:1
# Image "tout-en-un" PATRIA pour Render : nginx + 6 microservices Laravel + frontend
# Construit depuis la racine du dépôt (dockerContext: . dans render.yaml).

# ---------- Étape 1 : build des assets Vite du frontend ----------
FROM node:20-alpine AS assets
WORKDIR /build
COPY frontend/package.json frontend/package-lock.json* ./
RUN npm ci
COPY frontend/ ./
RUN npm run build

# ---------- Étape 2 : image finale PHP + nginx ----------
FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Code des 7 applications (vendor/.env exclus par le .dockerignore racine)
COPY services/auth-service       ./services/auth-service
COPY services/asset-service      ./services/asset-service
COPY services/movement-service   ./services/movement-service
COPY services/inventory-service  ./services/inventory-service
COPY services/maintenance-service ./services/maintenance-service
COPY services/reporting-service  ./services/reporting-service
COPY frontend                    ./frontend
COPY --from=assets /build/public/build ./frontend/public/build

# Dépendances Composer de chaque application
RUN set -e; \
    for app in \
        services/auth-service \
        services/asset-service \
        services/movement-service \
        services/inventory-service \
        services/maintenance-service \
        services/reporting-service \
        frontend ; do \
        echo "==> composer install $app"; \
        mkdir -p "$app/bootstrap/cache" && \
        (cd "$app" && composer install --no-dev --optimize-autoloader --no-interaction); \
    done

# Configuration nginx passerelle + script de démarrage + .env par application
COPY deploy/all-in-one/nginx.conf /etc/nginx/nginx.conf
COPY deploy/all-in-one/start.sh /usr/local/bin/start.sh
COPY deploy/all-in-one/env /app/deploy/all-in-one/env

RUN chmod +x /usr/local/bin/start.sh \
    && mkdir -p /var/log/nginx /var/lib/nginx /run /var/cache/nginx

# Render expose le port 10000 par défaut sur les web services
EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
