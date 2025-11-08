<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\MembershipSession;
use Illuminate\Console\Command;

class UpdateCompletedSessions extends Command
{
    protected $signature = 'sessions:update-completed';
    protected $description = 'Update status sesi membership yang sudah lewat menjadi completed';

    public function handle()
    {
        $now = Carbon::now();

        // Cari sesi yang jadwalnya sudah lewat (end_time < now)
        // dan masih berstatus 'scheduled'
        $sessions = MembershipSession::where('status', 'scheduled')
            ->where('end_time', '<', $now)
            ->get();

        $count = 0;
        foreach ($sessions as $session) {
            $session->status = 'completed';
            $session->save();
            $count++;
        }

        $this->info("Berhasil memperbarui $count sesi menjadi completed");

        return Command::SUCCESS;
    }
}
