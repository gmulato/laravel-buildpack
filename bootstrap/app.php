<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->api([
            \App\Http\Middleware\EnsureJsonResponse::class,
        ]);

        $middleware->alias([
            'prevent.back.history' => \App\Http\Middleware\PreventBackHistory::class,
            'ensure.json.response' => \App\Http\Middleware\EnsureJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
