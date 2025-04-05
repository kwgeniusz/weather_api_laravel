<?php

namespace App\Services\Interfaces;

use App\DTO\V1\WeatherDTO;

interface WeatherServiceInterface
{
    /**
     * Get current weather for a city
     *
     * @param string $city
     * @param array $options
     * @return WeatherDTO
     */
    public function getCurrentWeather(string $city, array $options = []): WeatherDTO;

    /**
     * Get weather forecast for a city
     *
     * @param string $city
     * @param int $days
     * @param array $options
     * @return array
     */
    public function getForecast(string $city, int $days = 3, array $options = []): array;

    /**
     * Search for cities by name
     *
     * @param string $query
     * @return array
     */
    public function searchCity(string $query): array;

    /**
     * Save weather history for a user
     *
     * @param int $userId
     * @param WeatherDTO $weatherData
     * @return bool
     */
    public function saveHistory(int $userId, WeatherDTO $weatherData): bool;

    /**
     * Get weather history for a user
     *
     * @param int $userId
     * @param array $filters
     * @return array
     */
    public function getUserHistory(int $userId, array $filters = []): array;
}
