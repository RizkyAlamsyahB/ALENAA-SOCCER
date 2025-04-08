<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\Field;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\FieldBooking;
use App\Models\MembershipSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipExpired;
use App\Mail\MembershipRenewalInvoice;
use App\Mail\MembershipRenewalSuccess;
use App\Models\MembershipSubscription;

class MembershipController extends Controller
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
     * Menampilkan daftar paket membership
     */
    public function index()
    {
        $fields = Field::all();
        $memberships = Membership::where('status', 'active')->get();

        return view('users.membership.index', compact('fields', 'memberships'));
    }

    /**
     * Menampilkan detail membership
     */
    public function show($id)
    {
        $membership = Membership::findOrFail($id);
        $field = $membership->field;

        return view('users.membership.show', compact('membership', 'field'));
    }

    /**
     * Menampilkan form pemilihan jadwal membership
     */
    public function selectSchedule($id)
    {
        $membership = Membership::findOrFail(id: $id);
        $field = $membership->field;

        // Dapatkan slot waktu yang tersedia untuk lapangan ini
        $availableSlots = $this->getAvailableTimeSlots($field->id);

        return view('users.membership.schedule', compact('membership', 'field', 'availableSlots'));
    }

    public function saveScheduleToCart(Request $request, $id)
    {
        Log::debug('Received request data', $request->all());

        try {
            // Gunakan tanggal dari request sebagai referensi untuk validasi
            $todayDate = $request->input('today_date', date('Y-m-d'));

            $request->validate([
                'sessions' => 'required|array|size:3',
                'sessions.*.day' => 'required|date|date_format:Y-m-d', // Hapus after_or_equal:today
                'sessions.*.time' => 'required|string',
            ]);

            $membership = Membership::findOrFail($id);

            // Periksa duplikasi jadwal
            $sessions = collect($request->sessions);
            $uniqueSessions = $sessions->unique(function ($session) {
                return $session['day'] . '|' . $session['time'];
            });

            if ($uniqueSessions->count() < $sessions->count()) {
                return back()->with('error', 'Tidak boleh ada jadwal yang sama (hari dan jam yang sama)');
            }

            // Pendekatan alternatif: rentang 7 hari dari tanggal paling awal
            $dates = $sessions->pluck('day')->toArray();

            // Urutkan tanggal
            sort($dates);

            // Log tanggal untuk debugging
            Log::debug('Sorted dates', $dates);

            // Ambil tanggal paling awal dan paling akhir
            $earliestDate = Carbon::parse($dates[0]);
            $latestDate = Carbon::parse($dates[count($dates) - 1]);

            // Hitung perbedaan hari
            $daysDifference = $earliestDate->diffInDays($latestDate);

            // Jika perbedaan lebih dari 6 hari (lebih dari minggu yang dipilih), validasi gagal
            if ($daysDifference > 6) {
                Log::debug('Validasi rentang tanggal gagal', [
                    'tanggal_awal' => $earliestDate->format('Y-m-d'),
                    'tanggal_akhir' => $latestDate->format('Y-m-d'),
                    'perbedaan_hari' => $daysDifference,
                ]);

                return back()->with('error', 'Semua jadwal harus berada dalam rentang maksimal 7 hari');
            }

            // Memeriksa konflik jadwal
            $sessionsData = [];
            $sessionNumber = 1; // Inisialisasi nomor sesi

            foreach ($request->sessions as $sessionData) {
                $date = $sessionData['day'];
                [$startTime, $endTime] = explode(' - ', $sessionData['time']);

                // Debug data yang diterima
                Log::debug('Processing session', [
                    'date' => $date,
                    'time' => $sessionData['time'],
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                ]);

                try {
                    // Set waktu lokal dengan jelas
                    $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startTime}", 'Asia/Jakarta');
                    $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$endTime}", 'Asia/Jakarta');

                    // Log untuk debug
                    Log::debug('Created datetime objects', [
                        'start' => $startDateTime->toDateTimeString(),
                        'end' => $endDateTime->toDateTimeString(),
                        'timezone' => $startDateTime->timezone->getName(),
                    ]);
                } catch (\Exception $e) {
                    // Log error parsing
                    Log::error('Error parsing dates: ' . $e->getMessage(), [
                        'date' => $date,
                        'startTime' => $startTime,
                        'endTime' => $endTime,
                    ]);

                    throw new \Exception('Format tanggal atau waktu tidak valid: ' . $e->getMessage());
                }

                // Periksa konflik dengan booking lapangan yang sudah ada
                $isConflict = $this->checkTimeConflict($membership->field_id, $startDateTime, $endDateTime);
                if ($isConflict) {
                    return back()->with('error', "Jadwal {$startTime} - {$endTime} pada tanggal {$date} sudah tidak tersedia");
                }

                $sessionsData[] = [
                    'date' => $date,
                    'start_time' => $startDateTime->format('Y-m-d H:i:s'),
                    'end_time' => $endDateTime->format('Y-m-d H:i:s'),
                    'session_number' => $sessionNumber++, // Tambahkan nomor sesi dan increment
                ];
            }

            // Debug data sesi yang sudah diolah
            Log::debug('Processed sessions data:', $sessionsData);

            // Simpan data sesi ke session untuk digunakan saat checkout
            session()->put('membership_sessions', [
                'membership_id' => $membership->id,
                'sessions' => $sessionsData,
            ]);

            // Debug simpan ke session
            Log::debug('Saved session data', [
                'membership_id' => $membership->id,
                'sessions' => $sessionsData,
            ]);

            // Redirect ke controller cart untuk menambahkan ke keranjang
            return redirect()->route('user.cart.add.membership', ['id' => $membership->id]);
        } catch (\Exception $e) {
            Log::error('Error in saveScheduleToCart: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memeriksa konflik waktu dengan booking yang sudah ada
     */
    private function checkTimeConflict($fieldId, $startTime, $endTime)
    {
        // Cek konflik dengan field bookings (termasuk yang berasal dari membership)
        // Hapus cek status 'on_hold' karena sudah tidak digunakan lagi
        $conflictBookings = DB::table('field_bookings')
            ->where('field_id', $fieldId)
            ->whereIn('status', ['pending', 'confirmed']) // hapus 'on_hold' dari sini
            ->where(function ($query) use ($startTime, $endTime) {
                $query
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)->where('end_time', '>', $startTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)->where('end_time', '>=', $endTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '>=', $startTime)->where('end_time', '<=', $endTime);
                    });
            })
            ->exists();

        return $conflictBookings;
    }

    /**
     * Mendapatkan slot waktu yang tersedia
     */
    private function getAvailableTimeSlots($fieldId)
    {
        // Definisikan semua slot waktu (1 jam per slot)
        $allSlots = [['start' => '08:00', 'end' => '09:00'], ['start' => '09:00', 'end' => '10:00'], ['start' => '10:00', 'end' => '11:00'], ['start' => '11:00', 'end' => '12:00'], ['start' => '12:00', 'end' => '13:00'], ['start' => '13:00', 'end' => '14:00'], ['start' => '14:00', 'end' => '15:00'], ['start' => '15:00', 'end' => '16:00'], ['start' => '16:00', 'end' => '17:00'], ['start' => '17:00', 'end' => '18:00'], ['start' => '18:00', 'end' => '19:00'], ['start' => '19:00', 'end' => '20:00'], ['start' => '20:00', 'end' => '21:00'], ['start' => '21:00', 'end' => '22:00'], ['start' => '22:00', 'end' => '23:00']];

        return $allSlots;
    }

    /**
     * Mendapatkan slot waktu yang tersedia berdasarkan tanggal
     */
    public function getAvailableTimeSlotsByDate(Request $request, $fieldId)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;

        // Ambil semua slot waktu
        $allSlots = $this->getAvailableTimeSlots($fieldId);

        // Dapatkan booking yang sudah ada pada tanggal tersebut
        // Hapus status 'on_hold' dari query
        $bookedSlots = DB::table('field_bookings')
            ->where('field_id', $fieldId)
            ->whereDate('start_time', $date)
            ->whereIn('status', ['pending', 'confirmed']) // hapus 'on_hold'
            ->get(['start_time', 'end_time']);

        // Filter slot yang tersedia
        $availableSlots = [];
        foreach ($allSlots as $slot) {
            $startTime = Carbon::parse("{$date} {$slot['start']}");
            $endTime = Carbon::parse("{$date} {$slot['end']}");
            $isAvailable = true;

            foreach ($bookedSlots as $bookedSlot) {
                $bookedStart = Carbon::parse($bookedSlot->start_time);
                $bookedEnd = Carbon::parse($bookedSlot->end_time);

                if (($startTime >= $bookedStart && $startTime < $bookedEnd) || ($endTime > $bookedStart && $endTime <= $bookedEnd) || ($startTime <= $bookedStart && $endTime >= $bookedEnd)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSlots[] = $slot;
            }
        }

        return response()->json($availableSlots);
    }

    /**
     * Menampilkan riwayat membership user
     */
    public function myMemberships()
    {
        $subscriptions = MembershipSubscription::where('user_id', Auth::id())
            ->with(['membership', 'membership.field', 'sessions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.membership.my-memberships', compact('subscriptions'));
    }

    /**
     * Menampilkan detail langganan membership
     */
    public function subscriptionDetail($id)
    {
        $subscription = MembershipSubscription::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['membership', 'membership.field', 'payment'])
            ->firstOrFail();

        // Load sessions
        $subscription->load('sessions');

        // Update status sesi yang sudah lewat
        $now = Carbon::now();
        foreach ($subscription->sessions as $session) {
            if ($session->status === 'scheduled' && $now > Carbon::parse($session->end_time)) {
                $session->status = 'completed';
                $session->save();
            }
        }

        // Reload sessions setelah update
        $subscription->load('sessions');

        // Manual sorting of sessions by date first, then by time
        $sortedSessions = $subscription->sessions->sortBy([
            ['start_time', 'asc']
        ]);

        // Replace the collection with the sorted one
        $subscription->setRelation('sessions', $sortedSessions);

        return view('users.membership.subscription-detail', compact('subscription'));
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
                $expiresAt = Carbon::parse($lastSession->end_time);
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
        $snapToken = Snap::getSnapToken($params);

        return view('users.membership.renewal_payment', [
            'payment' => $payment,
            'subscription' => $subscription,
            'membership' => $membership,
            'snap_token' => $snapToken,
            'order_id' => $newOrderId, // Kirim order ID baru ke view
        ]);
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
     * Jadwalkan pengiriman invoice perpanjangan membership
     */
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
     * Membuat booking baru untuk perpanjangan membership
     */
    public function createNewBookingsForRenewal(MembershipSubscription $subscription)
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
