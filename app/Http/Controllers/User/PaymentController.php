<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
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
     * Checkout page after booking from cart
     */

    public function checkout(Request $request)
    {
        // Menerima berbagai jenis item IDs
        $fieldBookingIds = $request->field_bookings ?? [];
        $rentalBookingIds = $request->rental_bookings ?? [];
        $membershipIds = $request->memberships ?? [];
        $photographerBookingIds = $request->photographer_bookings ?? [];

        if (
            empty($fieldBookingIds) && empty($rentalBookingIds) &&
            empty($membershipIds) && empty($photographerBookingIds)
        ) {
            return redirect()->route('user.cart.view')
                ->with('error', 'Tidak ada item yang dipilih untuk checkout');
        }

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
                return redirect()->route('user.cart.view')
                    ->with('error', 'Booking lapangan tidak ditemukan atau status telah berubah');
            }

            // Proses rental bookings
            $rentalBookings = RentalBooking::whereIn('id', $rentalBookingIds)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            if (count($rentalBookingIds) > 0 && $rentalBookings->isEmpty()) {
                DB::rollBack();
                return redirect()->route('user.cart.view')
                    ->with('error', 'Booking penyewaan tidak ditemukan atau status telah berubah');
            }

            // Proses membership subscriptions
            $membershipSubscriptions = MembershipSubscription::whereIn('id', $membershipIds)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            if (count($membershipIds) > 0 && $membershipSubscriptions->isEmpty()) {
                DB::rollBack();
                return redirect()->route('user.cart.view')
                    ->with('error', 'Membership tidak ditemukan atau status telah berubah');
            }

            // Proses photographer bookings
            $photographerBookings = PhotographerBooking::whereIn('id', $photographerBookingIds)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            if (count($photographerBookingIds) > 0 && $photographerBookings->isEmpty()) {
                DB::rollBack();
                return redirect()->route('user.cart.view')
                    ->with('error', 'Booking fotografer tidak ditemukan atau status telah berubah');
            }

            // Kalkulasi total harga dari semua jenis item
            $totalPrice = $fieldBookings->sum('total_price') +
                $rentalBookings->sum('total_price') +
                $membershipSubscriptions->sum('price') +
                $photographerBookings->sum('price');

            // Periksa ketersediaan field bookings
            foreach ($fieldBookings as $booking) {
                $conflictingBooking = FieldBooking::where('field_id', $booking->field_id)
                    ->where('id', '!=', $booking->id)
                    ->where('status', 'confirmed')
                    ->where(function ($query) use ($booking) {
                        // Cek overlap waktu
                        $query->where(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                                ->where('end_time', '>', $booking->start_time);
                        })->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<', $booking->end_time)
                                ->where('end_time', '>=', $booking->end_time);
                        })->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '>=', $booking->start_time)
                                ->where('end_time', '<=', $booking->end_time);
                        });
                    })
                    ->lockForUpdate()
                    ->first();

                if ($conflictingBooking) {
                    DB::rollBack();
                    return redirect()->route('user.cart.view')
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
                        $query->where(function ($q) use ($booking) {
                            $q->where('start_time', '>=', $booking->start_time)
                                ->where('start_time', '<', $booking->end_time);
                        })->orWhere(function ($q) use ($booking) {
                            $q->where('end_time', '>', $booking->start_time)
                                ->where('end_time', '<=', $booking->end_time);
                        })->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                                ->where('end_time', '>=', $booking->end_time);
                        });
                    })
                    ->sum('quantity');

                $rentalItem = $booking->rentalItem;
                $availableQuantity = $rentalItem->stock_total - $bookedQuantity;

                if ($booking->quantity > $availableQuantity) {
                    DB::rollBack();
                    return redirect()->route('user.cart.view')
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
                        $query->where(function ($q) use ($booking) {
                            $q->where('start_time', '<=', $booking->start_time)
                                ->where('end_time', '>', $booking->start_time);
                        })->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '<', $booking->end_time)
                                ->where('end_time', '>=', $booking->end_time);
                        })->orWhere(function ($q) use ($booking) {
                            $q->where('start_time', '>=', $booking->start_time)
                                ->where('end_time', '<=', $booking->end_time);
                        });
                    })
                    ->lockForUpdate()
                    ->first();

                if ($conflictingBooking) {
                    DB::rollBack();
                    return redirect()->route('user.cart.view')
                        ->with('error', 'Maaf, slot untuk fotografer ' . $booking->photographer->name . ' sudah dibooking oleh pengguna lain');
                }
            }

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

            // Add field bookings to item details
            foreach ($fieldBookings as $booking) {
                $field = $booking->field;
                $startTime = Carbon::parse($booking->start_time)->format('d M Y H:i');
                $endTime = Carbon::parse($booking->end_time)->format('H:i');

                $params['item_details'][] = [
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

                $params['item_details'][] = [
                    'id' => 'RENTAL-' . $booking->id,
                    'price' => $booking->total_price,
                    'quantity' => 1,
                    'name' => $rentalItem->name . ' (Jumlah: ' . $booking->quantity . ', ' . $startTime . ' - ' . $endTime . ')',
                ];
            }

            // Add membership subscriptions to item details
            foreach ($membershipSubscriptions as $subscription) {
                $membership = $subscription->membership;

                $params['item_details'][] = [
                    'id' => 'MEMBER-' . $subscription->id,
                    'price' => $subscription->price,
                    'quantity' => 1,
                    'name' => $membership->name . ' (Durasi: ' . $membership->duration . ' bulan)',
                ];
            }

            // Add photographer bookings to item details
            foreach ($photographerBookings as $booking) {
                $photographer = $booking->photographer;
                $startTime = Carbon::parse($booking->start_time)->format('d M Y H:i');
                $endTime = Carbon::parse($booking->end_time)->format('H:i');

                $params['item_details'][] = [
                    'id' => 'PHOTO-' . $booking->id,
                    'price' => $booking->price,
                    'quantity' => 1,
                    'name' => $photographer->name . ' (' . $startTime . ' - ' . $endTime . ')',
                ];
            }

            // Get Snap token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Create payment record
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
                'photographer_bookings' => $photographerBookings
            ];

            DB::commit();
            return view('users.payment.checkout', compact('snapToken', 'payment', 'allBookings'));
        } catch (\PDOException $e) {
            DB::rollBack();
            Log::error('Checkout PDO Error: ' . $e->getMessage());

            // Tangani kesalahan database spesifik
            if (strpos($e->getMessage(), 'deadlock') !== false || strpos($e->getMessage(), 'lock') !== false) {
                return redirect()->route('user.cart.view')
                    ->with('error', 'Sistem sedang sibuk. Silakan coba lagi dalam beberapa saat');
            }

            return redirect()->route('user.cart.view')
                ->with('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        } catch (MidtransApiException $e) {
            DB::rollBack();
            Log::error('Midtrans API Error: ' . $e->getMessage());

            return redirect()->route('user.cart.view')
                ->with('error', 'Terjadi kesalahan pada payment gateway: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->route('user.cart.view')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

        // Redirect ke detail pembayaran
        return redirect()
            ->route('user.payment.detail', ['id' => $payment->id])
            ->with('error', 'Terjadi kesalahan pada pembayaran. Silakan coba lagi.');
    }

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

        // Hanya bisa melanjutkan pembayaran dengan status pending
        if ($payment->transaction_status !== 'pending') {
            return redirect()
                ->route('user.payment.detail', ['id' => $payment->id])
                ->with('error', 'Tidak dapat melanjutkan pembayaran dengan status saat ini.');
        }

        try {
            // Rekonfigurasi Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Siapkan data item untuk Midtrans
            $itemDetails = [];

            // Perlu memproses semua jenis booking
            // Field bookings
            foreach ($payment->fieldBookings as $booking) {
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

            // Rental bookings
            foreach ($payment->rentalBookings as $booking) {
                $rentalItem = $booking->rentalItem;
                $startTime = Carbon::parse($booking->start_time)->format('d M Y H:i');
                $endTime = Carbon::parse($booking->end_time)->format('H:i');

                $itemDetails[] = [
                    'id' => 'RENTAL-' . $booking->id,
                    'price' => $booking->total_price,
                    'quantity' => 1, // Set to 1 so the price doesn't get multiplied again
                    'name' => $rentalItem->name . ' (Jumlah: ' . $booking->quantity . ', ' . $startTime . ' - ' . $endTime . ')',
                ];
            }

            // Membership subscriptions (dikomentari)
            /*
            foreach ($payment->membershipSubscriptions as $subscription) {
                $membership = $subscription->membership;

                $itemDetails[] = [
                    'id' => 'MEMBER-' . $subscription->id,
                    'price' => $subscription->price,
                    'quantity' => 1,
                    'name' => $membership->name . ' (Durasi: ' . $membership->duration . ' bulan)',
                ];
            }
            */

            // Photographer bookings (dikomentari)
            /*
            foreach ($payment->photographerBookings as $booking) {
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
            */

            // Buat koleksi cart_items yang sesuai format yang diharapkan view
            $allBookings = [
                'field_bookings' => $payment->fieldBookings,
                'rental_bookings' => $payment->rentalBookings,
                // 'membership_subscriptions' => $payment->membershipSubscriptions ?? [],  // dikomentari
                // 'photographer_bookings' => $payment->photographerBookings ?? []       // dikomentari
            ];

            // Buat order_id baru dengan menambahkan suffix untuk menghindari duplicate
            $originalOrderId = $payment->order_id;
            $newOrderId = $originalOrderId . '-RETRY-' . time();

            // Buat parameter transaksi dengan order_id baru
            $params = [
                'transaction_details' => [
                    'order_id' => $newOrderId,  // Gunakan order_id baru!
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

            // Kirim ke view dengan format data yang sesuai
            return view('users.payment.checkout', [
                'snap_token' => $snapToken,
                'order_id' => $newOrderId,  // Gunakan order_id baru di view
                'original_order_id' => $originalOrderId,  // Simpan original order_id
                'total_price' => $payment->amount,
                'allBookings' => $allBookings,
                'is_continue_payment' => true,
                'payment_id' => $payment->id  // Sertakan payment_id untuk callback
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
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        return view('users.payment.history', compact('payments'));
    }

    /**
     * Show payment detail
     */
    public function detail($id)
    {
        $payment = Payment::with([
            'fieldBookings.field',
            'rentalBookings.rentalItem',
        ])
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

    /**
     * Download invoice as PDF
     *
     * @param int $id Payment ID
     * @return \Illuminate\Http\Response
     */
    /**
     * Download invoice as PDF
     *
     * @param int $id Payment ID
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice($id)
    {
        // Ambil data payment dengan eager loading untuk semua jenis booking dan user
        $payment = Payment::with([
            'fieldBookings.field',
            'rentalBookings.rentalItem',
            'user'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Only allow downloading invoices for successful payments
        if ($payment->transaction_status !== 'success') {
            return redirect()
                ->route('user.payment.detail', ['id' => $payment->id])
                ->with('error', 'Invoice hanya tersedia untuk pembayaran yang berhasil.');
        }

        // Load view dan teruskan objek payment langsung
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('users.payment.invoice', compact('payment'));

        // Set PDF options
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF for download
        return $pdf->download('Invoice-' . $payment->order_id . '.pdf');
    }
}
