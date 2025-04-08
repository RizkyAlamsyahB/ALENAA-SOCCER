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
        ->whereIn('status', ['confirmed']) // Hapus 'on_hold' dari kondisi
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

        // Update membership subscriptions
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
                }

                Log::info('Membership #' . $subscription->id . ' status updated to: active');
            }
        }

        // Update photographer bookings
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

                        // Buat booking baru untuk periode berikutnya
                        $this->createNewBookingsForRenewal($subscription);

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

        // Ambil sesi terakhir untuk menentukan batas waktu pembayaran
        $lastSession = MembershipSession::where('membership_subscription_id', $subscription->id)
            ->orderBy('start_time', 'desc')
            ->first();

        // Set tanggal kedaluwarsa invoice berdasarkan jadwal sesi terakhir atau default 3 hari
        if ($lastSession && Carbon::parse($lastSession->start_time)->gt(now())) {
            $expiresAt = Carbon::parse($lastSession->start_time);
            Log::info('Setting invoice expiry based on last session', [
                'subscription_id' => $subscription->id,
                'session_id' => $lastSession->id,
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
        // Ambil sesi membership yang ada, diurutkan berdasarkan tanggal
        $existingSessions = MembershipSession::where('membership_subscription_id', $subscription->id)
            ->get()
            ->sortBy(function($session) {
                return Carbon::parse($session->start_time)->timestamp;
            });

        if ($existingSessions->isEmpty()) {
            Log::error('Tidak ada sesi existing untuk subscription #' . $subscription->id);
            return;
        }

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
        $field = Field::find($fieldId);
        $originalPrice = $field ? $field->price : 0;

        // Temukan tanggal sesi pertama dan terakhir
        $firstSession = $existingSessions->first();
        $lastSession = $existingSessions->last();

        $firstSessionDate = Carbon::parse($firstSession->start_time);
        $lastSessionDate = Carbon::parse($lastSession->start_time);

        // Hitung durasi dalam hari dari sesi pertama ke sesi terakhir
        $periodDuration = $lastSessionDate->diffInDays($firstSessionDate);

        // Tanggal pertama periode baru adalah hari setelah tanggal terakhir periode lama
        $newFirstDate = $lastSessionDate->copy()->addDay();

        // Siapkan array untuk menyimpan informasi detail setiap sesi
        $sessionInfos = [];
        foreach ($existingSessions as $index => $session) {
            $startTime = Carbon::parse($session->start_time);
            $endTime = Carbon::parse($session->end_time);

            $sessionInfos[] = [
                'session_number' => $session->session_number,
                'original_date' => $startTime->format('Y-m-d'),
                'start_hour' => $startTime->format('H:i'),
                'end_hour' => $endTime->format('H:i'),
                'day_of_week' => $startTime->dayOfWeek
            ];
        }

        // Urutkan session_infos berdasarkan tanggal original
        usort($sessionInfos, function($a, $b) {
            return strtotime($a['original_date']) - strtotime($b['original_date']);
        });

        // Tanggal untuk sesi pertama di periode baru
        $newFirstDate = $lastSessionDate->copy()->addDay();

        // Hitung tanggal untuk setiap sesi baru
        $newSessionDates = [];

        foreach ($sessionInfos as $index => $info) {
            // Cari hari dalam seminggu yang sama dengan sesi original
            $targetDayOfWeek = $info['day_of_week'];
            $newDate = $newFirstDate->copy();

            // Sesuaikan ke hari dalam seminggu yang sama
            $daysToAdd = ($targetDayOfWeek - $newDate->dayOfWeek + 7) % 7;
            if ($daysToAdd == 0) {
                // Jika kebetulan sudah hari yang sama, tambah 7 hari untuk minggu berikutnya
                // kecuali untuk sesi pertama
                if ($index > 0) {
                    $daysToAdd = 7;
                }
            }

            $newDate->addDays($daysToAdd);

            // Simpan tanggal baru untuk sesi ini
            $newSessionDates[$info['session_number']] = [
                'date' => $newDate->format('Y-m-d'),
                'start_hour' => $info['start_hour'],
                'end_hour' => $info['end_hour']
            ];
        }

        // Log tanggal-tanggal yang akan digunakan
        $dateArray = array_map(function($item) {
            return $item['date'];
        }, $newSessionDates);

        Log::info('Jadwal baru untuk perpanjangan membership:', $dateArray);

        // Buat booking dan session baru
        foreach ($newSessionDates as $sessionNumber => $details) {
            $newSessionDate = $details['date'];
            $newStartTime = Carbon::parse($newSessionDate . ' ' . $details['start_hour']);
            $newEndTime = Carbon::parse($newSessionDate . ' ' . $details['end_hour']);

            // 1. Buat field booking baru
            $newBooking = new FieldBooking();
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


}
