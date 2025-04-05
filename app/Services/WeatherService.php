<?php

namespace App\Services;

use App\DTO\V1\WeatherDTO;
use App\Repositories\Interfaces\WeatherRepositoryInterface;
use App\Repositories\Interfaces\HistoryRepositoryInterface;
use App\Services\Interfaces\WeatherServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class WeatherService implements WeatherServiceInterface
{
    public function __construct(
        private readonly WeatherRepositoryInterface $weatherRepository,
        private readonly HistoryRepositoryInterface $historyRepository
    ) {
    }

    public function getCurrentWeather(string $city, array $options = []): WeatherDTO
    {
        try {
            return $this->weatherRepository->getCurrentWeather($city, $options);
        } catch (Exception $e) {
            Log::error("Error getting current weather: " . $e->getMessage());
            throw $e;
        }
    }

    public function getForecast(string $city, int $days = 3, array $options = []): array
    {
        try {
            return $this->weatherRepository->getForecast($city, $days, $options);
        } catch (Exception $e) {
            Log::error("Error getting forecast: " . $e->getMessage());
            throw $e;
        }
    }

    public function searchCity(string $query): array
    {
        $cacheKey = "city_search_" . md5($query);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $results = $this->weatherRepository->searchCity($query);
            
            // Cache results for 24 hours as city data doesn't change frequently
            Cache::put($cacheKey, $results, now()->addHours(24));
            
            return $results;
        } catch (Exception $e) {
            Log::error("Error searching city: " . $e->getMessage());
            throw $e;
        }
    }

    public function saveHistory(int $userId, WeatherDTO $weatherData): bool
    {
        try {
            return $this->historyRepository->create([
                'user_id' => $userId,
                'city' => $weatherData->getCity(),
                'country' => $weatherData->getCountry(),
                'temperature' => $weatherData->getTemperature(),
                'description' => $weatherData->getDescription(),
                'humidity' => $weatherData->getHumidity(),
                'wind_speed' => $weatherData->getWindSpeed(),
                'request_data' => json_encode($weatherData->getRequestData()),
                'response_data' => json_encode($weatherData->getResponseData()),
            ]) !== null;
        } catch (Exception $e) {
            Log::error("Error saving weather history: " . $e->getMessage());
            return false;
        }
    }

    public function getUserHistory(int $userId, array $filters = []): array
    {
        try {
            return $this->historyRepository->getUserHistory($userId, $filters);
        } catch (Exception $e) {
            Log::error("Error getting user history: " . $e->getMessage());
            return [];
        }
    }
}
