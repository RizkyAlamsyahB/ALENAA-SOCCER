<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\Cart;
use App\Models\User;
use App\Models\Field;
use App\Models\Review;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\Discount;
use Barryvdh\DomPDF\PDF;
use App\Models\Membership;
use Midtrans\Notification;
use Illuminate\Support\Str;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use App\Models\DiscountUsage;
use App\Models\RentalBooking;
use App\Mail\MembershipExpired;
use App\Models\MembershipSession;
use Illuminate\Support\Facades\DB;
use App\Models\PhotographerBooking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipRenewalInvoice;
use App\Mail\MembershipRenewalSuccess;
use App\Models\MembershipSubscription;
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

            // Batalkan semua booking reguler
            $this->cancelAllBookings($payment);

            // Batalkan semua booking on_hold jika ini adalah pembayaran perpanjangan
            if (strpos($payment->order_id, 'RENEW-MEM-') === 0) {
                $this->cancelOnHoldBookings($payment);
            }

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

    $subtotal = 0;
    $discountId = null;
    $discountAmount = 0;
    $totalPrice = 0;
    $orderId = 'ORDER-' . time() . '-' . Str::random(5);
    $customer = Auth::user();

    DB::beginTransaction();
    try {
        // Proses field bookings
        $fieldBookings = FieldBooking::whereIn('id', $fieldBookingIds)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        if (count($fieldBookingIds) > 0 && $fieldBookings->isEmpty()) {
            DB::rollBack();
            return redirect()->route('user.cart.view')->with('error', 'Booking lapangan tidak ditemukan atau status telah berubah');
        }

        // Proses rental bookings
        $rentalBookings = RentalBooking::whereIn('id', $rentalBookingIds)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        if (count($rentalBookingIds) > 0 && $rentalBookings->isEmpty()) {
            DB::rollBack();
            return redirect()->route('user.cart.view')->with('error', 'Booking penyewaan tidak ditemukan atau status telah berubah');
        }

        // Proses membership subscriptions
        $membershipSubscriptions = MembershipSubscription::whereIn('id', $membershipIds)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        if (count($membershipIds) > 0 && $membershipSubscriptions->isEmpty()) {
            DB::rollBack();
            return redirect()->route('user.cart.view')->with('error', 'Membership tidak ditemukan atau status telah berubah');
        }

        // Proses photographer bookings
        $photographerBookings = PhotographerBooking::whereIn('id', $photographerBookingIds)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        if (count($photographerBookingIds) > 0 && $photographerBookings->isEmpty()) {
            DB::rollBack();
            return redirect()->route('user.cart.view')->with('error', 'Booking fotografer tidak ditemukan atau status telah berubah');
        }

        // Kalkulasi subtotal harga dari semua jenis item
        $subtotal = $fieldBookings->sum('total_price') +
                   $rentalBookings->sum('total_price') +
                   $membershipSubscriptions->sum('price') +
                   $photographerBookings->sum('price');

        // Cek apakah ada diskon yang diterapkan
        if (session()->has('cart_discount')) {
            $cartDiscount = session('cart_discount');
            $discountId = $cartDiscount['id'];
            $discountAmount = $cartDiscount['amount'];

            // Verifikasi ulang diskon
            $discount = Discount::find($discountId);

            if (!$discount || !$discount->isValidForUser(Auth::id())) {
                // Diskon tidak valid, hapus dari session
                session()->forget('cart_discount');
                return redirect()->route('user.cart.view')->with('error', 'Kupon diskon tidak valid atau sudah tidak dapat digunakan');
            }

            // Re-calculate discount (untuk keamanan)
            $discountAmount = $discount->calculateDiscount($subtotal);
        }

        // Hitung total setelah diskon
        $totalPrice = $subtotal - $discountAmount;

        // Periksa ketersediaan field bookings
        foreach ($fieldBookings as $booking) {
            $conflictingBooking = FieldBooking::where('field_id', $booking->field_id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['confirmed', 'on_hold']) // Tambahkan on_hold ke dalam kondisi
                ->where(function ($query) use ($booking) {
                    // Cek overlap waktu
                    $query
                        ->where(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                              ->where('end_time', '>', $booking->start_time);
                        })
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<', $booking->end_time)
                              ->where('end_time', '>=', $booking->end_time);
                        })
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '>=', $booking->start_time)
                              ->where('end_time', '<=', $booking->end_time);
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
                            $q->where('start_time', '>=', $booking->start_time)
                              ->where('start_time', '<', $booking->end_time);
                        })
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('end_time', '>', $booking->start_time)
                              ->where('end_time', '<=', $booking->end_time);
                        })
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                              ->where('end_time', '>=', $booking->end_time);
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
                            $q->where('start_time', '<=', $booking->start_time)
                              ->where('end_time', '>', $booking->start_time);
                        })
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<', $booking->end_time)
                              ->where('end_time', '>=', $booking->end_time);
                        })
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '>=', $booking->start_time)
                              ->where('end_time', '<=', $booking->end_time);
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

        // Jika ada diskon, tambahkan sebagai item negatif
        if ($discountAmount > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -$discountAmount,
                'quantity' => 1,
                'name' => 'Diskon: ' . ($discount->name ?? 'Kupon Diskon'),
            ];
        }

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

        // Create payment record dengan informasi diskon
        $payment = Payment::create([
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'amount' => $totalPrice,
            'discount_id' => $discountId,
            'discount_amount' => $discountAmount,
            'original_amount' => $subtotal,
            'transaction_status' => 'pending',
            'expires_at' => now()->addMinutes(30),
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

        return view('users.payment.checkout', [
            'snap_token' => $snapToken,
            'order_id' => $orderId,
            'total_price' => $totalPrice,
            'original_amount' => $subtotal,
            'discount_amount' => $discountAmount,
            'allBookings' => $allBookings,
            'payment' => $payment,
            'expires_at' => $payment->expires_at,
        ]);
    } catch (\PDOException $e) {
        DB::rollBack();
        Log::error('Checkout PDO Error: ' . $e->getMessage());

        // Tangani kesalahan database spesifik
        if (strpos($e->getMessage(), 'deadlock') !== false || strpos($e->getMessage(), 'lock') !== false) {
            return redirect()->route('user.cart.view')
                ->with('error', 'Sistem sedang sibuk. Silakan coba lagi dalam beberapa saat');
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
     * Melanjutkan pembayaran yang belum selesai
     */
    public function continuePayment($id)
    {
        $payment = Payment::with(['fieldBookings.field', 'rentalBookings.rentalItem', 'discount', 'membershipSubscriptions.membership', 'photographerBookings.photographer'])
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

            // Jika ada diskon, tambahkan sebagai item negatif
            if ($payment->discount_amount > 0) {
                $itemDetails[] = [
                    'id' => 'DISCOUNT',
                    'price' => -$payment->discount_amount,
                    'quantity' => 1,
                    'name' => 'Diskon: ' . ($payment->discount ? $payment->discount->name : 'Kupon Diskon'),
                ];
            }

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
            $snapToken = Snap::getSnapToken($params);

            // Kirim ke view dengan format data yang sesuai, termasuk expires_at
            return view('users.payment.checkout', [
                'snap_token' => $snapToken,
                'order_id' => $newOrderId, // Gunakan order_id baru di view
                'original_order_id' => $originalOrderId, // Simpan original order_id
                'total_price' => $payment->amount,
                'original_amount' => $payment->original_amount,
                'discount_amount' => $payment->discount_amount,
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

        // Simpan status transaksi sebelumnya
        $previousStatus = $payment->transaction_status;

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

        // Tambahkan points jika payment statusnya berubah menjadi success dan sebelumnya bukan success
        if ($payment->transaction_status === 'success' && $previousStatus !== 'success') {
            // Berikan points kepada user (1 point untuk setiap 10000 rupiah)
            $user = User::find($payment->user_id);
            if ($user) {
                // Kalkulasi points berdasarkan amount pembayaran (1 point untuk setiap 10000 rupiah)
                $points = floor($payment->original_amount / 10000);
                $user->points += $points;
                $user->save();

                Log::info('Added ' . $points . ' points to user #' . $user->id . ' for payment #' . $payment->id);
            }
        }

        // Catat penggunaan diskon hanya jika pembayaran berhasil dan status berubah dari pending ke success
        if ($payment->transaction_status === 'success' && $previousStatus !== 'success' && $payment->discount_id) {
            // Cek apakah sudah ada catatan penggunaan
            $existingUsage = DiscountUsage::where('payment_id', $payment->id)->where('discount_id', $payment->discount_id)->first();

            // Jika belum ada, buat catatan baru
            if (!$existingUsage) {
                DiscountUsage::create([
                    'discount_id' => $payment->discount_id,
                    'user_id' => $payment->user_id,
                    'payment_id' => $payment->id,
                    'discount_amount' => $payment->discount_amount,
                ]);

                Log::info('Discount usage recorded for payment #' . $payment->id . ' with discount #' . $payment->discount_id);
            }
        }

        // Update field bookings
        if (method_exists($payment, 'fieldBookings') && $payment->fieldBookings && $payment->fieldBookings->count() > 0) {
            foreach ($payment->fieldBookings as $booking) {
                $booking->status = 'confirmed';
                $booking->save();
                Log::info('Field Booking #' . $booking->id . ' status updated to: confirmed');
            }
        }

        // Update rental bookings
        if (method_exists($payment, 'rentalBookings') && $payment->rentalBookings && $payment->rentalBookings->count() > 0) {
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

        // Jika ada session membership, update juga statusnya menjadi scheduled
        if (method_exists($subscription, 'sessions') && $subscription->sessions) {
            foreach ($subscription->sessions as $session) {
                $session->status = 'scheduled';
                $session->save();

                // Perbarui juga status field booking terkait jika ada
                if (method_exists($session, 'fieldBooking') && $session->fieldBooking) {
                    $fieldBooking = $session->fieldBooking;
                    $fieldBooking->status = 'confirmed';
                    $fieldBooking->save();
                    Log::info('Field Booking #' . $fieldBooking->id . ' for Membership Session #' . $session->id . ' status updated to: confirmed');
                }
            }

            // BUAT BOOKING ON_HOLD HANYA JIKA TRANSAKSI BERHASIL (success/settlement)
            if ($payment->transaction_status === 'success') {
                $this->createNextPeriodOnHoldBookings($subscription);
            }
        }

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

        // Tambahkan penanganan untuk pembayaran perpanjangan membership
        if (strpos($orderId, 'RENEW-MEM-') === 0 && $payment->transaction_status === 'success') {
            // Ekstrak subscription ID dari order_id
            $subscriptionId = null;
            $orderParts = explode('-', $orderId);

            // Cek format order_id dan ambil subscription ID
            if (count($orderParts) >= 3) {
                // Format: RENEW-MEM-{subscription_id}-{anything}
                $subscriptionId = $orderParts[2];

                // Jika ada 'RETRY' dalam order_id, sesuaikan pengambilan subscription ID
                if ($subscriptionId === 'RETRY' && count($orderParts) >= 4) {
                    $subscriptionId = $orderParts[3];
                }

                Log::info('Processing membership renewal', ['subscription_id' => $subscriptionId, 'order_id' => $orderId]);

                // Cari subscription berdasarkan ID
                $subscription = MembershipSubscription::find($subscriptionId);

                if ($subscription) {
                    DB::beginTransaction();
                    try {
                        // Update status renewal
                        $subscription->renewal_status = 'renewed';
                        $subscription->last_payment_date = now();

                        // Hitung tanggal baru untuk periode berikutnya (7 hari untuk membership mingguan)
                        $newEndDate = Carbon::parse($subscription->end_date)->addDays(7);
                        $subscription->end_date = $newEndDate;
                        $subscription->save();

                        Log::info('Membership #' . $subscription->id . ' has been renewed until ' . $newEndDate);

                        // Proses booking on_hold
                        if (!empty($payment->on_hold_booking_ids)) {
                            $this->confirmOnHoldBookings($subscription, $payment);
                        }

                        // Reload subscription dengan relasi yang dibutuhkan
                        $subscription = $subscription->fresh(['membership', 'sessions']);

                        // Siapkan pengiriman email
                        Log::info('Preparing to send renewal success email', [
                            'user_id' => $subscription->user_id,
                            'subscription_id' => $subscription->id,
                            'sessions_count' => $subscription->sessions->count(),
                        ]);

                        // Kirim email konfirmasi perpanjangan
                        try {
                            Mail::to($subscription->user->email)->send(
                                new MembershipRenewalSuccess([
                                    'user' => $subscription->user,
                                    'subscription' => $subscription,
                                ]),
                            );
                            Log::info('Success email sent successfully');
                        } catch (\Exception $emailError) {
                            // Tangkap error pengiriman email tanpa menggagalkan transaksi
                            Log::error('Failed to send success email: ' . $emailError->getMessage(), [
                                'user_id' => $subscription->user_id,
                                'subscription_id' => $subscription->id,
                            ]);
                        }

                        DB::commit();
                        Log::info('Membership renewal transaction completed successfully', [
                            'subscription_id' => $subscription->id,
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Error processing membership renewal: ' . $e->getMessage(), [
                            'subscription_id' => $subscriptionId,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                } else {
                    Log::error('Subscription not found for renewal', [
                        'subscription_id' => $subscriptionId,
                        'order_id' => $orderId,
                    ]);
                }
            } else {
                Log::error('Invalid order_id format for membership renewal', [
                    'order_id' => $orderId,
                ]);
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

        // Tidak perlu menambahkan points lagi karena sudah ditambahkan di notification()
        if ($payment->transaction_status == 'success') {
            // Hanya menghitung points untuk ditampilkan di view, tanpa menyimpannya ke database
            $pointsEarned = floor($payment->original_amount / 10000);

            // Cek jika ini pembayaran perpanjangan membership berdasarkan order_id
            if (strpos($payment->order_id, 'RENEW-MEM-') === 0) {
                // Ambil informasi subscription yang diperbarui
                $subscription = MembershipSubscription::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->where('renewal_status', 'renewed')
                    ->first();

                return view('users.payment.renewal_success', compact('payment', 'pointsEarned', 'subscription'));
            }

            return view('users.payment.success', compact('payment', 'pointsEarned'));
        } elseif ($payment->transaction_status == 'pending') {
            return view('users.payment.unfinish', ['orderId' => $orderId]);
        } else {
            return view('users.payment.error', ['errorMessage' => 'Pembayaran gagal atau dibatalkan']);
        }
    }

    /**
     * Handle payment finish/unfinish/error redirect dari Midtrans
     * Identifikasi jenis pembayaran dari order_id
     */
    private function identifyPaymentType($orderId)
    {
        if (strpos($orderId, 'RENEW-MEM-') === 0) {
            return 'membership_renewal';
        }
        return 'regular';
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

        // Periksa jika pembayaran sudah kedaluarsa
        $payment = $this->checkExpiredPayment($payment);

        // Identifikasi tipe pembayaran dari order_id
        $paymentType = $this->identifyPaymentType($orderId);

        // Jika ini adalah pembayaran perpanjangan membership
        if ($paymentType === 'membership_renewal') {
            // Jika statusnya berubah menjadi failed karena kadaluarsa
            if ($payment->transaction_status === 'failed') {
                return redirect()
                    ->route('user.membership.my-memberships')
                    ->with('error', 'Pembayaran perpanjangan telah kadaluwarsa. Silakan mencoba lagi.');
            }

            // Redirect ke halaman memberships
            return redirect()
                ->route('user.membership.my-memberships')
                ->with('warning', 'Pembayaran perpanjangan belum selesai. Anda dapat melanjutkan pembayaran dari halaman Membership Saya.');
        } else {
            // Proses normal untuk pembayaran biasa
            // Jika statusnya berubah menjadi failed karena kadaluarsa
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

        // Jika ini adalah pembayaran perpanjangan membership
        if ($payment->payment_type === 'membership_renewal') {
            // Jika statusnya berubah menjadi failed karena kadaluarsa
            if ($payment->transaction_status === 'failed') {
                return redirect()
                    ->route('user.membership.my-memberships')
                    ->with('error', 'Pembayaran perpanjangan telah kadaluwarsa. Silakan mencoba lagi.');
            }

            // Redirect ke halaman memberships
            return redirect()
                ->route('user.membership.my-memberships')
                ->with('error', 'Terjadi kesalahan pada pembayaran perpanjangan. Silakan coba lagi.');
        } else {
            // Proses normal untuk pembayaran biasa
            // Jika statusnya berubah menjadi failed karena kadaluarsa
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
        $payment = Payment::with(['fieldBookings.field', 'rentalBookings.rentalItem', 'photographerBookings.photographer', 'membershipSubscriptions.membership'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Cek apakah pembayaran sudah kedaluwarsa tapi belum diupdate statusnya
        $payment = $this->checkExpiredPayment($payment);

        // Hitung points yang didapat dari transaksi ini
        $pointsEarned = 0;
        if ($payment->transaction_status === 'success') {
            $pointsEarned = floor($payment->original_amount / 10000);
        }

        // Identifikasi jenis pembayaran (pembayaran biasa atau perpanjangan membership)
        $isMembershipRenewal = strpos($payment->order_id, 'RENEW-MEM-') === 0;
        $membershipInfo = null;

        // Jika ini adalah pembayaran perpanjangan, ambil data subscription terkait
        if ($isMembershipRenewal) {
            // Jika ini adalah pembayaran perpanjangan yang berhasil, cari subscription yang sudah diperbarui
            if ($payment->transaction_status === 'success') {
                $subscription = MembershipSubscription::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->where('renewal_status', 'renewed')
                    ->first();
            } else {
                // Jika belum berhasil, cari subscription dalam status renewal_pending
                $subscription = MembershipSubscription::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->where('renewal_status', 'renewal_pending')
                    ->first();
            }

            if ($subscription) {
                $membershipInfo = [
                    'subscription' => $subscription,
                    'membership' => $subscription->membership,
                    'field' => $subscription->membership->field ?? null,
                    'next_period_start' => Carbon::parse($subscription->end_date)->format('d M Y'),
                    'next_period_end' => Carbon::parse($subscription->end_date)
                        ->addMonths($subscription->membership->duration)
                        ->format('d M Y')
                ];
            }
        }

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

        // Kirim semua data yang dibutuhkan ke view
        return view('users.payment.detail', compact(
            'payment',
            'reviewedItems',
            'pointsEarned',
            'isMembershipRenewal',
            'membershipInfo'
        ));
    }

    /**
     * Handle recurring payment notification from Midtrans
     */
    public function recurringNotification(Request $request)
    {
        // Set Midtrans configuration
        $this->setupMidtransConfig(false);

        try {
            $notification = new Notification();

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
            $notification = new Notification();

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
        $payment = Payment::with(['fieldBookings.field', 'rentalBookings.rentalItem', 'photographerBookings.photographer', 'membershipSubscriptions.membership', 'user'])
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
            'isRemoteEnabled' => true,
        ];

        // Load view dengan konfigurasi PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('users.payment.invoice', compact('payment'));

        // Atur options
        $dompdf = $pdf->getDomPDF();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->set('defaultFont', 'sans-serif');

        // Tentukan lokasi font
        $options->set('fontDir', [public_path('fonts/poppins')]);

        // Atur kembali options
        $dompdf->setOptions($options);

        // Set PDF options
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF for download
        return $pdf->download('Invoice-' . $payment->order_id . '.pdf');
    }

    public function scheduleMembershipRenewalInvoices()
    {
        // Temukan booking sesi kedua yang akan datang (dalam 7 hari)
        $secondSessionBookings = FieldBooking::where('is_membership', true)
            ->whereHas('membershipSession', function ($query) {
                $query->where('session_number', 2);
                $query->whereDate('start_time', '>=', Carbon::now())
                    ->whereDate('start_time', '<=', Carbon::now()->addDays(7));
                $query->whereHas('subscription', function ($q) {
                    $q->where('status', 'active')
                        ->where('renewal_status', 'not_due')
                        ->where('invoice_sent', false); // Pastikan belum dikirim
                });
            })
            ->get();

        $count = 0;
        foreach ($secondSessionBookings as $booking) {
            $session = $booking->membershipSession;
            $subscription = $session->subscription;

            // Tambahkan pengecekan untuk memastikan invoice belum dikirim
            if ($subscription->invoice_sent) {
                Log::info('Invoice sudah dikirim untuk subscription #' . $subscription->id);
                continue;
            }

            // Tandai bahwa invoice telah dikirim
            $subscription->invoice_sent = true;
            $subscription->renewal_status = 'renewal_pending';
            $subscription->next_invoice_date = now();
            $subscription->save();

            // Buat invoice dengan objek subscription
            $payment = $this->createRenewalInvoice($subscription);

            // Jika payment berhasil dibuat, kirim email
            if ($payment) {
                // Kirim email invoice
                try {
                    Mail::to($subscription->user->email)->send(
                        new MembershipRenewalInvoice([
                            'user' => $subscription->user,
                            'membership' => $subscription->membership,
                            'subscription' => $subscription,
                            'payment' => $payment,
                            'payment_url' => route('user.membership.renewal.pay', ['id' => $payment->id]),
                            'deadline' => Carbon::parse($payment->expires_at)->format('d M Y H:i'),
                        ])
                    );
                    Log::info('Renewal invoice email sent to ' . $subscription->user->email . ' for subscription #' . $subscription->id);
                    $count++;
                } catch (\Exception $e) {
                    Log::error('Failed to send renewal invoice email: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'message' => $count . ' renewal invoices scheduled',
            'bookings' => $secondSessionBookings->pluck('id'),
        ]);
    }

/**
 * Membuat invoice untuk perpanjangan membership
 *
 * @param \Illuminate\Http\Request|MembershipSubscription|int $subscriptionOrRequest
 * @param int|null $id ID dari subscription (jika parameter pertama adalah Request)
 * @return \Illuminate\Http\JsonResponse|Payment|null
 */
public function createRenewalInvoice($subscriptionOrRequest, $id = null)
{
    try {
        // Tentukan mode (AJAX request atau direct call)
        $isAjaxRequest = $subscriptionOrRequest instanceof Request;

        // Ambil subscription berdasarkan parameter yang diberikan
        $subscription = null;

        if ($isAjaxRequest) {
            $id = $id ?? $subscriptionOrRequest->route('id'); // Pastikan id diambil dari route jika null
            // Mode AJAX request (dipanggil dari web)
            $subscription = MembershipSubscription::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->first();
        } else {
            // Mode direct call (dipanggil dari scheduleMembershipRenewalInvoices)
            if (is_numeric($subscriptionOrRequest)) {
                $subscription = MembershipSubscription::where('id', $subscriptionOrRequest)
                    ->where('status', 'active')
                    ->first();
            } elseif ($subscriptionOrRequest instanceof MembershipSubscription) {
                $subscription = $subscriptionOrRequest;
            }
        }

        // Validasi subscription
        if (!$subscription || !($subscription instanceof MembershipSubscription)) {
            $errorMessage = 'Invalid subscription parameter in createRenewalInvoice';
            Log::error($errorMessage);

            if ($isAjaxRequest) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Membership tidak ditemukan atau tidak aktif',
                    ],
                    404,
                );
            }

            return null;
        }

        // Cek jika sudah ada invoice pending untuk membership ini
        $existingPayment = Payment::where('user_id', $subscription->user_id)
            ->where('order_id', 'like', 'RENEW-MEM-' . $subscription->id . '%')
            ->where('transaction_status', 'pending')
            ->first();

        if ($existingPayment) {
            Log::info('Using existing pending renewal invoice', ['payment_id' => $existingPayment->id]);

            if ($isAjaxRequest) {
                return response()->json([
                    'success' => true,
                    'message' => 'Melanjutkan pembayaran yang sudah ada',
                    'payment_url' => route('user.membership.renewal.pay', ['id' => $existingPayment->id]),
                ]);
            }

            return $existingPayment;
        }

        // Buat order ID unik (format: RENEW-MEM-{subscription_id}-{random}-{timestamp})
        $orderId = 'RENEW-MEM-' . $subscription->id . '-' . substr(md5(uniqid()), 0, 8) . '-' . time();

        // Ambil jadwal sesi ke-3 untuk menentukan batas waktu pembayaran
        $thirdSession = MembershipSession::where('membership_subscription_id', $subscription->id)
            ->where('session_number', 3)
            ->first();

        // Set tanggal kedaluwarsa invoice berdasarkan jadwal sesi ke-3 atau default 3 hari
        if ($thirdSession && $thirdSession->start_time > now()) {
            $expiresAt = Carbon::parse($thirdSession->start_time);
            Log::info('Setting invoice expiry based on third session', [
                'subscription_id' => $subscription->id,
                'session_id' => $thirdSession->id,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ]);
        } else {
            $expiresAt = now()->addDays(3);
            Log::info('Setting default invoice expiry (3 days)', [
                'subscription_id' => $subscription->id,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ]);
        }

        // Buat record payment
        $payment = Payment::create([
            'order_id' => $orderId,
            'user_id' => $subscription->user_id,
            'amount' => $subscription->price,
            'original_amount' => $subscription->price,
            'transaction_status' => 'pending',
            'expires_at' => $expiresAt,
            'payment_type' => 'membership_renewal', // Tandai sebagai pembayaran perpanjangan
        ]);

        // Update status renewal subscription
        $subscription->renewal_status = 'renewal_pending';
        $subscription->next_invoice_date = now();
        $subscription->save();

        // Tidak perlu membuat booking on_hold lagi karena sudah dibuat sejak awal periode
        // Dapatkan ID booking on_hold yang sudah ada untuk payment ini
        if (!empty($subscription->next_period_bookings)) {
            $bookingIds = json_decode($subscription->next_period_bookings, true);
            if (is_array($bookingIds) && !empty($bookingIds)) {
                // Update payment dengan ID booking on_hold yang sudah ada
                $payment->on_hold_booking_ids = json_encode($bookingIds);
                $payment->save();

                // Update booking dengan renewal_payment_id
                foreach ($bookingIds as $bookingId) {
                    $booking = FieldBooking::find($bookingId);
                    if ($booking && $booking->status === 'on_hold') {
                        $booking->renewal_payment_id = $payment->id;
                        $booking->save();
                    }
                }

                Log::info('Menggunakan booking on_hold yang sudah ada untuk pembayaran perpanjangan', [
                    'payment_id' => $payment->id,
                    'booking_ids' => $bookingIds
                ]);
            }
        }

        Log::info('Renewal invoice created successfully', [
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        // Return sesuai dengan mode
        if ($isAjaxRequest) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice perpanjangan berhasil dibuat',
                'payment_url' => route('user.membership.renewal.pay', ['id' => $payment->id]),
            ]);
        }

        return $payment;
    } catch (\Exception $e) {
        Log::error('Create Renewal Invoice Error: ' . $e->getMessage(), [
            'subscription_id' => $id ?? ($subscription->id ?? 'unknown'),
            'trace' => $e->getTraceAsString(),
        ]);

        if ($isAjaxRequest) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ],
                500,
            );
        }

        return null;
    }
}

    /**
     * Kirim email invoice perpanjangan
     */
    private function sendRenewalInvoiceEmail($subscription, $payment)
    {
        try {
            $user = User::find($subscription->user_id);
            $membership = Membership::find($subscription->membership_id);

            // URL untuk pembayaran
            $paymentUrl = route('user.membership.renewal.pay', ['id' => $payment->id]);

            // Tanggal berakhir invoice dalam format yang mudah dibaca
            $deadlineFormatted = Carbon::parse($payment->expires_at)->format('d M Y H:i');

            // Kirim email
            Mail::to($user->email)->send(
                new MembershipRenewalInvoice([
                    'user' => $user,
                    'membership' => $membership,
                    'subscription' => $subscription,
                    'payment' => $payment,
                    'payment_url' => $paymentUrl,
                    'deadline' => $deadlineFormatted,
                ]),
            );

            Log::info('Email invoice perpanjangan berhasil dikirim', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email invoice perpanjangan: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);

            return false;
        }
    }
    /**
 * Cek membership yang kedaluwarsa setiap hari
 * Dipanggil oleh scheduler setiap hari
 */
public function checkExpiredMembershipRenewals()
{
    // Ambil membership yang sedang dalam proses perpanjangan (renewal_pending)
    // dan jadwal sesi ketiga sudah lewat
    $now = Carbon::now();

    // Pendekatan 1: Cari berdasarkan sesi yang sudah selesai
    $expiredSubscriptions = DB::table('membership_sessions')
        ->join('membership_subscriptions', 'membership_sessions.membership_subscription_id', '=', 'membership_subscriptions.id')
        ->where('membership_sessions.session_number', 3)
        ->where('membership_sessions.end_time', '<', $now) // Sesi ke-3 sudah lewat
        ->where('membership_subscriptions.renewal_status', 'renewal_pending')
        ->where('membership_subscriptions.status', 'active')
        ->select('membership_subscriptions.id')
        ->distinct()
        ->get()
        ->pluck('id')
        ->toArray();

    // Jika tidak ada data, cari juga berdasarkan tanggal kedaluwarsa pembayaran
    if (empty($expiredSubscriptions)) {
        $expiredPayments = Payment::where('payment_type', 'membership_renewal')
            ->where('transaction_status', 'pending')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($expiredPayments as $payment) {
            // Ekstrak subscription ID dari order_id (format: RENEW-MEM-{subscription_id}-{timestamp})
            $orderParts = explode('-', $payment->order_id);
            if (count($orderParts) >= 3) {
                $subscriptionId = $orderParts[2];
                $expiredSubscriptions[] = $subscriptionId;
            }
        }
    }

    $expiredCount = 0;
    $processedIds = [];

    // Proses subscription yang sudah kedaluwarsa
    foreach ($expiredSubscriptions as $subscriptionId) {
        // Hindari pemrosesan duplikat
        if (in_array($subscriptionId, $processedIds)) {
            continue;
        }

        $processedIds[] = $subscriptionId;

        $subscription = MembershipSubscription::find($subscriptionId);
        if (!$subscription || $subscription->status !== 'active' || $subscription->renewal_status !== 'renewal_pending') {
            continue;
        }

        Log::info('Processing expired membership renewal', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
        ]);

        // Cek apakah ada pembayaran perpanjangan yang pending
        $pendingPayment = Payment::where('order_id', 'like', 'RENEW-MEM-' . $subscription->id . '%')
            ->where('transaction_status', 'pending')
            ->first();

        if ($pendingPayment) {
            // Update status payment jadi expired
            $pendingPayment->transaction_status = 'failed';
            $pendingPayment->save();

            // Batalkan booking on_hold yang terkait
            if (!empty($pendingPayment->on_hold_booking_ids)) {
                $bookingIds = json_decode($pendingPayment->on_hold_booking_ids, true);
                if (is_array($bookingIds)) {
                    foreach ($bookingIds as $bookingId) {
                        $booking = FieldBooking::find($bookingId);
                        if ($booking && $booking->status === 'on_hold') {
                            $booking->status = 'cancelled';
                            $booking->save();

                            Log::info('On-hold booking #' . $booking->id . ' dibatalkan karena invoice perpanjangan kedaluwarsa');
                        }
                    }
                }
            }

            Log::info('Marked pending renewal payment as failed', [
                'payment_id' => $pendingPayment->id,
                'subscription_id' => $subscription->id,
            ]);
        }

        // Update status subscription menjadi expired
        $subscription->status = 'expired';
        $subscription->renewal_status = 'not_due'; // atau nilai lain yang sesuai
        $subscription->save();
        $expiredCount++;

        // Kirim email notifikasi
        try {
            Mail::to($subscription->user->email)->send(
                new MembershipExpired([
                    'user' => $subscription->user,
                    'subscription' => $subscription,
                ]),
            );

            Log::info('Sent expiration email to user', [
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send expiration email: ' . $e->getMessage(), [
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
            ]);
        }

        Log::info('Membership has been marked as expired due to non-payment', [
            'subscription_id' => $subscription->id,
        ]);
    }

    return response()->json([
        'message' => $expiredCount . ' memberships deactivated',
        'subscription_ids' => $processedIds,
    ]);
}

/**
 * Halaman pembayaran perpanjangan membership
 */
public function showRenewalPayment($id)
{
    $payment = Payment::where('id', $id)
                     ->where('order_id', 'like', 'RENEW-MEM-%')
                     ->where('transaction_status', 'pending')
                     ->firstOrFail();

    // Cek apakah sudah kadaluarsa
    if (Carbon::parse($payment->expires_at)->isPast()) {
        return redirect()->route('user.membership.my-memberships')
               ->with('error', 'Invoice perpanjangan sudah kedaluarsa');
    }

    // Cek apakah ini adalah pembayaran milik user yang login
    if ($payment->user_id !== Auth::id()) {
        return redirect()->route('user.membership.my-memberships')
               ->with('error', 'Anda tidak memiliki akses ke pembayaran ini');
    }

    // Ekstrak subscription ID dari order_id (format: RENEW-MEM-{subscription_id}-{random}-{timestamp})
    $orderParts = explode('-', $payment->order_id);
    $subscriptionId = isset($orderParts[2]) ? $orderParts[2] : null;

    if (!$subscriptionId) {
        return redirect()->route('user.membership.my-memberships')
               ->with('error', 'Format order ID tidak valid');
    }

    // Cari subscription terkait
    $subscription = MembershipSubscription::find($subscriptionId);

    if (!$subscription) {
        return redirect()->route('user.membership.my-memberships')
               ->with('error', 'Membership tidak ditemukan');
    }

    // Setup Midtrans
    $this->setupMidtransConfig();

    // Ambil data membership
    $membership = $subscription->membership;

    // Buat order ID baru untuk Midtrans (untuk menghindari duplikat)
    $newOrderId = $payment->order_id . '-RETRY-' . time();

    // Siapkan item details untuk Midtrans
    $itemDetails = [
        [
            'id' => 'RENEW-MEM-' . $subscription->id,
            'price' => $payment->amount,
            'quantity' => 1,
            'name' => 'Perpanjangan ' . $membership->name,
        ],
    ];

    // Parameter untuk Midtrans
    $params = [
        'transaction_details' => [
            'order_id' => $newOrderId, // Gunakan order ID baru
            'gross_amount' => $payment->amount,
        ],
        'customer_details' => [
            'first_name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'phone' => Auth::user()->phone ?? '',
        ],
        'item_details' => $itemDetails,
    ];

    // Dapatkan Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return view('users.membership.renewal_payment', [
        'payment' => $payment,
        'subscription' => $subscription,
        'membership' => $membership,
        'snap_token' => $snapToken,
        'order_id' => $newOrderId, // Kirim order ID baru ke view
    ]);
}

    private function createNewBookingsForRenewal(MembershipSubscription $subscription)
    {
        try {
            // Ambil sesi membership yang ada, diurutkan berdasarkan session_number
            $existingSessions = MembershipSession::where('membership_subscription_id', $subscription->id)
                ->orderBy('session_number', 'asc')
                ->get();

            if ($existingSessions->isEmpty()) {
                Log::error('Tidak ada sesi existing untuk subscription #' . $subscription->id);
                return;
            }

            // Hitung tanggal mulai periode baru (dari akhir periode saat ini)
            $newPeriodStart = Carbon::parse($subscription->end_date);

            // Ambil payment ID dari membership yang baru dibayar
            $paymentId = null;
            $payment = Payment::where('order_id', 'like', 'RENEW-MEM-' . $subscription->id . '%')
                ->where('transaction_status', 'success')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($payment) {
                $paymentId = $payment->id;
            }

            // Ambil field ID dan harga dari membership
            $membership = $subscription->membership;
            $fieldId = $membership->field_id;
            $field = \App\Models\Field::find($fieldId);
            $originalPrice = $field ? $field->price : 0;

            // Untuk membership mingguan, kita perlu mempertahankan pola hari
            // Tanggal pertama dari periode baru adalah hari setelah tanggal terakhir membership sebelumnya
            $dayOfWeekMap = [];

            foreach ($existingSessions as $session) {
                $startTime = Carbon::parse($session->start_time);
                $dayOfWeekMap[$session->session_number] = [
                    'dayOfWeek' => $startTime->dayOfWeek,
                    'startHour' => $startTime->format('H:i'),
                    'endHour' => Carbon::parse($session->end_time)->format('H:i')
                ];
            }

            // Temukan tanggal pertama dari periode baru untuk setiap sesi
            $newSessionDates = [];
            $firstSessionDate = null;

            // Untuk session pertama, gunakan hari berikutnya yang sesuai setelah newPeriodStart
            $firstDayOfWeek = $dayOfWeekMap[1]['dayOfWeek'];
            $firstSessionDate = $newPeriodStart->copy();

            // Jika hari yang diinginkan berbeda dengan hari newPeriodStart, cari hari berikutnya
            if ($firstSessionDate->dayOfWeek != $firstDayOfWeek) {
                $firstSessionDate = $firstSessionDate->next($firstDayOfWeek);
            }

            $newSessionDates[1] = $firstSessionDate->format('Y-m-d');

            // Untuk session 2 dan 3, cari hari berikutnya yang sesuai setelah session sebelumnya
            for ($i = 2; $i <= count($existingSessions); $i++) {
                $prevDate = Carbon::parse($newSessionDates[$i - 1]);
                $targetDayOfWeek = $dayOfWeekMap[$i]['dayOfWeek'];

                $nextDate = $prevDate->copy();
                // Jika hari saat ini sama dengan target, maka cari di minggu depan
                if ($nextDate->dayOfWeek == $targetDayOfWeek) {
                    $nextDate->addDays(7);
                } else {
                    // Cari hari berikutnya yang sesuai
                    $nextDate = $nextDate->next($targetDayOfWeek);
                }

                $newSessionDates[$i] = $nextDate->format('Y-m-d');
            }

            Log::info('Jadwal baru untuk perpanjangan membership:', $newSessionDates);

            // Buat booking dan session baru
            foreach ($dayOfWeekMap as $sessionNumber => $details) {
                $newSessionDate = $newSessionDates[$sessionNumber];
                $newStartTime = Carbon::parse($newSessionDate . ' ' . $details['startHour']);
                $newEndTime = Carbon::parse($newSessionDate . ' ' . $details['endHour']);

                // 1. Buat field booking baru
                $newBooking = new \App\Models\FieldBooking();
                $newBooking->user_id = $subscription->user_id;
                $newBooking->field_id = $fieldId;
                $newBooking->payment_id = $paymentId;
                $newBooking->start_time = $newStartTime;
                $newBooking->end_time = $newEndTime;
                $newBooking->total_price = $originalPrice;
                $newBooking->status = 'confirmed';
                $newBooking->is_membership = true;
                $newBooking->save();

                // 2. Buat membership session baru
                $newSession = new MembershipSession();
                $newSession->membership_subscription_id = $subscription->id;
                $newSession->session_number = $sessionNumber;
                $newSession->status = 'scheduled';
                $newSession->session_date = $newStartTime->format('Y-m-d');
                $newSession->start_time = $newStartTime;
                $newSession->end_time = $newEndTime;
                $newSession->save();

                // 3. Update field booking dengan session ID
                $newBooking->membership_session_id = $newSession->id;
                $newBooking->save();

                Log::info('Membuat jadwal #' . $sessionNumber . ' baru: ' . $newStartTime->format('Y-m-d H:i') . ' - ' . $newEndTime->format('H:i'));
            }

            Log::info('Berhasil membuat jadwal baru untuk perpanjangan membership #' . $subscription->id);
        } catch (\Exception $e) {
            Log::error('Error creating new bookings for renewal: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

/**
 * Batalkan semua booking on_hold terkait dengan pembayaran perpanjangan
 *
 * @param Payment $payment
 * @return void
 */
private function cancelOnHoldBookings(Payment $payment)
{
    // Cek apakah ada booking IDs dalam payment
    if (empty($payment->on_hold_booking_ids)) {
        return;
    }

    try {
        $bookingIds = json_decode($payment->on_hold_booking_ids, true);
        if (!is_array($bookingIds)) {
            return;
        }

        foreach ($bookingIds as $bookingId) {
            $booking = \App\Models\FieldBooking::find($bookingId);
            if ($booking && $booking->status === 'on_hold') {
                $booking->status = 'cancelled';
                $booking->save();

                Log::info('On-hold booking #' . $booking->id . ' dibatalkan karena invoice perpanjangan kedaluwarsa');
            }
        }

        // Juga perbarui subscription untuk menandai bahwa booking on_hold telah dibatalkan
        if (strpos($payment->order_id, 'RENEW-MEM-') === 0) {
            // Ekstrak subscription ID dari order_id
            $orderParts = explode('-', $payment->order_id);
            if (count($orderParts) >= 3) {
                $subscriptionId = $orderParts[2];
                $subscription = MembershipSubscription::find($subscriptionId);

                if ($subscription) {
                    // Tandai bahwa booking on_hold telah dibatalkan
                    $subscription->next_period_bookings = null;
                    $subscription->save();
                }
            }
        }
    } catch (\Exception $e) {
        Log::error('Error cancelling on-hold bookings: ' . $e->getMessage(), [
            'payment_id' => $payment->id,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
/**
 * Proses booking on_hold menjadi confirmed dan tambahkan ke subscription setelah pembayaran berhasil
 *
 * @param MembershipSubscription $subscription
 * @param Payment $payment
 * @return void
 */
private function confirmOnHoldBookings(MembershipSubscription $subscription, Payment $payment)
{
    if (empty($payment->on_hold_booking_ids)) {
        Log::info('Tidak ada booking on_hold untuk diproses', [
            'payment_id' => $payment->id
        ]);
        return;
    }

    try {
        // Ambil booking IDs dengan menangani berbagai kemungkinan format
        $bookingIds = [];

        if (is_string($payment->on_hold_booking_ids)) {
            // Jika masih berupa string JSON, decode terlebih dahulu
            $bookingIds = json_decode($payment->on_hold_booking_ids, true);
        } elseif (is_array($payment->on_hold_booking_ids)) {
            // Jika sudah berupa array, gunakan langsung
            $bookingIds = $payment->on_hold_booking_ids;
        }

        // Periksa hasil setelah decoding
        if (!is_array($bookingIds) || empty($bookingIds)) {
            Log::error('Format on_hold_booking_ids tidak valid atau kosong', [
                'payment_id' => $payment->id,
                'type' => gettype($payment->on_hold_booking_ids),
                'value' => $payment->on_hold_booking_ids
            ]);
            return;
        }

        Log::info('Memproses ' . count($bookingIds) . ' booking on_hold', [
            'payment_id' => $payment->id,
            'booking_ids' => $bookingIds
        ]);

        // Ambil semua booking on_hold sekaligus untuk menghemat query
        $bookings = FieldBooking::whereIn('id', $bookingIds)
                              ->where('status', 'on_hold')
                              ->get();

        // Check jika jumlah booking yang ditemukan tidak sesuai
        if ($bookings->count() !== count($bookingIds)) {
            Log::warning('Beberapa booking tidak ditemukan atau bukan on_hold', [
                'expected' => count($bookingIds),
                'found' => $bookings->count()
            ]);
        }

        // Buat array untuk menyimpan nomor sesi yang sudah digunakan
        $usedSessionNumbers = [];

        foreach ($bookings as $booking) {
            DB::beginTransaction();
            try {
                // Ubah status booking dari on_hold menjadi confirmed
                $booking->status = 'confirmed';
                $booking->payment_id = $payment->id; // Set payment_id ke payment yang berhasil
                $booking->save();

                Log::info('Booking #' . $booking->id . ' berhasil diupdate ke confirmed');

                // Tentukan nomor sesi untuk booking ini
                $sessionNumber = $this->determineSessionNumber($booking, $subscription);

                // Pastikan nomor sesi unik
                while (in_array($sessionNumber, $usedSessionNumbers)) {
                    $sessionNumber++;
                }
                $usedSessionNumbers[] = $sessionNumber;

                // Buat session membership baru
                $newSession = new MembershipSession();
                $newSession->membership_subscription_id = $subscription->id;
                $newSession->session_number = $sessionNumber;
                $newSession->status = 'scheduled';
                $newSession->session_date = Carbon::parse($booking->start_time)->format('Y-m-d');
                $newSession->start_time = $booking->start_time;
                $newSession->end_time = $booking->end_time;
                $newSession->save();

                Log::info('Session membership baru dibuat', [
                    'session_id' => $newSession->id,
                    'session_number' => $newSession->session_number
                ]);

                // Update field booking dengan session ID
                $booking->membership_session_id = $newSession->id;
                $booking->save();

                Log::info('On-hold booking #' . $booking->id . ' diubah menjadi confirmed untuk perpanjangan membership');

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saat memproses booking #' . $booking->id . ': ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    } catch (\Exception $e) {
        Log::error('Error confirming on-hold bookings: ' . $e->getMessage(), [
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
/**
 * Menentukan nomor sesi berdasarkan urutan tanggal booking
 *
 * @param FieldBooking $newBooking
 * @param MembershipSubscription $subscription
 * @return int
 */
private function determineSessionNumber($newBooking, $subscription)
{
    // Ambil jadwal dari tanggal booking yang akan diproses
    $bookingDate = Carbon::parse($newBooking->start_time)->format('Y-m-d');

    // Cari semua booking yang terkait dengan perpanjangan ini, urutkan berdasarkan tanggal
    $renewalBookings = FieldBooking::where('field_id', $newBooking->field_id)
        ->where('user_id', $subscription->user_id)
        ->where('is_membership', true)
        ->where(function ($query) use ($newBooking) {
            // Termasuk booking yang sedang diproses dan booking lain yang terkait perpanjangan
            $query->where('id', $newBooking->id)
                  ->orWhere('renewal_payment_id', $newBooking->renewal_payment_id);
        })
        ->orderBy('start_time', 'asc')
        ->get();

    // Temukan posisi booking saat ini dalam daftar yang diurutkan
    $position = 1; // Default ke sesi pertama

    // Cari posisi booking berdasarkan tanggal mulai
    foreach ($renewalBookings as $index => $booking) {
        if (Carbon::parse($booking->start_time)->format('Y-m-d') === $bookingDate) {
            $position = $index + 1;
            break;
        }
    }

    Log::info('Menentukan session number', [
        'booking_id' => $newBooking->id,
        'start_time' => $newBooking->start_time,
        'position' => $position
    ]);

    return $position;
}

/**
 * Membuat booking on_hold untuk periode berikutnya sejak awal membership diaktifkan
 *
 * @param MembershipSubscription $subscription
 * @return void
 */
private function createNextPeriodOnHoldBookings(MembershipSubscription $subscription)
{
    try {
        // Ambil sesi membership yang ada, diurutkan berdasarkan session_number
        $existingSessions = MembershipSession::where('membership_subscription_id', $subscription->id)
            ->orderBy('session_number', 'asc')
            ->get();

        if ($existingSessions->isEmpty()) {
            Log::error('Tidak ada sesi existing untuk subscription #' . $subscription->id);
            return;
        }

        // Hitung tanggal mulai periode baru (dari akhir periode saat ini)
        $newPeriodStart = Carbon::parse($subscription->end_date);

        // Ambil field ID dan harga dari membership
        $membership = $subscription->membership;
        $fieldId = $membership->field_id;
        $field = Field::find($fieldId);
        $originalPrice = $field ? $field->price : 0;

        // Cek apakah semua sesi di hari yang sama
        $firstSession = $existingSessions->first();
        $firstSessionDay = Carbon::parse($firstSession->start_time)->format('Y-m-d');
        $allSameDay = $existingSessions->every(function ($session) use ($firstSessionDay) {
            return Carbon::parse($session->start_time)->format('Y-m-d') === $firstSessionDay;
        });

        // Buat booking on_hold untuk setiap sesi
        $onHoldBookings = [];

        if ($allSameDay) {
            // Kasus khusus: semua sesi di hari yang sama
            // Tentukan hari dalam seminggu dari sesi existing
            $dayOfWeek = Carbon::parse($firstSession->start_time)->dayOfWeek;

            // Tentukan tanggal di minggu berikutnya dengan hari yang sama
            $newSessionDate = $newPeriodStart->copy();

            // Jika tanggal akhir adalah hari yang sama, tambahkan 7 hari
            if ($newPeriodStart->dayOfWeek === $dayOfWeek) {
                $newSessionDate->addDays(7);
            } else {
                // Jika tidak, cari hari yang sama pada minggu yang sama atau berikutnya
                while ($newSessionDate->dayOfWeek !== $dayOfWeek) {
                    $newSessionDate->addDay();
                }
            }

            $newDateStr = $newSessionDate->format('Y-m-d');
            Log::info('Jadwal on_hold untuk periode berikutnya: ["' . $newDateStr . '"]');

            // Buat booking on_hold untuk setiap sesi dengan tanggal yang sama
            foreach ($existingSessions as $session) {
                $sessionStartTime = Carbon::parse($session->start_time);
                $sessionEndTime = Carbon::parse($session->end_time);

                // Ambil jam dan menit dari sesi existing
                $startTimeStr = $sessionStartTime->format('H:i');
                $endTimeStr = $sessionEndTime->format('H:i');

                // Buat waktu baru dengan tanggal baru dan jam yang sama
                $newStartTime = Carbon::parse($newDateStr . ' ' . $startTimeStr);
                $newEndTime = Carbon::parse($newDateStr . ' ' . $endTimeStr);

                // Buat field booking dengan status on_hold
                $onHoldBooking = new FieldBooking();
                $onHoldBooking->user_id = $subscription->user_id;
                $onHoldBooking->field_id = $fieldId;
                $onHoldBooking->start_time = $newStartTime;
                $onHoldBooking->end_time = $newEndTime;
                $onHoldBooking->total_price = $originalPrice;
                $onHoldBooking->status = 'on_hold';
                $onHoldBooking->is_membership = true;
                $onHoldBooking->save();

                $onHoldBookings[] = $onHoldBooking->id;

                Log::info('Membuat jadwal on_hold #' . $session->session_number . ' untuk periode berikutnya: ' .
                         $newStartTime->format('Y-m-d H:i') . ' - ' . $newEndTime->format('H:i'));
            }
        } else {
            // Kasus umum: sesi di hari yang berbeda-beda
            // Untuk membership mingguan, kita perlu mempertahankan pola hari
            $dayOfWeekMap = [];
            foreach ($existingSessions as $session) {
                $startTime = Carbon::parse($session->start_time);
                $endTime = Carbon::parse($session->end_time);

                $dayOfWeekMap[$session->session_number] = [
                    'dayOfWeek' => $startTime->dayOfWeek,
                    'startHour' => $startTime->format('H:i'),
                    'endHour' => $endTime->format('H:i'),
                ];
            }

            // Temukan tanggal pertama dari periode baru untuk setiap sesi
            $newSessionDates = [];

            // Untuk session pertama, gunakan hari berikutnya yang sesuai setelah newPeriodStart
            $firstDayOfWeek = $dayOfWeekMap[1]['dayOfWeek'];
            $firstSessionDate = $newPeriodStart->copy();

            // Jika hari yang diinginkan berbeda dengan hari newPeriodStart, cari hari berikutnya
            if ($firstSessionDate->dayOfWeek != $firstDayOfWeek) {
                $firstSessionDate = $firstSessionDate->next($firstDayOfWeek);
            } else {
                // Jika sama, tambahkan 7 hari (1 minggu)
                $firstSessionDate->addDays(7);
            }

            $newSessionDates[1] = $firstSessionDate->format('Y-m-d');

            // Untuk session 2 dan 3, cari hari berikutnya yang sesuai
            for ($i = 2; $i <= count($existingSessions); $i++) {
                $targetDayOfWeek = $dayOfWeekMap[$i]['dayOfWeek'];

                $nextDate = $newPeriodStart->copy();
                while ($nextDate->dayOfWeek !== $targetDayOfWeek || $nextDate->lt($newPeriodStart)) {
                    $nextDate->addDay();
                }

                // Jika tanggal sama dengan tanggal start, tambahkan 7 hari
                if ($nextDate->format('Y-m-d') === $newPeriodStart->format('Y-m-d')) {
                    $nextDate->addDays(7);
                }

                $newSessionDates[$i] = $nextDate->format('Y-m-d');
            }

            Log::info('Jadwal on_hold untuk periode berikutnya:', $newSessionDates);

            // Buat booking on_hold untuk setiap sesi
            foreach ($dayOfWeekMap as $sessionNumber => $details) {
                $newSessionDate = $newSessionDates[$sessionNumber];
                $newStartTime = Carbon::parse($newSessionDate . ' ' . $details['startHour']);
                $newEndTime = Carbon::parse($newSessionDate . ' ' . $details['endHour']);

                // Buat field booking dengan status on_hold
                $onHoldBooking = new \App\Models\FieldBooking();
                $onHoldBooking->user_id = $subscription->user_id;
                $onHoldBooking->field_id = $fieldId;
                $onHoldBooking->start_time = $newStartTime;
                $onHoldBooking->end_time = $newEndTime;
                $onHoldBooking->total_price = $originalPrice;
                $onHoldBooking->status = 'on_hold';
                $onHoldBooking->is_membership = true;
                $onHoldBooking->save();

                $onHoldBookings[] = $onHoldBooking->id;

                Log::info('Membuat jadwal on_hold #' . $sessionNumber . ' untuk periode berikutnya: ' .
                         $newStartTime->format('Y-m-d H:i') . ' - ' . $newEndTime->format('H:i'));
            }
        }

        // Simpan informasi booking on_hold untuk penggunaan di masa depan
        $subscription->next_period_bookings = json_encode($onHoldBookings);
        $subscription->save();

        Log::info('Berhasil membuat jadwal on_hold untuk periode berikutnya membership #' . $subscription->id);
    } catch (\Exception $e) {
        Log::error('Error creating on_hold bookings for next period: ' . $e->getMessage(), [
            'subscription_id' => $subscription->id,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}

}
