<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Weather API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the external weather API service.
    |
    */

    'api_key' => env('WEATHER_API_KEY', ''),
    
    'base_url' => env('WEATHER_API_URL', 'https://api.weatherapi.com/v1'),
    
    'endpoints' => [
        'current' => '/current.json',
        'forecast' => '/forecast.json',
        'search' => '/search.json',
    ],
    
    'cache' => [
        'enabled' => env('WEATHER_API_CACHE_ENABLED', true),
        'ttl' => env('WEATHER_API_CACHE_TTL', 30), // minutes
    ],
    
    'timeout' => env('WEATHER_API_TIMEOUT', 5), // seconds
    
    'retry' => [
        'times' => env('WEATHER_API_RETRY_TIMES', 3),
        'sleep' => env('WEATHER_API_RETRY_SLEEP', 1000), // milliseconds
    ],
];
