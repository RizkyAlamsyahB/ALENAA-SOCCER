<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\MembershipSession;
use App\Models\MembershipSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipInvoiceMail;
use App\Models\Payment;

class SendMembershipInvoices extends Command
{
    protected $signature = 'membership:send-invoices';
    protected $description = 'Send invoices for membership subscriptions on second session';

    public function handle()
    {
        $today = Carbon::today();

        // Ambil semua subscription yang aktif
        $subscriptions = MembershipSubscription::where('status', 'active')
            ->where('invoice_sent', false)
            ->get();

        foreach ($subscriptions as $subscription) {
            // Hitung jumlah sesi yang telah berlalu
            $completedSessions = $subscription->sessions()
                ->where('status', 'completed')
                ->count();

            // Dapatkan sesi berikutnya yang dijadwalkan
            $nextSession = $subscription->sessions()
                ->where('status', 'scheduled')
                ->orderBy('session_date')
                ->first();

            // Jika sudah 1 sesi selesai dan akan ke sesi ke-2, kirim invoice
            if ($completedSessions == 1 && $nextSession) {
                // Buat payment record untuk invoice
                $payment = Payment::create([
                    'order_id' => 'MEMBER-INV-' . now()->format('Ymd-His') . '-' . mt_rand(1000, 9999),
                    'user_id' => $subscription->user_id,
                    'amount' => $subscription->price,
                    'original_amount' => $subscription->price,
                    'transaction_status' => 'pending',
                    'expires_at' => Carbon::parse($nextSession->session_date)->addDays(7),
                ]);

                // Update subscription dengan invoice status
                $subscription->update([
                    'invoice_sent' => true,
                    'payment_id' => $payment->id,
                ]);

                // Kirim email invoice
                try {
                    Mail::to($subscription->user->email)
                        ->send(new MembershipInvoiceMail($subscription, $payment));

                    $this->info("Invoice sent to {$subscription->user->email} for subscription #{$subscription->id}");
                } catch (\Exception $e) {
                    $this->error("Failed to send invoice email for subscription #{$subscription->id}: {$e->getMessage()}");
                }
            }
        }

        return 0;
    }
}
