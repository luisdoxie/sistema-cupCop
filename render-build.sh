#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Installing Composer dependencies..."
composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

echo "Installing Node dependencies..."
npm install
echo "Building assets..."
npm run build

echo "Caching configuration..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Linking storage..."
php artisan storage:link

echo "Running database migrations..."
php artisan migrate --force

echo "Build successful!"
