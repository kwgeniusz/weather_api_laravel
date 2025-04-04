<?php

namespace App\Repositories;

use App\DTO\V1\WeatherDTO;
use App\Models\WeatherHistory;
use App\Repositories\Interfaces\HistoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * Save a weather request to history.
     *
     * @param int $userId
     * @param WeatherDTO $weatherDTO
     * @return WeatherHistory
     */
    public function saveToHistory(int $userId, WeatherDTO $weatherDTO): WeatherHistory
    {
        return WeatherHistory::create([
            'user_id' => $userId,
            'city' => $weatherDTO->getCity(),
            'country' => $weatherDTO->getCountry(),
            'temperature' => $weatherDTO->getTemperature(),
            'description' => $weatherDTO->getDescription(),
            'humidity' => $weatherDTO->getHumidity(),
            'wind_speed' => $weatherDTO->getWindSpeed(),
            'request_data' => $weatherDTO->getRequestData(),
            'response_data' => $weatherDTO->getResponseData(),
        ]);
    }

    /**
     * Get history for a user.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserHistory(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return WeatherHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Delete a history item.
     *
     * @param int $userId
     * @param int $historyId
     * @return bool
     */
    public function deleteHistoryItem(int $userId, int $historyId): bool
    {
        return WeatherHistory::where('user_id', $userId)
            ->where('id', $historyId)
            ->delete() > 0;
    }

    /**
     * Clear all history for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function clearUserHistory(int $userId): bool
    {
        return WeatherHistory::where('user_id', $userId)->delete() > 0;
    }
}
