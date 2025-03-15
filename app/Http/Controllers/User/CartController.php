<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Field;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Membership;
use App\Models\RentalItem;
use App\Models\FieldBooking;
use App\Models\Photographer;
use Illuminate\Http\Request;
use App\Models\DiscountUsage;
use App\Models\RentalBooking;
use Illuminate\Support\Facades\DB;
use App\Models\PhotographerBooking;
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
                'type' => 'required|in:field_booking,rental_item,membership,photographer',
            ]);

            // Dapatkan atau buat cart untuk user yang login
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Routing ke method yang sesuai berdasarkan tipe
            switch ($request->type) {
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
                'request' => $request->all(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ],
                500,
            );
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
            'slots.*' => 'string',
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
            'slots_count' => count($selectedSlots),
        ]);

        foreach ($selectedSlots as $slot) {
            // Parse slot info (format: "08:00 - 09:00")
            [$startTime, $endTime] = explode(' - ', $slot);

            // Buat full datetime untuk start dan end time
            $startDateTime = Carbon::parse("{$date} {$startTime}");
            $endDateTime = Carbon::parse("{$date} {$endTime}");

            // Periksa apakah slot sudah ada di cart
            $existingItem = CartItem::where('cart_id', $cart->id)->where('type', 'field_booking')->where('item_id', $field->id)->where('start_time', $startDateTime)->where('end_time', $endDateTime)->first();

            if (!$existingItem) {
                // Periksa apakah slot sudah di-booking oleh user lain
                $isBooked = FieldBooking::where('field_id', $field->id)
                    ->whereDate('start_time', $startDateTime->toDateString())
                    ->where(function ($query) use ($startDateTime, $endDateTime) {
                        // PERBAIKAN: Logika diubah untuk menghindari false positive pada slot berdekatan
                        $query
                            ->where(function ($q) use ($startDateTime, $endDateTime) {
                                // Waktu mulai slot berada dalam rentang booking (kecuali tepat di akhir)
                                $q->where('start_time', '<=', $startDateTime)->where('end_time', '>', $startDateTime);
                            })
                            ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                                // Waktu akhir slot berada dalam rentang booking (kecuali tepat di awal)
                                $q->where('start_time', '<', $endDateTime)->where('end_time', '>=', $endDateTime);
                            })
                            ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                                // Booking berada seluruhnya dalam rentang slot
                                $q->where('start_time', '>=', $startDateTime)->where('end_time', '<=', $endDateTime);
                            });
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($isBooked) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Slot {$slot} sudah di-booking. Silakan pilih slot lain.",
                        ],
                        400,
                    );
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
            'message' => $itemsAdded > 0 ? 'Slot waktu berhasil ditambahkan ke keranjang' : 'Semua slot yang dipilih sudah ada di keranjang',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Menambahkan item rental ke keranjang dengan dukungan untuk jumlah berbeda per slot waktu
     */
    private function addRentalItemToCart(Request $request, Cart $cart)
    {
        // Validasi input dasar
        $request->validate([
            'rental_item_id' => 'required|exists:rental_items,id',
            'date' => 'required|date|after_or_equal:today',
            'slots' => 'required|array', // Mengubah validasi untuk menerima array slot waktu
        ]);

        $rentalItem = RentalItem::findOrFail($request->rental_item_id);
        $date = $request->date;

        // Pastikan slots memiliki format yang benar
        foreach ($request->slots as $slotData) {
            if (!isset($slotData['start_time']) || !isset($slotData['end_time']) || !isset($slotData['quantity'])) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Format data slot tidak valid',
                    ],
                    422,
                );
            }
        }

        // Tambahkan setiap slot ke keranjang secara terpisah
        foreach ($request->slots as $slotData) {
            $startTime = $slotData['start_time'];
            $endTime = $slotData['end_time'];
            $quantity = intval($slotData['quantity']);

            // Lewati slot dengan quantity 0
            if ($quantity <= 0) {
                continue;
            }

            // Buat full datetime untuk start dan end time
            $startDateTime = Carbon::parse("{$date} {$startTime}");
            $endDateTime = Carbon::parse("{$date} {$endTime}");

            // Log untuk debugging
            Log::info('Rental item slot details', [
                'rental_item_id' => $rentalItem->id,
                'rental_item_name' => $rentalItem->name,
                'date' => $date,
                'start_time' => $startDateTime->format('Y-m-d H:i:s'),
                'end_time' => $endDateTime->format('Y-m-d H:i:s'),
                'quantity' => $quantity,
            ]);

            // Periksa apakah item dengan waktu yang sama sudah ada di cart
            $existingItem = CartItem::where('cart_id', $cart->id)->where('type', 'rental_item')->where('item_id', $rentalItem->id)->where('start_time', $startDateTime)->where('end_time', $endDateTime)->first();

            if ($existingItem) {
                // Jika sudah ada, update quantity (tambahkan)
                $newQuantity = $existingItem->quantity + $quantity;
                $existingItem->quantity = $newQuantity;
                $existingItem->price = $rentalItem->rental_price * $newQuantity;
                $existingItem->save();
            } else {
                // Jika belum ada, buat item baru
                CartItem::create([
                    'cart_id' => $cart->id,
                    'type' => 'rental_item',
                    'item_id' => $rentalItem->id,
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'quantity' => $quantity,
                    'price' => $rentalItem->rental_price * $quantity,
                ]);
            }
        }

        // Hitung jumlah item di cart
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Menambahkan membership ke keranjang
     */

