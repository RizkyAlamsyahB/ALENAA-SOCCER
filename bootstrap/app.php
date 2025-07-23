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
        // Daftarkan custom commands yang sudah ada
        \App\Console\Commands\ScheduleMembershipRenewalInvoices::class,
        \App\Console\Commands\CheckExpiredMembershipRenewals::class,
        \App\Console\Commands\UpdateCompletedSessions::class,

        // TAMBAHAN BARU: Command untuk booking reminders
        \App\Console\Commands\SendBookingReminders::class,
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
        // ============================================
        // EXISTING SCHEDULES (yang sudah ada)
        // ============================================

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

        // ============================================
        // NEW BOOKING REMINDER SCHEDULES
        // ============================================

        // Kirim reminder 24 jam sebelum booking (cek setiap jam)
        $schedule->command('bookings:send-reminders 24hours')
                 ->hourly()
                 ->between('8:00', '22:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/booking-reminders-24h.log'))
                 ->emailOutputOnFailure('admin@yourapp.com'); // Opsional: kirim email jika gagal

        // Kirim reminder 1 jam sebelum booking (cek setiap 15 menit)
        $schedule->command('bookings:send-reminders 1hour')
                 ->everyFifteenMinutes()
                 ->between('7:00', '23:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/booking-reminders-1h.log'))
                 ->emailOutputOnFailure('admin@yourapp.com');

        // Kirim reminder 30 menit sebelum booking (cek setiap 5 menit)
        $schedule->command('bookings:send-reminders 30minutes')
                 ->everyFiveMinutes()
                 ->between('7:30', '23:30')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/booking-reminders-30m.log'))
                 ->emailOutputOnFailure('admin@yourapp.com');

        // TAMBAHAN: Command untuk cleanup reminder flags yang sudah kadaluarsa (opsional)
        $schedule->command('bookings:cleanup-reminder-flags')
                 ->daily()
                 ->at('02:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/booking-reminder-cleanup.log'));

        // TAMBAHAN: Generate laporan reminder harian untuk admin (opsional)
        $schedule->command('bookings:reminder-report')
                 ->dailyAt('23:55')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/booking-reminder-reports.log'));
    })
    ->create();
