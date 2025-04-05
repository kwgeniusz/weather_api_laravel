<?php

namespace App\Services\Interfaces;

use App\DTO\V1\WeatherDTO;

interface HistoryServiceInterface
{
    /**
     * Save weather search to history
     *
     * @param int $userId
     * @param WeatherDTO $weatherDTO
     * @return bool
     */
    public function saveToHistory(int $userId, WeatherDTO $weatherDTO): bool;

    /**
     * Get user's search history
     *
     * @param int $userId
     * @param array $filters Optional filters (date range, city, etc.)
     * @return array
     */
    public function getUserHistory(int $userId, array $filters = []): array;

    /**
     * Clear user's search history
     *
     * @param int $userId
     * @return bool
     */
    public function clearHistory(int $userId): bool;

    /**
     * Get most searched cities by user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getMostSearchedCities(int $userId, int $limit = 5): array;

    /**
     * Get recent searches by user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentSearches(int $userId, int $limit = 10): array;

    /**
     * Delete specific history entry
     *
     * @param int $userId
     * @param int $historyId
     * @return bool
     */
    public function deleteHistoryEntry(int $userId, int $historyId): bool;
}
