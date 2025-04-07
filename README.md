<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Weather API Laravel

## About The Project

Weather API Laravel es una API RESTful para consultar el clima actual y pronósticos de cualquier ciudad del mundo. Utiliza la API de WeatherAPI.com para obtener datos meteorológicos y ofrece funcionalidades adicionales como autenticación de usuarios, gestión de favoritos e historial de búsquedas.

## Features

- **User Authentication**: Registro, inicio de sesión, cierre de sesión y gestión de perfil utilizando Laravel Sanctum
- **Weather Data**: Clima actual y pronósticos para cualquier ciudad
- **City Search**: Búsqueda de ciudades por nombre
- **Favorites Management**: Guardar ciudades favoritas y establecer ciudad predeterminada
- **History Tracking**: Seguimiento automático del historial de búsquedas de clima para usuarios autenticados
- **Multilingual Support**: Respuestas de API en múltiples idiomas

## Architecture

La aplicación sigue la Arquitectura Limpia con el patrón Repositorio-Servicio:
- **Models**: User, WeatherHistory, Favorite
- **DTOs**: UserDTO, WeatherDTO, FavoriteDTO
- **Repositories**: Interfaces e implementaciones para User, Weather, History y Favorites
- **Services**: Lógica de negocio para autenticación, datos meteorológicos, favoritos e historial
- **Controllers**: Endpoints de API para la aplicación
- **Authentication**: Implementación de Laravel Sanctum para API tokens

## Requirements

- PHP 8.1+
- Composer
- MySQL/MariaDB
- Redis (recomendado para caché)
- WeatherAPI.com API Key

## Installation

### Using Docker

1. Clonar el repositorio:
```bash
git clone https://github.com/yourusername/weather_api_laravel.git
cd weather_api_laravel
```

2. Copiar el archivo de entorno:
```bash
cp .env.example .env
```

3. Configurar el archivo .env con sus credenciales de base de datos y la clave API de WeatherAPI.com:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=weather_api
DB_USERNAME=your_username
DB_PASSWORD=your_password

WEATHER_API_KEY=your_api_key
```

4. Construir e iniciar los contenedores Docker:
```bash
docker-compose up -d
```

5. Instalar dependencias:
```bash
docker-compose exec app composer install
```

6. Generar clave de aplicación:
```bash
docker-compose exec app php artisan key:generate
```

7. Ejecutar migraciones:
```bash
docker-compose exec app php artisan migrate
```

### Without Docker

1. Clonar el repositorio:
```bash
git clone https://github.com/yourusername/weather_api_laravel.git
cd weather_api_laravel
```

2. Copiar el archivo de entorno:
```bash
cp .env.example .env
```

3. Configurar el archivo .env con sus credenciales de base de datos y la clave API de WeatherAPI.com.

4. Instalar dependencias:
```bash
composer install
```

5. Generar clave de aplicación:
```bash
php artisan key:generate
```

6. Ejecutar migraciones:
```bash
php artisan migrate
```

7. Iniciar el servidor:
```bash
php artisan serve
```

## API Documentation

Se incluye una colección de Postman en el repositorio (`Weather_API_Laravel.postman_collection.json`). Importe esta colección en Postman para explorar y probar todos los endpoints disponibles.

### Authentication Endpoints

- `POST /api/v1/register` - Registrar un nuevo usuario
- `POST /api/v1/login` - Iniciar sesión y obtener token de autenticación
- `POST /api/v1/logout` - Cerrar sesión y revocar token (requiere autenticación)

### User Profile Endpoints

- `GET /api/v1/profile` - Obtener perfil de usuario (requiere autenticación)
- `PUT /api/v1/profile` - Actualizar perfil de usuario (requiere autenticación)
- `PUT /api/v1/profile/password` - Cambiar contraseña de usuario (requiere autenticación)

### Weather Endpoints

- `GET /api/v1/weather/current?city={city}` - Obtener clima actual de una ciudad
  - Si se proporciona un token de autenticación, la consulta se guarda automáticamente en el historial
- `GET /api/v1/weather/forecast?city={city}&days={days}` - Obtener pronóstico del tiempo para una ciudad
- `GET /api/v1/weather/search?query={query}` - Buscar ciudades por nombre

### Weather History Endpoints (requieren autenticación)

- `GET /api/v1/weather/history` - Obtener historial de búsquedas de clima del usuario
  - Soporta paginación con parámetro `per_page`
  - Soporta filtrado por fechas con parámetros `from_date` y `to_date`
  - Soporta filtrado por ciudad con parámetro `city`

### Favorites Endpoints (requieren autenticación)

- `GET /api/v1/favorites` - Obtener todas las ciudades favoritas
- `GET /api/v1/favorites/default` - Obtener ciudad favorita predeterminada
- `POST /api/v1/favorites` - Añadir una ciudad a favoritos
- `DELETE /api/v1/favorites/{id}` - Eliminar una ciudad de favoritos
- `PUT /api/v1/favorites/{id}/default` - Establecer una ciudad favorita como predeterminada

## Autenticación con Sanctum

Esta API utiliza Laravel Sanctum para la autenticación. Para acceder a los endpoints protegidos:

1. Registre un usuario o inicie sesión para obtener un token de autenticación
2. Incluya el token en el encabezado de sus solicitudes:
   ```
   Authorization: Bearer {your_token}
   ```

## Historial de Clima

La funcionalidad de historial de clima permite a los usuarios autenticados:

- Guardar automáticamente las consultas de clima realizadas
- Ver su historial de consultas con filtros y paginación
- Los datos guardados incluyen ciudad, país, temperatura, descripción, humedad, velocidad del viento y datos completos de la respuesta de la API

## Testing

Ejecute las pruebas de características para asegurarse de que los endpoints de la API funcionan correctamente:

```bash
php artisan test --filter=Feature
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
