<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\MembershipController;

class ScheduleMembershipRenewalInvoices extends Command
{
    protected $signature = 'membership:schedule-renewals';
    protected $description = 'Schedule membership renewal invoices for upcoming second sessions';

    public function handle()
    {
        $this->info('Memulai proses pengiriman invoice perpanjangan membership...');

        try {
            $controller = new MembershipController();
            $result = $controller->scheduleMembershipRenewalInvoices();

            // Parse response dari JSON
            $data = json_decode($result->getContent(), true);

            $this->info('Proses selesai. ' . $data['message']);

            if (!empty($data['bookings'])) {
                // Periksa apakah bookings adalah array atau object
                if (is_object($data['bookings']) && method_exists($data['bookings'], 'toArray')) {
                    $bookingIds = implode(', ', $data['bookings']->toArray());
                } else {
                    $bookingIds = is_array($data['bookings']) ? implode(', ', $data['bookings']) : $data['bookings'];
                }
                $this->info('Booking IDs: ' . $bookingIds);
            }

            Log::info('Command membership:schedule-renewals berhasil dijalankan', [
                'result' => $data
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());

            Log::error('Command membership:schedule-renewals gagal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
