<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Field;
use App\Models\FieldBooking;
use App\Models\MembershipSession;
use App\Models\MembershipSubscription;
use App\Models\PhotographerBooking;
use App\Models\RentalBooking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
    /**
     * Tampilkan halaman kalender jadwal
     */
    public function index()
    {
        $fields = Field::all();
        return view('admin.schedule.index', compact('fields'));
    }

    /**
     * Tampilkan halaman jadwal detail lapangan
     */
    public function fieldSchedule($fieldId)
    {
        $field = Field::findOrFail($fieldId);
        $bookings = FieldBooking::where('field_id', $fieldId)
                    ->where('status', '!=', 'cancelled')
                    ->whereDate('start_time', '>=', Carbon::today())
                    ->orderBy('start_time')
                    ->get();

        return view('admin.schedule.field-schedule', compact('field', 'bookings'));
    }

    /**
     * Tampilkan halaman khusus jadwal membership
     */
    public function membershipSchedule()
    {
        $memberships = MembershipSubscription::with('user', 'membership')
                        ->where('status', 'active')
                        ->get();

        return view('admin.schedule.membership-schedule', compact('memberships'));
    }

/**
 * Tampilkan halaman detail jadwal membership
 */
public function membershipDetail($id)
{
    $subscription = MembershipSubscription::with('user', 'membership', 'sessions.fieldBooking')
                    ->findOrFail($id);

    // Update status sesi jika sudah berlalu
    $now = Carbon::now();
    foreach ($subscription->sessions as $session) {
        if ($session->status === 'scheduled') {
            if ($now->between($session->start_time, $session->end_time)) {
                $session->status = 'ongoing';
                $session->save();
            } elseif ($now->gt($session->end_time)) {
                $session->status = 'completed';
                $session->save();
            }
        } elseif ($session->status === 'ongoing' && $now->gt($session->end_time)) {
            $session->status = 'completed';
            $session->save();
        }
    }

    // Reload sessions setelah update
    $subscription->load('sessions');

    // Urutkan sesi berdasarkan waktu mulai
    $sortedSessions = $subscription->sessions->sortBy('start_time');
    $subscription->setRelation('sessions', $sortedSessions);

    return view('admin.schedule.membership-detail', compact('subscription'));
}

    /**
     * Ambil data jadwal dalam format JSON untuk kalender
     */
    public function getScheduleEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $fieldId = $request->input('field_id');

        $query = FieldBooking::with(['user', 'field']);

        // Filter berdasarkan lapangan jika ada
        if ($fieldId) {
            $query->where('field_id', $fieldId);
        }

        // Filter berdasarkan rentang tanggal
        $query->where(function($q) use ($start, $end) {
            $q->whereBetween('start_time', [$start, $end])
              ->orWhereBetween('end_time', [$start, $end])
              ->orWhere(function($query) use ($start, $end) {
                  $query->where('start_time', '<=', $start)
                        ->where('end_time', '>=', $end);
              });
        });

        // Hanya ambil booking yang aktif
        $query->whereIn('status', ['pending', 'confirmed']);

        $bookings = $query->get();

        $events = [];

        foreach ($bookings as $booking) {
            // Tentukan warna berdasarkan status
            $color = $booking->status === 'confirmed' ? '#28a745' : '#ffc107';

            // Warna khusus untuk booking dari membership
            if ($booking->is_membership) {
                $color = '#007bff';
            }

            // Tentukan label berdasarkan status
            $statusLabel = $booking->status === 'confirmed' ? 'Confirmed' : 'Pending';
            if ($booking->is_membership) {
                $statusLabel = 'Membership';
            }

            $events[] = [
                'id' => $booking->id,
                'title' => $booking->user->name . ' - ' . $booking->field->name . ' (' . $statusLabel . ')',
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'color' => $color,
                'extendedProps' => [
                    'user_id' => $booking->user_id,
                    'user_name' => $booking->user->name,
                    'field_id' => $booking->field_id,
                    'field_name' => $booking->field->name,
                    'status' => $booking->status,
                    'is_membership' => $booking->is_membership,
                    'membership_session_id' => $booking->membership_session_id,
                ]
            ];
        }

        return response()->json($events);
    }

/**
 * Mengambil detail booking untuk ditampilkan di modal
 */
