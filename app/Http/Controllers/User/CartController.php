<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Field;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\RentalItem;
use App\Models\Membership;
use App\Models\Photographer;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Menambahkan item ke keranjang berdasarkan tipe
     */
    public function addToCart(Request $request)
    {
        try {
            // Log request untuk debugging
            Log::info('Cart Add Request', ['data' => $request->all()]);

            // Validasi input dasar
            $request->validate([
                'type' => 'required|in:field_booking,rental_item,membership,photographer'
            ]);

            // Dapatkan atau buat cart untuk user yang login
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Routing ke method yang sesuai berdasarkan tipe
            switch($request->type) {
                case 'field_booking':
                    return $this->addFieldBookingToCart($request, $cart);
                case 'rental_item':
                    return $this->addRentalItemToCart($request, $cart);
                case 'membership':
                    return $this->addMembershipToCart($request, $cart);
                case 'photographer':
                    return $this->addPhotographerToCart($request, $cart);
            }
        } catch (\Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menambahkan booking lapangan ke keranjang
     */
    private function addFieldBookingToCart(Request $request, Cart $cart)
    {
        // Validasi input spesifik untuk field booking
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
            'slots' => 'required|array|min:1',
            'slots.*' => 'string'
        ]);

        $field = Field::findOrFail($request->field_id);
        $date = $request->date;
        $selectedSlots = $request->slots;
        $itemsAdded = 0;

        // Log untuk debugging
        Log::info('Field booking details', [
            'field_id' => $field->id,
            'field_name' => $field->name,
            'date' => $date,
            'slots_count' => count($selectedSlots)
        ]);

        foreach ($selectedSlots as $slot) {
            // Parse slot info (format: "08:00 - 09:00")
            list($startTime, $endTime) = explode(' - ', $slot);

            // Buat full datetime untuk start dan end time
            $startDateTime = Carbon::parse("{$date} {$startTime}");
            $endDateTime = Carbon::parse("{$date} {$endTime}");

            // Periksa apakah slot sudah ada di cart
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('type', 'field_booking')
                ->where('item_id', $field->id)
                ->where('start_time', $startDateTime)
                ->where('end_time', $endDateTime)
                ->first();

            if (!$existingItem) {
                // Periksa apakah slot sudah di-booking oleh user lain
                $isBooked = FieldBooking::where('field_id', $field->id)
                    ->whereDate('start_time', $startDateTime->toDateString())
                    ->where(function($query) use ($startDateTime, $endDateTime) {
                        $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                            ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                            ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                                $q->where('start_time', '<=', $startDateTime)
                                  ->where('end_time', '>=', $endDateTime);
                            });
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($isBooked) {
                    return response()->json([
                        'success' => false,
                        'message' => "Slot {$slot} sudah di-booking. Silakan pilih slot lain."
                    ], 400);
                }

                // Tambahkan item ke cart
                CartItem::create([
                    'cart_id' => $cart->id,
                    'type' => 'field_booking',
                    'item_id' => $field->id,
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'price' => $field->price,
                ]);

                $itemsAdded++;
            }
        }

        // Hitung jumlah item di cart
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => $itemsAdded > 0
                ? 'Slot waktu berhasil ditambahkan ke keranjang'
                : 'Semua slot yang dipilih sudah ada di keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Menambahkan rental item ke keranjang
     */
    private function addRentalItemToCart(Request $request, Cart $cart)
    {
        // Validasi input
        $request->validate([
            'item_id' => 'required|exists:rental_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $rentalItem = RentalItem::findOrFail($request->item_id);
        $price = $rentalItem->price * $request->quantity;

        // Periksa apakah item sudah ada di cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('type', 'rental_item')
            ->where('item_id', $rentalItem->id)
            ->first();

        if ($existingItem) {
            // Update quantity jika sudah ada
            $existingItem->quantity += $request->quantity;
            $existingItem->price = $rentalItem->price * $existingItem->quantity;
            $existingItem->save();
        } else {
            // Tambahkan baru jika belum ada
            CartItem::create([
                'cart_id' => $cart->id,
                'type' => 'rental_item',
                'item_id' => $rentalItem->id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }

        // Hitung jumlah item di cart
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Menambahkan membership ke keranjang
     */
    private function addMembershipToCart(Request $request, Cart $cart)
    {
        // Validasi input
        $request->validate([
            'item_id' => 'required|exists:memberships,id',
        ]);

        // Cek jika user sudah memiliki membership di cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('type', 'membership')
            ->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Membership sudah ada di keranjang Anda'
            ], 400);
        }

        $membership = Membership::findOrFail($request->item_id);

        CartItem::create([
            'cart_id' => $cart->id,
            'type' => 'membership',
            'item_id' => $membership->id,
            'price' => $membership->price,
        ]);

        // Hitung jumlah item di cart
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Membership berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Menambahkan booking fotografer ke keranjang
     */
    private function addPhotographerToCart(Request $request, Cart $cart)
    {
        // Validasi input
        $request->validate([
            'item_id' => 'required|exists:photographers,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $photographer = Photographer::findOrFail($request->item_id);

        // Buat full datetime untuk start dan end time
        $startDateTime = Carbon::parse("{$request->date} {$request->start_time}");
        $endDateTime = Carbon::parse("{$request->date} {$request->end_time}");

        // Hitung durasi dalam jam
        $durationInHours = $startDateTime->diffInHours($endDateTime);
        $price = $photographer->hourly_rate * $durationInHours;

        // Periksa ketersediaan fotografer
        $isAvailable = $this->checkPhotographerAvailability($photographer->id, $startDateTime, $endDateTime);
        if (!$isAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'Fotografer tidak tersedia pada waktu yang dipilih'
            ], 400);
        }

        CartItem::create([
            'cart_id' => $cart->id,
            'type' => 'photographer',
            'item_id' => $photographer->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'price' => $price,
        ]);

        // Hitung jumlah item di cart
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Jasa fotografer berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Memeriksa ketersediaan fotografer pada waktu tertentu
     */
    private function checkPhotographerAvailability($photographerId, $startTime, $endTime)
    {
        // Cek di cart items
        $inCart = CartItem::where('type', 'photographer')
            ->where('item_id', $photographerId)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        // Jika sudah ada di cart user lain, maka tidak tersedia
        if ($inCart) {
            return false;
        }

        // Jika perlu, tambahkan cek ketersediaan di model Booking lain yang sesuai
        // Contoh: cek di table photographer_bookings

        return true;
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
                switch ($item->type) {
                    case 'field_booking':
                        $field = Field::find($item->item_id);
                        if ($field) {
                            $item->name = $field->name;
                            $item->type_name = 'Booking Lapangan';
                            $item->image = $field->image;
                            $item->formatted_date = Carbon::parse($item->start_time)->format('d M Y');
                            $item->start_time_formatted = Carbon::parse($item->start_time)->format('H:i');
                            $item->end_time_formatted = Carbon::parse($item->end_time)->format('H:i');
                            $item->details = $field->type . ' - ' . $item->start_time_formatted . ' - ' . $item->end_time_formatted;
                        }
                        break;

                    case 'rental_item':
                        $rentalItem = RentalItem::find($item->item_id);
                        if ($rentalItem) {
                            $item->name = $rentalItem->name;
                            $item->type_name = 'Penyewaan Peralatan';
                            $item->image = $rentalItem->image;
                            $item->details = 'Jumlah: ' . $item->quantity;
                        }
                        break;

                    case 'membership':
                        $membership = Membership::find($item->item_id);
                        if ($membership) {
                            $item->name = $membership->name;
                            $item->type_name = 'Keanggotaan';
                            $item->image = $membership->image;
                            $item->details = 'Durasi: ' . $membership->duration . ' bulan';
                        }
                        break;

                    case 'photographer':
                        $photographer = Photographer::find($item->item_id);
                        if ($photographer) {
                            $item->name = $photographer->name;
                            $item->type_name = 'Jasa Fotografer';
                            $item->image = $photographer->image;
                            $item->formatted_date = Carbon::parse($item->start_time)->format('d M Y');
                            $item->start_time_formatted = Carbon::parse($item->start_time)->format('H:i');
                            $item->end_time_formatted = Carbon::parse($item->end_time)->format('H:i');
                            $item->details = $item->formatted_date . ' ' . $item->start_time_formatted . ' - ' . $item->end_time_formatted;
                        }
                        break;
                }

                $totalPrice += $item->price;
            }
        }

        return view('users.cart.index', compact('cartItems', 'totalPrice'));
    }

    /**
     * API untuk menghapus item dari keranjang (untuk AJAX)
     */
    public function apiRemoveFromCart($itemId)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error removing item from cart: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus item dari keranjang
     */
    public function removeFromCart($itemId)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error removing item from cart: ' . $e->getMessage());

            return back()->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
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

    /**
     * Proses checkout dan pembayaran
     */
    public function checkout(Request $request)
    {
        // Dapatkan cart user yang sedang login
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return redirect()->route('user.fields.cart')->with('error', 'Keranjang Anda kosong');
        }

        // Dapatkan semua item di keranjang
        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.fields.cart')->with('error', 'Keranjang Anda kosong');
        }

        // Hitung total harga
        $totalPrice = $cartItems->sum('price');

        // Generate unique order ID
        $orderId = 'ORDER-' . uniqid();

        try {
            // Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Siapkan data item untuk Midtrans
            $itemDetails = [];
            foreach ($cartItems as $item) {
                // Isi item detail berdasarkan tipe
                switch ($item->type) {
                    case 'field_booking':
                        $field = Field::find($item->item_id);
                        $itemName = $field ? $field->name : 'Booking Lapangan';
                        $startTime = Carbon::parse($item->start_time)->format('d M Y H:i');
                        $endTime = Carbon::parse($item->end_time)->format('H:i');
                        $detail = $itemName . ' (' . $startTime . ' - ' . $endTime . ')';
                        break;

                    case 'rental_item':
                        $rentalItem = RentalItem::find($item->item_id);
                        $itemName = $rentalItem ? $rentalItem->name : 'Penyewaan Peralatan';
                        $detail = $itemName . ' (Jumlah: ' . $item->quantity . ')';
                        break;

                    case 'membership':
                        $membership = Membership::find($item->item_id);
                        $itemName = $membership ? $membership->name : 'Keanggotaan';
                        $detail = $itemName;
                        break;

                    case 'photographer':
                        $photographer = Photographer::find($item->item_id);
                        $itemName = $photographer ? $photographer->name : 'Jasa Fotografer';
                        $startTime = Carbon::parse($item->start_time)->format('d M Y H:i');
                        $endTime = Carbon::parse($item->end_time)->format('H:i');
                        $detail = $itemName . ' (' . $startTime . ' - ' . $endTime . ')';
                        break;

                    default:
                        $detail = 'Item #' . $item->id;
                }

                $itemDetails[] = [
                    'id' => $item->id,
                    'price' => $item->price,
                    'quantity' => $item->quantity ?? 1,
                    'name' => $detail
                ];
            }

            // Siapkan parameter transaksi
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $totalPrice,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone ?? '',
                ],
                'item_details' => $itemDetails
            ];

            // Dapatkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan informasi pembayaran
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'amount' => $totalPrice,
                'transaction_status' => 'pending',
            ]);

            // Render view payment checkout dengan data yang diperlukan
            return view('users.payment.checkout', [
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'total_price' => $totalPrice,
                'cart_items' => $cartItems
            ]);

        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Checkout Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Redirect ke halaman cart dengan pesan error
