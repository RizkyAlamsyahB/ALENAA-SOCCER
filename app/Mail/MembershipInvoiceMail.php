<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\MembershipSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $payment;

    public function __construct(MembershipSubscription $subscription, Payment $payment)
    {
        $this->subscription = $subscription;
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->subject('Invoice Membership Anda')
            ->view('emails.membership-invoice')
            ->with([
                'subscription' => $this->subscription,
                'payment' => $this->payment,
                'user' => $this->subscription->user,
                'membership' => $this->subscription->membership,
                'nextSession' => $this->subscription->sessions()
                    ->where('status', 'scheduled')
                    ->orderBy('session_date')
                    ->first(),
            ]);
    }
}
