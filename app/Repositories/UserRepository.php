<?php

namespace App\Repositories;

use App\DTO\V1\UserDTO;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     *
     * @param UserDTO $userDTO
     * @return User
     */
    public function create(UserDTO $userDTO): User
    {
        return User::create([
            'name' => $userDTO->getName(),
            'email' => $userDTO->getEmail(),
            'password' => Hash::make($userDTO->getPassword()),
        ]);
    }

    /**
     * Update an existing user.
     *
     * @param int $id
     * @param UserDTO $userDTO
     * @return User|null
     */
    public function update(int $id, UserDTO $userDTO): ?User
    {
        $user = $this->findById($id);

        if (!$user) {
            return null;
        }

        $data = [
            'name' => $userDTO->getName(),
            'email' => $userDTO->getEmail(),
        ];

        if ($userDTO->getPassword()) {
            $data['password'] = Hash::make($userDTO->getPassword());
        }

        $user->update($data);

        return $user;
    }

    /**
     * Delete a user.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }
}
