<?php

namespace App\Mail;

use App\Models\User;
use App\Models\PhotographerBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PhotographerBookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $photographer;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param PhotographerBooking $booking
     * @param User $photographerUser
     * @param User $user
     * @return void
     */
    public function __construct(PhotographerBooking $booking, User $photographerUser, User $user)
    {
        $this->booking = $booking;
        $this->photographer = $photographerUser;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Notifikasi Jadwal Foto Baru')
                    ->view('emails.photographer_booking_notification');
    }
}
