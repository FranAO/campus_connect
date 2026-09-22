<?php

use App\Http\Middleware\EnsureAdministrativo;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['admin' => EnsureAdministrativo::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Los datos proporcionados no son válidos.', 'errors' => $exception->errors()], 422);
            }
        });
        $exceptions->render(function (AuthenticationException $exception, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
            }
        });
        $exceptions->render(function (HttpExceptionInterface $exception, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $exception->getMessage() ?: 'Error en la solicitud.'], $exception->getStatusCode());
            }
        });
    })->create();
