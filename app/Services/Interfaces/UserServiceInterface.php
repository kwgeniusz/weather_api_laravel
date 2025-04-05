<?php

namespace App\Services\Interfaces;

use App\DTO\V1\UserDTO;

interface UserServiceInterface
{
    /**
     * Register a new user
     *
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function register(UserDTO $userDTO): UserDTO;

    /**
     * Authenticate user and return token
     *
     * @param string $email
     * @param string $password
     * @return array Contains user data and token
     */
    public function login(string $email, string $password): array;

    /**
     * Logout user and revoke token
     *
     * @param int $userId
     * @return bool
     */
    public function logout(int $userId): bool;

    /**
     * Get user profile
     *
     * @param int $userId
     * @return UserDTO
     */
    public function getProfile(int $userId): UserDTO;

    /**
     * Update user profile
     *
     * @param int $userId
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function updateProfile(int $userId, UserDTO $userDTO): UserDTO;

    /**
     * Change user password
     *
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool;
}
