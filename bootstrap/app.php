<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ApiTokenMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // this is api routing
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // just append on api this middleware
        $middleware->api(append: [ApiTokenMiddleware::class]);

        /* or do something like this, then we can use different route
        $middleware->alias([
            'api.token' => ApiTokenMiddleware::class,
        ]);
        Route::middleware(['api.token'])->post('/upload-media', [MediaController::class, 'store']);
        */
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
