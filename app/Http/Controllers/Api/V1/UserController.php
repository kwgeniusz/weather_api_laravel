<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\V1\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ChangePasswordRequest;
use App\Http\Requests\V1\UpdateProfileRequest;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {
    }

    public function profile(): JsonResponse
    {
        $profile = $this->userService->getProfile(auth()->id());

        return response()->json([
            'data' => $profile->toArray()
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $userDTO = new UserDTO(
            auth()->id(),
            $request->name,
            $request->email
        );

        $profile = $this->userService->updateProfile(auth()->id(), $userDTO);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $profile->toArray()
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->userService->changePassword(
            auth()->id(),
            $request->current_password,
            $request->new_password
        );

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}
