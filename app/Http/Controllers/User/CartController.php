<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Field;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Menambahkan lapangan ke keranjang
     */
    public function addFieldToCart(Request $request)
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
                'type' => 'field',
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
     * Menampilkan halaman keranjang
     */
    public function viewCart()
    {
        $cartItems = session()->get('booking_cart', []);
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $totalPrice += $item['price'];
        }

        return view('users.cart.index', compact('cartItems', 'totalPrice'));
    }

    /**
     * Menghapus item dari keranjang
     */
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

        return redirect()->route('user.cart.index')
            ->with('success', 'Keranjang berhasil dikosongkan');
    }

    /**
     * Mendapatkan slot yang sudah direservasi
     */
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
            if ($item['type'] === 'field' &&
                $item['field_id'] == $request->field_id &&
                $item['date'] == $request->date) {
                $cartSlots[] = $item['time_slot'];
            }
        }

        return response()->json($cartSlots);
    }
}
