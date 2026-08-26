<?php

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
    ->withMiddleware(function (Middleware $middleware): void {
        // id: Aktifkan Sanctum stateful API agar sesi login dari browser (cookie)
        //     dikenali oleh route /api/* — tanpa ini auth:sanctum membalas 401
        //     untuk request dari SPA meski user sudah login.
        // en: Enable Sanctum stateful API so the browser login session (cookie)
        //     is recognized by /api/* routes — without this, auth:sanctum answers 401
        //     for SPA requests even though the user is already logged in.
        $middleware->statefulApi();

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
