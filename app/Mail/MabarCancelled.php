<?php

namespace App\Mail;

use App\Models\User;
use App\Models\OpenMabar;
use App\Models\MabarParticipant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class MabarCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $openMabar;
    public $organizer;
    public $participant;
    public $cancellationReason;
    public $refundInfo;

    /**
     * Create a new message instance.
     *
     * @param OpenMabar $openMabar
     * @param User $organizer
     * @param MabarParticipant $participant
     * @param string|null $cancellationReason
     * @param string|null $refundInfo
     * @return void
     */
    public function __construct(
        OpenMabar $openMabar,
        User $organizer,
        MabarParticipant $participant,
        ?string $cancellationReason = null,
        ?string $refundInfo = null
    ) {
        $this->openMabar = $openMabar;
        $this->organizer = $organizer;
        $this->participant = $participant;
        $this->cancellationReason = $cancellationReason;
        $this->refundInfo = $refundInfo;

        // Log untuk debugging
        Log::info('Constructing MabarCancelled email', [
            'mabar_id' => $openMabar->id,
            'mabar_title' => $openMabar->title,
            'participant_id' => $participant->id,
            'recipient_email' => $participant->user->email,
            'has_cancellation_reason' => !empty($cancellationReason),
            'has_refund_info' => !empty($refundInfo)
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Open Mabar "' . $this->openMabar->title . '" Telah Dibatalkan';

        // Log untuk debugging
        Log::info('Building MabarCancelled email', [
            'subject' => $subject,
            'recipient' => $this->participant->user->email
        ]);

        try {
            return $this->subject($subject)
                     ->view('emails.mabar-cancelled');
        } catch (\Exception $e) {
            Log::error('Error building MabarCancelled email: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
