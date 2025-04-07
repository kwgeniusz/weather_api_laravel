<?php

namespace App\Services;

use App\DTO\V1\FavoriteDTO;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use App\Services\Interfaces\FavoriteServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class FavoriteService implements FavoriteServiceInterface
{
    public function __construct(
        private readonly FavoriteRepositoryInterface $favoriteRepository
    ) {
    }

    public function addFavorite(int $userId, FavoriteDTO $favoriteDTO): FavoriteDTO
    {
        try {
            // Check if city is already in favorites
            if ($this->isCityFavorite($userId, $favoriteDTO->getCity())) {
                throw ValidationException::withMessages([
                    'city' => ['This city is already in your favorites.'],
                ]);
            }

            // If this is the first favorite, make it default
            $isFirst = count($this->getUserFavorites($userId)) === 0;
            if ($isFirst) {
                $favoriteDTO->setDefault(true);
            }

            $favorite = $this->favoriteRepository->addFavorite($userId, $favoriteDTO);

            return new FavoriteDTO(
                $favorite->id,
                $favorite->user_id,
                $favorite->city,
                $favorite->country,
                $favorite->latitude,
                $favorite->longitude,
                $favorite->is_default
            );
        } catch (Exception $e) {
            Log::error("Error adding favorite: " . $e->getMessage());
            throw $e;
        }
    }

    public function removeFavorite(int $userId, int $favoriteId): bool
    {
        try {
            $favorite = $this->favoriteRepository->getFavorite($userId, $favoriteId);

            if (!$favorite) {
                throw ValidationException::withMessages([
                    'favorite' => ['Favorite not found.'],
                ]);
            }

            // If this was the default favorite, set another one as default
            if ($favorite->is_default) {
                $this->setNewDefaultAfterRemoval($userId, $favoriteId);
            }

            return $this->favoriteRepository->deleteFavorite($userId, $favoriteId);
        } catch (Exception $e) {
            Log::error("Error removing favorite: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserFavorites(int $userId): array
    {
        try {
            $favorites = $this->favoriteRepository->getUserFavorites($userId);
            
            // Convertir la colección a un array
            $favoritesArray = $favorites->all();
            
            return array_map(function ($favorite) {
                return new FavoriteDTO(
                    $favorite->id,
                    $favorite->user_id,
                    $favorite->city,
                    $favorite->country,
                    $favorite->latitude,
                    $favorite->longitude,
                    $favorite->is_default
                );
            }, $favoritesArray);
        } catch (Exception $e) {
            Log::error("Error getting user favorites: " . $e->getMessage());
            return [];
        }
    }

    public function setDefaultFavorite(int $userId, int $favoriteId): FavoriteDTO
    {
        try {
            DB::beginTransaction();

            $favorite = $this->favoriteRepository->getFavorite($userId, $favoriteId);

            if (!$favorite) {
                throw ValidationException::withMessages([
                    'favorite' => ['Favorite not found.'],
                ]);
            }

            // Set this one as default
            $favorite = $this->favoriteRepository->setDefaultFavorite($userId, $favoriteId);

            DB::commit();

            return new FavoriteDTO(
                $favorite->id,
                $favorite->user_id,
                $favorite->city,
                $favorite->country,
                $favorite->latitude,
                $favorite->longitude,
                $favorite->is_default
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error setting default favorite: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDefaultFavorite(int $userId): ?FavoriteDTO
    {
        try {
            $favorite = $this->favoriteRepository->getDefaultFavorite($userId);

            if (!$favorite) {
                return null;
            }

            return new FavoriteDTO(
                $favorite->id,
                $favorite->user_id,
                $favorite->city,
                $favorite->country,
                $favorite->latitude,
                $favorite->longitude,
                $favorite->is_default
            );
        } catch (Exception $e) {
            Log::error("Error getting default favorite: " . $e->getMessage());
            return null;
        }
    }

    public function isCityFavorite(int $userId, string $city): bool
    {
        try {
            return $this->favoriteRepository->isCityFavorite($userId, $city);
        } catch (Exception $e) {
            Log::error("Error checking if city is favorite: " . $e->getMessage());
            return false;
        }
    }

    private function setNewDefaultAfterRemoval(int $userId, int $excludeFavoriteId): void
    {
        $nextFavorite = $this->favoriteRepository->getFirstNonDefaultFavorite($userId, $excludeFavoriteId);
        
        if ($nextFavorite) {
            $this->favoriteRepository->setDefaultFavorite($userId, $nextFavorite->id);
        }
    }
}
