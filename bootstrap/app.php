<?php

use App\Models\Constants;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api('throttle:api');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => Constants::RESOURCE_UNAUTHORIZED_STATUS,
                    'message' => 'Unauthorized',
                    'description' => 'You must be authenticated to access this resource. Please provide valid authentication credentials and try again.',
                ], Constants::RESOURCE_UNAUTHORIZED_STATUS);
            }
        });
    })->create();
