<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\V1\FavoriteDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FavoriteRequest;
use App\Services\Interfaces\FavoriteServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteServiceInterface $favoriteService
    ) {
    }

    public function index(): JsonResponse
    {
        $favorites = $this->favoriteService->getUserFavorites(auth()->id());

        return response()->json([
            'data' => $favorites
        ]);
    }

    public function store(FavoriteRequest $request): JsonResponse
    {
        $favoriteDTO = new FavoriteDTO(
            null,
            auth()->id(),
            $request->city,
            $request->country,
            $request->latitude,
            $request->longitude,
            $request->is_default ?? false
        );

        $favorite = $this->favoriteService->addFavorite(auth()->id(), $favoriteDTO);

        return response()->json([
            'message' => 'Favorite added successfully',
            'data' => $favorite->toArray()
        ], Response::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->favoriteService->removeFavorite(auth()->id(), $id);

        return response()->json([
            'message' => 'Favorite removed successfully'
        ]);
    }

    public function setDefault(int $id): JsonResponse
    {
        $favorite = $this->favoriteService->setDefaultFavorite(auth()->id(), $id);

        return response()->json([
            'message' => 'Default favorite updated successfully',
            'data' => $favorite->toArray()
        ]);
    }

    public function getDefault(): JsonResponse
    {
        $favorite = $this->favoriteService->getDefaultFavorite(auth()->id());

        return response()->json([
            'data' => $favorite?->toArray()
        ]);
    }
}