// Sesudah
return redirect()->route('user.cart.view')->with('error', 'Keranjang Anda kosong')
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
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
                switch ($item->type) {
                    case 'field_booking':
                        $field = Field::find($item->item_id);
                        if ($field) {
                            $item->name = $field->name;
                            $item->type_name = 'Booking Lapangan';
                            $item->image = $field->image;
                            $item->formatted_date = Carbon::parse($item->start_time)->format('d M Y');
                            $item->start_time_formatted = Carbon::parse($item->start_time)->format('H:i');
                            $item->end_time_formatted = Carbon::parse($item->end_time)->format('H:i');
                        }
                        break;

                    case 'rental_item':
                        $rentalItem = RentalItem::find($item->item_id);
                        if ($rentalItem) {
                            $item->name = $rentalItem->name;
                            $item->type_name = 'Penyewaan Peralatan';
                            $item->image = $rentalItem->image;
                        }
                        break;

                    case 'membership':
                        $membership = Membership::find($item->item_id);
                        if ($membership) {
                            $item->name = $membership->name;
                            $item->type_name = 'Keanggotaan';
                            $item->image = $membership->image;
                        }
                        break;

                    case 'photographer':
                        $photographer = Photographer::find($item->item_id);
                        if ($photographer) {
                            $item->name = $photographer->name;
                            $item->type_name = 'Jasa Fotografer';
                            $item->image = $photographer->image;
                            $item->formatted_date = Carbon::parse($item->start_time)->format('d M Y');
                            $item->start_time_formatted = Carbon::parse($item->start_time)->format('H:i');
                            $item->end_time_formatted = Carbon::parse($item->end_time)->format('H:i');
                        }
                        break;
                }

                $totalPrice += $item->price;
            }
        }

        return view('components.cart-sidebar', compact('cartItems', 'totalPrice'))->render();
    }
}
