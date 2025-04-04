#!/bin/bash

# Crear directorio para MySQL si no existe
mkdir -p ./docker/mysql

# Construir las imágenes de Docker
docker-compose build

# Iniciar los contenedores
docker-compose up -d

# Instalar dependencias de Composer
docker-compose exec app composer install

# Generar clave de aplicación
docker-compose exec app php artisan key:generate

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Limpiar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

echo "La aplicación está en funcionamiento en http://localhost:8000"
