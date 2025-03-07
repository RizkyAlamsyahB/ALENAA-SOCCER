<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tambahkan definisi middleware role
        $middleware->alias([
            'checkRole' => CheckRole::class,
        ]);

        // Pengecualian CSRF untuk webhook Midtrans
        $middleware->validateCsrfTokens(except: [
            'payment/notification',
            'payment/recurring-notification',
            'payment/pay-account-notification'
        ]);
    })->create();
