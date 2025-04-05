<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\WeatherRequest;
use App\Services\Interfaces\WeatherServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherServiceInterface $weatherService
    ) {
    }

    public function current(WeatherRequest $request): JsonResponse
    {
        $weather = $this->weatherService->getCurrentWeather(
            $request->city,
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

    public function forecast(WeatherRequest $request): JsonResponse
    {
        $forecast = $this->weatherService->getForecast(
            $request->city,
            $request->get('days', 3),
            $request->only(['language', 'units'])
        );

        return response()->json([
            'data' => $forecast
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $cities = $this->weatherService->searchCity($request->query('q'));

        return response()->json([
            'data' => $cities
        ]);
    }

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
