<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\V1\FavoriteDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FavoriteRequest;
use App\Services\Interfaces\FavoriteServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Favoritos",
 *     description="Endpoints para gestionar las ciudades favoritas del usuario"
 * )
 */
class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteServiceInterface $favoriteService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/favorites",
     *     tags={"Favoritos"},
     *     summary="Listar favoritos",
     *     description="Obtiene la lista de ciudades favoritas del usuario",
     *     operationId="listarFavoritos",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="ciudad", type="string"),
     *                     @OA\Property(property="pais", type="string"),
     *                     @OA\Property(property="latitud", type="number"),
     *                     @OA\Property(property="longitud", type="number"),
     *                     @OA\Property(property="es_predeterminado", type="boolean")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $favorites = $this->favoriteService->getUserFavorites(auth()->id());
        
        // Convertir cada favorito a un array
        $favoritesArray = array_map(function($favorite) {
            return $favorite->toArray();
        }, $favorites);

        return response()->json([
            'data' => $favoritesArray
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/favorites",
     *     tags={"Favoritos"},
     *     summary="Agregar favorito",
     *     description="Agrega una nueva ciudad a favoritos",
     *     operationId="agregarFavorito",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city","country","latitude","longitude"},
     *             @OA\Property(property="city", type="string", example="Madrid"),
     *             @OA\Property(property="country", type="string", example="España"),
     *             @OA\Property(property="latitude", type="number", format="float", example=40.4168),
     *             @OA\Property(property="longitude", type="number", format="float", example=-3.7038),
     *             @OA\Property(property="is_default", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Favorito agregado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Favorito agregado exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="ciudad", type="string"),
     *                 @OA\Property(property="pais", type="string"),
     *                 @OA\Property(property="latitud", type="number"),
     *                 @OA\Property(property="longitud", type="number"),
     *                 @OA\Property(property="es_predeterminado", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/v1/favorites/{id}",
     *     tags={"Favoritos"},
     *     summary="Eliminar favorito",
     *     description="Elimina una ciudad de favoritos",
     *     operationId="eliminarFavorito",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del favorito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorito eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Favorito eliminado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favorito no encontrado"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->favoriteService->removeFavorite(auth()->id(), $id);

        return response()->json([
            'message' => 'Favorite removed successfully'
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/favorites/{id}/default",
     *     tags={"Favoritos"},
     *     summary="Establecer favorito predeterminado",
     *     description="Establece una ciudad como favorito predeterminado",
     *     operationId="establecerPredeterminado",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del favorito",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorito predeterminado actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Favorito predeterminado actualizado exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="ciudad", type="string"),
     *                 @OA\Property(property="pais", type="string"),
     *                 @OA\Property(property="es_predeterminado", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favorito no encontrado"
     *     )
     * )
     */
    public function setDefault(int $id): JsonResponse
    {
        $favorite = $this->favoriteService->setDefaultFavorite(auth()->id(), $id);

        return response()->json([
            'message' => 'Default favorite updated successfully',
            'data' => $favorite->toArray()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/favorites/default",
     *     tags={"Favoritos"},
     *     summary="Obtener favorito predeterminado",
     *     description="Obtiene la ciudad favorita predeterminada del usuario",
     *     operationId="obtenerPredeterminado",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="ciudad", type="string"),
     *                 @OA\Property(property="pais", type="string"),
     *                 @OA\Property(property="latitud", type="number"),
     *                 @OA\Property(property="longitud", type="number"),
     *                 @OA\Property(property="es_predeterminado", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No hay favorito predeterminado"
     *     )
     * )
     */
    public function getDefault(): JsonResponse
    {
        $favorite = $this->favoriteService->getDefaultFavorite(auth()->id());

        return response()->json([
            'data' => $favorite?->toArray()
        ]);
    }
}
