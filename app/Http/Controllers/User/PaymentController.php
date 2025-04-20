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
use App\Models\PointRedemption;
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
     * Handle payment finish redirect from Midtrans
     */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $paymentId = $request->payment_id;

        // Jika ada payment_id, maka ini adalah lanjutan pembayaran
        if ($paymentId) {
            $payment = Payment::where('id', $paymentId)
                              ->where('user_id', Auth::id()) // Tambahkan pengecekan kepemilikan
                              ->firstOrFail();
        } else {
            $payment = Payment::where('order_id', $orderId)
                              ->where('user_id', Auth::id()) // Tambahkan pengecekan kepemilikan
                              ->first();
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
        $pointRedemptionId = null; // Tambahkan ini
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

                       if (session()->has('cart_discount')) {
                        $cartDiscount = session('cart_discount');
                        $discountId = $cartDiscount['id'];
                        $discountAmount = $cartDiscount['amount'];

                        // Cek apakah ini diskon dari penukaran poin
                        if (isset($cartDiscount['is_point_redemption']) && $cartDiscount['is_point_redemption']) {
                            $pointRedemptionId = $cartDiscount['point_redemption_id'];

                            // Verifikasi ulang redemption
                            $redemption = PointRedemption::find($pointRedemptionId);

                            if (!$redemption || $redemption->user_id != Auth::id() || $redemption->status !== 'active') {
                                // Voucher tidak valid, hapus dari session
                                session()->forget('cart_discount');
                                return redirect()->route('user.cart.view')->with('error', 'Voucher poin tidak valid atau sudah digunakan');
                            }

                            // Verifikasi apakah voucher masih berlaku
                            if ($redemption->expires_at && Carbon::parse($redemption->expires_at)->isPast()) {
                                session()->forget('cart_discount');
                                return redirect()->route('user.cart.view')->with('error', 'Voucher poin sudah kadaluarsa');
                            }

                            // Re-calculate discount (untuk keamanan)
                            $pointVoucher = $redemption->pointVoucher;
                            $discountAmount = $pointVoucher->calculateDiscount($subtotal);
                        } else {
                            // Verifikasi ulang diskon biasa
                            $discount = Discount::find($discountId);

                            if (!$discount || !$discount->isValidForUser(Auth::id())) {
                                // Diskon tidak valid, hapus dari session
                                session()->forget('cart_discount');
                                return redirect()->route('user.cart.view')->with('error', 'Kupon diskon tidak valid atau sudah tidak dapat digunakan');
                            }

                            // Re-calculate discount (untuk keamanan)
                            $discountAmount = $discount->calculateDiscount($subtotal);
                        }
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
            $snapToken = Snap::getSnapToken($params);

// Create payment record dengan informasi diskon
$payment = Payment::create([
    'order_id' => $orderId,
    'user_id' => Auth::id(),
    'amount' => $totalPrice,
    'discount_id' => $discountId,
    'point_redemption_id' => $pointRedemptionId, // Tambahkan ini
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
    // Cek apakah ini diskon dari redeem points atau diskon biasa
    $discountName = 'Kupon Diskon';

    if ($payment->point_redemption_id) {
        // Jika dari redeem points, ambil data dari point redemption
        $redemption = PointRedemption::find($payment->point_redemption_id);
        if ($redemption && $redemption->pointVoucher) {
            $discountName = 'Voucher Poin: ' . $redemption->pointVoucher->name;
        }
    } else if ($payment->discount) {
        // Jika diskon biasa, gunakan nama diskon
        $discountName = 'Diskon: ' . $payment->discount->name;
    }

    $itemDetails[] = [
        'id' => 'DISCOUNT',
        'price' => -$payment->discount_amount,
        'quantity' => 1,
        'name' => $discountName,
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
               // Cari cart user
    $cart = Cart::where('user_id', $payment->user_id)->first();
    if ($cart) {
        // Hapus semua item dari cart
        CartItem::where('cart_id', $cart->id)->delete();
        Log::info('Cart items cleared for user #' . $payment->user_id . ' after successful payment #' . $payment->id);
    }

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

            if ($payment->transaction_status === 'success' && $previousStatus !== 'success') {
                // Cari cart user
                $cart = Cart::where('user_id', $payment->user_id)->first();
                if ($cart) {
                    // Hapus semua item dari cart
                    CartItem::where('cart_id', $cart->id)->delete();
                    Log::info('Cart items cleared for user #' . $payment->user_id . ' after successful payment #' . $payment->id);
                }

                // Berikan points kepada user (1 point untuk setiap 10000 rupiah)
                $user = User::find($payment->user_id);
                if ($user) {
                    // Kalkulasi points berdasarkan amount pembayaran (1 point untuk setiap 10000 rupiah)
                    $points = floor($payment->original_amount / 10000);
                    $user->points += $points;
                    $user->save();

                    Log::info('Added ' . $points . ' points to user #' . $user->id . ' for payment #' . $payment->id);
                }

                // Proses diskon atau voucher poin
                if ($payment->point_redemption_id) {
                    // Jika ini adalah pembayaran dengan voucher poin
                    $redemption = PointRedemption::find($payment->point_redemption_id);
                    if ($redemption && $redemption->status === 'active') {
                        $redemption->status = 'used';
                        $redemption->used_at = now();
                        $redemption->payment_id = $payment->id;
                        $redemption->save();

                        Log::info('Point redemption #' . $redemption->id . ' marked as used for payment #' . $payment->id);
                    }
                } else if ($payment->discount_id) {
                    // Jika ini adalah pembayaran dengan diskon reguler
                    $existingUsage = DiscountUsage::where('payment_id', $payment->id)
                        ->where('discount_id', $payment->discount_id)
                        ->first();

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
// Update fotografer bookings yang terkait dengan field booking dari membership
if ($payment->transaction_status === 'success') {
    // Update untuk field bookings yang sudah ada di kode Anda
    // ...

    // TAMBAHAN: Update photographer bookings yang terkait dengan membership
    PhotographerBooking::where('payment_id', $payment->id)
        ->where('is_membership', true)
        ->update(['status' => 'confirmed']);

    // TAMBAHAN: Update rental bookings yang terkait dengan membership
    RentalBooking::where('payment_id', $payment->id)
        ->where('is_membership', true)
        ->update(['status' => 'confirmed']);

    Log::info('Updated photographer and rental bookings for payment #' . $payment->id);
}
            // Tambahkan penanganan untuk pembayaran perpanjangan membership
            if (strpos($orderId, 'RENEW-MEM-') === 0 && $payment->transaction_status === 'success') {
                // Panggil metode createNewBookingsForRenewal di MembershipController
                $membershipController = app(MembershipController::class);

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
                            $newEndDate = Carbon::parse($subscription->end_date)->addDays(7);                            $subscription->end_date = $newEndDate;
                            $subscription->save();

                            Log::info('Membership #' . $subscription->id . ' has been renewed until ' . $newEndDate);

                            // Buat booking baru untuk periode berikutnya
                            $membershipController->createNewBookingsForRenewal($subscription);

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

}
