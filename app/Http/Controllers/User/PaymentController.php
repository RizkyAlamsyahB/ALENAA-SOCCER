<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FieldBooking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Checkout page after booking from cart
     */
    public function checkout(Request $request)
    {
        if ($request->has('bookings')) {
            $bookingIds = is_array($request->bookings) ? $request->bookings : [$request->bookings];
        } else {
            return redirect()->route('user.cart.view')->with('error', 'Tidak ada booking yang dipilih');
        }

        $bookings = FieldBooking::whereIn('id', $bookingIds)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->get();

        if ($bookings->isEmpty()) {
            return redirect()->route('user.cart.view')->with('error', 'Booking tidak ditemukan');
        }

        $totalPrice = $bookings->sum('total_price');
        $orderId = 'FIELD-' . time() . '-' . Str::random(5);

        // Get some customer details for Midtrans
        $customer = Auth::user();

        // Set Midtrans configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone ?? '',
            ],
            'item_details' => []
        ];

        // Add each booking as an item
        foreach ($bookings as $booking) {
            $field = $booking->field;
            $startTime = Carbon::parse($booking->start_time)->format('d M Y H:i');
            $endTime = Carbon::parse($booking->end_time)->format('H:i');

            $params['item_details'][] = [
                'id' => 'BOOK-' . $booking->id,
                'price' => $booking->total_price,
                'quantity' => 1,
                'name' => $field->name . ' (' . $startTime . ' - ' . $endTime . ')',
            ];
        }

        try {
            // Get Snap token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Create payment record
            DB::beginTransaction();
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'amount' => $totalPrice,
                'transaction_status' => 'pending',
            ]);

            // Update bookings with payment ID
            foreach ($bookings as $booking) {
                $booking->payment_id = $payment->id;
                $booking->save();
            }
            DB::commit();

            return view('users.payment.checkout', compact('snapToken', 'payment', 'bookings'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.cart.view')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment notification from Midtrans
     */
    public function notification(Request $request)
    {
        // Set Midtrans configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $status = $notification->transaction_status;
            $type = $notification->payment_type;
            $fraudStatus = $notification->fraud_status;
            $transactionId = $notification->transaction_id;

            // Get the payment
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                return response('Payment not found', 404);
            }

            $payment->payment_type = $type;
            $payment->transaction_id = $transactionId;
            $payment->payment_details = json_encode($notification);

            // Update payment status based on notification
            if ($status == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->transaction_status = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $payment->transaction_status = 'success';
                }
            } else if ($status == 'settlement') {
                $payment->transaction_status = 'success';
                $payment->transaction_time = $notification->settlement_time;
            } else if ($status == 'cancel' || $status == 'deny' || $status == 'expire') {
                $payment->transaction_status = 'failed';
            } else if ($status == 'pending') {
                $payment->transaction_status = 'pending';
            }

            $payment->save();

            // Update booking status
            if ($payment->transaction_status == 'success') {
                foreach ($payment->fieldBookings as $booking) {
                    $booking->status = 'confirmed';
                    $booking->save();
                }
            } else if ($payment->transaction_status == 'failed') {
                foreach ($payment->fieldBookings as $booking) {
                    $booking->status = 'cancelled';
                    $booking->save();
                }
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Handle payment finish redirect from Midtrans
     */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return redirect()->route('user.fields.index')->with('error', 'Pembayaran tidak ditemukan');
        }

        if ($payment->transaction_status == 'success') {
            return view('users.payment.success', compact('payment'));
        } else if ($payment->transaction_status == 'pending') {
            return view('users.payment.unfinish', ['orderId' => $orderId]);
        } else {
            return view('users.payment.error', ['errorMessage' => 'Pembayaran gagal atau dibatalkan']);
        }
    }



    /**
     * Handle payment unfinish redirect from Midtrans
     */
    public function unfinish(Request $request)
    {
        return redirect()->route('users.dashboard')->with('warning', 'Pembayaran belum selesai');
    }

    /**
     * Handle payment error redirect from Midtrans
     */
    public function error(Request $request)
    {
        return redirect()->route('users.dashboard')->with('error', 'Terjadi kesalahan pada pembayaran');
    }

    /**
     * Show payment history
     */
    public function history()
    {
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.payment.history', compact('payments'));
    }

    /**
     * Show payment detail
     */
    public function detail($id)
    {
        $payment = Payment::with('fieldBookings.field')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('users.payment.detail', compact('payment'));
    }

    /**
 * Handle recurring payment notification from Midtrans
 */
public function recurringNotification(Request $request)
{
    // Set Midtrans configuration
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');

    try {
        $notification = new \Midtrans\Notification();

        // Log or process the recurring payment notification
        // This would depend on your specific implementation needs

        return response('OK', 200);
    } catch (\Exception $e) {
        return response($e->getMessage(), 500);
    }
}
/**
 * Handle pay account notification from Midtrans
 */
public function payAccountNotification(Request $request)
{
    // Set Midtrans configuration
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');

    try {
        $notification = new \Midtrans\Notification();

        // Process the pay account notification
        // You might want to log the notification or update relevant records

        return response('OK', 200);
    } catch (\Exception $e) {
        return response($e->getMessage(), 500);
    }
}
}
