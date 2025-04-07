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

Weather API Laravel is a RESTful API application built with Laravel that provides weather information for cities around the world. The application integrates with WeatherAPI.com to fetch current weather data and forecasts, while allowing users to manage their favorite cities and search history.

## Features

- **User Authentication**: Register, login, logout, profile management
- **Weather Data**: Current weather and forecasts for any city
- **City Search**: Search for cities by name
- **Favorites Management**: Save favorite cities and set default city
- **History Tracking**: Track and manage weather search history
- **Multilingual Support**: API responses in multiple languages

## Architecture

The application follows Clean Architecture with Repository-Service pattern:
- **Models**: User, WeatherHistory, Favorite
- **DTOs**: UserDTO, WeatherDTO, FavoriteDTO
- **Repositories**: Interfaces and implementations for User, Weather, History, and Favorites
- **Services**: Business logic for authentication, weather data, favorites, and history
- **Controllers**: API endpoints for the application

## Requirements

- PHP 8.1+
- Composer
- MySQL/MariaDB
- Redis (recommended for caching)
- WeatherAPI.com API Key

## Installation

### Using Docker

1. Clone the repository:
```bash
git clone https://github.com/yourusername/weather_api_laravel.git
cd weather_api_laravel
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Configure your .env file with your database credentials and WeatherAPI.com API key:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=weather_api
DB_USERNAME=your_username
DB_PASSWORD=your_password

WEATHER_API_KEY=your_api_key
```

4. Build and start the Docker containers:
```bash
docker-compose up -d
```

5. Install dependencies:
```bash
docker-compose exec app composer install
```

6. Generate application key:
```bash
docker-compose exec app php artisan key:generate
```

7. Run migrations:
```bash
docker-compose exec app php artisan migrate
```

### Without Docker

1. Clone the repository:
```bash
git clone https://github.com/yourusername/weather_api_laravel.git
cd weather_api_laravel
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Configure your .env file with your database credentials and WeatherAPI.com API key.

4. Install dependencies:
```bash
composer install
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Start the server:
```bash
php artisan serve
```

## API Documentation

A Postman collection is included in the repository (`Weather_API_Laravel.postman_collection.json`). Import this collection into Postman to explore and test all available endpoints.

### Authentication Endpoints

- `POST /api/v1/auth/register` - Register a new user
- `POST /api/v1/auth/login` - Login and get authentication token
- `POST /api/v1/auth/logout` - Logout and revoke token
- `GET /api/v1/auth/profile` - Get user profile
- `PUT /api/v1/auth/profile` - Update user profile
- `PUT /api/v1/auth/password` - Change user password

### Weather Endpoints

- `GET /api/v1/weather/current?city={city}` - Get current weather for a city
- `GET /api/v1/weather/forecast?city={city}&days={days}` - Get weather forecast for a city
- `GET /api/v1/weather/search?query={query}` - Search for cities by name
- `GET /api/v1/weather/history` - Get user's weather search history
- `DELETE /api/v1/weather/history` - Clear all weather search history
- `DELETE /api/v1/weather/history/{id}` - Delete specific history item

### Favorites Endpoints

- `GET /api/v1/favorites` - Get all favorite cities
- `GET /api/v1/favorites/default` - Get default favorite city
- `POST /api/v1/favorites` - Add a city to favorites
- `DELETE /api/v1/favorites/{id}` - Remove a city from favorites
- `PUT /api/v1/favorites/{id}/default` - Set a favorite city as default

## Testing

Run the feature tests to ensure the API endpoints are working correctly:

```bash
php artisan test --filter=Feature
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
