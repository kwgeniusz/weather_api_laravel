<?php

namespace App\Services;

use App\DTO\V1\UserDTO;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function register(UserDTO $userDTO): UserDTO
    {
        // Validate that email doesn't exist
        if ($this->userRepository->findByEmail($userDTO->getEmail())) {
            throw ValidationException::withMessages([
                'email' => ['This email is already registered.'],
            ]);
        }

        // Hash password
        $userDTO->setPassword(Hash::make($userDTO->getPassword()));

        // Create user
        $user = $this->userRepository->create($userDTO);

        return new UserDTO(
            $user->id,
            $user->name,
            $user->email
        );
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new UserDTO(
                $user->id,
                $user->name,
                $user->email,
                null,
                $token
            ),
            'token' => $token,
        ];
    }

    public function logout(int $userId): bool
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            return false;
        }

        // Revoke all tokens
        $user->tokens()->delete();

        return true;
    }

    public function getProfile(int $userId): UserDTO
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw ValidationException::withMessages([
                'user' => ['User not found.'],
            ]);
        }

        return new UserDTO(
            $user->id,
            $user->name,
            $user->email
        );
    }

    public function updateProfile(int $userId, UserDTO $userDTO): UserDTO
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw ValidationException::withMessages([
                'user' => ['User not found.'],
            ]);
        }

        // Check if email is being changed and validate it's not taken
        if ($user->email !== $userDTO->getEmail() && 
            $this->userRepository->findByEmail($userDTO->getEmail())) {
            throw ValidationException::withMessages([
                'email' => ['This email is already taken.'],
            ]);
        }

        // Update user
        $userData = $userDTO->toArray();
        unset($userData['id']); // Don't update ID

        $user = $this->userRepository->update($userId, $userData);

        return new UserDTO(
            $user->id,
            $user->name,
            $user->email
        );
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->find($userId);

        if (!$user || !Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        return $this->userRepository->update($userId, [
            'password' => Hash::make($newPassword),
        ]) !== null;
    }
}