/**
 * Menambahkan booking fotografer ke keranjang
 */
private function addPhotographerToCart(Request $request, Cart $cart)
{
    // Validasi input spesifik untuk booking fotografer
    $request->validate([
        'photographer_id' => 'required|exists:photographers,id',
        'date' => 'required|date|after_or_equal:today',
        'start_time' => 'required',
        'end_time' => 'required',
    ]);

    $photographer = Photographer::findOrFail($request->photographer_id);
    $date = $request->date;
    $startTime = $request->start_time;
    $endTime = $request->end_time;

    // Buat full datetime untuk start dan end time
    $startDateTime = Carbon::parse("{$date} {$startTime}");
    $endDateTime = Carbon::parse("{$date} {$endTime}");

    // Log untuk debugging
    Log::info('Photographer booking details', [
        'photographer_id' => $photographer->id,
        'photographer_name' => $photographer->name,
        'date' => $date,
        'start_time' => $startDateTime->format('Y-m-d H:i:s'),
        'end_time' => $endDateTime->format('Y-m-d H:i:s'),
    ]);

    // Periksa apakah fotografer sudah tersedia pada waktu tersebut
    $isBooked = $this->checkPhotographerAvailability($photographer->id, $startDateTime, $endDateTime);

    if ($isBooked) {
        return response()->json([
            'success' => false,
            'message' => "Fotografer tidak tersedia pada waktu yang dipilih. Silakan pilih waktu lain.",
        ], 400);
    }

    // Periksa apakah booking dengan waktu yang sama sudah ada di cart
    $existingItem = CartItem::where('cart_id', $cart->id)
        ->where('type', 'photographer')
        ->where('item_id', $photographer->id)
        ->where('start_time', $startDateTime)
        ->where('end_time', $endDateTime)
        ->first();

    if (!$existingItem) {
        // Tambahkan item ke cart
        CartItem::create([
            'cart_id' => $cart->id,
            'type' => 'photographer',
            'item_id' => $photographer->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'price' => $photographer->price,
        ]);
    }

    // Hitung jumlah item di cart
    $cartCount = CartItem::where('cart_id', $cart->id)->count();

    return response()->json([
        'success' => true,
        'message' => 'Booking fotografer berhasil ditambahkan ke keranjang',
        'cart_count' => $cartCount,
    ]);
}

/**
 * Memeriksa ketersediaan fotografer pada waktu tertentu
 */
