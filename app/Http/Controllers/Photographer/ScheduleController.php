<?php

namespace App\Http\Controllers\Photographer;

use Carbon\Carbon;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use App\Mail\BookingConfirmedMail;
use App\Models\PhotographerBooking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PhotoGalleryDeliveredMail;

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
                'completion_status' => $booking->completion_status,
                'photo_gallery_link' => $booking->photo_gallery_link,
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
                    'completion_status' => $booking->completion_status,
                    'notes' => $booking->notes,
                    'photographer_notes' => $booking->photographer_notes,
                    'photo_gallery_link' => $booking->photo_gallery_link,
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

    /**
     * Konfirmasi booking
     */
    public function confirmBooking($bookingId, $bookingType)
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

                // Kirim email konfirmasi ke user
                try {
                    Mail::to($booking->user->email)->send(new BookingConfirmedMail($booking));
                } catch (\Exception $e) {
                    // Log error tapi jangan gagalkan proses konfirmasi
                    Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dikonfirmasi dan email notifikasi telah dikirim ke pelanggan.'
                ]);

            } elseif ($bookingType === 'field') {
                $field = $photographer->assignedField;

                if (!$field) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak ditugaskan ke lapangan manapun',
                    ], 403);
                }

                $booking = FieldBooking::where('id', $bookingId)
                    ->where('field_id', $field->id)
                    ->where('status', 'pending')
                    ->firstOrFail();

                $booking->update(['status' => 'confirmed']);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking lapangan berhasil dikonfirmasi.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan atau tidak dapat dikonfirmasi.'
            ], 404);
        }
    }

    /**
     * Tandai shooting selesai
     */
    public function markShootingCompleted($bookingId)
    {
        try {
            $photographer = Auth::user()->photographer;

            $booking = PhotographerBooking::where('id', $bookingId)
                ->where('photographer_id', $photographer->id)
                ->where('status', 'confirmed')
                ->firstOrFail();

            $booking->update([
                'completion_status' => 'shooting_completed',
                'completed_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shooting berhasil ditandai selesai. Silakan kirim link galeri foto.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan atau tidak dapat diperbarui.'
            ], 404);
        }
    }

    /**
     * Kirim link galeri foto
     */
    public function sendPhotoGallery(Request $request, $bookingId)
    {
        $request->validate([
            'photo_gallery_link' => 'required|url',
            'photographer_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $photographer = Auth::user()->photographer;

            $booking = PhotographerBooking::where('id', $bookingId)
                ->where('photographer_id', $photographer->id)
                ->where('completion_status', 'shooting_completed')
                ->firstOrFail();

            $booking->update([
                'photo_gallery_link' => $request->photo_gallery_link,
                'photographer_notes' => $request->photographer_notes,
                'completion_status' => 'delivered'
            ]);

            // Kirim email dengan link galeri foto
            try {
                Mail::to($booking->user->email)->send(new PhotoGalleryDeliveredMail($booking));
            } catch (\Exception $e) {
                // Log error tapi jangan gagalkan proses
                Log::error('Failed to send photo gallery email: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Link galeri berhasil disimpan tetapi email gagal dikirim. Silakan hubungi pelanggan secara manual.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Link galeri foto berhasil dikirim ke pelanggan melalui email.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan atau tidak dapat diperbarui.'
            ], 404);
        }
    }
}
