<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\User\PaymentController;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php', // Pastikan file ini ada
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias Middleware
        $middleware->alias([
            'checkRole' => CheckRole::class,
        ]);

        // Pengecualian CSRF untuk webhook Midtrans
        $middleware->validateCsrfTokens(except: [
            'payment/notification',
            'payment/recurring-notification',
            'payment/pay-account-notification'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Menangani error secara custom jika diperlukan
    })
    ->withSchedule(function (Schedule $schedule) {
        // Jalankan command setiap menit untuk memeriksa pembayaran yang kedaluwarsa
        $schedule->command('payments:update-expired')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/payments-expired.log'));

      // Cek setiap jam
$schedule->call([PaymentController::class, 'checkExpiredMembershipRenewals'])
->hourly();

// Atau setiap 30 menit
$schedule->call([PaymentController::class, 'checkExpiredMembershipRenewals'])
->everyThirtyMinutes();

                 // Jalankan setiap jam
    $schedule->command('sessions:update-completed')->hourly();
    })
    ->create();
