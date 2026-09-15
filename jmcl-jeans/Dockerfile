# syntax=docker/dockerfile:1
#
# This delivery's zip only contains the app-specific code (app/, config/,
# database/, resources/, routes/, bootstrap/app.php) — it's missing the
# standard Laravel skeleton files (artisan, public/index.php, base config
# files) on purpose, per DEPLOY.md's "copy onto a fresh Laravel install"
# instructions. This Dockerfile does that merge automatically at build time,
# then packages everything for Render.

# ---------- Stage 1: build frontend assets (Tailwind/Vite) ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN npm run build

# ---------- Stage 2: a fresh Laravel 11 skeleton (artisan, public/index.php, base config/*) ----------
FROM composer:2 AS base
RUN composer create-project laravel/laravel:^11.0 /base --prefer-dist --no-interaction --no-scripts

# ---------- Stage 3: final PHP application image ----------
FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev sqlite3 libsqlite3-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql pdo_pgsql mbstring bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Foundation: the stock Laravel skeleton (gives us artisan, public/index.php,
# public/.htaccess, and every default config/*.php file)
COPY --from=base /base/ .

# Overlay: this project's actual application code, replacing/adding onto
# the skeleton's defaults
COPY app/ ./app/
COPY bootstrap/app.php ./bootstrap/app.php
COPY config/ ./config/
COPY database/ ./database/
COPY resources/ ./resources/
COPY routes/ ./routes/
COPY public/images ./public/images
COPY composer.json ./composer.json
COPY package.json ./package.json

# Install this project's actual dependencies (adds laravel/sanctum,
# intervention/image, etc. on top of the base skeleton's packages)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Bring in the compiled CSS/JS from Stage 1
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["/entrypoint.sh"]
