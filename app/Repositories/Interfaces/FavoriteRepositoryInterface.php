<?php

namespace App\Repositories\Interfaces;

use App\DTO\V1\FavoriteDTO;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteRepositoryInterface
{
    /**
     * Get all favorites for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserFavorites(int $userId): Collection;

    /**
     * Get a specific favorite for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @return Favorite|null
     */
    public function getFavorite(int $userId, int $favoriteId): ?Favorite;

    /**
     * Add a new favorite for a user.
     *
     * @param int $userId
     * @param FavoriteDTO $favoriteDTO
     * @return Favorite
     */
    public function addFavorite(int $userId, FavoriteDTO $favoriteDTO): Favorite;

    /**
     * Update a favorite for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @param FavoriteDTO $favoriteDTO
     * @return Favorite|null
     */
    public function updateFavorite(int $userId, int $favoriteId, FavoriteDTO $favoriteDTO): ?Favorite;

    /**
     * Delete a favorite for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @return bool
     */
    public function deleteFavorite(int $userId, int $favoriteId): bool;

    /**
     * Set a favorite as default for a user.
     *
     * @param int $userId
     * @param int $favoriteId
     * @return Favorite|null
     */
    public function setDefaultFavorite(int $userId, int $favoriteId): ?Favorite;
}
