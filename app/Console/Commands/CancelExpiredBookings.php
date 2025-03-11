<?php

namespace App\Console\Commands;

use App\Models\FieldBooking;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';
    protected $description = 'Cancel field bookings that have expired payment timeout';

    public function handle()
    {
        $timeoutMinutes = 15; // Batas waktu 30 menit
        $timeoutTime = now()->subMinutes($timeoutMinutes);

        // Cari booking yang masih pending dan sudah melewati batas waktu
        $timedOutBookings = FieldBooking::where('status', 'pending')
            ->where('created_at', '<', $timeoutTime)
            ->get();

        $count = 0;
        foreach ($timedOutBookings as $booking) {
            $booking->status = 'cancelled';
            $booking->save();
            $count++;

            // Update payment juga jika ada
            if ($booking->payment_id) {
                $payment = Payment::find($booking->payment_id);
                if ($payment && $payment->transaction_status == 'pending') {
                    $payment->transaction_status = 'expired';
                    $payment->save();
                }
            }

            Log::info('Booking #' . $booking->id . ' automatically cancelled due to timeout');
        }

        $this->info("Cancelled {$count} expired bookings");
    }
}
