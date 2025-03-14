<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Review;
use App\Models\Payment;
use App\Models\CartItem;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Str;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use App\Models\RentalBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Midtrans\Exceptions\MidtransApiException;

class PaymentController extends Controller
{
    /**
     * Set konfigurasi Midtrans
     *
     * @param bool $isFull Jika true, akan mengatur semua konfigurasi
     * @return void
     */
    private function setupMidtransConfig($isFull = true)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);

        if ($isFull) {
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized', true);
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds', true);
        }
    }

    /**
     * Siapkan detail item untuk Midtrans dari berbagai jenis booking
     *
     * @param array $fieldBookings Array FieldBooking
     * @param array $rentalBookings Array RentalBooking
     * @param array $membershipSubscriptions Array MembershipSubscription (opsional)
     * @param array $photographerBookings Array PhotographerBooking (opsional)
     * @return array Item details untuk Midtrans
     */
    private function prepareItemDetails($fieldBookings = [], $rentalBookings = [], $membershipSubscriptions = [], $photographerBookings = [])
    {
        $itemDetails = [];

        // Add field bookings to item details
        foreach ($fieldBookings as $booking) {
            $field = $booking->field;
            $startTime = Carbon::parse($booking->start_time)->format('d M Y H:i');
            $endTime = Carbon::parse($booking->end_time)->format('H:i');

            $itemDetails[] = [
                'id' => 'FIELD-' . $booking->id,
                'price' => $booking->total_price,
                'quantity' => 1,
                'name' => $field->name . ' (' . $startTime . ' - ' . $endTime . ')',
            ];
        }

        // Add rental bookings to item details
        foreach ($rentalBookings as $booking) {
            $rentalItem = $booking->rentalItem;
            $startTime = Carbon::parse($booking->start_time)->format('d M Y H:i');
            $endTime = Carbon::parse($booking->end_time)->format('H:i');

            $itemDetails[] = [
                'id' => 'RENTAL-' . $booking->id,
                'price' => $booking->total_price,
                'quantity' => 1,
                'name' => $rentalItem->name . ' (Jumlah: ' . $booking->quantity . ', ' . $startTime . ' - ' . $endTime . ')',
            ];
        }

        // Add membership subscriptions to item details
        foreach ($membershipSubscriptions as $subscription) {
            if (!isset($subscription->membership)) {
                continue;
            }

            $membership = $subscription->membership;

            $itemDetails[] = [
                'id' => 'MEMBER-' . $subscription->id,
                'price' => $subscription->price,
                'quantity' => 1,
                'name' => $membership->name . ' (Durasi: ' . $membership->duration . ' bulan)',
            ];
        }

        // Add photographer bookings to item details
        foreach ($photographerBookings as $booking) {
            if (!isset($booking->photographer)) {
                continue;
            }

            $photographer = $booking->photographer;
            $startTime = Carbon::parse($booking->start_time)->format('d M Y H:i');
            $endTime = Carbon::parse($booking->end_time)->format('H:i');

            $itemDetails[] = [
                'id' => 'PHOTO-' . $booking->id,
                'price' => $booking->price,
                'quantity' => 1,
                'name' => $photographer->name . ' (' . $startTime . ' - ' . $endTime . ')',
            ];
        }

        return $itemDetails;
    }

    /**
     * Batalkan semua booking terkait dengan pembayaran
     *
     * @param Payment $payment
     * @return void
     */
    private function cancelAllBookings(Payment $payment)
    {
        // Update field bookings
        if (method_exists($payment, 'fieldBookings') && $payment->fieldBookings) {
            foreach ($payment->fieldBookings as $booking) {
                $booking->status = 'cancelled';
                $booking->save();
            }
        }

        // Update rental bookings
        if (method_exists($payment, 'rentalBookings') && $payment->rentalBookings) {
            foreach ($payment->rentalBookings as $booking) {
                $booking->status = 'cancelled';
                $booking->save();
            }
        }

        // Update membership subscriptions
        if (method_exists($payment, 'membershipSubscriptions') && $payment->membershipSubscriptions) {
            foreach ($payment->membershipSubscriptions as $subscription) {
                $subscription->status = 'cancelled';
                $subscription->save();
            }
        }

        // Update photographer bookings
        if (method_exists($payment, 'photographerBookings') && $payment->photographerBookings) {
            foreach ($payment->photographerBookings as $booking) {
                $booking->status = 'cancelled';
                $booking->save();
            }
        }

        Log::info('All bookings for Payment #' . $payment->id . ' (Order: ' . $payment->order_id . ') cancelled due to payment expiration');
    }

    /**
     * Cek dan update pembayaran yang sudah kedaluwarsa
     *
     * @param Payment $payment
     * @return Payment
     */
    private function checkExpiredPayment($payment)
    {
        // Jika pembayaran masih pending dan sudah kedaluwarsa, update statusnya
        if ($payment->transaction_status === 'pending' && $payment->expires_at && Carbon::parse($payment->expires_at)->isPast()) {
            DB::beginTransaction();
            try {
                // Update status pembayaran
                $payment->transaction_status = 'failed';
                $payment->save();

                // Gunakan fungsi helper untuk membatalkan semua booking
                $this->cancelAllBookings($payment);

                DB::commit();
                Log::info('Payment #' . $payment->id . ' (Order: ' . $payment->order_id . ') status updated to failed due to expiration on page access');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating expired payment #' . $payment->id . ' on page access: ' . $e->getMessage());
            }

            // Reload payment dengan data terbaru
            $payment = $payment->fresh();
        }

        return $payment;
    }

    /**
     * Checkout page after booking from cart
     */
    public function checkout(Request $request)
    {
        // Menerima berbagai jenis item IDs
        $fieldBookingIds = $request->field_bookings ?? [];
        $rentalBookingIds = $request->rental_bookings ?? [];
        $membershipIds = $request->memberships ?? [];
        $photographerBookingIds = $request->photographer_bookings ?? [];

        if (empty($fieldBookingIds) && empty($rentalBookingIds) && empty($membershipIds) && empty($photographerBookingIds)) {
            return redirect()->route('user.cart.view')->with('error', 'Tidak ada item yang dipilih untuk checkout');
        }

        $totalPrice = 0;
        $orderId = 'ORDER-' . time() . '-' . Str::random(5);
        $customer = Auth::user();

        DB::beginTransaction();
        try {
            // Proses field bookings
            $fieldBookings = FieldBooking::whereIn('id', $fieldBookingIds)->where('user_id', Auth::id())->where('status', 'pending')->lockForUpdate()->get();

            if (count($fieldBookingIds) > 0 && $fieldBookings->isEmpty()) {
                DB::rollBack();
                return redirect()->route('user.cart.view')->with('error', 'Booking lapangan tidak ditemukan atau status telah berubah');
            }

            // Proses rental bookings
            $rentalBookings = RentalBooking::whereIn('id', $rentalBookingIds)->where('user_id', Auth::id())->where('status', 'pending')->lockForUpdate()->get();

            if (count($rentalBookingIds) > 0 && $rentalBookings->isEmpty()) {
                DB::rollBack();
                return redirect()->route('user.cart.view')->with('error', 'Booking penyewaan tidak ditemukan atau status telah berubah');
            }

            // Proses membership subscriptions
            $membershipSubscriptions = MembershipSubscription::whereIn('id', $membershipIds)->where('user_id', Auth::id())->where('status', 'pending')->lockForUpdate()->get();

            if (count($membershipIds) > 0 && $membershipSubscriptions->isEmpty()) {
                DB::rollBack();
                return redirect()->route('user.cart.view')->with('error', 'Membership tidak ditemukan atau status telah berubah');
            }

            // Proses photographer bookings
            $photographerBookings = PhotographerBooking::whereIn('id', $photographerBookingIds)->where('user_id', Auth::id())->where('status', 'pending')->lockForUpdate()->get();

            if (count($photographerBookingIds) > 0 && $photographerBookings->isEmpty()) {
                DB::rollBack();
                return redirect()->route('user.cart.view')->with('error', 'Booking fotografer tidak ditemukan atau status telah berubah');
            }

            // Kalkulasi total harga dari semua jenis item
            $totalPrice = $fieldBookings->sum('total_price') + $rentalBookings->sum('total_price') + $membershipSubscriptions->sum('price') + $photographerBookings->sum('price');

            // Periksa ketersediaan field bookings
            foreach ($fieldBookings as $booking) {
                $conflictingBooking = FieldBooking::where('field_id', $booking->field_id)
                    ->where('id', '!=', $booking->id)
                    ->where('status', 'confirmed')
                    ->where(function ($query) use ($booking) {
                        // Cek overlap waktu
                        $query
                            ->where(function ($q) use ($booking) {
                                $q->where('start_time', '<=', $booking->start_time)->where('end_time', '>', $booking->start_time);
                            })
                            ->orWhere(function ($q) use ($booking) {
                                $q->where('start_time', '<', $booking->end_time)->where('end_time', '>=', $booking->end_time);
                            })
                            ->orWhere(function ($q) use ($booking) {
                                $q->where('start_time', '>=', $booking->start_time)->where('end_time', '<=', $booking->end_time);
                            });
                    })
                    ->lockForUpdate()
                    ->first();

                if ($conflictingBooking) {
                    DB::rollBack();
                    return redirect()
                        ->route('user.cart.view')
                        ->with('error', 'Maaf, slot untuk ' . $booking->field->name . ' sudah dibooking oleh pengguna lain');
                }
            }

            // Periksa ketersediaan rental bookings
            foreach ($rentalBookings as $booking) {
                // Hitung jumlah yang sudah dipesan dalam rentang waktu yang sama
                $bookedQuantity = RentalBooking::where('rental_item_id', $booking->rental_item_id)
                    ->where('id', '!=', $booking->id)
                    ->whereNotIn('status', ['cancelled', 'pending'])
                    ->where(function ($query) use ($booking) {
                        // Logika yang sama seperti di CartController
                        $query
                            ->where(function ($q) use ($booking) {
                                $q->where('start_time', '>=', $booking->start_time)->where('start_time', '<', $booking->end_time);
                            })
                            ->orWhere(function ($q) use ($booking) {
                                $q->where('end_time', '>', $booking->start_time)->where('end_time', '<=', $booking->end_time);
                            })
                            ->orWhere(function ($q) use ($booking) {
                                $q->where('start_time', '<=', $booking->start_time)->where('end_time', '>=', $booking->end_time);
                            });
                    })
                    ->sum('quantity');

                $rentalItem = $booking->rentalItem;
                $availableQuantity = $rentalItem->stock_total - $bookedQuantity;

                if ($booking->quantity > $availableQuantity) {
                    DB::rollBack();
                    return redirect()
                        ->route('user.cart.view')
                        ->with('error', 'Maaf, stok untuk ' . $rentalItem->name . ' tidak mencukupi. Tersedia: ' . $availableQuantity);
                }
            }

            // Periksa ketersediaan photographer bookings
            foreach ($photographerBookings as $booking) {
                $conflictingBooking = PhotographerBooking::where('photographer_id', $booking->photographer_id)
                    ->where('id', '!=', $booking->id)
                    ->where('status', 'confirmed')
                    ->where(function ($query) use ($booking) {
                        // Cek overlap waktu
                        $query
                            ->where(function ($q) use ($booking) {
                                $q->where('start_time', '<=', $booking->start_time)->where('end_time', '>', $booking->start_time);
                            })
                            ->orWhere(function ($q) use ($booking) {
                                $q->where('start_time', '<', $booking->end_time)->where('end_time', '>=', $booking->end_time);
                            })
                            ->orWhere(function ($q) use ($booking) {
                                $q->where('start_time', '>=', $booking->start_time)->where('end_time', '<=', $booking->end_time);
                            });
                    })
                    ->lockForUpdate()
                    ->first();

                if ($conflictingBooking) {
                    DB::rollBack();
                    return redirect()
                        ->route('user.cart.view')
                        ->with('error', 'Maaf, slot untuk fotografer ' . $booking->photographer->name . ' sudah dibooking oleh pengguna lain');
                }
            }

            // Set Midtrans configuration
            $this->setupMidtransConfig();

            // Siapkan item details untuk Midtrans
            $itemDetails = $this->prepareItemDetails($fieldBookings, $rentalBookings, $membershipSubscriptions, $photographerBookings);

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
                'item_details' => $itemDetails,
            ];

            // Get Snap token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Create payment record (tanpa menambahkan expires_at)
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'amount' => $totalPrice,
                'transaction_status' => 'pending',
            ]);

            // Update all bookings with payment ID
            foreach ($fieldBookings as $booking) {
                $booking->payment_id = $payment->id;
                $booking->save();
            }

            foreach ($rentalBookings as $booking) {
                $booking->payment_id = $payment->id;
                $booking->save();
            }

            foreach ($membershipSubscriptions as $subscription) {
                $subscription->payment_id = $payment->id;
                $subscription->save();
            }

            foreach ($photographerBookings as $booking) {
                $booking->payment_id = $payment->id;
                $booking->save();
            }

            // Gabungkan semua booking untuk view
            $allBookings = [
                'field_bookings' => $fieldBookings,
                'rental_bookings' => $rentalBookings,
                'membership_subscriptions' => $membershipSubscriptions,
                'photographer_bookings' => $photographerBookings,
            ];

            DB::commit();

            // Cek apakah pembayaran sudah kadaluwarsa
            $payment = $this->checkExpiredPayment($payment);

            // Jika statusnya telah berubah menjadi failed akibat kadaluwarsa
            if ($payment->transaction_status === 'failed') {
                return redirect()
                    ->route('user.payment.detail', ['id' => $payment->id])
                    ->with('error', 'Pembayaran telah kadaluwarsa. Silakan membuat pesanan baru.');
            }

            return view('users.payment.checkout', compact('snapToken', 'payment', 'allBookings'));
        } catch (\PDOException $e) {
            DB::rollBack();
            Log::error('Checkout PDO Error: ' . $e->getMessage());

            // Tangani kesalahan database spesifik
            if (strpos($e->getMessage(), 'deadlock') !== false || strpos($e->getMessage(), 'lock') !== false) {
                return redirect()->route('user.cart.view')->with('error', 'Sistem sedang sibuk. Silakan coba lagi dalam beberapa saat');
            }

            return redirect()
                ->route('user.cart.view')
                ->with('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        } catch (MidtransApiException $e) {
            DB::rollBack();
            Log::error('Midtrans API Error: ' . $e->getMessage());

            return redirect()
                ->route('user.cart.view')
                ->with('error', 'Terjadi kesalahan pada payment gateway: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()
                ->route('user.cart.view')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment notification from Midtrans
     */
    public function notification(Request $request)
    {
        // Set Midtrans configuration
        $this->setupMidtransConfig(false);

        try {
            // Simpan data mentah dulu
            $rawData = $request->all();
            Log::info('Notification raw data:', $rawData);

            // Ambil order_id dari data mentah
            $orderId = $rawData['order_id'] ?? null;
            if (!$orderId) {
                Log::warning('No order_id found in notification data');
                return response('No order_id found', 400);
            }

            // Cek apakah ini order_id retry (memiliki format originalId-RETRY-timestamp)
            $isRetry = strpos($orderId, '-RETRY-') !== false;
            $payment = null;

            if ($isRetry) {
                // Ambil original order_id jika ini adalah retry payment
                $originalOrderId = explode('-RETRY-', $orderId)[0];
                $payment = Payment::where('order_id', $originalOrderId)->first();

                if ($payment) {
                    Log::info('Retry payment notification for original order_id: ' . $originalOrderId);
                } else {
                    Log::error('Payment not found for retry order_id: ' . $orderId);
                    return response('Payment not found', 404);
                }
            } else {
                // Jika bukan retry, ambil payment berdasarkan order_id asli
                $payment = Payment::where('order_id', $orderId)->first();

                if (!$payment) {
                    Log::error('Payment not found for order_id: ' . $orderId);
                    return response('Payment not found', 404);
                }
            }

            // Simpan data notifikasi mentah di payment_details
            $payment->payment_details = json_encode($rawData);

            // Update payment dengan data dari notifikasi
            $status = $rawData['transaction_status'] ?? '';
            $type = $rawData['payment_type'] ?? '';
            $fraudStatus = $rawData['fraud_status'] ?? '';
            $transactionId = $rawData['transaction_id'] ?? '';

            $payment->payment_type = $type;
            $payment->transaction_id = $transactionId;

            // Update payment status based on notification
            if ($status == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->transaction_status = 'challenge';
                } elseif ($fraudStatus == 'accept') {
                    $payment->transaction_status = 'success';
                }
            } elseif ($status == 'settlement') {
                $payment->transaction_status = 'success';
                $payment->transaction_time = $rawData['settlement_time'] ?? now();
            } elseif ($status == 'cancel' || $status == 'deny' || $status == 'expire') {
                $payment->transaction_status = 'failed';
            } elseif ($status == 'pending') {
                $payment->transaction_status = 'pending';
            }

            $payment->save();
            Log::info('Payment status updated to: ' . $payment->transaction_status);

            // Update field bookings
            if ($payment->fieldBookings && $payment->fieldBookings->count() > 0) {
                foreach ($payment->fieldBookings as $booking) {
                    $booking->status = 'confirmed';
                    $booking->save();
                    Log::info('Field Booking #' . $booking->id . ' status updated to: confirmed');
                }
            }

            // Update rental bookings
            if ($payment->rentalBookings && $payment->rentalBookings->count() > 0) {
                foreach ($payment->rentalBookings as $booking) {
                    $booking->status = 'confirmed';
                    $booking->save();
                    Log::info('Rental Booking #' . $booking->id . ' status updated to: confirmed');
                }
            }

            // Update membership subscriptions - only if the relationship exists
            if (method_exists($payment, 'membershipSubscriptions') && $payment->membershipSubscriptions && $payment->membershipSubscriptions->count() > 0) {
                foreach ($payment->membershipSubscriptions as $subscription) {
                    $subscription->status = 'active';
                    $subscription->save();
                    Log::info('Membership #' . $subscription->id . ' status updated to: active');
                }
            }

            // Update photographer bookings - only if the relationship exists
            if (method_exists($payment, 'photographerBookings') && $payment->photographerBookings && $payment->photographerBookings->count() > 0) {
                foreach ($payment->photographerBookings as $booking) {
                    $booking->status = 'confirmed';
                    $booking->save();
                    Log::info('Photographer Booking #' . $booking->id . ' status updated to: confirmed');
                }
            }
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Notification error: ' . $e->getMessage());
            Log::error('Notification error stack trace: ' . $e->getTraceAsString());
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Handle payment finish redirect from Midtrans
     */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $paymentId = $request->payment_id;

        // Jika ada payment_id, maka ini adalah lanjutan pembayaran
        if ($paymentId) {
            $payment = Payment::findOrFail($paymentId);
        } else {
            $payment = Payment::where('order_id', $orderId)->first();
        }

        if (!$payment) {
            return redirect()->route('user.fields.index')->with('error', 'Pembayaran tidak ditemukan');
        }

        // Periksa jika pembayaran sudah kadaluarsa
        $payment = $this->checkExpiredPayment($payment);

        if ($payment->transaction_status == 'success') {
            return view('users.payment.success', compact('payment'));
        } elseif ($payment->transaction_status == 'pending') {
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
        $orderId = $request->order_id;
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return redirect()->route('users.dashboard')->with('warning', 'Pembayaran belum selesai');
        }

        // Periksa jika pembayaran sudah kadaluarsa
        $payment = $this->checkExpiredPayment($payment);

        // Jika statusnya berubah menjadi failed, tampilkan pesan kadaluarsa
        if ($payment->transaction_status === 'failed') {
            return redirect()
                ->route('user.payment.detail', ['id' => $payment->id])
                ->with('error', 'Pembayaran telah kadaluwarsa. Silakan membuat pesanan baru.');
        }

        // Redirect ke detail pembayaran dengan pesan untuk melanjutkan pembayaran
        return redirect()
            ->route('user.payment.detail', ['id' => $payment->id])
            ->with('warning', 'Pembayaran belum selesai. Anda dapat melanjutkan pembayaran dari halaman ini.');
    }

    /**
     * Handle payment error redirect from Midtrans
     */
    public function error(Request $request)
    {
        $orderId = $request->order_id;
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return redirect()->route('users.dashboard')->with('error', 'Terjadi kesalahan pada pembayaran');
        }

        // Periksa jika pembayaran sudah kadaluarsa
        $payment = $this->checkExpiredPayment($payment);

        // Jika statusnya berubah menjadi failed karena kadaluarsa, tampilkan pesan kadaluarsa
        if ($payment->transaction_status === 'failed') {
            return redirect()
                ->route('user.payment.detail', ['id' => $payment->id])
                ->with('error', 'Pembayaran telah kadaluwarsa. Silakan membuat pesanan baru.');
        }

        // Redirect ke detail pembayaran
        return redirect()
            ->route('user.payment.detail', ['id' => $payment->id])
            ->with('error', 'Terjadi kesalahan pada pembayaran. Silakan coba lagi.');
    }

    /**
     * Melanjutkan pembayaran yang belum selesai
     */
    public function continuePayment($id)
    {
        $payment = Payment::with([
            'fieldBookings.field',
            'rentalBookings.rentalItem',
            // 'membershipSubscriptions.membership',  // dikomentari
            // 'photographerBookings.photographer'    // dikomentari
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Cek jika pembayaran sudah kedaluwarsa
        $payment = $this->checkExpiredPayment($payment);

        // Jika statusnya berubah menjadi failed karena kadaluarsa
        if ($payment->transaction_status === 'failed') {
            return redirect()
                ->route('user.payment.detail', ['id' => $payment->id])
                ->with('error', 'Pembayaran ini telah kedaluwarsa. Silakan membuat pesanan baru.');
        }

        // Hanya bisa melanjutkan pembayaran dengan status pending
        if ($payment->transaction_status !== 'pending') {
            return redirect()
                ->route('user.payment.detail', ['id' => $payment->id])
                ->with('error', 'Tidak dapat melanjutkan pembayaran dengan status saat ini.');
        }

        try {
            // Rekonfigurasi Midtrans
            $this->setupMidtransConfig();

            // Siapkan item details untuk Midtrans
            $itemDetails = $this->prepareItemDetails($payment->fieldBookings, $payment->rentalBookings, method_exists($payment, 'membershipSubscriptions') ? $payment->membershipSubscriptions : [], method_exists($payment, 'photographerBookings') ? $payment->photographerBookings : []);

            // Buat koleksi cart_items yang sesuai format yang diharapkan view
            $allBookings = [
                'field_bookings' => $payment->fieldBookings,
                'rental_bookings' => $payment->rentalBookings,
                'membership_subscriptions' => method_exists($payment, 'membershipSubscriptions') ? $payment->membershipSubscriptions : [],
                'photographer_bookings' => method_exists($payment, 'photographerBookings') ? $payment->photographerBookings : [],
            ];

            // Buat order_id baru dengan menambahkan suffix untuk menghindari duplicate
            $originalOrderId = $payment->order_id;
            $newOrderId = $originalOrderId . '-RETRY-' . time();

            // Buat parameter transaksi dengan order_id baru
            $params = [
                'transaction_details' => [
                    'order_id' => $newOrderId, // Gunakan order_id baru!
                    'gross_amount' => $payment->amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone ?? '',
                ],
                'item_details' => $itemDetails,
            ];

            // Dapatkan Snap Token baru
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Kirim ke view dengan format data yang sesuai, termasuk expires_at
            return view('users.payment.checkout', [
                'snap_token' => $snapToken,
                'order_id' => $newOrderId, // Gunakan order_id baru di view
                'original_order_id' => $originalOrderId, // Simpan original order_id
                'total_price' => $payment->amount,
                'allBookings' => $allBookings,
                'is_continue_payment' => true,
                'payment_id' => $payment->id, // Sertakan payment_id untuk callback
                'expires_at' => $payment->expires_at, // Kirimkan expires_at yang ada tanpa diubah
            ]);
        } catch (\Exception $e) {
            Log::error('Continue Payment Error: ' . $e->getMessage());
            return redirect()
                ->route('user.payment.detail', ['id' => $payment->id])
                ->with('error', 'Gagal melanjutkan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Show payment history
     */
    public function history()
    {
        $payments = Payment::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(3);

        // Cek pembayaran yang kedaluwarsa secara real-time
        foreach ($payments as $key => $payment) {
            $payments[$key] = $this->checkExpiredPayment($payment);
        }

        return view('users.payment.history', compact('payments'));
    }

/**
 * Show payment detail
 */
public function detail($id)
{
    $payment = Payment::with(['fieldBookings.field', 'rentalBookings.rentalItem'])
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    // Cek apakah pembayaran sudah kedaluwarsa tapi belum diupdate statusnya
    $payment = $this->checkExpiredPayment($payment);

    // Cek apakah user sudah memberikan review untuk item di payment ini
    $reviewedItems = [];
    if ($payment->transaction_status === 'success') {
        // Cek review untuk field bookings
        foreach ($payment->fieldBookings as $booking) {
            $review = Review::where('user_id', Auth::id())
                          ->where('item_id', $booking->field_id)
                          ->where('item_type', 'App\\Models\\Field')
                          ->where('payment_id', $payment->id)
                          ->first();

            $reviewedItems['field_' . $booking->field_id] = $review ? true : false;
        }

        // Cek review untuk rental bookings
        foreach ($payment->rentalBookings as $booking) {
            $review = Review::where('user_id', Auth::id())
                          ->where('item_id', $booking->rental_item_id)
                          ->where('item_type', 'App\\Models\\RentalItem')
                          ->where('payment_id', $payment->id)
                          ->first();

            $reviewedItems['rental_' . $booking->rental_item_id] = $review ? true : false;
        }

        // Jika ada booking fotografer, tambahkan cek review untuk itu juga
        if (method_exists($payment, 'photographerBookings') && $payment->photographerBookings) {
            foreach ($payment->photographerBookings as $booking) {
                $review = Review::where('user_id', Auth::id())
                              ->where('item_id', $booking->photographer_id)
                              ->where('item_type', 'App\\Models\\Photographer')
                              ->where('payment_id', $payment->id)
                              ->first();

                $reviewedItems['photographer_' . $booking->photographer_id] = $review ? true : false;
            }
        }
    }

    return view('users.payment.detail', compact('payment', 'reviewedItems'));
}
    /**
     * Handle recurring payment notification from Midtrans
     */
    public function recurringNotification(Request $request)
    {
        // Set Midtrans configuration
        $this->setupMidtransConfig(false);

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
        $this->setupMidtransConfig(false);

        try {
            $notification = new \Midtrans\Notification();

            // Process the pay account notification
            // You might want to log the notification or update relevant records

            return response('OK', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

/**
 * Download invoice as PDF
 *
 * @param int $id Payment ID
 * @return \Illuminate\Http\Response
 */
public function downloadInvoice($id)
{
    // Ambil data payment dengan eager loading untuk semua jenis booking dan user
    $payment = Payment::with(['fieldBookings.field', 'rentalBookings.rentalItem', 'user'])
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    // Only allow downloading invoices for successful payments
    if ($payment->transaction_status !== 'success') {
        return redirect()
            ->route('user.payment.detail', ['id' => $payment->id])
            ->with('error', 'Invoice hanya tersedia untuk pembayaran yang berhasil.');
    }

    // Konfigurasi DomPDF
    $config = [
        'fontDir' => public_path('fonts'),
        'fontCache' => storage_path('fonts'),
        'defaultFont' => 'sans-serif',
        'isRemoteEnabled' => true
    ];

    // Load view dengan konfigurasi PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('users.payment.invoice', compact('payment'));

    // Atur options
    $dompdf = $pdf->getDomPDF();
    $options = $dompdf->getOptions();
    $options->setIsRemoteEnabled(true);
    $options->set('defaultFont', 'sans-serif');

    // Tentukan lokasi font
    $options->set('fontDir', [
        public_path('fonts/poppins')
    ]);

    // Atur kembali options
    $dompdf->setOptions($options);

    // Set PDF options
    $pdf->setPaper('a4', 'portrait');

    // Return the PDF for download
    return $pdf->download('Invoice-' . $payment->order_id . '.pdf');
}
}
