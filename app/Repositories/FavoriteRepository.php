<?php

namespace App\Repositories;

use App\DTO\V1\FavoriteDTO;
use App\Models\Favorite;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    /**
     * Get all favorites for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserFavorites(int $userId): Collection
    {
        return Favorite::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('city')
            ->get();
    }

    /**
     * Get a specific favorite for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @return Favorite|null
     */
    public function getFavorite(int $userId, int $favoriteId): ?Favorite
    {
        return Favorite::where('user_id', $userId)
            ->where('id', $favoriteId)
            ->first();
    }

    /**
     * Add a new favorite for a user.
     *
     * @param int $userId
     * @param FavoriteDTO $favoriteDTO
     * @return Favorite
     */
    public function addFavorite(int $userId, FavoriteDTO $favoriteDTO): Favorite
    {
        // If this is the first favorite or set as default, unset any existing default
        if ($favoriteDTO->isDefault()) {
            $this->unsetDefaultFavorites($userId);
        }

        return Favorite::create([
            'user_id' => $userId,
            'city' => $favoriteDTO->getCity(),
            'country' => $favoriteDTO->getCountry(),
            'latitude' => $favoriteDTO->getLatitude(),
            'longitude' => $favoriteDTO->getLongitude(),
            'is_default' => $favoriteDTO->isDefault(),
        ]);
    }

    /**
     * Update a favorite for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @param FavoriteDTO $favoriteDTO
     * @return Favorite|null
     */
    public function updateFavorite(int $userId, int $favoriteId, FavoriteDTO $favoriteDTO): ?Favorite
    {
        $favorite = $this->getFavorite($userId, $favoriteId);

        if (!$favorite) {
            return null;
        }

        // If setting as default, unset any existing default
        if ($favoriteDTO->isDefault() && !$favorite->is_default) {
            $this->unsetDefaultFavorites($userId);
        }

        $favorite->update([
            'city' => $favoriteDTO->getCity(),
            'country' => $favoriteDTO->getCountry(),
            'latitude' => $favoriteDTO->getLatitude(),
            'longitude' => $favoriteDTO->getLongitude(),
            'is_default' => $favoriteDTO->isDefault(),
        ]);

        return $favorite;
    }

    /**
     * Delete a favorite for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @return bool
     */
    public function deleteFavorite(int $userId, int $favoriteId): bool
    {
        $favorite = $this->getFavorite($userId, $favoriteId);

        if (!$favorite) {
            return false;
        }

        // If deleting a default favorite, try to set another one as default
        if ($favorite->is_default) {
            $this->setNewDefaultAfterDeletion($userId, $favoriteId);
        }

        return $favorite->delete();
    }

    /**
     * Set a favorite as default for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @return Favorite|null
     */
    public function setDefaultFavorite(int $userId, int $favoriteId): ?Favorite
    {
        $favorite = $this->getFavorite($userId, $favoriteId);

        if (!$favorite) {
            return null;
        }

        // Unset any existing default favorites
        $this->unsetDefaultFavorites($userId);

        // Set the new default
        $favorite->update(['is_default' => true]);

        return $favorite;
    }

    /**
     * Check if a city is already in user's favorites.
     *
     * @param int $userId
     * @param string $city
     * @return bool
     */
    public function isCityFavorite(int $userId, string $city): bool
    {
        return Favorite::where('user_id', $userId)
            ->where('city', $city)
            ->exists();
    }

    /**
     * Unset all default favorites for a user.
     *
     * @param int $userId
     * @return void
     */
    private function unsetDefaultFavorites(int $userId): void
    {
        Favorite::where('user_id', $userId)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    /**
     * Set a new default favorite after deleting the current default.
     *
     * @param int $userId
     * @param int $deletedFavoriteId
     * @return void
     */
    private function setNewDefaultAfterDeletion(int $userId, int $deletedFavoriteId): void
    {
        $newDefault = Favorite::where('user_id', $userId)
            ->where('id', '!=', $deletedFavoriteId)
            ->orderBy('created_at')
            ->first();

        if ($newDefault) {
            $newDefault->update(['is_default' => true]);
        }
    }

    /**
     * Get the first non-default favorite for a user, excluding a specific ID.
     *
     * @param int $userId
     * @param int $excludeId
     * @return Favorite|null
     */
    public function getFirstNonDefaultFavorite(int $userId, int $excludeId): ?Favorite
    {
        return Favorite::where('user_id', $userId)
            ->where('id', '!=', $excludeId)
            ->where('is_default', false)
            ->orderBy('created_at')
            ->first();
    }
}
