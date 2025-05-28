<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Field;
use App\Models\CartItem;
use App\Models\FieldBooking;
use App\Models\Photographer;
use Illuminate\Http\Request;
use App\Models\PhotographerBooking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PhotographerBookingNotification;

class PhotographerController extends Controller
{
    /**
     * Menampilkan daftar fotografer untuk user
     */
    public function index()
    {
        // Ambil semua paket fotografer yang aktif
        $photographers = Photographer::where('status', 'active')->get();

        // Kelompokkan fotografer berdasarkan package_type
        $photographersByType = $photographers->groupBy('package_type');

        // Untuk setiap fotografer, tambahkan informasi rating dan lapangan
        foreach ($photographers as $photographer) {
            $photographer->rating = $photographer->getRatingAttribute();
            $photographer->reviews_count = $photographer->getReviewsCountAttribute();

            // Tambahkan info lapangan
            $field = Field::find($photographer->field_id);
            $photographer->assigned_field = $field ? $field->name : 'Tidak terkait dengan lapangan';
        }

        return view('users.photographers.index', compact('photographers', 'photographersByType'));
    }

    /**
     * Menampilkan detail fotografer
     */
    public function show($id)
    {
        $photographer = Photographer::findOrFail($id);

        // Tambahkan rating dan review count
        $photographer->rating = $photographer->getRatingAttribute();
        $photographer->reviews_count = $photographer->getReviewsCountAttribute();

        // Tambahkan info lapangan
        $field = Field::where('photographer_id', $photographer->user_id)->first();
        $photographer->assigned_field = $field;

        return view('users.photographers.show', compact('photographer'));
    }

