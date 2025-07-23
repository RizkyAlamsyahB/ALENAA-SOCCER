<?php

namespace App\Jobs;

use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\PhotographerBooking;
use App\Models\MembershipSession;
use App\Mail\BookingReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendBookingReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bookingType;
    protected $reminderType;

    public function __construct($bookingType, $reminderType = '24hours')
    {
        $this->bookingType = $bookingType;
        $this->reminderType = $reminderType;
    }

    public function handle()
    {
        try {
            $this->sendFieldBookingReminders();
            $this->sendRentalBookingReminders();
            $this->sendPhotographerBookingReminders();
            $this->sendMembershipSessionReminders();

            Log::info("Booking reminders sent successfully for type: {$this->reminderType}");
        } catch (\Exception $e) {
            Log::error("Error sending booking reminders: " . $e->getMessage());
        }
    }

    private function sendFieldBookingReminders()
    {
        $timeCondition = $this->getTimeCondition();

        $bookings = FieldBooking::with(['field', 'user'])
            ->where('status', 'confirmed')
            ->where('reminder_sent_' . $this->reminderType, false)
            ->whereBetween('start_time', $timeCondition)
            ->get();

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->user->email)
                    ->send(new BookingReminder($booking, $this->reminderType));

                // Update flag reminder
                $booking->update(['reminder_sent_' . $this->reminderType => true]);

                Log::info("Field booking reminder sent to {$booking->user->email} for booking #{$booking->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send field booking reminder for booking #{$booking->id}: " . $e->getMessage());
            }
        }
    }

    private function sendRentalBookingReminders()
    {
        $timeCondition = $this->getTimeCondition();

        $bookings = RentalBooking::with(['rentalItem', 'user'])
            ->where('status', 'confirmed')
            ->where('reminder_sent_' . $this->reminderType, false)
            ->whereBetween('start_time', $timeCondition)
            ->get();

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->user->email)
                    ->send(new BookingReminder($booking, $this->reminderType));

                $booking->update(['reminder_sent_' . $this->reminderType => true]);

                Log::info("Rental booking reminder sent to {$booking->user->email} for booking #{$booking->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send rental booking reminder for booking #{$booking->id}: " . $e->getMessage());
            }
        }
    }

    private function sendPhotographerBookingReminders()
    {
        $timeCondition = $this->getTimeCondition();

        $bookings = PhotographerBooking::with(['photographer', 'user'])
            ->where('status', 'confirmed')
            ->where('reminder_sent_' . $this->reminderType, false)
            ->whereBetween('start_time', $timeCondition)
            ->get();

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->user->email)
                    ->send(new BookingReminder($booking, $this->reminderType));

                $booking->update(['reminder_sent_' . $this->reminderType => true]);

                Log::info("Photographer booking reminder sent to {$booking->user->email} for booking #{$booking->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send photographer booking reminder for booking #{$booking->id}: " . $e->getMessage());
            }
        }
    }

    private function sendMembershipSessionReminders()
    {
        $timeCondition = $this->getTimeCondition();

        $sessions = MembershipSession::with(['subscription.user', 'fieldBooking.field'])
            ->where('status', 'scheduled')
            ->where('reminder_sent_' . $this->reminderType, false)
            ->whereBetween('start_time', $timeCondition)
            ->get();

        foreach ($sessions as $session) {
            try {
                if ($session->fieldBooking) {
                    Mail::to($session->subscription->user->email)
                        ->send(new BookingReminder($session->fieldBooking, $this->reminderType));

                    $session->update(['reminder_sent_' . $this->reminderType => true]);

                    Log::info("Membership session reminder sent to {$session->subscription->user->email} for session #{$session->id}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to send membership session reminder for session #{$session->id}: " . $e->getMessage());
            }
        }
    }

    private function getTimeCondition()
    {
        $now = Carbon::now();

        switch ($this->reminderType) {
            case '24hours':
                return [
                    $now->copy()->addHours(23)->addMinutes(30),
                    $now->copy()->addDay()->addMinutes(30)
                ];
            case '1hour':
                return [
                    $now->copy()->addMinutes(50),
                    $now->copy()->addHour()->addMinutes(10)
                ];
            case '30minutes':
                return [
                    $now->copy()->addMinutes(25),
                    $now->copy()->addMinutes(35)
                ];
            default:
                return [$now, $now->copy()->addDay()];
        }
    }
}

