<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Snap Payment Page
     */
    public function createSnapToken(Payment $payment)
    {
        $bookings = $payment->bookings;
        $items = [];

        // Format bookings for Midtrans items
        foreach ($bookings as $booking) {
            $items[] = [
                'id' => $booking->id,
                'price' => $booking->total_price,
                'quantity' => 1,
                'name' => $booking->field->name . ' (' . \Carbon\Carbon::parse($booking->start_time)->format('d M Y H:i') . ' - ' . \Carbon\Carbon::parse($booking->end_time)->format('H:i') . ')',
            ];
        }

        $customer = [
            'first_name' => $payment->user->name,
            'email' => $payment->user->email,
            'phone' => $payment->user->phone ?? '',
        ];

        $transaction_details = [
            'order_id' => $payment->invoice_number,
            'gross_amount' => $payment->amount,
        ];

        $payload = [
            'transaction_details' => $transaction_details,
            'item_details' => $items,
            'customer_details' => $customer,
        ];

        try {
            $snapToken = Snap::getSnapToken($payload);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle notification from Midtrans
     */
    public function handleNotification($notification)
    {
        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;

        $payment = Payment::where('invoice_number', $orderId)->first();

        if (!$payment) {
            return null;
        }

        // Update payment information
        $payment->transaction_id = $notification->transaction_id;
        $payment->transaction_status = $transaction;
        $payment->payment_type = $type;
        $payment->transaction_time = $notification->transaction_time ?? now();

        if (isset($notification->va_numbers) && !empty($notification->va_numbers)) {
            $payment->va_number = $notification->va_numbers[0]->va_number;
            $payment->bank = $notification->va_numbers[0]->bank;
        }

        $payment->fraud_status = $fraud;
        $payment->payment_details = json_encode($notification);

        $payment->save();

        // Update booking status based on transaction status
        if ($transaction == 'settlement') {
            foreach ($payment->bookings as $booking) {
                $booking->status = 'confirmed';
                $booking->save();
            }
        } elseif (in_array($transaction, ['deny', 'cancel', 'expire'])) {
            foreach ($payment->bookings as $booking) {
                $booking->status = 'cancelled';
                $booking->save();
            }
        }

        return $payment;
    }
}
