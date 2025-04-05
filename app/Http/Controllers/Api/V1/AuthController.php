<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\V1\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $userDTO = new UserDTO(
            null,
            $request->name,
            $request->email,
            $request->password
        );

        $user = $this->userService->register($userDTO);

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $user->toArray()
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->userService->login($request->email, $request->password);

        return response()->json([
            'message' => 'Logged in successfully',
            'data' => $result
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->userService->logout(auth()->id());

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
