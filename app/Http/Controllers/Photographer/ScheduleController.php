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
        $upcomingPhotographerBookings = PhotographerBooking::where('photographer_id', $photographer->id)->where('status', '!=', 'cancelled')->whereDate('start_time', '>=', Carbon::today())->orderBy('start_time')->limit(5)->get();

        // Jika fotografer juga ditugaskan ke lapangan tertentu
        $field = $photographer->assignedField;
        $upcomingFieldBookings = [];

        if ($field) {
            $upcomingFieldBookings = FieldBooking::where('field_id', $field->id)->where('status', '!=', 'cancelled')->whereDate('start_time', '>=', Carbon::today())->orderBy('start_time')->limit(5)->get();
        }

        return view('photographers.dashboard', compact('photographer', 'upcomingPhotographerBookings', 'field', 'upcomingFieldBookings'));
    }

    public function schedule()
    {
        $photographer = Auth::user()->photographer;

        // Ambil booking fotografer langsung
        $photographerBookings = PhotographerBooking::where('photographer_id', $photographer->id)->where('status', '!=', 'cancelled')->whereDate('start_time', '>=', Carbon::today())->orderBy('start_time')->get();

        // Jika fotografer juga ditugaskan ke lapangan
        $field = $photographer->assignedField;
        $fieldBookings = [];

        if ($field) {
            $fieldBookings = FieldBooking::where('field_id', $field->id)->where('status', '!=', 'cancelled')->whereDate('start_time', '>=', Carbon::today())->orderBy('start_time')->get();
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
                'status' => $booking->status,
            ];
        }

        foreach ($fieldBookings as $booking) {
            $allBookings[] = [
                'id' => $booking->id,
                'type' => 'field',
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user' => $booking->user->name,
                'status' => $booking->status,
            ];
        }

        // Urutkan semua booking berdasarkan waktu mulai
        usort($allBookings, function ($a, $b) {
            return $a['start_time'] <=> $b['start_time'];
        });

        return view('photographers.schedule', compact('photographer', 'allBookings'));
    }
    /**
     * Mendapatkan detail booking berdasarkan ID dan tipe
     */
    public function getBookingDetails($bookingId, $bookingType)
    {
        try {
            $photographer = Auth::user()->photographer;
            $booking = null;

            if ($bookingType === 'photographer') {
                $booking = PhotographerBooking::where('id', $bookingId)->where('photographer_id', $photographer->id)->firstOrFail();

                $bookingData = [
                    'id' => $booking->id,
                    'type' => 'photographer',
                    'date' => Carbon::parse($booking->start_time)->format('d M Y'),
                    'time_range' => Carbon::parse($booking->start_time)->format('H:i') . ' - ' . Carbon::parse($booking->end_time)->format('H:i'),
                    'user_name' => $booking->user->name,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                    'cancellation_reason' => $booking->cancellation_reason,
                ];
            } elseif ($bookingType === 'field') {
                // Pastikan fotografer memiliki akses ke lapangan ini
                $field = $photographer->assignedField;

                if (!$field) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Anda tidak ditugaskan ke lapangan manapun',
                        ],
                        403,
                    );
                }

                $booking = FieldBooking::where('id', $bookingId)->where('field_id', $field->id)->firstOrFail();

                $bookingData = [
                    'id' => $booking->id,
                    'type' => 'field',
                    'date' => Carbon::parse($booking->start_time)->format('d M Y'),
                    'time_range' => Carbon::parse($booking->start_time)->format('H:i') . ' - ' . Carbon::parse($booking->end_time)->format('H:i'),
                    'user_name' => $booking->user->name,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                    'cancellation_reason' => $booking->cancellation_reason,
                ];
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Tipe booking tidak valid',
                    ],
                    400,
                );
            }

            return response()->json([
                'success' => true,
                'data' => $bookingData,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Booking tidak ditemukan atau Anda tidak memiliki akses',
                ],
                404,
            );
        }
    }
}