    /**
     * Mendapatkan slot waktu yang tersedia untuk tanggal tertentu
     */
    public function getAvailableSlots(Request $request, $photographerId)
    {
        try {
            // Validasi input
            $request->validate([
                'date' => 'required|date'
            ]);

            // Cari fotografer
            $photographer = Photographer::findOrFail($photographerId);

            // Cek lapangan yang ditugaskan untuk fotografer ini
            $assignedField = Field::where('photographer_id', $photographer->user_id)->first();

            $date = $request->date;
            $carbonDate = Carbon::parse($date);

            // Get current time untuk comparison
            $now = Carbon::now();
            $isToday = $carbonDate->isToday();

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

            // Get cart items untuk fotografer ini pada tanggal yang dipilih
            $cartSlots = [];
            if (Auth::check()) {
                $userCart = Cart::where('user_id', Auth::id())->first();

                if ($userCart) {
                    $cartItems = CartItem::where('cart_id', $userCart->id)
                        ->where('type', 'photographer')
                        ->where('item_id', $photographerId)
                        ->whereDate('start_time', $date)
                        ->get();

                    foreach ($cartItems as $item) {
                        $startFormatted = Carbon::parse($item->start_time)->format('H:i');
                        $endFormatted = Carbon::parse($item->end_time)->format('H:i');
                        $cartSlots[] = $startFormatted . ' - ' . $endFormatted;
                    }
                }
            }

            // Dapatkan booking fotografer yang sudah ada pada tanggal tersebut
            $bookedSlots = PhotographerBooking::where('photographer_id', $photographerId)
                ->whereDate('start_time', $date)
                ->where('status', '!=', 'cancelled')
                ->get();

            // Jika fotografer ditugaskan ke lapangan, ambil juga booking lapangan
            $fieldBookedSlots = [];
            if ($assignedField) {
                $fieldBookedSlots = FieldBooking::where('field_id', $assignedField->id)
                    ->whereDate('start_time', $date)
                    ->where('status', '!=', 'cancelled')
                    ->get();
            }

            // Filter slot yang tersedia
            $availableSlots = [];
            foreach ($allSlots as $slot) {
                $startTime = Carbon::parse("{$date} {$slot['start']}");
                $endTime = Carbon::parse("{$date} {$slot['end']}");
                $displaySlot = $slot['start'] . ' - ' . $slot['end'];

                $isAvailable = true;
                $isInCart = in_array($displaySlot, $cartSlots);
                $bookingType = '';
                $isPastTime = false;

                // PERBAIKAN: Cek apakah slot waktu sudah lewat
                if ($isToday) {
                    // Jika tanggal yang dipilih adalah hari ini, cek apakah waktu AKHIR slot sudah lewat
                    // Slot disable jika waktu end sudah terlewati
                    if ($endTime <= $now) {
                        $isAvailable = false;
                        $isPastTime = true;
                    }
                }

                // Cek booking fotografer yang sudah ada (hanya jika bukan past time)
                if (!$isPastTime) {
                    foreach ($bookedSlots as $bookedBooking) {
                        if (
                            ($startTime >= $bookedBooking->start_time && $startTime < $bookedBooking->end_time) ||
                            ($endTime > $bookedBooking->start_time && $endTime <= $bookedBooking->end_time) ||
                            ($startTime <= $bookedBooking->start_time && $endTime >= $bookedBooking->end_time)
                        ) {
                            $isAvailable = false;
                            $bookingType = 'photographer';
                            break;
                        }
                    }

                    // Jika masih tersedia dan fotografer ditugaskan ke lapangan, cek slot lapangan
                    if ($isAvailable && $assignedField) {
                        foreach ($fieldBookedSlots as $fieldBooking) {
                            if (
                                ($startTime >= $fieldBooking->start_time && $startTime < $fieldBooking->end_time) ||
                                ($endTime > $fieldBooking->start_time && $endTime <= $fieldBooking->end_time) ||
                                ($startTime <= $fieldBooking->start_time && $endTime >= $fieldBooking->end_time)
                            ) {
                                $isAvailable = false;
                                $bookingType = 'field';
                                break;
                            }
                        }
                    }
                }

                // Harga booking fotografer
                $slotPrice = $photographer->price;

                // Tentukan status slot
                $status = 'available';
                if ($isPastTime) {
                    $status = 'past_time';
                } else if ($isInCart) {
                    $status = 'in_cart';
                } else if (!$isAvailable) {
                    $status = 'booked';
                }

                $availableSlots[] = [
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'display' => $displaySlot,
                    'is_available' => $isAvailable,
                    'in_cart' => $isInCart,
                    'price' => $slotPrice,
                    'status' => $status,
                    'booking_type' => $isAvailable ? '' : $bookingType,
                    'assigned_field' => $assignedField ? $assignedField->name : null,
                    'is_past_time' => $isPastTime
                ];
            }

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in getAvailableSlots for photographer', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to retrieve available slots',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membatalkan booking fotografer
     */
    public function cancelBooking($bookingId)
    {
        $booking = PhotographerBooking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status === 'pending' || $booking->status === 'confirmed') {
            $booking->status = 'cancelled';
            $booking->save();

            return back()->with('success', 'Booking fotografer berhasil dibatalkan');
        }

        return back()->with('error', 'Maaf, booking fotografer tidak dapat dibatalkan');
    }

    /**
     * Send booking notification to photographer
     */
    public function sendBookingNotification(PhotographerBooking $booking)
    {
        try {
            // Get the photographer record
            $photographer = Photographer::findOrFail($booking->photographer_id);

            // Get the photographer's user record
            $photographerUser = \App\Models\User::find($photographer->user_id);

            // Get the booking user
            $user = $booking->user;

            if ($photographerUser && $photographerUser->email) {
                // Pass both photographer user and booking user to the notification
                Mail::to($photographerUser->email)
                    ->send(new PhotographerBookingNotification($booking, $photographerUser, $user));

                $booking->notification_sent_at = now();
                $booking->save();

                Log::info('Photographer booking notification sent to: ' . $photographerUser->email);
                return true;
            } else {
                Log::warning('Photographer user or email not found for booking: ' . $booking->id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send photographer notification: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }

        return false;
    }
}
