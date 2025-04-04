<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Success response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse(mixed $data, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
            return $data->additional($response)->response()->setStatusCode($statusCode);
        }

        $response['data'] = $data;

        return response()->json($response, $statusCode);
    }

    /**
     * Error response
     *
     * @param string $message
     * @param string $errorCode
     * @param array $details
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function errorResponse(
        string $message,
        string $errorCode = 'ERROR',
        array $details = [],
        int $statusCode = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $errorCode,
                'details' => $details,
            ]
        ], $statusCode);
    }

    /**
     * Response with message only
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function messageResponse(string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }
}