private function checkPhotographerAvailability($photographerId, $startTime, $endTime)
{
    // Cek apakah fotografer sudah di-booking pada waktu yang overlap
    $existingBooking = DB::table('photographer_bookings')
        ->where('photographer_id', $photographerId)
        ->where('status', '!=', 'cancelled')
        ->where(function ($query) use ($startTime, $endTime) {
            // Cek overlap waktu
            $query->where(function ($q) use ($startTime, $endTime) {
                // Waktu mulai booking berada dalam rentang waktu yang dipilih
                $q->where('start_time', '<=', $startTime)
                   ->where('end_time', '>', $startTime);
            })
            ->orWhere(function ($q) use ($startTime, $endTime) {
                // Waktu akhir booking berada dalam rentang waktu yang dipilih
                $q->where('start_time', '<', $endTime)
                   ->where('end_time', '>=', $endTime);
            })
            ->orWhere(function ($q) use ($startTime, $endTime) {
                // Booking seluruhnya berada dalam rentang waktu yang dipilih
                $q->where('start_time', '>=', $startTime)
                   ->where('end_time', '<=', $endTime);
            });
        })
        ->exists();

    // Cek juga di cart untuk mencegah double booking yang belum dibayar
    $existingInCart = DB::table('cart_items')
        ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
        ->where('cart_items.type', 'photographer')
        ->where('cart_items.item_id', $photographerId)
        ->where('carts.user_id', '!=', Auth::id()) // Abaikan cart milik user yang sedang login
        ->where(function ($query) use ($startTime, $endTime) {
            // Logika overlap waktu yang sama seperti di atas
            $query->where(function ($q) use ($startTime, $endTime) {
                $q->where('cart_items.start_time', '<=', $startTime)
                   ->where('cart_items.end_time', '>', $startTime);
            })
            ->orWhere(function ($q) use ($startTime, $endTime) {
                $q->where('cart_items.start_time', '<', $endTime)
                   ->where('cart_items.end_time', '>=', $endTime);
            })
            ->orWhere(function ($q) use ($startTime, $endTime) {
                $q->where('cart_items.start_time', '>=', $startTime)
                   ->where('cart_items.end_time', '<=', $endTime);
            });
        })
        ->exists();

    return $existingBooking || $existingInCart;
}

 /**
 * Menampilkan halaman keranjang booking
 */
