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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.session' => \App\Http\Middleware\AuthenticateSession::class,
        ]);

        // Trust all proxies (nginx, Cloudflare) and force HTTPS in production
        $middleware->trustProxies(at: '*');
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
