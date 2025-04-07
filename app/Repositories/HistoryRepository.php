<?php

namespace App\Repositories;

use App\DTO\V1\WeatherDTO;
use App\Models\WeatherHistory;
use App\Repositories\Interfaces\HistoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

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
        try {
            Log::debug("HistoryRepository: Creando registro de historial", [
                'user_id' => $userId,
                'city' => $weatherDTO->getCity(),
                'country' => $weatherDTO->getCountry(),
                'temperature' => $weatherDTO->getTemperature(),
            ]);
            
            // Convertir los arrays a JSON si no están vacíos
            $requestData = !empty($weatherDTO->getRequestData()) ? json_encode($weatherDTO->getRequestData()) : null;
            $responseData = !empty($weatherDTO->getResponseData()) ? json_encode($weatherDTO->getResponseData()) : null;
            
            $history = WeatherHistory::create([
                'user_id' => $userId,
                'city' => $weatherDTO->getCity(),
                'country' => $weatherDTO->getCountry(),
                'temperature' => $weatherDTO->getTemperature(),
                'description' => $weatherDTO->getDescription(),
                'humidity' => $weatherDTO->getHumidity(),
                'wind_speed' => $weatherDTO->getWindSpeed(),
                'request_data' => $requestData,
                'response_data' => $responseData,
            ]);
            
            Log::debug("HistoryRepository: Registro de historial creado con ID: " . ($history ? $history->id : 'null'));
            
            return $history;
        } catch (\Exception $e) {
            Log::error("HistoryRepository: Error al guardar historial: " . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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
