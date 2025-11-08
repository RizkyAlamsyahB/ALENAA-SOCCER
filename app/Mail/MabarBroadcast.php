<?php

namespace App\Mail;

use App\Models\OpenMabar;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MabarBroadcast extends Mailable
{
    use Queueable, SerializesModels;

    public $openMabar;
    public $sender;
    public $messageSubject;
    public $messageContent;
    public $eventDetails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OpenMabar $openMabar, User $sender, $subject, $message, $eventDetails)
    {
        $this->openMabar = $openMabar;
        $this->sender = $sender;
        $this->messageSubject = $subject;
        $this->messageContent = $message;
        $this->eventDetails = $eventDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("[Alena Soccer - Open Mabar] " . $this->messageSubject)
                    ->view('emails.mabar.broadcast');
    }
}
