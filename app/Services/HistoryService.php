<?php

namespace App\Services;

use App\DTO\V1\WeatherDTO;
use App\Repositories\Interfaces\HistoryRepositoryInterface;
use App\Services\Interfaces\HistoryServiceInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class HistoryService implements HistoryServiceInterface
{
    public function __construct(
        private readonly HistoryRepositoryInterface $historyRepository
    ) {
    }

    public function saveToHistory(int $userId, WeatherDTO $weatherDTO): bool
    {
        try {
            return $this->historyRepository->create([
                'user_id' => $userId,
                'city' => $weatherDTO->getCity(),
                'country' => $weatherDTO->getCountry(),
                'temperature' => $weatherDTO->getTemperature(),
                'description' => $weatherDTO->getDescription(),
                'humidity' => $weatherDTO->getHumidity(),
                'wind_speed' => $weatherDTO->getWindSpeed(),
                'request_data' => json_encode($weatherDTO->getRequestData()),
                'response_data' => json_encode($weatherDTO->getResponseData()),
            ]) !== null;
        } catch (Exception $e) {
            Log::error("Error saving to history: " . $e->getMessage());
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

    public function clearHistory(int $userId): bool
    {
        try {
            return $this->historyRepository->clearUserHistory($userId);
        } catch (Exception $e) {
            Log::error("Error clearing history: " . $e->getMessage());
            return false;
        }
    }

    public function getMostSearchedCities(int $userId, int $limit = 5): array
    {
        try {
            return $this->historyRepository->getMostSearchedCities($userId, $limit);
        } catch (Exception $e) {
            Log::error("Error getting most searched cities: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentSearches(int $userId, int $limit = 10): array
    {
        try {
            return $this->historyRepository->getRecentSearches($userId, $limit);
        } catch (Exception $e) {
            Log::error("Error getting recent searches: " . $e->getMessage());
            return [];
        }
    }

    public function deleteHistoryEntry(int $userId, int $historyId): bool
    {
        try {
            $history = $this->historyRepository->find($historyId);

            if (!$history || $history->user_id !== $userId) {
                return false;
            }

            return $this->historyRepository->delete($historyId);
        } catch (Exception $e) {
            Log::error("Error deleting history entry: " . $e->getMessage());
            return false;
        }
    }
}
