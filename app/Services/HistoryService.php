<?php

namespace App\Services;

use App\DTO\V1\WeatherDTO;
use App\Repositories\Interfaces\HistoryRepositoryInterface;
use App\Services\Interfaces\HistoryServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class HistoryService implements HistoryServiceInterface
{
    public function __construct(
        private readonly HistoryRepositoryInterface $historyRepository
    ) {
    }

    public function saveToHistory(int $userId, WeatherDTO $weatherDTO): bool
    {
        try {
            Log::debug("Intentando guardar historial para usuario $userId, ciudad: " . $weatherDTO->getCity());
            
            $result = $this->historyRepository->saveToHistory($userId, $weatherDTO);
            
            Log::debug("Resultado de guardar historial: " . ($result ? "Éxito" : "Fallo"));
            
            return $result !== null;
        } catch (Exception $e) {
            Log::error("Error detallado al guardar historial: " . $e->getMessage(), [
                'user_id' => $userId,
                'city' => $weatherDTO->getCity(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function getUserHistory(int $userId, array $filters = []): array
    {
        try {
            // Extraer el valor de paginación del array de filtros, o usar 15 por defecto
            $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
            
            // Obtener el historial paginado
            $history = $this->historyRepository->getUserHistory($userId, $perPage);
            
            // Devolver los resultados como array
            return [
                'data' => $history->items(),
                'pagination' => [
                    'total' => $history->total(),
                    'per_page' => $history->perPage(),
                    'current_page' => $history->currentPage(),
                    'last_page' => $history->lastPage()
                ]
            ];
        } catch (Exception $e) {
            Log::error("Error getting user history: " . $e->getMessage(), [
                'user_id' => $userId,
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
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
