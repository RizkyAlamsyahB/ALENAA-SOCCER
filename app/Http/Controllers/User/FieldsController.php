<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Field;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
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
        return view('users.fields.index', compact('fields'));
    }

    /**
     * Menampilkan detail lapangan
     */
    public function show($id)
    {
        $field = Field::findOrFail($id);
        return view('users.fields.show', compact('field'));
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

            // Get cart items for the current date and field
            $cartItems = session()->get('booking_cart', []);
            $cartSlots = collect($cartItems)
                ->where('field_id', $fieldId)
                ->where('date', $date)
                ->pluck('time_slot')
                ->toArray();

            // Dapatkan booking yang sudah ada pada tanggal tersebut
            $bookedSlots = FieldBooking::where('field_id', $fieldId)
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
                    if (
                        ($startTime->between($bookedBooking->start_time, $bookedBooking->end_time)) ||
                        ($endTime->between($bookedBooking->start_time, $bookedBooking->end_time)) ||
                        ($bookedBooking->start_time->between($startTime, $endTime))
                    ) {
                        $isAvailable = false;
                        break;
                    }
                }

                // Calculate price (1 hours per slot)
                $slotPrice = $field->price * 1;

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
     * Menambahkan booking ke keranjang
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
            'slots' => 'required|array|min:1',
        ]);

        $field = Field::findOrFail($request->field_id);
        $date = $request->date;
        $selectedSlots = $request->slots;

        // Simpan data booking ke session cart
        $cart = session()->get('booking_cart', []);

        foreach ($selectedSlots as $slot) {
            // Parse slot info (format: "08:00-10:00")
            list($startTime, $endTime) = explode(' - ', $slot);

            // Generate unique item ID
            $itemId = uniqid();

            $cart[$itemId] = [
                'id' => $itemId,
                'field_id' => $field->id,
                'field_name' => $field->name,
                'field_type' => $field->type,
                'field_image' => $field->image,
                'price' => $field->price * 1, // 1 hours per slot
                'date' => $date,
                'formatted_date' => Carbon::parse($date)->format('d M Y'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'time_slot' => $slot,
            ];
        }

        session()->put('booking_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Slot waktu berhasil ditambahkan ke keranjang',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Menampilkan halaman keranjang booking
     */
    public function viewCart()
    {
        $cartItems = session()->get('booking_cart', []);
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $totalPrice += $item['price'];
        }

        return view('users.fields.cart', compact('cartItems', 'totalPrice'));
    }

    // Tambahkan metode ini ke FieldsController

    /**
     * API untuk menghapus item dari keranjang (untuk AJAX)
     */
    public function apiRemoveFromCart($itemId)
    {
        $cart = session()->get('booking_cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('booking_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart_count' => count($cart)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan'
        ], 404);
    }

    // Ubah metode removeFromCart untuk mendukung respons JSON jika diminta AJAX
    public function removeFromCart($itemId)
    {
        $cart = session()->get('booking_cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('booking_cart', $cart);

            // Check if this is an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus dari keranjang',
                    'cart_count' => count($cart)
                ]);
            }
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang');
    }
    /**
     * Mendapatkan HTML untuk cart sidebar (untuk AJAX refresh)
     */
    public function getCartSidebar()
    {
        $cartItems = session()->get('booking_cart', []);
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $totalPrice += $item['price'];
        }

        return view('components.cart-sidebar', compact('cartItems', 'totalPrice'))->render();
    }

    /**
     * Memproses checkout dari keranjang
     */
    public function checkout()
    {
        $cartItems = session()->get('booking_cart', []);

        if (empty($cartItems)) {
            return redirect()->route('user.fields.index')
                ->with('error', 'Keranjang booking kosong');
        }

        // Simpan semua booking dari keranjang
        $bookingIds = [];
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $startDateTime = Carbon::parse("{$item['date']} {$item['start_time']}");
            $endDateTime = Carbon::parse("{$item['date']} {$item['end_time']}");

            // Check if slot is still available
            $conflictBooking = FieldBooking::where('field_id', $item['field_id'])
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                        ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                        ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                            $q->where('start_time', '<=', $startDateTime)
                                ->where('end_time', '>=', $endDateTime);
                        });
                })
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($conflictBooking) {
                return back()->with('error', 'Maaf, beberapa slot yang Anda pilih sudah tidak tersedia. Silakan periksa kembali keranjang Anda.');
            }

            // Create booking
            $booking = new FieldBooking();
            $booking->user_id = Auth::id();
            $booking->field_id = $item['field_id'];
            $booking->start_time = $startDateTime;
            $booking->end_time = $endDateTime;
            $booking->total_price = $item['price'];
            $booking->status = 'pending';
            $booking->save();

            $bookingIds[] = $booking->id;
            $totalPrice += $item['price'];
        }

        // Clear cart after successful booking
        session()->forget('booking_cart');

        // Redirect to payment page
        return redirect()->route('user.payment.checkout', [
            'bookings' => $bookingIds,
            'total_price' => $totalPrice
        ])->with('success', 'Booking berhasil dibuat. Silakan selesaikan pembayaran.');
    }
    public function getCartSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'field_id' => 'required|exists:fields,id'
        ]);

        $cart = session()->get('booking_cart', []);
        $cartSlots = [];

        // Get slots from cart for this specific field and date
        foreach ($cart as $item) {
            if ($item['field_id'] == $request->field_id && $item['date'] == $request->date) {
                $cartSlots[] = $item['time_slot'];
            }
        }

        // Optionally, also retrieve booked slots from database
        $bookedSlots = Booking::where('field_id', $request->field_id)
            ->where('booking_date', $request->date)
            ->pluck('time_slot')
            ->toArray();

        // Merge cart and booked slots
        $allReservedSlots = array_merge($cartSlots, $bookedSlots);

        return response()->json($allReservedSlots);
    }
    /**
     * Membatalkan booking
     */
    public function cancelBooking($bookingId)
    {
        $booking = FieldBooking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status === 'pending' || $booking->status === 'confirmed') {
            $booking->status = 'cancelled';
            $booking->save();

            return back()->with('success', 'Booking berhasil dibatalkan');
        }

        return back()->with('error', 'Maaf, booking tidak dapat dibatalkan');
    }

    /**
     * Mendapatkan jumlah item di keranjang
     */
    public function getCartCount()
    {
        $cartItems = session()->get('booking_cart', []);

        return response()->json([
            'count' => count($cartItems)
        ]);
    }

    /**
     * Menghapus semua item dari keranjang
     */
    public function clearCart()
    {
        session()->forget('booking_cart');

        return redirect()->route('user.fields.cart')
            ->with('success', 'Keranjang berhasil dikosongkan');
    }
}
