<?php

namespace App\Services\Interfaces;

use App\DTO\V1\FavoriteDTO;

interface FavoriteServiceInterface
{
    /**
     * Add a city to user's favorites
     *
     * @param int $userId
     * @param FavoriteDTO $favoriteDTO
     * @return FavoriteDTO
     */
    public function addFavorite(int $userId, FavoriteDTO $favoriteDTO): FavoriteDTO;

    /**
     * Remove a city from user's favorites
     *
     * @param int $userId
     * @param int $favoriteId
     * @return bool
     */
    public function removeFavorite(int $userId, int $favoriteId): bool;

    /**
     * Get user's favorite cities
     *
     * @param int $userId
     * @return array
     */
    public function getUserFavorites(int $userId): array;

    /**
     * Set a favorite city as default
     *
     * @param int $userId
     * @param int $favoriteId
     * @return FavoriteDTO
     */
    public function setDefaultFavorite(int $userId, int $favoriteId): FavoriteDTO;

    /**
     * Get user's default favorite city
     *
     * @param int $userId
     * @return FavoriteDTO|null
     */
    public function getDefaultFavorite(int $userId): ?FavoriteDTO;

    /**
     * Check if a city is in user's favorites
     *
     * @param int $userId
     * @param string $city
     * @return bool
     */
    public function isCityFavorite(int $userId, string $city): bool;
}
