<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Field;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\MembershipSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\MembershipSubscription;

class MembershipController extends Controller
{
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

/**
 * Menyimpan jadwal membership yang dipilih dan menambahkan ke keranjang
 */
/**
 * Menyimpan jadwal membership yang dipilih dan menambahkan ke keranjang
 */
public function saveScheduleToCart(Request $request, $id)
{
    $request->validate([
        'sessions' => 'required|array|size:3',
        'sessions.*.day' => 'required|date|after_or_equal:today',
        'sessions.*.time' => 'required|string',
    ]);

    $membership = Membership::findOrFail($id);

    // Pendekatan alternatif: izinkan maksimal rentang 7 hari antara sesi pertama dan terakhir
    $dates = collect($request->sessions)->pluck('day')->toArray();

    // Urutkan tanggal
    sort($dates);

    // Ambil tanggal paling awal dan paling akhir
    $earliestDate = Carbon::parse($dates[0]);
    $latestDate = Carbon::parse($dates[count($dates) - 1]);

    // Hitung perbedaan hari
    $daysDifference = $earliestDate->diffInDays($latestDate);

    // Jika perbedaan lebih dari 6 hari (lebih dari 1 minggu), validasi gagal
    if ($daysDifference > 6) {
        Log::debug('Validasi rentang tanggal gagal', [
            'tanggal_awal' => $earliestDate->format('Y-m-d'),
            'tanggal_akhir' => $latestDate->format('Y-m-d'),
            'perbedaan_hari' => $daysDifference
        ]);

        return back()->with('error', 'Semua jadwal harus berada dalam rentang maksimal 7 hari');
    }

    // Memeriksa konflik jadwal
    $sessionsData = [];
    foreach ($request->sessions as $sessionData) {
        $date = $sessionData['day'];
        list($startTime, $endTime) = explode(' - ', $sessionData['time']);

        $startDateTime = Carbon::parse("{$date} {$startTime}");
        $endDateTime = Carbon::parse("{$date} {$endTime}");

        // Periksa konflik dengan booking lapangan yang sudah ada
        $isConflict = $this->checkTimeConflict($membership->field_id, $startDateTime, $endDateTime);
        if ($isConflict) {
            return back()->with('error', "Jadwal {$startTime} - {$endTime} pada tanggal {$date} sudah tidak tersedia");
        }

        $sessionsData[] = [
            'date' => $date,
            'start_time' => $startDateTime->format('Y-m-d H:i:s'),
            'end_time' => $endDateTime->format('Y-m-d H:i:s'),
        ];
    }

    // Simpan data sesi ke session untuk digunakan saat checkout
    session()->put('membership_sessions', [
        'membership_id' => $membership->id,
        'sessions' => $sessionsData
    ]);

    // Redirect ke controller cart untuk menambahkan ke keranjang
    return redirect()->route('user.cart.add.membership', ['id' => $membership->id]);
}
    /**
     * Memeriksa konflik waktu dengan booking yang sudah ada
     */
    private function checkTimeConflict($fieldId, $startTime, $endTime)
    {
        // Cek konflik dengan field bookings (termasuk yang berasal dari membership)
        $conflictBookings = DB::table('field_bookings')
            ->where('field_id', $fieldId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                       ->where('end_time', '>', $startTime);
                })
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                       ->where('end_time', '>=', $endTime);
                })
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                       ->where('end_time', '<=', $endTime);
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
        $allSlots = [
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '11:00', 'end' => '12:00'],
            ['start' => '12:00', 'end' => '13:00'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '15:00'],
            ['start' => '15:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '17:00'],
            ['start' => '17:00', 'end' => '18:00'],
            ['start' => '18:00', 'end' => '19:00'],
            ['start' => '19:00', 'end' => '20:00'],
            ['start' => '20:00', 'end' => '21:00'],
            ['start' => '21:00', 'end' => '22:00'],
            ['start' => '22:00', 'end' => '23:00'],
        ];

        return $allSlots;
    }

    /**
     * Mendapatkan slot waktu yang tersedia berdasarkan tanggal
     */
    public function getAvailableTimeSlotsByDate(Request $request, $fieldId)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = $request->date;

        // Ambil semua slot waktu
        $allSlots = $this->getAvailableTimeSlots($fieldId);

        // Dapatkan booking yang sudah ada pada tanggal tersebut
        $bookedSlots = DB::table('field_bookings')
            ->where('field_id', $fieldId)
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
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

                if (
                    ($startTime >= $bookedStart && $startTime < $bookedEnd) ||
                    ($endTime > $bookedStart && $endTime <= $bookedEnd) ||
                    ($startTime <= $bookedStart && $endTime >= $bookedEnd)
                ) {
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
            ->with(['membership', 'membership.field', 'sessions', 'payment'])
            ->firstOrFail();

        return view('users.membership.subscription-detail', compact('subscription'));
    }
}
