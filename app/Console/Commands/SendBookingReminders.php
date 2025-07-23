<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendBookingReminder;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders {type=all}';
    protected $description = 'Send booking reminders to users';

    public function handle()
    {
        $type = $this->argument('type');

        if ($type === 'all' || $type === '24hours') {
            SendBookingReminder::dispatch('all', '24hours');
            $this->info('24-hour reminders dispatched');
        }

        if ($type === 'all' || $type === '1hour') {
            SendBookingReminder::dispatch('all', '1hour');
            $this->info('1-hour reminders dispatched');
        }

        if ($type === 'all' || $type === '30minutes') {
            SendBookingReminder::dispatch('all', '30minutes');
            $this->info('30-minute reminders dispatched');
        }

        return 0;
    }
}

