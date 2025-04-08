<?php

namespace App\Console\Commands;

use App\Http\Controllers\User\MembershipController;
use Illuminate\Console\Command;
use App\Http\Controllers\User\PaymentController;
use Illuminate\Support\Facades\Log;

class CheckExpiredMembershipRenewals extends Command
{
    protected $signature = 'membership:check-expired';
    protected $description = 'Check and deactivate expired membership renewals';

    public function handle()
    {
        $this->info('Memulai proses pengecekan membership kedaluwarsa...');

        try {
            $controller = new MembershipController();
            $result = $controller->checkExpiredMembershipRenewals();

            // Parse response dari JSON
            $data = json_decode($result->getContent(), true);

            $this->info('Proses selesai. ' . $data['message']);

            if (!empty($data['subscription_ids'])) {
                $this->info('Subscription IDs: ' . implode(', ', $data['subscription_ids']));
            }

            Log::info('Command membership:check-expired berhasil dijalankan', [
                'result' => $data
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());

            Log::error('Command membership:check-expired gagal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
