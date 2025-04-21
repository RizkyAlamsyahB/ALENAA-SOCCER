<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerBooking;
use App\Models\FieldBooking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function dashboard()
    {
        $photographer = Auth::user()->photographer;
        $upcomingPhotographerBookings = PhotographerBooking::where('photographer_id', $photographer->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('start_time', '>=', Carbon::today())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Jika fotografer juga ditugaskan ke lapangan tertentu
        $field = $photographer->assignedField;
        $upcomingFieldBookings = [];

        if ($field) {
            $upcomingFieldBookings = FieldBooking::where('field_id', $field->id)
                ->where('status', '!=', 'cancelled')
                ->whereDate('start_time', '>=', Carbon::today())
                ->orderBy('start_time')
                ->limit(5)
                ->get();
        }

        return view('photographer.dashboard', compact('photographer', 'upcomingPhotographerBookings', 'field', 'upcomingFieldBookings'));
    }

    public function schedule()
    {
        $photographer = Auth::user()->photographer;

        // Ambil booking fotografer langsung
        $photographerBookings = PhotographerBooking::where('photographer_id', $photographer->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('start_time', '>=', Carbon::today())
            ->orderBy('start_time')
            ->get();

        // Jika fotografer juga ditugaskan ke lapangan
        $field = $photographer->assignedField;
        $fieldBookings = [];

        if ($field) {
            $fieldBookings = FieldBooking::where('field_id', $field->id)
                ->where('status', '!=', 'cancelled')
                ->whereDate('start_time', '>=', Carbon::today())
                ->orderBy('start_time')
                ->get();
        }

        // Gabungkan kedua jenis booking
        $allBookings = [];

        foreach ($photographerBookings as $booking) {
            $allBookings[] = [
                'id' => $booking->id,
                'type' => 'photographer',
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user' => $booking->user->name,
                'status' => $booking->status
            ];
        }

        foreach ($fieldBookings as $booking) {
            $allBookings[] = [
                'id' => $booking->id,
                'type' => 'field',
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user' => $booking->user->name,
                'status' => $booking->status
            ];
        }

        // Urutkan semua booking berdasarkan waktu mulai
        usort($allBookings, function($a, $b) {
            return $a['start_time'] <=> $b['start_time'];
        });

        return view('photographer.schedule', compact('photographer', 'allBookings'));
    }
}
