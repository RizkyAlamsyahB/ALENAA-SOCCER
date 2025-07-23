<?php

// 1. MAIL CLASS - app/Mail/BookingReminder.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $reminderType; // '24hours', '1hour', '30minutes'

    public function __construct($booking, $reminderType = '24hours')
    {
        $this->booking = $booking;
        $this->reminderType = $reminderType;
    }

    public function build()
    {
        $subject = $this->getSubject();

        return $this->subject($subject)
                    ->view('emails.booking-reminder')
                    ->with([
                        'booking' => $this->booking,
                        'reminderType' => $this->reminderType,
                    ]);
    }

    private function getSubject()
    {
        switch ($this->reminderType) {
            case '24hours':
                return 'Reminder: Booking Anda Besok - ' . $this->booking->field->name;
            case '1hour':
                return 'Reminder: Booking Anda 1 Jam Lagi - ' . $this->booking->field->name;
            case '30minutes':
                return 'Reminder: Booking Anda 30 Menit Lagi - ' . $this->booking->field->name;
            default:
                return 'Reminder: Booking Anda - ' . $this->booking->field->name;
        }
    }
}
