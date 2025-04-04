<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class WeatherApiException extends Exception
{
    protected $statusCode;
    protected $errorCode;
    protected $details;

    public function __construct(
        string $message = 'Weather API Error',
        int $statusCode = 500,
        string $errorCode = 'WEATHER_API_ERROR',
        array $details = []
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => $this->message,
                'code' => $this->errorCode,
                'details' => $this->details,
            ]
        ], $this->statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
