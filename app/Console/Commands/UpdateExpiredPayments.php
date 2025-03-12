<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired payments status to failed and related bookings to cancelled';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Waktu sekarang: ' . now()->format('Y-m-d H:i:s'));
        $this->info('Memulai proses pembaruan pembayaran kedaluwarsa...');

        $expiredPayments = Payment::where('transaction_status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        $this->info('Ditemukan ' . $expiredPayments->count() . ' pembayaran yang sudah kedaluwarsa.');

        $successCount = 0;
        $errorCount = 0;

        foreach ($expiredPayments as $payment) {
            $this->info('Memproses pembayaran dengan ID: ' . $payment->id . ' (Order ID: ' . $payment->order_id . ')');

            DB::beginTransaction();
            try {
                // Update payment status
                $payment->transaction_status = 'failed';
                $payment->save();

                $this->info('Status pembayaran diubah menjadi failed');

                // Update field bookings
                $fieldBookingCount = 0;
                if (method_exists($payment, 'fieldBookings') && $payment->fieldBookings) {
                    foreach ($payment->fieldBookings as $booking) {
                        $booking->status = 'cancelled';
                        $booking->save();
                        $fieldBookingCount++;
                    }
                    $this->info($fieldBookingCount . ' field booking diubah menjadi cancelled');
                }

                // Update rental bookings
                $rentalBookingCount = 0;
                if (method_exists($payment, 'rentalBookings') && $payment->rentalBookings) {
                    foreach ($payment->rentalBookings as $booking) {
                        $booking->status = 'cancelled';
                        $booking->save();
                        $rentalBookingCount++;
                    }
                    $this->info($rentalBookingCount . ' rental booking diubah menjadi cancelled');
                }

                // Update membership subscriptions
                $membershipCount = 0;
                if (method_exists($payment, 'membershipSubscriptions') && $payment->membershipSubscriptions) {
                    foreach ($payment->membershipSubscriptions as $subscription) {
                        $subscription->status = 'cancelled';
                        $subscription->save();
                        $membershipCount++;
                    }
                    $this->info($membershipCount . ' membership subscription diubah menjadi cancelled');
                }

                // Update photographer bookings
                $photographerBookingCount = 0;
                if (method_exists($payment, 'photographerBookings') && $payment->photographerBookings) {
                    foreach ($payment->photographerBookings as $booking) {
                        $booking->status = 'cancelled';
                        $booking->save();
                        $photographerBookingCount++;
                    }
                    $this->info($photographerBookingCount . ' photographer booking diubah menjadi cancelled');
                }

                DB::commit();
                $successCount++;
                Log::info('Payment #' . $payment->id . ' (Order: ' . $payment->order_id . ') status updated to failed due to expiration');

            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;
                $this->error('Error saat memproses pembayaran #' . $payment->id . ': ' . $e->getMessage());
                Log::error('Error updating expired payment #' . $payment->id . ': ' . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }

        $this->info('Proses selesai. Berhasil: ' . $successCount . ', Error: ' . $errorCount);

        return Command::SUCCESS;
    }
}
