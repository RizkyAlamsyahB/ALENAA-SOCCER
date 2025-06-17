<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerBooking;
use App\Models\FieldBooking;
use App\Mail\PhotoGalleryLinkMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        return view('photographers.dashboard', compact('photographer', 'upcomingPhotographerBookings', 'field', 'upcomingFieldBookings'));
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
                'status' => $booking->status,
                'completion_status' => $booking->completion_status ?? 'pending',
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
                'completion_status' => 'not_applicable', // Field booking tidak butuh completion status
            ];
        }

        // Urutkan semua booking berdasarkan waktu mulai
        usort($allBookings, function ($a, $b) {
            return $a['start_time'] <=> $b['start_time'];
        });

        return view('photographers.schedule', compact('photographer', 'allBookings'));
    }

    /**
     * Konfirmasi booking oleh fotografer
     */
    public function confirmBooking(Request $request, $bookingId, $bookingType)
    {
        try {
            $photographer = Auth::user()->photographer;

            if ($bookingType === 'photographer') {
                $booking = PhotographerBooking::where('id', $bookingId)
                    ->where('photographer_id', $photographer->id)
                    ->where('status', 'pending')
                    ->firstOrFail();

                $booking->update([
                    'status' => 'confirmed',
                    'completion_status' => 'confirmed'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking fotografer berhasil dikonfirmasi!'
                ]);

            } elseif ($bookingType === 'field') {
                // Untuk booking lapangan, pastikan fotografer memiliki akses
                $field = $photographer->assignedField;

                if (!$field) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak ditugaskan ke lapangan manapun'
                    ], 403);
                }

                $booking = FieldBooking::where('id', $bookingId)
                    ->where('field_id', $field->id)
                    ->where('status', 'pending')
                    ->firstOrFail();

                $booking->update(['status' => 'confirmed']);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking lapangan berhasil dikonfirmasi!'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi booking'
            ], 500);
        }
    }

    /**
     * Tandai pekerjaan selesai dan kirim link foto
     */
    public function completeWithLink(Request $request, $bookingId)
    {
        $request->validate([
            'photo_gallery_link' => 'required|url',
            'photographer_notes' => 'nullable|string|max:1000'
        ], [
            'photo_gallery_link.required' => 'Link galeri foto wajib diisi',
            'photo_gallery_link.url' => 'Format link tidak valid'
        ]);

        try {
            $photographer = Auth::user()->photographer;

            $booking = PhotographerBooking::where('id', $bookingId)
                ->where('photographer_id', $photographer->id)
                ->where('status', 'confirmed')
                ->firstOrFail();

            // Update booking dengan link dan status completion
            $booking->update([
                'photo_gallery_link' => $request->photo_gallery_link,
                'photographer_notes' => $request->photographer_notes,
                'completed_at' => now(),
                'completion_status' => 'delivered',
                'status' => 'completed'
            ]);

            // Kirim email ke user dengan link foto
            Mail::to($booking->user->email)->send(
                new PhotoGalleryLinkMail($booking, $request->photo_gallery_link)
            );

            return response()->json([
                'success' => true,
                'message' => 'Pekerjaan selesai! Link foto telah dikirim ke pelanggan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan pekerjaan'
            ], 500);
        }
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
                $booking = PhotographerBooking::where('id', $bookingId)
                    ->where('photographer_id', $photographer->id)
                    ->firstOrFail();

                $bookingData = [
                    'id' => $booking->id,
                    'type' => 'photographer',
                    'date' => Carbon::parse($booking->start_time)->format('d M Y'),
                    'time_range' => Carbon::parse($booking->start_time)->format('H:i') . ' - ' . Carbon::parse($booking->end_time)->format('H:i'),
                    'user_name' => $booking->user->name,
                    'user_email' => $booking->user->email,
                    'status' => $booking->status,
                    'completion_status' => $booking->completion_status ?? 'pending',
                    'notes' => $booking->notes,
                    'photographer_notes' => $booking->photographer_notes,
                    'photo_gallery_link' => $booking->photo_gallery_link,
                    'completed_at' => $booking->completed_at ? Carbon::parse($booking->completed_at)->format('d M Y H:i') : null,
                    'cancellation_reason' => $booking->cancellation_reason,
                ];
            } elseif ($bookingType === 'field') {
                // Pastikan fotografer memiliki akses ke lapangan ini
                $field = $photographer->assignedField;

                if (!$field) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak ditugaskan ke lapangan manapun',
                    ], 403);
                }

                $booking = FieldBooking::where('id', $bookingId)
                    ->where('field_id', $field->id)
                    ->firstOrFail();

                $bookingData = [
                    'id' => $booking->id,
                    'type' => 'field',
                    'date' => Carbon::parse($booking->start_time)->format('d M Y'),
                    'time_range' => Carbon::parse($booking->start_time)->format('H:i') . ' - ' . Carbon::parse($booking->end_time)->format('H:i'),
                    'user_name' => $booking->user->name,
                    'user_email' => $booking->user->email,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                    'cancellation_reason' => $booking->cancellation_reason,
                ];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe booking tidak valid',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $bookingData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan atau Anda tidak memiliki akses',
            ], 404);
        }
    }
}
