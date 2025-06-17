<?php

namespace App\Mail;

use App\Models\PhotographerBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PhotoGalleryDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(PhotographerBooking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Galeri Foto Sudah Siap - Booking #' . $this->booking->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.photo-gallery-delivered',
            with: [
                'booking' => $this->booking,
                'photographer' => $this->booking->photographer,
                'user' => $this->booking->user,
                'formattedDate' => Carbon::parse($this->booking->start_time)->format('d M Y'),
                'galleryLink' => $this->booking->photo_gallery_link,
                'photographerNotes' => $this->booking->photographer_notes,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
