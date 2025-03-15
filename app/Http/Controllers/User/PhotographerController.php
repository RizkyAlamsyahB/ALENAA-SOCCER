<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Photographer;
use App\Models\PhotographerBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;

class PhotographerController extends Controller
{
    /**
     * Menampilkan daftar fotografer untuk user
     */
    public function index()
    {
        $photographers = Photographer::where('status', 'active')->get();
        return view('users.photographers.index', compact('photographers'));
    }

    /**
     * Menampilkan detail fotografer
     */
    public function show($id)
    {
        $photographer = Photographer::findOrFail($id);
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

            $date = $request->date;
            $carbonDate = Carbon::parse($date);

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

            // Get cart items for current user, photographer, and date
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

            // Dapatkan booking yang sudah ada pada tanggal tersebut
            $bookedSlots = PhotographerBooking::where('photographer_id', $photographerId)
                ->whereDate('start_time', $date)
                ->where('status', '!=', 'cancelled')
                ->get();

            // Filter slot yang tersedia
            $availableSlots = [];
            foreach ($allSlots as $slot) {
                $startTime = Carbon::parse("{$date} {$slot['start']}");
                $endTime = Carbon::parse("{$date} {$slot['end']}");
                $displaySlot = $slot['start'] . ' - ' . $slot['end'];

                $isAvailable = true;
                $isInCart = in_array($displaySlot, $cartSlots);

                // Check against booked slots
                foreach ($bookedSlots as $bookedBooking) {
                    $bookedStart = $bookedBooking->start_time;
                    $bookedEnd = $bookedBooking->end_time;

                    if (
                        // Kasus 1: Waktu mulai slot berada di dalam range booking
                        ($startTime >= $bookedStart && $startTime < $bookedEnd) ||
                        // Kasus 2: Waktu selesai slot berada di dalam range booking
                        ($endTime > $bookedStart && $endTime <= $bookedEnd) ||
                        // Kasus 3: Booking berada di dalam range slot waktu
                        ($startTime <= $bookedStart && $endTime >= $bookedEnd)
                    ) {
                        $isAvailable = false;
                        break;
                    }
                }

                // Calculate price (price per hour)
                $slotPrice = $photographer->price;

                $availableSlots[] = [
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'display' => $displaySlot,
                    'is_available' => $isAvailable,
                    'in_cart' => $isInCart,
                    'price' => $slotPrice,
                    'status' => $isInCart ? 'in_cart' : ($isAvailable ? 'available' : 'booked')
                ];
            }

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            // Log full error
            Log::error('Error in getAvailableSlots', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error response
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
}
