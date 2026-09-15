#!/usr/bin/env bash
#
# Deploy script for a VPS or SSH-enabled host. Run this FROM the project
# directory on the server, after the first-time setup in ../../PRODUCTION.md
# has already been done once.
#
# Adjust the PHP-FPM service name below to match your server before
# using this for real.

set -e  # stop on the first error, don't limp forward with a half-deploy

echo "==> Pulling latest code..."
git pull origin main

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Installing and building frontend assets..."
npm install
npm run build

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Clearing and rebuilding caches..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Restarting PHP-FPM (adjust service name for your server)..."
sudo systemctl restart php8.2-fpm || echo "Could not restart php-fpm automatically — restart it manually."

echo "==> Deploy complete."
