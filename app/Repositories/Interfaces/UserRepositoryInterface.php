<?php

namespace App\Repositories\Interfaces;

use App\DTO\V1\UserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     *
     * @param UserDTO $userDTO
     * @return User
     */
    public function create(UserDTO $userDTO): User;

    /**
     * Update an existing user.
     *
     * @param int $id
     * @param UserDTO $userDTO
     * @return User|null
     */
    public function update(int $id, UserDTO $userDTO): ?User;

    /**
     * Delete a user.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
