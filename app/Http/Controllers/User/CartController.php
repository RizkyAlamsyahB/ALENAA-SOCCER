<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Field;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Menambahkan booking lapangan ke keranjang
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

        // Dapatkan atau buat cart untuk user yang login
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()]
        );

        foreach ($selectedSlots as $slot) {
            // Parse slot info (format: "08:00 - 09:00")
            list($startTime, $endTime) = explode(' - ', $slot);

            // Buat full datetime untuk start dan end time
            $startDateTime = Carbon::parse("{$date} {$startTime}");
            $endDateTime = Carbon::parse("{$date} {$endTime}");

            // Periksa apakah item sudah ada di cart
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('type', 'field_booking')
                ->where('item_id', $field->id)
                ->where('start_time', $startDateTime)
                ->where('end_time', $endDateTime)
                ->first();

            if (!$existingItem) {
                // Tambahkan item ke cart jika belum ada
                CartItem::create([
                    'cart_id' => $cart->id,
                    'type' => 'field_booking',
                    'item_id' => $field->id,
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'price' => $field->price,
                ]);
            }
        }

        // Hitung jumlah item di cart
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Slot waktu berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Menampilkan halaman keranjang booking
     */
    public function viewCart()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        $cartItems = [];
        $totalPrice = 0;

        if ($cart) {
            $cartItems = CartItem::where('cart_id', $cart->id)->get();

            // Prepare data for view and calculate total price
            foreach ($cartItems as $item) {
                // Enhance cart item with additional data based on type
                if ($item->type === 'field_booking') {
                    $field = Field::find($item->item_id);
                    if ($field) {
                        $item->field_name = $field->name;
                        $item->field_type = $field->type;
                        $item->field_image = $field->image;
                        $item->formatted_date = Carbon::parse($item->start_time)->format('d M Y');
                    }
                }

                $totalPrice += $item->price;
            }
        }

// Menjadi ini:
return view('users.cart.index', compact('cartItems', 'totalPrice'));    }

    /**
     * API untuk menghapus item dari keranjang (untuk AJAX)
     */
    public function apiRemoveFromCart($itemId)
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang tidak ditemukan'
            ], 404);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        $cartItem->delete();
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Menghapus item dari keranjang
     */
    public function removeFromCart($itemId)
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return back()->with('error', 'Keranjang tidak ditemukan');
        }

        $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();

        if (!$cartItem) {
            return back()->with('error', 'Item tidak ditemukan');
        }

        $cartItem->delete();

        // Check if this is an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            $cartCount = CartItem::where('cart_id', $cart->id)->count();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart_count' => $cartCount
            ]);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang');
    }

    /**
     * Mendapatkan HTML untuk cart sidebar (untuk AJAX refresh)
     */
    public function getCartSidebar()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        $cartItems = [];
        $totalPrice = 0;

        if ($cart) {
            $cartItems = CartItem::where('cart_id', $cart->id)->get();

            // Prepare data for view and calculate total price
            foreach ($cartItems as $item) {
                // Enhance cart item with additional data based on type
                if ($item->type === 'field_booking') {
                    $field = Field::find($item->item_id);
                    if ($field) {
                        $item->field_name = $field->name;
                        $item->field_type = $field->type;
                        $item->field_image = $field->image;
                        $item->formatted_date = Carbon::parse($item->start_time)->format('d M Y');
                        $item->start_time_formatted = Carbon::parse($item->start_time)->format('H:i');
                        $item->end_time_formatted = Carbon::parse($item->end_time)->format('H:i');
                    }
                }

                $totalPrice += $item->price;
            }
        }

        return view('components.cart-sidebar', compact('cartItems', 'totalPrice'))->render();
    }

    /**
     * Memproses checkout dari keranjang
     */
    public function checkout()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart || CartItem::where('cart_id', $cart->id)->count() === 0) {
            return redirect()->route('user.fields.index')
                ->with('error', 'Keranjang booking kosong');
        }

        // Get field booking items from cart
        $fieldBookingItems = CartItem::where('cart_id', $cart->id)
            ->where('type', 'field_booking')
            ->get();

        // Simpan semua booking dari keranjang
        $bookingIds = [];
        $totalPrice = 0;

        foreach ($fieldBookingItems as $item) {
            // Check if slot is still available
            $conflictBooking = FieldBooking::where('field_id', $item->item_id)
                ->where(function ($query) use ($item) {
                    $query->whereBetween('start_time', [$item->start_time, $item->end_time])
                        ->orWhereBetween('end_time', [$item->start_time, $item->end_time])
                        ->orWhere(function ($q) use ($item) {
                            $q->where('start_time', '<=', $item->start_time)
                                ->where('end_time', '>=', $item->end_time);
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
            $booking->field_id = $item->item_id;
            $booking->start_time = $item->start_time;
            $booking->end_time = $item->end_time;
            $booking->total_price = $item->price;
            $booking->status = 'pending';
            $booking->save();

            $bookingIds[] = $booking->id;
            $totalPrice += $item->price;
        }

        // Clear cart after successful booking (only remove field booking items)
        foreach ($fieldBookingItems as $item) {
            $item->delete();
        }

        // Redirect to payment page
        return redirect()->route('user.payment.checkout', [
            'bookings' => $bookingIds,
            'total_price' => $totalPrice
        ])->with('success', 'Booking berhasil dibuat. Silakan selesaikan pembayaran.');
    }

    /**
     * Mendapatkan jumlah item di keranjang
     */
    public function getCartCount()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        $count = 0;

        if ($cart) {
            $count = CartItem::where('cart_id', $cart->id)->count();
        }

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Menghapus semua item dari keranjang
     */
    public function clearCart()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
        }

        return redirect()->route('user.fields.cart')
            ->with('success', 'Keranjang berhasil dikosongkan');
    }
}
