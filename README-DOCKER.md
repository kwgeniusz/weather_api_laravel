# Weather API Laravel - Configuración Docker

Esta aplicación ha sido dockerizada para asegurar la compatibilidad con PHP 8.1 y facilitar el desarrollo y despliegue.

## Requisitos previos

- Docker
- Docker Compose

## Estructura de Docker

La aplicación está configurada con los siguientes servicios:

1. **app**: Servicio PHP 8.1 con todas las extensiones necesarias para Laravel
2. **nginx**: Servidor web para servir la aplicación
3. **db**: Base de datos MySQL 8.0
4. **redis**: Servicio Redis para caché

## Instrucciones de instalación

### 1. Configurar el archivo .env

Asegúrate de que tu archivo `.env` esté configurado correctamente. Puedes copiar el archivo `.env.example`:

```bash
cp .env.example .env
```

Luego edita el archivo `.env` con la siguiente configuración para la base de datos:

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=password
```

Y para Redis:

```
CACHE_DRIVER=redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Iniciar los contenedores

En Windows, puedes ejecutar los siguientes comandos en PowerShell:

```powershell
# Crear directorio para MySQL si no existe
mkdir -p ./docker/mysql

# Construir e iniciar los contenedores
docker-compose build
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
```

Alternativamente, si estás en un entorno Linux/Mac, puedes usar el script proporcionado:

```bash
chmod +x docker-compose-up.sh
./docker-compose-up.sh
```

### 3. Acceder a la aplicación

Una vez que los contenedores estén en funcionamiento, puedes acceder a la aplicación en:

```
http://localhost:8000
```

## Comandos útiles

### Ver logs de los contenedores

```bash
docker-compose logs -f
```

### Ejecutar comandos Artisan

```bash
docker-compose exec app php artisan [comando]
```

### Acceder al shell del contenedor

```bash
docker-compose exec app bash
```

### Detener los contenedores

```bash
docker-compose down
```

## Desarrollo

Todos los archivos de la aplicación están montados como volúmenes en el contenedor, por lo que cualquier cambio que hagas en el código se reflejará inmediatamente sin necesidad de reconstruir la imagen.

## Notas adicionales

- La configuración de Nginx se encuentra en `docker/nginx/laravel.conf`
- Los datos de MySQL se persisten en `docker/mysql`
- El puerto expuesto para la aplicación es el 8000
