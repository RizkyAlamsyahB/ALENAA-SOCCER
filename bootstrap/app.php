<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php', // Pastikan file ini ada
        health: '/up',
    )
    ->withCommands([
        // Daftarkan custom commands
        \App\Console\Commands\ScheduleMembershipRenewalInvoices::class,
        \App\Console\Commands\CheckExpiredMembershipRenewals::class,
        \App\Console\Commands\UpdateCompletedSessions::class,
    ])
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

        // Schedule invoice perpanjangan membership setiap hari
        $schedule->command('membership:schedule-renewals')
                 ->daily()
                 ->at('09:12')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/membership-renewals.log'));

        // Cek membership yang kedaluwarsa setiap jam
        $schedule->command('membership:check-expired')
                 ->hourly()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/membership-expired.log'));

        // Update status sesi yang sudah selesai setiap jam
        $schedule->command('sessions:update-completed')
                 ->hourly()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/sessions-completed.log'));
    })
    ->create();
