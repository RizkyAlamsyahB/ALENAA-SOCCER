<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\User;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Membership;
use App\Models\RentalItem;
use Illuminate\Support\Str;
use App\Models\FieldBooking;
use App\Models\Photographer;
use Illuminate\Http\Request;
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
use App\Mail\PhotographerBookingNotification;
use App\Mail\MembershipRenewalFailedNotification;

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
        $membership = Membership::findOrFail($id);
        $field = $membership->field;

        // Tentukan jumlah jam total yang perlu dipilih berdasarkan tipe membership
        $requiredHours = $this->getRequiredHoursByType($membership->type);

        // Ambil semua slot waktu yang tersedia untuk lapangan ini
        $availableSlots = $this->getAvailableTimeSlots($field->id);

        return view('users.membership.schedule', compact('membership', 'field', 'availableSlots', 'requiredHours'));
    }
    /**
     * Menentukan jumlah jam yang dibutuhkan berdasarkan tipe membership
     */
    private function getRequiredHoursByType($type)
    {
        switch ($type) {
            case 'bronze':
                return 3; // Bronze: 3 sesi × 1 jam = 3 jam total
            case 'silver':
                return 6; // Silver: 3 sesi × 2 jam = 6 jam total
            case 'gold':
                return 9; // Gold: 3 sesi × 3 jam = 9 jam total
            default:
                return 3; // Default ke bronze jika tipe tidak dikenali
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
    $carbonDate = Carbon::parse($date);

    // Get current time untuk comparison
    $now = Carbon::now();
    $isToday = $carbonDate->isToday();

    // Ambil semua slot waktu
    $allSlots = $this->getAvailableTimeSlots($fieldId);

    // Dapatkan booking yang sudah ada pada tanggal tersebut
    $bookedSlots = DB::table('field_bookings')
        ->where('field_id', $fieldId)
        ->whereDate('start_time', $date)
        ->whereIn('status', ['pending', 'confirmed'])
        ->get(['start_time', 'end_time']);

    // Filter slot yang tersedia
    $availableSlots = [];
    foreach ($allSlots as $slot) {
        $startTime = Carbon::parse("{$date} {$slot['start']}");
        $endTime = Carbon::parse("{$date} {$slot['end']}");
        $isAvailable = true;
        $isPastTime = false;

        // PERBAIKAN: Cek apakah slot waktu sudah lewat
        if ($isToday) {
            // Jika tanggal yang dipilih adalah hari ini, cek apakah waktu AKHIR slot sudah lewat
            // Slot disable jika waktu end sudah terlewati
            if ($endTime <= $now) {
                $isPastTime = true;
                $isAvailable = false;
            }
        }

        // Jika bukan past time, cek konflik booking
        if (!$isPastTime) {
            foreach ($bookedSlots as $bookedSlot) {
                $bookedStart = Carbon::parse($bookedSlot->start_time);
                $bookedEnd = Carbon::parse($bookedSlot->end_time);

                if (($startTime >= $bookedStart && $startTime < $bookedEnd) ||
                    ($endTime > $bookedStart && $endTime <= $bookedEnd) ||
                    ($startTime <= $bookedStart && $endTime >= $bookedEnd)) {
                    $isAvailable = false;
                    break;
                }
            }
        }

        // Hanya tambahkan slot yang available (bukan past time dan tidak booked)
        if ($isAvailable) {
            $availableSlots[] = [
                'start' => $slot['start'],
                'end' => $slot['end'],
                'display' => $slot['start'] . ' - ' . $slot['end'],
                'date' => $date,
                'value' => $date . '|' . $slot['start'] . ' - ' . $slot['end'] . '|' . $fieldId,
                'is_past_time' => $isPastTime
            ];
        }
    }

    return response()->json($availableSlots);
}

// Tambahkan pengecekan past time di method saveScheduleToCart()
public function saveScheduleToCart(Request $request, $id)
{
    Log::debug('Received request data', $request->all());

    try {
        // Gunakan tanggal dari request sebagai referensi untuk validasi
        $todayDate = $request->input('today_date', date('Y-m-d'));
        $now = Carbon::now();

        // Validasi data yang diterima
        $request->validate([
            'selected_slots' => 'required|array', // Array slot yang dipilih
            'payment_period' => 'required|in:weekly,monthly', // Periode pembayaran
        ]);

        $membership = Membership::findOrFail($id);
        $paymentPeriod = $request->payment_period;

        // Ambil array dari slot yang dipilih
        $selectedSlots = $request->selected_slots;

        // Periksa jumlah slot berdasarkan tipe membership
        $requiredHours = $this->getRequiredHoursByType($membership->type);

        if (count($selectedSlots) != $requiredHours) {
            return back()->with('error', "Untuk paket {$membership->type}, Anda harus memilih total {$requiredHours} jam slot waktu.");
        }

        // Kelompokkan slot berdasarkan tanggal untuk validasi
        $slotsByDate = [];
        $pastTimeSlots = []; // Array untuk slot yang sudah lewat

        foreach ($selectedSlots as $slot) {
            $parts = explode('|', $slot);
            if (count($parts) != 3) {
                return back()->with('error', 'Format slot tidak valid');
            }

            $date = $parts[0];
            $timeRange = $parts[1];
            [$startTime, $endTime] = explode(' - ', $timeRange);

            // PERBAIKAN: Cek past time untuk setiap slot
            try {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startTime}", 'Asia/Jakarta');
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$endTime}", 'Asia/Jakarta');

                // Cek apakah slot sudah lewat
                if ($endDateTime <= $now) {
                    $pastTimeSlots[] = "{$startTime} - {$endTime} pada tanggal {$date}";
                    continue; // Skip slot yang sudah lewat
                }
            } catch (\Exception $e) {
                Log::error('Error parsing dates for past time check: ' . $e->getMessage(), [
                    'date' => $date,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                ]);
                return back()->with('error', 'Format tanggal atau waktu tidak valid: ' . $e->getMessage());
            }

            if (!isset($slotsByDate[$date])) {
                $slotsByDate[$date] = [];
            }

            $slotsByDate[$date][] = [
                'time' => $timeRange,
                'start' => $startTime,
                'end' => $endTime,
            ];
        }

        // Jika ada slot yang past time, return error
        if (!empty($pastTimeSlots)) {
            return back()->with('error', 'Beberapa slot waktu yang dipilih sudah lewat: ' . implode(', ', $pastTimeSlots) . '. Silakan pilih slot waktu yang masih tersedia.');
        }

        // Validasi: semua tanggal harus dalam rentang 7 hari
        $dates = array_keys($slotsByDate);
        sort($dates);

        if (empty($dates)) {
            return back()->with('error', 'Tidak ada tanggal yang valid');
        }

        $earliestDate = Carbon::parse($dates[0]);
        $latestDate = Carbon::parse($dates[count($dates) - 1]);
        $daysDifference = $earliestDate->diffInDays($latestDate);

        if ($daysDifference > 6) {
            return back()->with('error', 'Semua jadwal harus berada dalam rentang maksimal 7 hari');
        }

        // Memeriksa konflik jadwal untuk semua slot yang dipilih (yang tidak past time)
        $conflictingSlots = [];
        foreach ($selectedSlots as $slot) {
            $parts = explode('|', $slot);
            $date = $parts[0];
            $timeRange = $parts[1];
            [$startTime, $endTime] = explode(' - ', $timeRange);

            try {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startTime}", 'Asia/Jakarta');
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$endTime}", 'Asia/Jakarta');

                // Skip cek konflik jika slot sudah past time (sudah difilter di atas)
                if ($endDateTime <= $now) {
                    continue;
                }

                // Periksa konflik dengan booking lapangan yang sudah ada
                $isConflict = $this->checkTimeConflict($membership->field_id, $startDateTime, $endDateTime);
                if ($isConflict) {
                    $conflictingSlots[] = "{$startTime} - {$endTime} pada tanggal {$date}";
                }

                // Periksa ketersediaan fotografer dan rental item jika ada
                $availabilityCheck = $this->checkAvailabilityForMembership($membership->field_id, [$slot], $membership);
                if ($availabilityCheck['error']) {
                    $conflictingSlots[] = "{$startTime} - {$endTime} pada tanggal {$date} ({$availabilityCheck['message']})";
                }
            } catch (\Exception $e) {
                Log::error('Error parsing dates: ' . $e->getMessage(), [
                    'date' => $date,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                ]);

                return back()->with('error', 'Format tanggal atau waktu tidak valid: ' . $e->getMessage());
            }
        }

        if (!empty($conflictingSlots)) {
            return back()->with('error', 'Beberapa slot waktu sudah tidak tersedia: ' . implode(', ', $conflictingSlots));
        }

        // Konversi slot terpilih ke format session untuk disimpan
        $sessionsData = [];
        $sessionNumber = 1;

        foreach ($selectedSlots as $slot) {
            $parts = explode('|', $slot);
            $date = $parts[0];
            $timeRange = $parts[1];
            [$startTime, $endTime] = explode(' - ', $timeRange);

            try {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startTime}", 'Asia/Jakarta');
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$endTime}", 'Asia/Jakarta');

                // Skip slot yang past time
                if ($endDateTime <= $now) {
                    continue;
                }

                $sessionsData[] = [
                    'date' => $date,
                    'start_time' => $startDateTime->format('Y-m-d H:i:s'),
                    'end_time' => $endDateTime->format('Y-m-d H:i:s'),
                    'session_number' => $sessionNumber++,
                ];
            } catch (\Exception $e) {
                Log::error('Error parsing session dates: ' . $e->getMessage());
                return back()->with('error', 'Error saat memproses tanggal: ' . $e->getMessage());
            }
        }

        // Pastikan masih ada session yang valid setelah filter past time
        if (empty($sessionsData)) {
            return back()->with('error', 'Semua slot waktu yang dipilih sudah lewat. Silakan pilih slot waktu yang masih tersedia.');
        }

        // Hitung harga berdasarkan periode pembayaran
        $price = $membership->price; // Harga default mingguan
        if ($paymentPeriod === 'monthly') {
            $price = $membership->price * 4; // Harga bulanan (4 minggu)
        }

        // Simpan data sesi ke session untuk digunakan saat checkout
        session()->put('membership_sessions', [
            'membership_id' => $membership->id,
            'sessions' => $sessionsData,
            'payment_period' => $paymentPeriod,
            'price' => $price,
        ]);

        // Debug simpan ke session
        Log::debug('Saved session data', [
            'membership_id' => $membership->id,
            'sessions' => $sessionsData,
            'payment_period' => $paymentPeriod,
            'price' => $price,
        ]);

        // Redirect ke controller cart untuk menambahkan ke keranjang
        return redirect()->route('user.cart.add.membership', ['id' => $membership->id]);
    } catch (\Exception $e) {
        Log::error('Error in saveScheduleToCart: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

// Tambahkan pengecekan past time di method checkAvailabilityForMembership()
private function checkAvailabilityForMembership($fieldId, $selectedSlots, $membership)
{
    $unavailableSlots = [];
    $includesPhotographer = $membership->includes_photographer && $membership->photographer_id;
    $includesRentalItem = $membership->includes_rental_item && $membership->rental_item_id;
    $now = Carbon::now();

    $photographer = null;
    $rentalItem = null;

    if ($includesPhotographer) {
        $photographer = Photographer::find($membership->photographer_id);
        if (!$photographer) {
            return ['error' => true, 'message' => 'Fotografer tidak ditemukan'];
        }
    }

    if ($includesRentalItem) {
        $rentalItem = RentalItem::find($membership->rental_item_id);
        if (!$rentalItem) {
            return ['error' => true, 'message' => 'Item rental tidak ditemukan'];
        }
    }

    foreach ($selectedSlots as $slot) {
        $parts = explode('|', $slot);
        $date = $parts[0];
        $timeRange = $parts[1];
        [$startTime, $endTime] = explode(' - ', $timeRange);

        try {
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startTime}", 'Asia/Jakarta');
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$endTime}", 'Asia/Jakarta');

            // PERBAIKAN: Cek past time terlebih dahulu
            if ($endDateTime <= $now) {
                $unavailableSlots[] = "{$startTime} - {$endTime} pada tanggal {$date} (waktu sudah lewat)";
                continue;
            }

            // Cek konflik dengan booking lapangan yang sudah ada
            $isFieldConflict = $this->checkTimeConflict($fieldId, $startDateTime, $endDateTime);
            if ($isFieldConflict) {
                $unavailableSlots[] = "{$startTime} - {$endTime} pada tanggal {$date} (lapangan sudah dibooking)";
                continue;
            }

            // Cek ketersediaan fotografer jika membership include fotografer
            if ($includesPhotographer && $photographer) {
                $isPhotographerConflict = $this->checkPhotographerConflict($photographer->id, $startDateTime, $endDateTime);
                if ($isPhotographerConflict) {
                    $unavailableSlots[] = "{$startTime} - {$endTime} pada tanggal {$date} (fotografer tidak tersedia)";
                    continue;
                }
            }

            // Cek ketersediaan rental item jika membership include rental item
            if ($includesRentalItem && $rentalItem) {
                $requiredQuantity = $membership->rental_item_quantity ?? 1;
                $isRentalItemAvailable = $this->checkRentalItemAvailability($rentalItem->id, $startDateTime, $endDateTime, $requiredQuantity);

                if (!$isRentalItemAvailable['available']) {
                    $unavailableSlots[] = "{$startTime} - {$endTime} pada tanggal {$date} (stok {$rentalItem->name} tidak cukup: tersedia {$isRentalItemAvailable['available_quantity']}, dibutuhkan {$requiredQuantity})";
                    continue;
                }
            }

        } catch (\Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage(), [
                'date' => $date,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ]);

            return ['error' => true, 'message' => 'Format tanggal atau waktu tidak valid: ' . $e->getMessage()];
        }
    }

    if (!empty($unavailableSlots)) {
        return [
            'error' => true,
            'message' => 'Beberapa slot waktu tidak tersedia: ' . implode(', ', $unavailableSlots)
        ];
    }

    return ['error' => false];
}


    /**
     * Memeriksa konflik waktu dengan booking fotografer yang sudah ada
     */
    private function checkPhotographerConflict($photographerId, $startTime, $endTime)
    {
        // Cek konflik dengan photographer bookings
        $conflictBookings = DB::table('photographer_bookings')
            ->where('photographer_id', $photographerId)
            ->whereIn('status', ['pending', 'confirmed'])
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
     * Memeriksa ketersediaan rental item
     */
    private function checkRentalItemAvailability($rentalItemId, $startTime, $endTime, $requiredQuantity)
    {
        // Cek item rental yang sudah dipesan pada waktu tersebut
        $bookedQuantity = DB::table('rental_bookings')
            ->where('rental_item_id', $rentalItemId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '>=', $startTime)->where('start_time', '<', $endTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('end_time', '>', $startTime)->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)->where('end_time', '>=', $endTime);
                    });
            })
            ->sum('quantity');

        // Dapatkan jumlah stok total item rental
        $rentalItem = RentalItem::find($rentalItemId);
        if (!$rentalItem) {
            return ['available' => false, 'available_quantity' => 0];
        }

        $availableQuantity = $rentalItem->stock_total - $bookedQuantity;
        $isAvailable = $availableQuantity >= $requiredQuantity;

        return [
            'available' => $isAvailable,
            'available_quantity' => $availableQuantity,
        ];
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

        $now = Carbon::now();

        foreach ($subscription->sessions as $session) {
            if ($session->status === 'scheduled') {
                if ($now->between($session->start_time, $session->end_time)) {
                    $session->status = 'ongoing';
                } elseif ($now->gt($session->end_time)) {
                    $session->status = 'completed';
                }
                $session->save();
            }

            // Optional: bisa tambahkan pengecekan jika ongoing tapi waktu sudah habis
            elseif ($session->status === 'ongoing' && $now->gt($session->end_time)) {
                $session->status = 'completed';
                $session->save();
            }
        }

        // Reload sessions setelah update
        $subscription->load('sessions');

        // Manual sorting of sessions by date first, then by time
        $sortedSessions = $subscription->sessions->sortBy([['start_time', 'asc']]);

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
                $subscription = MembershipSubscription::where('id', $id)->where('user_id', Auth::id())->where('status', 'active')->first();
            } else {
                // Mode direct call (dipanggil dari scheduleMembershipRenewalInvoices)
                if (is_numeric($subscriptionOrRequest)) {
                    $subscription = MembershipSubscription::where('id', $subscriptionOrRequest)->where('status', 'active')->first();
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

            // Set tanggal kedaluwarsa invoice 3 hari dari sekarang
            // Ini untuk memberikan waktu yang cukup untuk pembayaran
            $expiresAt = now()->addDays(3);

            Log::info('Setting invoice expiry (3 days from now)', [
                'subscription_id' => $subscription->id,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ]);

            // Buat record payment
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => $subscription->user_id,
                'amount' => $subscription->membership->price,
                'original_amount' => $subscription->membership->price,
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
        $payment = Payment::where('id', $id)->where('order_id', 'like', 'RENEW-MEM-%')->where('transaction_status', 'pending')->firstOrFail();

        // Cek apakah sudah kadaluarsa
        if (Carbon::parse($payment->expires_at)->isPast()) {
            return redirect()->route('user.membership.my-memberships')->with('error', 'Invoice perpanjangan sudah kedaluarsa');
        }

        // Cek apakah ini adalah pembayaran milik user yang login
        if ($payment->user_id !== Auth::id()) {
            return redirect()->route('user.membership.my-memberships')->with('error', 'Anda tidak memiliki akses ke pembayaran ini');
        }

        // Ekstrak subscription ID dari order_id (format: RENEW-MEM-{subscription_id}-{random}-{timestamp})
        $orderParts = explode('-', $payment->order_id);
        $subscriptionId = isset($orderParts[2]) ? $orderParts[2] : null;

        if (!$subscriptionId) {
            return redirect()->route('user.membership.my-memberships')->with('error', 'Format order ID tidak valid');
        }

        // Cari subscription terkait
        $subscription = MembershipSubscription::find($subscriptionId);

        if (!$subscription) {
            return redirect()->route('user.membership.my-memberships')->with('error', 'Membership tidak ditemukan');
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
            $expiredPayments = Payment::where('payment_type', 'membership_renewal')->where('transaction_status', 'pending')->where('expires_at', '<', $now)->get();

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
     * Mengirim invoice 3 hari sebelum masa berlaku membership berakhir
     */
    public function scheduleMembershipRenewalInvoices()
    {
        // Ambil semua subscription aktif yang akan berakhir dalam 3 hari
        // dan belum dikirim invoice (invoice_sent = false)
        $expiringSubscriptions = MembershipSubscription::where('status', 'active')
            ->where('renewal_status', 'not_due')
            ->where('invoice_sent', false)
            ->where('end_date', '<=', now()->addDays(3))
            ->where('end_date', '>', now())
            ->get();

        $count = 0;

        foreach ($expiringSubscriptions as $subscription) {
            Log::info('Memproses invoice perpanjangan untuk subscription', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'end_date' => $subscription->end_date,
            ]);

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
                        ]),
                    );
                    Log::info('Email invoice perpanjangan berhasil dikirim', [
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'payment_id' => $payment->id,
                        'deadline' => Carbon::parse($payment->expires_at)->format('d M Y H:i'),
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email invoice perpanjangan', [
                        'error' => $e->getMessage(),
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                    ]);
                }
            } else {
                Log::error('Gagal membuat payment untuk invoice perpanjangan', [
                    'subscription_id' => $subscription->id,
                ]);
            }
        }

        return response()->json([
            'message' => $count . ' invoice perpanjangan telah dijadwalkan',
            'subscriptions' => $expiringSubscriptions->pluck('id'),
        ]);
    }

    /**
     * Membuat booking baru untuk perpanjangan membership
     */
    public function createNewBookingsForRenewal(MembershipSubscription $subscription)
    {
        try {
            // Ambil data membership
            $membership = $subscription->membership;
            if (!$membership) {
                Log::error('Membership tidak ditemukan untuk subscription #' . $subscription->id);
                return;
            }

            // Tentukan jumlah jam yang dibutuhkan berdasarkan tipe membership
            $requiredHours = $this->getRequiredHoursByType($membership->type);

            // Ambil sesi membership yang ada sesuai jumlah jam yang dibutuhkan
            $existingSessions = MembershipSession::where('membership_subscription_id', $subscription->id)->orderBy('session_number')->take($requiredHours)->get();

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

            // Ambil field ID
            $fieldId = $membership->field_id;

            // Ambil data fotografer dan rental item jika dibutuhkan
            $photographer = null;
            $rentalItem = null;
            $requiredRentalQuantity = 0;

            // Cek apakah membership include fotografer
            if ($membership->includes_photographer && $membership->photographer_id) {
                $photographer = Photographer::find($membership->photographer_id);
                if (!$photographer) {
                    Log::error('Fotografer tidak ditemukan untuk perpanjangan membership #' . $subscription->id);
                    return;
                }
            }

            // Cek apakah membership include rental item
            if ($membership->includes_rental_item && $membership->rental_item_id) {
                $rentalItem = RentalItem::find($membership->rental_item_id);
                if (!$rentalItem) {
                    Log::error('Item rental tidak ditemukan untuk perpanjangan membership #' . $subscription->id);
                    return;
                }
                $requiredRentalQuantity = $membership->rental_item_quantity ?? 1;
            }

            // Tentukan tanggal mulai perpanjangan (7 hari setelah end_date saat ini)
            $renewalStartDate = Carbon::parse($subscription->end_date)->subDays(6);

            // Siapkan array untuk sesi baru
            $newSessions = [];

            // Gunakan pola waktu dari sesi yang ada untuk membuat sesi baru
            foreach ($existingSessions as $index => $session) {
                // Ambil jam dari sesi yang ada
                $origStartTime = Carbon::parse($session->start_time);
                $origEndTime = Carbon::parse($session->end_time);
                $startHour = $origStartTime->format('H:i');
                $endHour = $origEndTime->format('H:i');

                $newDate = $renewalStartDate->copy();

                $newSessions[] = [
                    'session_number' => $index + 1,
                    'date' => $newDate->format('Y-m-d'),
                    'start_hour' => $startHour,
                    'end_hour' => $endHour,
                ];
            }

            // Log tanggal-tanggal yang akan digunakan
            $dateArray = array_map(function ($item) {
                return $item['date'];
            }, $newSessions);

            Log::info('Jadwal baru untuk perpanjangan membership:', $dateArray);
            Log::info('Jumlah sesi yang dibuat: ' . count($newSessions) . ' untuk tipe membership ' . $membership->type);

            // TAMBAHAN BARU: Validasi ketersediaan semua sumber daya
            $unavailableSessions = [];
            foreach ($newSessions as $sessionData) {
                $newSessionDate = $sessionData['date'];
                $newStartTime = Carbon::parse($newSessionDate . ' ' . $sessionData['start_hour']);
                $newEndTime = Carbon::parse($newSessionDate . ' ' . $sessionData['end_hour']);

                // Cek ketersediaan lapangan
                $isFieldBooked = FieldBooking::where('field_id', $fieldId)
                    ->where(function ($query) use ($newStartTime, $newEndTime) {
                        $query
                            ->where(function ($q) use ($newStartTime, $newEndTime) {
                                $q->where('start_time', '<=', $newStartTime)->where('end_time', '>', $newStartTime);
                            })
                            ->orWhere(function ($q) use ($newStartTime, $newEndTime) {
                                $q->where('start_time', '<', $newEndTime)->where('end_time', '>=', $newEndTime);
                            })
                            ->orWhere(function ($q) use ($newStartTime, $newEndTime) {
                                $q->where('start_time', '>=', $newStartTime)->where('end_time', '<=', $newEndTime);
                            });
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($isFieldBooked) {
                    $unavailableSessions[] = 'Lapangan pada ' . $newStartTime->format('d M Y H:i') . ' - ' . $newEndTime->format('H:i');
                    continue;
                }

                // Cek ketersediaan fotografer jika include fotografer
                if ($photographer) {
                    $isPhotographerBooked = PhotographerBooking::where('photographer_id', $photographer->id)
                        ->where(function ($query) use ($newStartTime, $newEndTime) {
                            $query
                                ->where(function ($q) use ($newStartTime, $newEndTime) {
                                    $q->where('start_time', '<=', $newStartTime)->where('end_time', '>', $newStartTime);
                                })
                                ->orWhere(function ($q) use ($newStartTime, $newEndTime) {
                                    $q->where('start_time', '<', $newEndTime)->where('end_time', '>=', $newEndTime);
                                })
                                ->orWhere(function ($q) use ($newStartTime, $newEndTime) {
                                    $q->where('start_time', '>=', $newStartTime)->where('end_time', '<=', $newEndTime);
                                });
                        })
                        ->where('status', '!=', 'cancelled')
                        ->exists();

                    if ($isPhotographerBooked) {
                        $unavailableSessions[] = 'Fotografer ' . $photographer->name . ' pada ' . $newStartTime->format('d M Y H:i') . ' - ' . $newEndTime->format('H:i');
                        continue;
                    }
                }

                // Cek ketersediaan rental item jika include rental item
                if ($rentalItem && $requiredRentalQuantity > 0) {
                    $bookedQuantity = RentalBooking::where('rental_item_id', $rentalItem->id)
                        ->whereNotIn('status', ['cancelled'])
                        ->where(function ($query) use ($newStartTime, $newEndTime) {
                            $query
                                ->where(function ($q) use ($newStartTime, $newEndTime) {
                                    $q->where('start_time', '>=', $newStartTime)->where('start_time', '<', $newEndTime);
                                })
                                ->orWhere(function ($q) use ($newStartTime, $newEndTime) {
                                    $q->where('end_time', '>', $newStartTime)->where('end_time', '<=', $newEndTime);
                                })
                                ->orWhere(function ($q) use ($newStartTime, $newEndTime) {
                                    $q->where('start_time', '<=', $newStartTime)->where('end_time', '>=', $newEndTime);
                                });
                        })
                        ->sum('quantity');

                    $availableQuantity = $rentalItem->stock_total - $bookedQuantity;

                    if ($requiredRentalQuantity > $availableQuantity) {
                        $unavailableSessions[] = 'Item ' . $rentalItem->name . ' pada ' . $newStartTime->format('d M Y H:i') . ' - ' . $newEndTime->format('H:i') . ' (Tersedia: ' . $availableQuantity . ', Dibutuhkan: ' . $requiredRentalQuantity . ')';
                        continue;
                    }
                }
            }

            // Jika ada sesi yang tidak tersedia, catat di log dan batalkan perpanjangan
            if (!empty($unavailableSessions)) {
                Log::error('Perpanjangan membership #' . $subscription->id . ' gagal karena konflik: ' . implode(', ', $unavailableSessions));

                // Update status subscription
                $subscription->renewal_status = 'renewal_failed';
                $subscription->renewal_notes = 'Perpanjangan gagal: ' . implode(', ', $unavailableSessions);
                $subscription->save();

                // Kirim email notifikasi ke admin
                try {
                    Mail::to(config('mail.admin_email'))->send(
                        new MembershipRenewalFailedNotification([
                            'subscription' => $subscription,
                            'unavailableSessions' => $unavailableSessions,
                        ]),
                    );
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim notifikasi email: ' . $e->getMessage());
                }

                return;
            }

            // Buat booking dan session baru jika semua validasi berhasil
            foreach ($newSessions as $sessionData) {
                $newSessionDate = $sessionData['date'];
                $newStartTime = Carbon::parse($newSessionDate . ' ' . $sessionData['start_hour']);
                $newEndTime = Carbon::parse($newSessionDate . ' ' . $sessionData['end_hour']);
                $sessionNumber = $sessionData['session_number'];

                // 1. Buat field booking baru
                $newBooking = new FieldBooking();
                $newBooking->user_id = $subscription->user_id;
                $newBooking->field_id = $fieldId;
                $newBooking->payment_id = $paymentId;
                $newBooking->start_time = $newStartTime;
                $newBooking->end_time = $newEndTime;
                $newBooking->total_price = 0; //Sudah termasuk diskon membership
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

                // 4. Buat fotografer booking jika include fotografer
                if ($photographer) {
                    $photographerBooking = new PhotographerBooking();
                    $photographerBooking->user_id = $subscription->user_id;
                    $photographerBooking->photographer_id = $photographer->id;
                    $photographerBooking->payment_id = $paymentId;
                    $photographerBooking->field_booking_id = $newBooking->id;
                    $photographerBooking->membership_session_id = $newSession->id;
                    $photographerBooking->start_time = $newStartTime;
                    $photographerBooking->end_time = $newEndTime;
                    $photographerBooking->price = 0; // Gratis karena sudah termasuk dalam membership
                    $photographerBooking->status = 'confirmed';
                    $photographerBooking->is_membership = true;
                    $photographerBooking->save();

                    // TAMBAHAN KODE: Kirim notifikasi email ke fotografer
                    try {
                        // Ambil data user fotografer
                        $photographerUser = User::find($photographer->user_id);

                        // Ambil data user yang melakukan booking
                        $user = User::find($subscription->user_id);

                        if ($photographerUser && $photographerUser->email) {
                            // Kirim email notifikasi
                            Mail::to($photographerUser->email)->send(new PhotographerBookingNotification($photographerBooking, $photographerUser, $user));

                            // Catat waktu pengiriman notifikasi
                            $photographerBooking->notification_sent_at = now();
                            $photographerBooking->save();

                            Log::info('Photographer booking notification sent for renewal to: ' . $photographerUser->email, [
                                'subscription_id' => $subscription->id,
                                'photographer_id' => $photographer->id,
                                'booking_id' => $photographerBooking->id,
                                'session_number' => $sessionNumber,
                            ]);
                        } else {
                            Log::warning('Photographer user or email not found for renewal booking', [
                                'booking_id' => $photographerBooking->id,
                                'photographer_id' => $photographer->id,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send photographer notification for renewal: ' . $e->getMessage(), [
                            'booking_id' => $photographerBooking->id,
                            'photographer_id' => $photographer->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    Log::info('Membuat booking fotografer untuk perpanjangan membership #' . $subscription->id . ' session #' . $sessionNumber);
                }

                // 5. Buat rental item booking jika include rental item
                if ($rentalItem) {
                    $rentalBooking = new RentalBooking();
                    $rentalBooking->user_id = $subscription->user_id;
                    $rentalBooking->rental_item_id = $rentalItem->id;
                    $rentalBooking->payment_id = $paymentId;
                    $rentalBooking->field_booking_id = $newBooking->id;
                    $rentalBooking->membership_session_id = $newSession->id;
                    $rentalBooking->start_time = $newStartTime;
                    $rentalBooking->end_time = $newEndTime;
                    $rentalBooking->quantity = $membership->rental_item_quantity ?? 1;
                    $rentalBooking->total_price = 0; // Gratis karena sudah termasuk dalam membership
                    $rentalBooking->status = 'confirmed';
                    $rentalBooking->is_membership = true;
                    $rentalBooking->save();

                    Log::info('Membuat booking rental item untuk perpanjangan membership #' . $subscription->id . ' session #' . $sessionNumber);
                }

                Log::info('Membuat jadwal #' . $sessionNumber . ' baru: ' . $newStartTime->format('Y-m-d H:i') . ' - ' . $newEndTime->format('H:i'));
            }

            Log::info('Berhasil membuat jadwal baru untuk perpanjangan membership #' . $subscription->id);
        } catch (\Exception $e) {
            Log::error('Error creating new bookings for renewal: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'trace' => $e->getTraceAsString(),
            ]);

            // Update status subscription jika terjadi error
            try {
                $subscription->renewal_status = 'renewal_error';
                $subscription->renewal_notes = 'Error: ' . $e->getMessage();
                $subscription->save();
            } catch (\Exception $e2) {
                Log::error('Error updating subscription status: ' . $e2->getMessage());
            }
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
        $renewalBookings = FieldBooking::where('field_id', $newBooking->field_id)->where('user_id', $subscription->user_id)->where('is_membership', true)->orderBy('start_time', 'asc')->get();

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
            'position' => $position,
        ]);

        return $position;
    }

    // Tambahkan method baru di MembershipController
    public function manualRenewal($id)
    {
        try {
            Log::info('Memulai perpanjangan manual', ['subscription_id' => $id, 'user_id' => Auth::id()]);

            // Cari subscription yang akan diperpanjang
            $subscription = MembershipSubscription::where('id', $id)->where('user_id', Auth::id())->where('status', 'active')->first();

            if (!$subscription) {
                Log::error('Subscription tidak ditemukan', ['id' => $id, 'user_id' => Auth::id()]);
                return redirect()->back()->with('error', 'Membership tidak ditemukan');
            }

            // Cek jika sudah ada invoice pending
            $existingPayment = Payment::where('user_id', Auth::id())
                ->where('order_id', 'like', 'RENEW-MEM-' . $id . '%')
                ->where('transaction_status', 'pending')
                ->first();

            // Jika sudah ada invoice, redirect ke halaman pembayaran
            if ($existingPayment) {
                Log::info('Menggunakan invoice yang sudah ada', ['payment_id' => $existingPayment->id]);
                return redirect()->route('user.membership.renewal.pay', ['id' => $existingPayment->id]);
            }

            // Buat order ID baru
            $orderId = 'RENEW-MEM-' . $id . '-' . substr(md5(uniqid()), 0, 8) . '-' . time();
            $expiresAt = now()->addDays(3);

            // Buat payment baru
            $payment = new Payment();
            $payment->order_id = $orderId;
            $payment->user_id = Auth::id();
            $payment->amount = $subscription->membership->price;
            $payment->original_amount = $subscription->membership->price;
            $payment->transaction_status = 'pending';
            $payment->expires_at = $expiresAt;
            $payment->payment_type = 'membership_renewal';
            $payment->save();

            // Update status subscription
            $subscription->renewal_status = 'renewal_pending';
            $subscription->next_invoice_date = now();
            $subscription->save();

            Log::info('Berhasil membuat invoice perpanjangan', [
                'payment_id' => $payment->id,
                'subscription_id' => $id,
            ]);

            // Redirect ke halaman pembayaran
            return redirect()->route('user.membership.renewal.pay', ['id' => $payment->id]);
        } catch (\Exception $e) {
            Log::error('Error saat perpanjangan manual: ' . $e->getMessage(), [
                'subscription_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
