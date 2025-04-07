<?php

namespace App\Services;

use App\DTO\V1\WeatherDTO;
use App\Repositories\Interfaces\WeatherRepositoryInterface;
use App\Services\Interfaces\HistoryServiceInterface;
use App\Services\Interfaces\WeatherServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class WeatherService implements WeatherServiceInterface
{
    public function __construct(
        private readonly WeatherRepositoryInterface $weatherRepository,
        private readonly HistoryServiceInterface $historyService
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
            return $this->historyService->saveToHistory($userId, $weatherData);
        } catch (Exception $e) {
            Log::error("Error saving weather history: " . $e->getMessage());
            return false;
        }
    }

    public function getHistory(int $userId, array $filters = []): array
    {
        try {
            $perPage = 10;
            
            if (isset($filters['per_page']) && is_numeric($filters['per_page'])) {
                $perPage = (int) $filters['per_page'];
            }
            
            $historyResult = $this->historyService->getUserHistory($userId, $filters);
            
            // Si el historyService devuelve un array vacío, devolver un resultado vacío
            if (empty($historyResult)) {
                return [
                    'data' => [],
                    'meta' => [
                        'current_page' => 1,
                        'per_page' => $perPage,
                        'total' => 0,
                        'last_page' => 1,
                    ],
                ];
            }
            
            // El historyService ahora devuelve un array con 'data' y 'pagination'
            return [
                'data' => $historyResult['data'],
                'meta' => [
                    'current_page' => $historyResult['pagination']['current_page'],
                    'per_page' => $historyResult['pagination']['per_page'],
                    'total' => $historyResult['pagination']['total'],
                    'last_page' => $historyResult['pagination']['last_page'],
                ],
            ];
        } catch (Exception $e) {
            Log::error("Error getting history: " . $e->getMessage(), [
                'user_id' => $userId,
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                    'last_page' => 1,
                ],
            ];
        }
    }
}