public function getBookingDetail($id)
{
    // Tambahkan eager loading untuk rental items
    $booking = FieldBooking::with([
        'user',
        'field',
        'payment',
        'membershipSession.subscription.membership',
        'photographerBookings.photographer', // Eager load photographer
        'rentalBookings.rentalItem'          // Eager load rental items dengan relasinya
    ])->findOrFail($id);

    // Kembalikan data lengkap tanpa perlu query terpisah
    return response()->json([
        'booking' => $booking
    ]);
}

/**
 * Tampilkan halaman jadwal semua booking dalam bentuk tabel
 */
public function allBookingsTable(Request $request)
{
    if ($request->ajax()) {
        $bookings = FieldBooking::with(['user', 'field', 'membershipSession.subscription.membership']);

        return DataTables::of($bookings)
            ->addColumn('user_name', function ($booking) {
                return $booking->user->name;
            })
            ->addColumn('field_name', function ($booking) {
                return $booking->field->name;
            })
            ->addColumn('booking_type', function ($booking) {
                return $booking->is_membership ? 'Membership' : 'Regular';
            })
            ->addColumn('membership_info', function ($booking) {
                if ($booking->is_membership && $booking->membershipSession) {
                    $subscription = $booking->membershipSession->subscription;
                    if ($subscription && $subscription->membership) {
                        return $subscription->membership->name . ' (Session ' . $booking->membershipSession->session_number . ')';
                    }
                }
                return '-';
            })
            ->editColumn('start_time', function ($booking) {
                return Carbon::parse($booking->start_time)->format('d M Y H:i');
            })
            ->editColumn('end_time', function ($booking) {
                return Carbon::parse($booking->end_time)->format('H:i');
            })
            ->editColumn('status', function ($booking) {
                $statusClass = '';
                switch ($booking->status) {
                    case 'confirmed':
                        $statusClass = 'bg-success';
                        break;
                    case 'pending':
                        $statusClass = 'bg-warning';
                        break;
                    case 'cancelled':
                        $statusClass = 'bg-danger';
                        break;
                    default:
                        $statusClass = 'bg-secondary';
                }
                return '<span class="badge ' . $statusClass . '">' . ucfirst($booking->status) . '</span>';
            })
            ->addColumn('action', function ($booking) {
                return '<div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-info view-details" data-id="' . $booking->id . '">Detail</button>
                    </div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    return view('admin.schedule.all-bookings');
}

    // /**
    //  * Halaman edit status booking
    //  */
    // public function editBooking($id)
    // {
    //     $booking = FieldBooking::with(['user', 'field', 'membershipSession.membershipSubscription.membership'])->findOrFail($id);
    //     return view('admin.schedule.edit-booking', compact('booking'));
    // }

    // /**
    //  * Update status booking
    //  */
    // public function updateBooking(Request $request, $id)
    // {
    //     $booking = FieldBooking::findOrFail($id);

    //     $request->validate([
    //         'status' => 'required|in:pending,confirmed,cancelled',
    //         'notes' => 'nullable|string|max:255',
    //     ]);

    //     $booking->status = $request->status;
    //     $booking->notes = $request->notes;

    //     // Jika status dibatalkan dan alasan pembatalan disediakan
    //     if ($request->status === 'cancelled' && $request->filled('cancellation_reason')) {
    //         $booking->cancellation_reason = $request->cancellation_reason;
    //     }

    //     $booking->save();

    //     // Update status membership session jika ini adalah booking dari membership
    //     if ($booking->is_membership && $booking->membership_session_id) {
    //         $session = MembershipSession::find($booking->membership_session_id);
    //         if ($session) {
    //             $session->status = $request->status === 'cancelled' ? 'cancelled' : 'scheduled';
    //             $session->save();
    //         }
    //     }

    //     // Update status booking fotografer terkait jika ada
    //     $photographerBooking = PhotographerBooking::where('field_booking_id', $booking->id)->first();
    //     if ($photographerBooking) {
    //         $photographerBooking->status = $request->status;
    //         $photographerBooking->save();
    //     }

    //     // Update status booking rental terkait jika ada
    //     $rentalBookings = RentalBooking::where('field_booking_id', $booking->id)->get();
    //     foreach ($rentalBookings as $rentalBooking) {
    //         $rentalBooking->status = $request->status;
    //         $rentalBooking->save();
    //     }

    //     return redirect()->route('admin.schedule.all-bookings')->with('success', 'Status booking berhasil diperbarui');
    // }
}
