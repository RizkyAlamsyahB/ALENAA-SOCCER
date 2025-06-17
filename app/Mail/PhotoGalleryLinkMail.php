<?php

namespace App\Mail;

use App\Models\PhotographerBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PhotoGalleryLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $galleryLink;

    public function __construct(PhotographerBooking $booking, $galleryLink)
    {
        $this->booking = $booking;
        $this->galleryLink = $galleryLink;
    }

    public function build()
    {
        return $this->subject('Foto Hasil Sesi Fotografi Anda Sudah Siap!')
                    ->view('emails.photo-gallery-link')
                    ->with([
                        'userName' => $this->booking->user->name,
                        'photographerName' => $this->booking->photographer->name,
                        'bookingDate' => $this->booking->start_time->format('d M Y'),
                        'bookingTime' => $this->booking->start_time->format('H:i') . ' - ' . $this->booking->end_time->format('H:i'),
                        'galleryLink' => $this->galleryLink,
                        'photographerNotes' => $this->booking->photographer_notes,
                    ]);
    }
}
