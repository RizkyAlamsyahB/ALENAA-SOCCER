<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Field;
use App\Models\CartItem;
use App\Models\Membership;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FieldsController extends Controller
{
    /**
     * Menampilkan daftar lapangan untuk user
     */
    public function index()
    {
        $fields = Field::all();

        // Tambahkan rating dan review count untuk setiap field
        foreach ($fields as $field) {
            $field->rating = $field->getRatingAttribute();
            $field->reviews_count = $field->getReviewsCountAttribute();
        }

        return view('users.fields.index', compact('fields'));
    }

    /**
     * Menampilkan detail lapangan
     */
    public function show($id)
    {
        $field = Field::findOrFail($id);

        // Tambahkan rating dan review count
        $field->rating = $field->getRatingAttribute();
        $field->reviews_count = $field->getReviewsCountAttribute();

        $memberships = Membership::where('field_id', $id)->get();
        return view('users.fields.show', compact('field', 'memberships'));
    }

/**
 * Mendapatkan slot waktu yang tersedia untuk tanggal tertentu
 */
public function getAvailableSlots(Request $request, $fieldId)
{
    try {
        // Validasi input
        $request->validate([
            'date' => 'required|date'
        ]);

        // Cari lapangan
        $field = Field::findOrFail($fieldId);

        $date = $request->date;
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0 (Minggu) sampai 6 (Sabtu)

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

        // Get cart items for current user, field, and date
        $cartSlots = [];
        if (Auth::check()) {
            $userCart = Cart::where('user_id', Auth::id())->first();

            if ($userCart) {
                $cartItems = CartItem::where('cart_id', $userCart->id)
                    ->where('type', 'field_booking')
                    ->where('item_id', $fieldId)
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
        $bookedSlots = FieldBooking::where('field_id', $fieldId)
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        // Dapatkan membership slots yang aktif
        // 1. Ambil semua active membership untuk field ini
        $activeSubscriptions = DB::table('membership_subscriptions')
            ->join('memberships', 'membership_subscriptions.membership_id', '=', 'memberships.id')
            ->where('memberships.field_id', $fieldId)
            ->where('membership_subscriptions.status', 'active')
            ->select('membership_subscriptions.id')
            ->get()
            ->pluck('id')
            ->toArray();

        // 2. Ambil semua session dari active memberships
        $membershipSlots = [];
        if (!empty($activeSubscriptions)) {
            $membershipSessions = DB::table('membership_sessions')
                ->whereIn('membership_subscription_id', $activeSubscriptions)
                ->where('status', '!=', 'cancelled')
                ->get();

            // 3. Temukan pola membership yang jatuh pada hari yang sama
            foreach ($membershipSessions as $session) {
                $sessionStartTime = Carbon::parse($session->start_time);
                $sessionEndTime = Carbon::parse($session->end_time);

                // Jika hari dalam seminggu sama dengan tanggal yang diminta
                if ($sessionStartTime->dayOfWeek === $dayOfWeek) {
                    $startFormatted = $sessionStartTime->format('H:i');
                    $endFormatted = $sessionEndTime->format('H:i');

                    $membershipSlots[] = [
                        'start' => $startFormatted,
                        'end' => $endFormatted,
                        'display' => $startFormatted . ' - ' . $endFormatted
                    ];
                }
            }
        }

        // Cek apakah lapangan ini memiliki fotografer
        $hasPhotographer = false;
        $photographerInfo = null;

        if ($field->photographer_id) {
            $photographer = DB::table('photographers')
                ->where('id', $field->photographer_id)
                ->first();

            if ($photographer) {
                $hasPhotographer = true;
                $photographerInfo = [
                    'id' => $photographer->id,
                    'name' => $photographer->name,
                    'price' => $photographer->price
                ];
            }
        }

        // Filter slot yang tersedia
        $availableSlots = [];
        foreach ($allSlots as $slot) {
            $startTime = Carbon::parse("{$date} {$slot['start']}");
            $endTime = Carbon::parse("{$date} {$slot['end']}");
            $displaySlot = $slot['start'] . ' - ' . $slot['end'];

            $isAvailable = true;
            $isInCart = in_array($displaySlot, $cartSlots);
            $isMembershipSlot = false;

            // Cek apakah slot ini termasuk dalam membership pattern
            foreach ($membershipSlots as $membershipSlot) {
                if ($slot['start'] === $membershipSlot['start'] && $slot['end'] === $membershipSlot['end']) {
                    $isAvailable = false;
                    $isMembershipSlot = true;
                    break;
                }
            }

            // Jika belum ditandai sebagai slot membership, cek terhadap booking reguler
            if (!$isMembershipSlot) {
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

                        // Cek apakah booking ini adalah booking dari membership
                        if ($bookedBooking->is_membership) {
                            $isMembershipSlot = true;
                        }

                        break;
                    }
                }
            }

            // Calculate price (1 hour per slot)
            $slotPrice = $field->price * 1;

            // Tentukan status slot
            $status = 'available';
            if ($isInCart) {
                $status = 'in_cart';
            } else if (!$isAvailable) {
                if ($isMembershipSlot) {
                    $status = 'membership';
                } else {
                    $status = 'booked';
                }
            }

            $availableSlots[] = [
                'start' => $slot['start'],
                'end' => $slot['end'],
                'display' => $displaySlot,
                'is_available' => $isAvailable,
                'in_cart' => $isInCart,
                'price' => $slotPrice,
                'status' => $status,
                'is_membership' => $isMembershipSlot,
                'has_photographer' => $hasPhotographer,
                'photographer' => $hasPhotographer ? $photographerInfo : null
            ];
        }

        Log::debug('Membership slots found:', $membershipSlots);
        Log::debug('Final available slots:', $availableSlots);

        // Frontend hanya mengharapkan array slot langsung, bukan objek dengan property 'available_slots'
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
     * Membatalkan booking
     */
    public function cancelBooking($bookingId)
    {
        $booking = FieldBooking::where('id', $bookingId)->where('user_id', Auth::id())->firstOrFail();

        if ($booking->status === 'pending' || $booking->status === 'confirmed') {
            $booking->status = 'cancelled';
            $booking->save();

            return back()->with('success', 'Booking berhasil dibatalkan');
        }

        return back()->with('error', 'Maaf, booking tidak dapat dibatalkan');
    }

    /**
     * Mendapatkan slot yang ada di keranjang
     */
    public function getCartSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'field_id' => 'required|exists:fields,id',
        ]);

        $fieldId = $request->field_id;
        $date = $request->date;
        $reservedSlots = [];

        // Get slots from user's cart for this specific field and date
        if (Auth::check()) {
            $userCart = Cart::where('user_id', Auth::id())->first();

            if ($userCart) {
                $cartItems = CartItem::where('cart_id', $userCart->id)->where('type', 'field_booking')->where('item_id', $fieldId)->whereDate('start_time', $date)->get();

                foreach ($cartItems as $item) {
                    $startFormatted = Carbon::parse($item->start_time)->format('H:i');
                    $endFormatted = Carbon::parse($item->end_time)->format('H:i');
                    $reservedSlots[] = $startFormatted . ' - ' . $endFormatted;
                }
            }
        }

        // Also get booked slots from FieldBooking table
        $bookedSlots = FieldBooking::where('field_id', $fieldId)->whereDate('start_time', $date)->where('status', '!=', 'cancelled')->get();

        foreach ($bookedSlots as $booking) {
            $startFormatted = Carbon::parse($booking->start_time)->format('H:i');
            $endFormatted = Carbon::parse($booking->end_time)->format('H:i');
            $reservedSlots[] = $startFormatted . ' - ' . $endFormatted;
        }

        return response()->json($reservedSlots);
    }
}
