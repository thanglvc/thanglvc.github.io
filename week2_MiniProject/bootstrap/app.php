<?php

use App\Http\Middleware\RequireJson;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )  // Nạp routes/api.php và tự thêm tiền tố /api

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'require.json' => RequireJson::class,
        ]); // Đặt tên require.json cho class RequireJson
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api', 'api/*') || $request->expectsJson(),
        );

        // Định dạng lỗi API thống nhất thành message và errors
        $exceptions->render(function (
            Throwable $exception,
            Request $request
        ): ?JsonResponse {
            if (! $request->is('api', 'api/*')) {
                return null;
            }

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $exception->errors(),
                ], 422);
            }

            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            $headers = $exception instanceof HttpExceptionInterface
                ? $exception->getHeaders()
                : [];

            $message = match ($status) {
                400 => 'Bad request.',
                401 => 'Unauthenticated.',
                403 => 'Forbidden.',
                404 => 'Resource not found.',
                405 => 'Method not allowed.',
                415 => 'Content-Type must be application/json.',
                429 => 'Too many requests.',
                default => $status >= 500
                    ? 'Internal server error.'
                    : 'Request failed.',
            };

            return response()->json([
                'message' => $message,
                'errors' => (object) [],
            ], $status, $headers);
        });
    })
    ->create();
