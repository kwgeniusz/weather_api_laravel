<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (WeatherApiException $e) {
            return $e->render();
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => [
                        'message' => 'The given data was invalid.',
                        'code' => 'VALIDATION_ERROR',
                        'details' => $e->errors(),
                    ]
                ], 422);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => [
                        'message' => 'Resource not found.',
                        'code' => 'NOT_FOUND',
                    ]
                ], 404);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
