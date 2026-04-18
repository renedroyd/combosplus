#!/bin/bash
set -e

# Esperar a que la base de datos esté lista
echo "Waiting for database to be ready..."
sleep 5

# Ejecutar migraciones
echo "Running migrations..."
php artisan migrate --force

# Ejecutar seeders (opcional)
# php artisan db:seed --force

# Crear cache table si no existe
php artisan cache:table
php artisan migrate --force

# Optimizar Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Iniciar supervisor
exec "$@"
