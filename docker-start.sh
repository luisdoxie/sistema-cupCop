#!/bin/bash
set -e

# Render asigna el puerto via $PORT (por defecto 10000)
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Cachear configuración Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Enlazar storage
php artisan storage:link || true

# Ejecutar migraciones
php artisan migrate --force

echo "Iniciando Apache en puerto ${PORT}..."
exec apache2-foreground
