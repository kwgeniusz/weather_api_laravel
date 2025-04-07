# Weather API Laravel

![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.1-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

API RESTful para consultar el clima actual y pronósticos de cualquier ciudad del mundo, con gestión de usuarios, favoritos e historial de búsquedas.

## 🌦️ Características

- **Datos meteorológicos** - Clima actual y pronósticos utilizando WeatherAPI.com
- **Autenticación** - Sistema completo con Laravel Sanctum
- **Historial de consultas** - Seguimiento automático para usuarios autenticados
- **Gestión de favoritos** - Guardar y establecer ciudades favoritas
- **Multilenguaje** - Soporte para respuestas en varios idiomas
- **Dockerizado** - Configuración lista para desarrollo y producción

## 🏗️ Arquitectura

El proyecto sigue una **Arquitectura Limpia** con patrón Repositorio-Servicio:

```
app/
├── Models/             # Modelos Eloquent (User, WeatherHistory, Favorite)
├── DTO/                # Objetos de transferencia de datos
├── Repositories/       # Implementación de repositorios
│   └── Interfaces/     # Interfaces de repositorios
├── Services/           # Servicios con lógica de negocio
│   └── Interfaces/     # Interfaces de servicios
├── Http/
│   ├── Controllers/    # Controladores de API
│   └── Requests/       # Validación de solicitudes
└── Exceptions/         # Manejo de excepciones
```

## 🛠️ Stack Tecnológico

Este proyecto utiliza un stack moderno de tecnologías para ofrecer una API robusta y escalable:

- **Backend**: [Laravel 10](https://laravel.com/) - Framework PHP de alto rendimiento
- **PHP**: Versión 8.1+ - Aprovechando las características modernas del lenguaje
- **Base de Datos**: MySQL/MariaDB - Para almacenamiento relacional
- **Caché**: Redis - Para optimizar el rendimiento de consultas frecuentes
- **Autenticación**: [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum) - Autenticación API con tokens
- **API Externa**: [WeatherAPI.com](https://www.weatherapi.com/) - Proveedor de datos meteorológicos
- **Contenedores**: Docker y Docker Compose - Para entornos de desarrollo y producción consistentes
- **Validación**: Laravel Form Requests - Para validación de entrada robusta

## 🏛️ Arquitectura y Patrones de Diseño

### Arquitectura Limpia (Clean Architecture)

El proyecto implementa los principios de la Arquitectura Limpia para mantener una separación clara de responsabilidades:

1. **Capa de Dominio**: Contiene la lógica de negocio central
   - DTOs (Data Transfer Objects)
   - Interfaces de Servicios y Repositorios
   - Reglas de negocio

2. **Capa de Aplicación**: Orquesta el flujo de datos entre las capas externas y el dominio
   - Servicios
   - Casos de uso

3. **Capa de Infraestructura**: Implementa las interfaces definidas en el dominio
   - Repositorios
   - Implementaciones de servicios externos
   - Configuración de base de datos

4. **Capa de Presentación**: Maneja la interacción con el cliente
   - Controladores API
   - Transformadores de datos
   - Middleware

### Patrones de Diseño Implementados

- **Patrón Repositorio**: Abstrae la lógica de persistencia de datos, permitiendo cambiar la fuente de datos sin afectar la lógica de negocio
- **Patrón Servicio**: Encapsula la lógica de negocio y coordina entre múltiples repositorios
- **Patrón DTO (Data Transfer Object)**: Transporta datos entre procesos, reduciendo llamadas y desacoplando capas
- **Patrón Fachada**: Simplifica interfaces complejas (implementado en los servicios)
- **Inyección de Dependencias**: Proporciona instancias de clases a otras clases que las necesitan
- **Patrón Singleton**: Utilizado a través del contenedor de servicios de Laravel

### Flujo de Datos

```
Cliente → Controlador → Servicio → Repositorio → Modelo → Base de Datos
   ↑          ↓            ↓          ↑
   └──────────────────────────────────┘
           (Respuesta DTO)
```

## 🔧 Requisitos

- PHP 8.1+
- Composer
- MySQL/MariaDB
- Redis (recomendado para caché)
- Clave API de WeatherAPI.com

## 🚀 Instalación

## 🐳 Instalación con Docker

El proyecto está completamente dockerizado para facilitar su despliegue y desarrollo. La configuración incluye:

- Contenedor PHP 8.1-FPM
- Servidor web Nginx
- Base de datos MySQL
- Redis para caché
- Composer para gestión de dependencias

### Requisitos Previos

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git

### Pasos de Instalación

1. **Clonar el repositorio**

```bash
git clone https://github.com/kwgeniusz/weather_api_laravel.git
cd weather_api_laravel
```

2. **Configurar variables de entorno**

```bash
cp .env.example .env
```

3. **Ejemplo de configuración .env**

```env
APP_NAME="Weather API"
APP_ENV=local
APP_KEY=base64:jAyOA1OKZd/npkf5ZJ/i3M9dS6j8gaakjf7rD39DyfQ=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=weather_api
DB_USERNAME=root
DB_PASSWORD=root

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Weather API Configuration
WEATHER_API_KEY=63a4401d8b85477ab5035628250504
WEATHER_API_URL=http://api.weatherapi.com/v1
WEATHER_API_CACHE_ENABLED=true
WEATHER_API_CACHE_TTL=3600
WEATHER_API_TIMEOUT=5
WEATHER_API_RETRY_TIMES=3
WEATHER_API_RETRY_SLEEP=1000

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,localhost:8000
SESSION_DOMAIN=localhost
```

4. **Construir y levantar los contenedores**

```bash
docker-compose build
docker-compose up -d
```

5. **Instalar dependencias de PHP**

```bash
docker-compose exec app composer install
```

6. **Generar clave de aplicación**

```bash
docker-compose exec app php artisan key:generate
```

7. **Ejecutar migraciones**

```bash
docker-compose exec app php artisan migrate
```

8. **Opcional: Cargar datos de prueba**

```bash
docker-compose exec app php artisan db:seed
```

### Estructura de Docker

El proyecto utiliza varios contenedores interconectados:

- **app**: Contenedor PHP 8.1-FPM con todas las extensiones necesarias
- **web**: Servidor Nginx configurado para Laravel
- **db**: Base de datos MySQL/MariaDB
- **redis**: Servidor Redis para caché

### Comandos Docker Útiles

```bash
# Ver logs de contenedores
docker-compose logs -f

# Acceder al shell del contenedor PHP
docker-compose exec app bash

# Ejecutar pruebas
docker-compose exec app php artisan test

# Detener todos los contenedores
docker-compose down
```

## 🔌 Endpoints de la API

### Autenticación

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/v1/register` | Registrar nuevo usuario |
| POST | `/api/v1/login` | Iniciar sesión y obtener token |
| POST | `/api/v1/logout` | Cerrar sesión (autenticación requerida) |

### Perfil de Usuario (autenticación requerida)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/profile` | Obtener perfil |
| PUT | `/api/v1/profile` | Actualizar perfil |
| PUT | `/api/v1/profile/password` | Cambiar contraseña |

### Clima

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/weather/current?city={city}` | Clima actual (guarda historial si está autenticado) |
| GET | `/api/v1/weather/forecast?city={city}&days={days}` | Pronóstico del tiempo |
| GET | `/api/v1/weather/search?query={query}` | Buscar ciudades |

### Historial (autenticación requerida)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/weather/history` | Obtener historial con filtros y paginación |

### Favoritos (autenticación requerida)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/favorites` | Listar favoritos |
| POST | `/api/v1/favorites` | Añadir favorito |
| DELETE | `/api/v1/favorites/{id}` | Eliminar favorito |
| GET | `/api/v1/favorites/default` | Obtener favorito predeterminado |
| PUT | `/api/v1/favorites/{id}/default` | Establecer favorito predeterminado |

## 🔐 Autenticación con Sanctum

Para acceder a los endpoints protegidos:

1. Registra un usuario o inicia sesión para obtener un token
2. Incluye el token en tus solicitudes:
   ```
   Authorization: Bearer {tu_token}
   ```

## 📋 Historial de Clima

La funcionalidad de historial permite:

- Guardar automáticamente consultas de clima para usuarios autenticados
- Filtrar por fechas (`from_date`, `to_date`) y ciudad (`city`)
- Paginar resultados (`per_page`)

Ejemplo:
```
GET /api/v1/weather/history?city=Madrid&from_date=2025-01-01&per_page=10
```

## 🧪 Pruebas

```bash
# Ejecutar todas las pruebas
php artisan test

# Solo pruebas de características
php artisan test --filter=Feature
```

## 📄 Licencia

Este proyecto está licenciado bajo [MIT License](https://opensource.org/licenses/MIT).
