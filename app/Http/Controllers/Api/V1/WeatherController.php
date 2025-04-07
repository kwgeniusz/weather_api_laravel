<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\WeatherRequest;
use App\Services\Interfaces\WeatherServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Clima",
 *     description="Endpoints de la API para operaciones relacionadas con el clima"
 * )
 */
class WeatherController extends Controller
{
    private readonly WeatherServiceInterface $weatherService;

    public function __construct(WeatherServiceInterface $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/weather/current",
     *     tags={"Clima"},
     *     summary="Obtener clima actual de una ciudad",
     *     description="Devuelve la información meteorológica actual de la ciudad especificada",
     *     operationId="obtenerClimaActual",
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Nombre de la ciudad",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Idioma (es, en, etc.)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="units",
     *         in="query",
     *         description="Unidades de medida (metric/imperial)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"metric", "imperial"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="ciudad", type="string"),
     *                 @OA\Property(property="temperatura", type="number"),
     *                 @OA\Property(property="humedad", type="integer"),
     *                 @OA\Property(property="velocidad_viento", type="number"),
     *                 @OA\Property(property="descripcion", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ciudad no encontrada"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Demasiadas solicitudes"
     *     )
     * )
     */
    public function current(WeatherRequest $request): JsonResponse
    {
        $weather = $this->weatherService->getCurrentWeather(
            $request->query('city'),
            $request->only(['language', 'units'])
        );

        // Save to history if user is authenticated
        if (auth()->check()) {
            $this->weatherService->saveHistory(auth()->id(), $weather);
        }

        return response()->json([
            'data' => $weather->toArray()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/weather/forecast",
     *     tags={"Clima"},
     *     summary="Obtener pronóstico del clima para una ciudad",
     *     description="Devuelve el pronóstico del tiempo para los próximos días",
     *     operationId="obtenerPronostico",
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Nombre de la ciudad",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Número de días para el pronóstico",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=7)
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Idioma (es, en, etc.)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="units",
     *         in="query",
     *         description="Unidades de medida (metric/imperial)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"metric", "imperial"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="fecha", type="string"),
     *                     @OA\Property(property="temperatura", type="object",
     *                         @OA\Property(property="minima", type="number"),
     *                         @OA\Property(property="maxima", type="number")
     *                     ),
     *                     @OA\Property(property="humedad", type="integer"),
     *                     @OA\Property(property="descripcion", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ciudad no encontrada"
     *     )
     * )
     */
    public function forecast(WeatherRequest $request): JsonResponse
    {
        $forecast = $this->weatherService->getForecast(
            $request->query('city'),
            $request->query('days', 3),
            $request->only(['language', 'units'])
        );

        return response()->json([
            'data' => $forecast
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/weather/search",
     *     tags={"Clima"},
     *     summary="Buscar ciudades",
     *     description="Devuelve una lista de ciudades que coinciden con la búsqueda",
     *     operationId="buscarCiudad",
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Término de búsqueda",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="nombre", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $cities = $this->weatherService->searchCity($request->query('q'));
        return response()->json([
            'data' => $cities
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/weather/history",
     *     tags={"Clima"},
     *     summary="Obtener historial del clima del usuario",
     *     description="Devuelve una lista de consultas meteorológicas realizadas por el usuario",
     *     operationId="obtenerHistorialUsuario",
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Fecha de inicio para el historial",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Fecha final para el historial",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Nombre de la ciudad",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="ciudad", type="string"),
     *                     @OA\Property(property="temperatura", type="number"),
     *                     @OA\Property(property="humedad", type="integer"),
     *                     @OA\Property(property="velocidad_viento", type="number"),
     *                     @OA\Property(property="descripcion", type="string"),
     *                     @OA\Property(property="fecha_creacion", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function history(Request $request): JsonResponse
    {
        $history = $this->weatherService->getUserHistory(
            auth()->id(),
            $request->only(['from_date', 'to_date', 'city'])
        );

        return response()->json([
            'data' => $history
        ]);
    }
}
