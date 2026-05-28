#!/usr/bin/env bash
# exit on error
set -o errexit

# Install PHP dependencies
echo "Installing Composer dependencies..."
composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets (if Vite/Mix is used)
echo "Installing Node dependencies..."
npm install
echo "Building assets..."
npm run build

# Cache configuration and routes for performance
echo "Caching configuration..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

echo "Build successful!"
