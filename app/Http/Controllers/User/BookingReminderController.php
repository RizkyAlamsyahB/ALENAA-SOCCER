<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\PhotographerBooking;
use App\Models\MembershipSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingReminderController extends Controller
{
    public function upcomingBookings()
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $nextWeek = $now->copy()->addWeek();

        // Ambil semua booking mendatang
        $fieldBookings = FieldBooking::with('field')
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->whereBetween('start_time', [$now, $nextWeek])
            ->orderBy('start_time')
            ->get();

        $rentalBookings = RentalBooking::with('rentalItem')
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->whereBetween('start_time', [$now, $nextWeek])
            ->orderBy('start_time')
            ->get();

        $photographerBookings = PhotographerBooking::with('photographer')
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->whereBetween('start_time', [$now, $nextWeek])
            ->orderBy('start_time')
            ->get();

        $membershipSessions = MembershipSession::with(['subscription.membership', 'fieldBooking.field'])
            ->whereHas('subscription', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'scheduled')
            ->whereBetween('start_time', [$now, $nextWeek])
            ->orderBy('start_time')
            ->get();

        return view('users.bookings.upcoming', compact(
            'fieldBookings',
            'rentalBookings',
            'photographerBookings',
            'membershipSessions'
        ));
    }

    public function updateReminderPreferences(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'reminder_24hours' => $request->has('reminder_24hours'),
            'reminder_1hour' => $request->has('reminder_1hour'),
            'reminder_30minutes' => $request->has('reminder_30minutes'),
        ]);

        return back()->with('success', 'Preferensi reminder berhasil diperbarui');
    }
}