public function viewCart()
{
    $cart = Cart::where('user_id', Auth::id())->first();
    $cartItems = [];
    $subtotal = 0;
    $discount = null;
    $discountAmount = 0;
    $totalPrice = 0;

    if ($cart) {
        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        // Calculate subtotal
        $subtotal = $cartItems->sum('price');

        // Check if there's a discount applied
        if (session()->has('cart_discount')) {
            $discount = session('cart_discount');
            $discountAmount = $discount['amount'];

            // Recalculate discount amount to ensure it's accurate
            $discountObj = Discount::find($discount['id']);
            if ($discountObj) {
                if ($discountObj->applicable_to !== 'all') {
                    // Only apply to specific item types
                    $applicableItems = $cartItems->filter(function ($item) use ($discountObj) {
                        return $item->type === $discountObj->applicable_to;
                    });

                    $applicableSubtotal = $applicableItems->sum('price');
                    $discountAmount = $discountObj->calculateDiscount($applicableSubtotal);
                } else {
                    // Apply to all items
                    $discountAmount = $discountObj->calculateDiscount($subtotal);
                }

                // Update session with recalculated amount
                $discount['amount'] = $discountAmount;
                session()->put('cart_discount', $discount);
            }
        }

        // Calculate total price after discount
        $totalPrice = $subtotal - $discountAmount;

        // Prepare data for view and enhance items with additional information
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
        }
    }

    // Ambil data diskon aktif untuk ditampilkan di modal "Lihat Promo"
    $activeDiscounts = Discount::where('is_active', true)
        ->where(function($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
        })
        ->orderBy('value', 'desc')
        ->limit(10)
        ->get();

    return view('users.cart.index', compact('cartItems', 'subtotal', 'discount', 'discountAmount', 'totalPrice', 'activeDiscounts'));
}

    /**
     * API untuk menghapus item dari keranjang (untuk AJAX)
     */
    public function apiRemoveFromCart($itemId)
    {
        try {
            $cart = Cart::where('user_id', Auth::id())->first();

            if (!$cart) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Keranjang tidak ditemukan',
                    ],
                    404,
                );
            }

            $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();

            if (!$cartItem) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Item tidak ditemukan',
                    ],
                    404,
                );
            }

            $cartItem->delete();
            $cartCount = CartItem::where('cart_id', $cart->id)->count();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart_count' => $cartCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing item from cart: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Menghapus item dari keranjang
     */
    public function removeFromCart($itemId)
    {
        try {
            $cart = Cart::where('user_id', operator: Auth::id())->first();

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
                    'cart_count' => $cartCount,
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
            'count' => $count,
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

        return redirect()->route('user.cart.view')->with('success', 'Keranjang berhasil dikosongkan');
    }

/**
 * Memproses kode kupon diskon
 */
public function applyDiscount(Request $request)
{
    $request->validate([
        'discount_code' => 'required|string|max:50',
    ]);

    $discountCode = $request->discount_code;
    $cart = Cart::where('user_id', Auth::id())->first();

    if (!$cart) {
        return back()->with('error', 'Keranjang tidak ditemukan');
    }

    $cartItems = CartItem::where('cart_id', $cart->id)->get();
    if ($cartItems->isEmpty()) {
        return back()->with('error', 'Keranjang Anda kosong');
    }

    // Hitung subtotal
    $subtotal = $cartItems->sum('price');

    // Cari diskon berdasarkan kode
    $discount = Discount::where('code', $discountCode)
        ->where('is_active', true)
        ->first();

    // Cek apakah diskon ditemukan
    if (!$discount) {
        return back()->with('error', 'Kode kupon tidak valid');
    }

    // Cek apakah diskon masih berlaku untuk user ini
    if (!$discount->isValidForUser(Auth::id())) {
        return back()->with('error', 'Kode kupon tidak dapat digunakan');
    }

    // Cek minimal pembelian
    if ($subtotal < $discount->min_order) {
        return back()->with('error', 'Minimal pembelian untuk kupon ini adalah Rp ' . number_format($discount->min_order, 0, ',', '.'));
    }

    // Cek jenis item yang applicable
    if ($discount->applicable_to !== 'all') {
        // Filter cart items berdasarkan jenis yang applicable
        $applicableItems = $cartItems->filter(function ($item) use ($discount) {
            return $item->type === $discount->applicable_to;
        });

        if ($applicableItems->isEmpty()) {
            return back()->with('error', 'Kupon ini hanya berlaku untuk ' . $this->getItemTypeName($discount->applicable_to));
        }

        // Hitung subtotal untuk item yang applicable
        $applicableSubtotal = $applicableItems->sum('price');
        // Hitung diskon
        $discountAmount = $discount->calculateDiscount($applicableSubtotal);
    } else {
        // Hitung diskon untuk semua item
        $discountAmount = $discount->calculateDiscount($subtotal);
    }

    // Simpan informasi diskon di session
    session()->put('cart_discount', [
        'id' => $discount->id,
        'code' => $discount->code,
        'name' => $discount->name,
        'amount' => $discountAmount,
        'subtotal' => $subtotal,
        'total' => $subtotal - $discountAmount,
    ]);

    return back()->with('success', 'Kupon berhasil diterapkan: ' . $discount->name);
}

    /**
     * Menghapus kode kupon dari keranjang
     */
    public function removeDiscount()
    {
        session()->forget('cart_discount');
        return back()->with('success', 'Kupon berhasil dihapus');
    }

/**
 * Helper untuk mendapatkan nama jenis item
 */
private function getItemTypeName($type)
{
    $types = [
        'field_booking' => 'Booking Lapangan',
        'rental_item' => 'Penyewaan Peralatan',
        'membership' => 'Keanggotaan',
        'photographer' => 'Jasa Fotografer',
    ];

    return $types[$type] ?? $type;
}

    /**
     * Proses checkout dan pembayaran
     */
    public function checkout(Request $request)
    {
        // Dapatkan cart user yang sedang login
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return redirect()->route('user.cart.view')->with('error', 'Keranjang Anda kosong');
        }

        // Dapatkan semua item di keranjang
        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.view')->with('error', 'Keranjang Anda kosong');
        }

        // Cek ketersediaan item
        $unavailableItems = [];

        foreach ($cartItems as $item) {
            if ($item->type == 'field_booking') {
                // Cek apakah masih tersedia
                $field = Field::find($item->item_id);
                if ($field) {
                    $isBooked = FieldBooking::where('field_id', $item->item_id)
                        ->whereDate('start_time', Carbon::parse($item->start_time)->toDateString())
                        ->where(function ($query) use ($item) {
                            // Logika pengecekan overlap waktu
                            $query
                                ->where(function ($q) use ($item) {
                                    $q->where('start_time', '<=', $item->start_time)->where('end_time', '>', $item->start_time);
                                })
                                ->orWhere(function ($q) use ($item) {
                                    $q->where('start_time', '<', $item->end_time)->where('end_time', '>=', $item->end_time);
                                })
                                ->orWhere(function ($q) use ($item) {
                                    $q->where('start_time', '>=', $item->start_time)->where('end_time', '<=', $item->end_time);
                                });
                        })
                        ->where('status', '!=', 'cancelled')
                        ->exists();

                    if ($isBooked) {
                        $startTime = Carbon::parse($item->start_time)->format('d M Y H:i');
                        $endTime = Carbon::parse($item->end_time)->format('H:i');
                        $unavailableItems[] = $field->name . ' (' . $startTime . ' - ' . $endTime . ')';
                    }
                }
            } elseif ($item->type == 'rental_item') {
                // Cek ketersediaan rental item
                $rentalItem = RentalItem::find($item->item_id);
                if ($rentalItem) {
                    // Hanya memeriksa booking yang sudah dikonfirmasi, abaikan item di keranjang
                    $bookedQuantity = RentalBooking::where('rental_item_id', $rentalItem->id)
                        ->whereNotIn('status', ['cancelled'])
                        ->where(function ($query) use ($item) {
                            $query
                                ->where(function ($q) use ($item) {
                                    $q->where('start_time', '>=', $item->start_time)->where('start_time', '<', $item->end_time);
                                })
                                ->orWhere(function ($q) use ($item) {
                                    $q->where('end_time', '>', $item->start_time)->where('end_time', '<=', $item->end_time);
                                })
                                ->orWhere(function ($q) use ($item) {
                                    $q->where('start_time', '<=', $item->start_time)->where('end_time', '>=', $item->end_time);
                                });
                        })
                        ->sum('quantity');

                    $availableQuantity = $rentalItem->stock_total - $bookedQuantity;

                    if ($item->quantity > $availableQuantity) {
                        $unavailableItems[] = $rentalItem->name . ' (Tersedia: ' . $availableQuantity . ', Diminta: ' . $item->quantity . ')';
                    }
                }
            }
            elseif ($item->type == 'photographer') {
                // Cek ketersediaan fotografer
                $photographer = Photographer::find($item->item_id);
                if ($photographer) {
                    $isBooked = PhotographerBooking::where('photographer_id', $item->item_id)
                        ->where(function ($query) use ($item) {
                            // Logika pengecekan overlap waktu
                            $query
                                ->where(function ($q) use ($item) {
                                    $q->where('start_time', '<=', $item->start_time)->where('end_time', '>', $item->start_time);
                                })
                                ->orWhere(function ($q) use ($item) {
                                    $q->where('start_time', '<', $item->end_time)->where('end_time', '>=', $item->end_time);
                                })
                                ->orWhere(function ($q) use ($item) {
                                    $q->where('start_time', '>=', $item->start_time)->where('end_time', '<=', $item->end_time);
                                });
                        })
                        ->where('status', '!=', 'cancelled')
                        ->exists();

                    if ($isBooked) {
                        $startTime = Carbon::parse($item->start_time)->format('d M Y H:i');
                        $endTime = Carbon::parse($item->end_time)->format('H:i');
                        $unavailableItems[] = $photographer->name . ' (' . $startTime . ' - ' . $endTime . ')';
                    }
                }
            }
            // Tambahkan pengecekan untuk jenis item lainnya jika diperlukan
        }

        if (!empty($unavailableItems)) {
            // Ada item yang sudah tidak tersedia
            return redirect()
                ->route('user.cart.view')
                ->with('error', 'Beberapa item yang Anda pilih sudah tidak tersedia: ' . implode(', ', $unavailableItems));
        }

        // Hitung subtotal
        $subtotal = $cartItems->sum('price');

        // Variabel untuk diskon
        $discountId = null;
        $discountAmount = 0;

        // Cek apakah ada diskon yang diterapkan
        if (session()->has('cart_discount')) {
            $cartDiscount = session('cart_discount');
            $discountId = $cartDiscount['id'];
            $discountAmount = $cartDiscount['amount'];

            // Verifikasi ulang diskon
            $discount = Discount::find($discountId);

            if (!$discount || !$discount->isValidForUser(Auth::id())) {
                // Diskon tidak valid, hapus dari session
                session()->forget('cart_discount');
                return redirect()->route('user.cart.view')->with('error', 'Kupon diskon tidak valid atau sudah tidak dapat digunakan');
            }

            // Re-calculate discount (untuk keamanan)
            $discountAmount = $discount->calculateDiscount($subtotal);
        }

        // Hitung total setelah diskon
        $totalPrice = $subtotal - $discountAmount;

        // Generate unique order ID
        $orderId = 'ORDER-' . now()->format('Ymd-His') . '-' . mt_rand(1000, 9999);

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
                    'price' => $item->type == 'rental_item' ? $item->price / $item->quantity : $item->price,
                    'quantity' => $item->quantity ?? 1,
                    'name' => $detail,
                ];
            }

            // Jika ada diskon, tambahkan sebagai item negatif
            if ($discountAmount > 0) {
                $itemDetails[] = [
                    'id' => 'DISCOUNT',
                    'price' => -$discountAmount,
                    'quantity' => 1,
                    'name' => 'Diskon: ' . ($cartDiscount['name'] ?? 'Kupon Diskon'),
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
                'item_details' => $itemDetails,
            ];

            // Simpan informasi pembayaran SEBELUM membuat booking
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'amount' => $totalPrice,
                'discount_id' => $discountId,
                'discount_amount' => $discountAmount,
                'original_amount' => $subtotal,
                'transaction_status' => 'pending',
                'expires_at' => now()->addMinutes(30), // 30 minutes expiration
            ]);

            // Buat booking records dalam transaction
            DB::beginTransaction();
            try {
                foreach ($cartItems as $item) {
                    if ($item->type == 'field_booking') {
                        $field = Field::find($item->item_id);
                        if ($field) {
                            FieldBooking::create([
                                'user_id' => Auth::id(),
                                'field_id' => $item->item_id,
                                'payment_id' => $payment->id,
                                'start_time' => $item->start_time,
                                'end_time' => $item->end_time,
                                'total_price' => $item->price,
                                'status' => 'pending',
                            ]);
                        }
                    } elseif ($item->type == 'rental_item') {
                        $rentalItem = RentalItem::find($item->item_id);
                        if ($rentalItem) {
                            RentalBooking::create([
                                'user_id' => Auth::id(),
                                'rental_item_id' => $item->item_id,
                                'payment_id' => $payment->id,
                                'start_time' => $item->start_time,
                                'end_time' => $item->end_time,
                                'quantity' => $item->quantity,
                                'total_price' => $item->price,
                                'status' => 'pending',
                            ]);
                        }
                    } elseif ($item->type == 'membership') {
                        $membership = Membership::find($item->item_id);
                        if ($membership) {
                            MembershipSubscription::create([
                                'user_id' => Auth::id(),
                                'membership_id' => $item->item_id,
                                'payment_id' => $payment->id,
                                'price' => $item->price,
                                'status' => 'pending',
                                'start_date' => now(),
                                'end_date' => now()->addMonths($membership->duration),
                            ]);
                        }
                    } elseif ($item->type == 'photographer') {
                        $photographer = Photographer::find($item->item_id);
                        if ($photographer) {
                            PhotographerBooking::create([
                                'user_id' => Auth::id(),
                                'photographer_id' => $item->item_id,
                                'payment_id' => $payment->id,
                                'start_time' => $item->start_time,
                                'end_time' => $item->end_time,
                                'price' => $item->price,
                                'status' => 'pending',
                            ]);
                        }
                    }
                }

                // Jika ada diskon, catat penggunaan diskon
                if ($discountId) {
                    DiscountUsage::create([
                        'discount_id' => $discountId,
                        'user_id' => Auth::id(),
                        'payment_id' => $payment->id,
                        'discount_amount' => $discountAmount,
                    ]);

                    // Hapus diskon dari session setelah digunakan
                    session()->forget('cart_discount');
                }

                // Hapus items dari cart setelah berhasil membuat booking
                $cartItemsToDelete = $cartItems->pluck('id')->toArray();
                CartItem::whereIn('id', $cartItemsToDelete)->delete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error creating bookings: ' . $e->getMessage());
                return redirect()
                    ->route('user.cart.view')
                    ->with('error', 'Gagal membuat booking: ' . $e->getMessage());
            }

            // Dapatkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Render view payment checkout dengan data yang diperlukan
            return view('users.payment.checkout', [
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'total_price' => $totalPrice,
                'original_amount' => $subtotal,
                'discount_amount' => $discountAmount,
                'cart_items' => $cartItems,
                'expires_at' => $payment->expires_at, // Kirim hanya expires_at saja
            ]);
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Checkout Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Redirect ke halaman cart dengan pesan error
            return redirect()
                ->route('user.cart.view')
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
