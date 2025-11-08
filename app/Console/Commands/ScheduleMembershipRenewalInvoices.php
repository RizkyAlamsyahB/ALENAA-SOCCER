<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\User\MembershipController;

class ScheduleMembershipRenewalInvoices extends Command
{
    protected $signature = 'membership:schedule-renewals';
    protected $description = 'Schedule membership renewal invoices for memberships expiring in 3 days';

    public function handle()
    {
        $this->info('Memulai proses pengiriman invoice perpanjangan membership...');

        try {
            $controller = new MembershipController();
            $result = $controller->scheduleMembershipRenewalInvoices();

            // Parse response dari JSON
            $data = json_decode($result->getContent(), true);

            $this->info('Proses selesai. ' . $data['message']);

            if (!empty($data['subscriptions'])) {
                // Periksa apakah subscriptions adalah array atau object
                if (is_object($data['subscriptions']) && method_exists($data['subscriptions'], 'toArray')) {
                    $subscriptionIds = implode(', ', $data['subscriptions']->toArray());
                } else {
                    $subscriptionIds = is_array($data['subscriptions']) ? implode(', ', $data['subscriptions']) : $data['subscriptions'];
                }
                $this->info('Subscription IDs: ' . $subscriptionIds);
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
