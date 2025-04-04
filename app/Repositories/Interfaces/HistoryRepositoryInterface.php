<?php

namespace App\Repositories\Interfaces;

use App\DTO\V1\WeatherDTO;
use App\Models\WeatherHistory;
use Illuminate\Pagination\LengthAwarePaginator;

interface HistoryRepositoryInterface
{
    /**
     * Save a weather request to history.
     *
     * @param int $userId
     * @param WeatherDTO $weatherDTO
     * @return WeatherHistory
     */
    public function saveToHistory(int $userId, WeatherDTO $weatherDTO): WeatherHistory;

    /**
     * Get history for a user.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserHistory(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Delete a history item.
     *
     * @param int $userId
     * @param int $historyId
     * @return bool
     */
    public function deleteHistoryItem(int $userId, int $historyId): bool;

    /**
     * Clear all history for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function clearUserHistory(int $userId): bool;
}
