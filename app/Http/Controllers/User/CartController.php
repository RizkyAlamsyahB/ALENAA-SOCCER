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
use App\Models\PointRedemption;
use App\Models\MembershipSession;
use Illuminate\Support\Facades\DB;
use App\Models\PhotographerBooking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\MembershipSubscription;

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

    // Tambahkan method ini di app/Http/Controllers/User/CartController.php

    /**
     * Menambahkan membership ke keranjang
     */
    private function addMembershipToCart(Request $request, Cart $cart)
    {
        // Validasi input spesifik untuk membership
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
        ]);

        $membership = Membership::findOrFail($request->membership_id);

        // Periksa apakah membership sudah ada di cart
        $existingItem = CartItem::where('cart_id', $cart->id)->where('type', 'membership')->where('item_id', $membership->id)->first();

        if (!$existingItem) {
            // Tambahkan item ke cart
            CartItem::create([
                'cart_id' => $cart->id,
                'type' => 'membership',
                'item_id' => $membership->id,
                'price' => $membership->price,
            ]);
        }

        // Hitung jumlah item di cart
        $cartCount = CartItem::where('cart_id', $cart->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Paket membership berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Route khusus untuk menambahkan membership ke cart (setelah pilih jadwal)
     */
/**
 * Route khusus untuk menambahkan membership ke cart (setelah pilih jadwal)
 */
public function addMembershipToCartRoute($id)
{
    try {
        // Tambahkan dd untuk debugging

        // Validasi bahwa session membership_sessions ada
        if (!session()->has('membership_sessions')) {
            return redirect()
                ->route('user.membership.select.schedule', ['id' => $id])
                ->with('error', 'Silakan pilih jadwal terlebih dahulu');
        }

        $sessionData = session('membership_sessions');
        if ($sessionData['membership_id'] != $id) {
            return redirect()
                ->route('user.membership.select.schedule', ['id' => $id])
                ->with('error', 'Data jadwal tidak valid, silakan pilih kembali');
        }

        // Dapatkan atau buat cart untuk user yang login
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $membership = Membership::findOrFail($id);

        // Periksa apakah membership sudah ada di cart
        $existingItem = CartItem::where('cart_id', $cart->id)->where('type', 'membership')->where('item_id', $membership->id)->first();

        if (!$existingItem) {
            // Ambil periode pembayaran dari session
            $paymentPeriod = $sessionData['payment_period'] ?? 'weekly';
            $price = $sessionData['price'] ?? $membership->price;

            // Tambahkan item ke cart
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'type' => 'membership',
                'item_id' => $membership->id,
                'price' => $price, // Gunakan harga sesuai periode pembayaran
                'membership_sessions' => json_encode($sessionData['sessions']),
                'payment_period' => $paymentPeriod, // Simpan periode pembayaran
            ]);
        }

        // Hapus session setelah ditambahkan ke cart
        session()->forget('membership_sessions');

        return redirect()->route('user.cart.view')->with('success', 'Paket membership berhasil ditambahkan ke keranjang');
    } catch (\Exception $e) {
        return redirect()
            ->route('user.membership.select.schedule', ['id' => $id])
            ->with('error', 'Gagal menambahkan ke keranjang: ' . $e->getMessage());
    }
}
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
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Fotografer tidak tersedia pada waktu yang dipilih. Silakan pilih waktu lain.',
                ],
                400,
            );
        }

        // Periksa apakah booking dengan waktu yang sama sudah ada di cart
        $existingItem = CartItem::where('cart_id', $cart->id)->where('type', 'photographer')->where('item_id', $photographer->id)->where('start_time', $startDateTime)->where('end_time', $endDateTime)->first();

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
                $query
                    ->where(function ($q) use ($startTime, $endTime) {
                        // Waktu mulai booking berada dalam rentang waktu yang dipilih
                        $q->where('start_time', '<=', $startTime)->where('end_time', '>', $startTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        // Waktu akhir booking berada dalam rentang waktu yang dipilih
                        $q->where('start_time', '<', $endTime)->where('end_time', '>=', $endTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        // Booking seluruhnya berada dalam rentang waktu yang dipilih
                        $q->where('start_time', '>=', $startTime)->where('end_time', '<=', $endTime);
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
                $query
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->where('cart_items.start_time', '<=', $startTime)->where('cart_items.end_time', '>', $startTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('cart_items.start_time', '<', $endTime)->where('cart_items.end_time', '>=', $endTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('cart_items.start_time', '>=', $startTime)->where('cart_items.end_time', '<=', $endTime);
                    });
            })
            ->exists();

        return $existingBooking || $existingInCart;
    }

    public function viewCart()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        $cartItems = [];
        $subtotal = 0;
        $totalPrice = 0;

        if ($cart) {
            $cartItems = CartItem::where('cart_id', $cart->id)->get();
            $subtotal = $cartItems->sum('price');
            $totalPrice = $subtotal;

            // Jika ada diskon di session, kurangi total price
            if (session()->has('cart_discount')) {
                $discountAmount = session('cart_discount')['amount'];
                $totalPrice -= $discountAmount;
            }
        }

        // Tambahkan query untuk mendapatkan semua diskon aktif
        $activeDiscounts = Discount::where('is_active', 1)
            ->where(function ($query) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->get();

        return view('users.cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'totalPrice' => $totalPrice,
            'discountAmount' => session('cart_discount')['amount'] ?? 0,
            'activeDiscounts' => $activeDiscounts // Tambahkan variabel ini
        ]);
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
 * Menerapkan diskon reguler pada keranjang
 */
public function applyDiscount(Request $request)
{
    Log::info('Apply Discount Started', [
        'discount_code' => $request->discount_code,
        'user_id' => Auth::id()
    ]);

    $discountCode = $request->discount_code;
    $cart = Cart::where('user_id', Auth::id())->first();

    if (!$cart) {
        Log::error('Cart not found');
        return back()->with('error', 'Keranjang tidak ditemukan');
    }

    $cartItems = CartItem::where('cart_id', $cart->id)->get();
    $subtotal = $cartItems->sum('price');

    Log::info('Cart Subtotal', ['subtotal' => $subtotal]);

    // Cari diskon reguler
    $discount = Discount::where('code', $discountCode)
        ->where('is_active', 1)
        ->where(function ($query) {
            $query->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
        })
        ->where(function ($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
        })
        ->first();

    if (!$discount) {
        Log::error('Discount not found or inactive');
        return back()->with('error', 'Kode diskon tidak valid atau sudah kadaluarsa');
    }

    // Verifikasi penggunaan diskon
    if (!$discount->isValidForUser(Auth::id())) {
        Log::error('Discount usage limit exceeded for user');
        return back()->with('error', 'Anda sudah mencapai batas penggunaan kupon ini');
    }

    // Verifikasi minimum order
    if ($subtotal < $discount->min_order) {
        Log::error('Minimum order not met', [
            'subtotal' => $subtotal,
            'min_order' => $discount->min_order
        ]);
        return back()->with('error', 'Minimum pembelian Rp ' . number_format($discount->min_order, 0, ',', '.') . ' untuk menggunakan kupon ini');
    }

    // Hitung diskon
    $discountAmount = $discount->calculateDiscount($subtotal);

    Log::info('Discount Calculation', [
        'subtotal' => $subtotal,
        'discount_amount' => $discountAmount
    ]);

    // Simpan ke session
    session()->put('cart_discount', [
        'id' => $discount->id,
        'code' => $discountCode,
        'name' => $discount->name,
        'amount' => $discountAmount,
        'subtotal' => $subtotal,
        'total' => $subtotal - $discountAmount,
        'is_point_redemption' => false
    ]);

    return back()->with('success', 'Diskon berhasil diterapkan: ' . $discount->name);
}

/**
 * Menerapkan voucher dari penukaran poin pada keranjang
 */
public function applyPointVoucher(Request $request)
{
    Log::info('Apply Point Voucher Started', [
        'voucher_code' => $request->voucher_code,
        'user_id' => Auth::id()
    ]);

    $voucherCode = $request->voucher_code;
    $cart = Cart::where('user_id', Auth::id())->first();

    if (!$cart) {
        Log::error('Cart not found');
        return back()->with('error', 'Keranjang tidak ditemukan');
    }

    $cartItems = CartItem::where('cart_id', $cart->id)->get();
    $subtotal = $cartItems->sum('price');

    Log::info('Cart Subtotal', ['subtotal' => $subtotal]);

    // Cari voucher poin
    $pointRedemption = PointRedemption::where('discount_code', $voucherCode)
        ->where('user_id', Auth::id())
        ->where('status', 'active')
        ->first();

    if (!$pointRedemption) {
        Log::error('Point voucher not found or inactive');
        return back()->with('error', 'Kode voucher poin tidak valid atau sudah digunakan');
    }

    $pointVoucher = $pointRedemption->pointVoucher;

    Log::info('Point Voucher Found', [
        'voucher_id' => $pointVoucher->id,
        'discount_type' => $pointVoucher->discount_type,
        'discount_value' => $pointVoucher->discount_value
    ]);

    // Verifikasi apakah voucher masih berlaku
    if ($pointRedemption->expires_at && Carbon::parse($pointRedemption->expires_at)->isPast()) {
        Log::error('Point voucher expired');
        return back()->with('error', 'Voucher poin sudah kadaluarsa');
    }

    // Verifikasi minimum order
    if ($subtotal < $pointVoucher->min_order) {
        Log::error('Minimum order not met for point voucher', [
            'subtotal' => $subtotal,
            'min_order' => $pointVoucher->min_order
        ]);
        return back()->with('error', 'Minimum pembelian Rp ' . number_format($pointVoucher->min_order, 0, ',', '.') . ' untuk menggunakan voucher ini');
    }

    // Hitung diskon
    $discountAmount = $pointVoucher->calculateDiscount($subtotal);

    Log::info('Point Voucher Discount Calculation', [
        'subtotal' => $subtotal,
        'discount_amount' => $discountAmount
    ]);

    // Simpan ke session dengan key yang berbeda
    session()->put('cart_point_voucher', [
        'id' => $pointVoucher->id,
        'code' => $voucherCode,
        'name' => $pointVoucher->name,
        'amount' => $discountAmount,
        'subtotal' => $subtotal,
        'total' => $subtotal - $discountAmount,
        'point_redemption_id' => $pointRedemption->id
    ]);

    return back()->with('success', 'Voucher poin berhasil diterapkan: ' . $pointVoucher->name);
}

/**
 * Menghapus diskon atau voucher poin dari keranjang
 */
public function removeDiscount(Request $request)
{
    // Cek diskon mana yang sedang aktif
    if (session()->has('cart_discount')) {
        session()->forget('cart_discount');
        $message = 'Diskon berhasil dihapus';
    } elseif (session()->has('cart_point_voucher')) {
        session()->forget('cart_point_voucher');
        $message = 'Voucher poin berhasil dihapus';
    } else {
        $message = 'Tidak ada diskon atau voucher untuk dihapus';
    }

    return back()->with('success', $message);
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
        } elseif ($item->type == 'photographer') {
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
        } elseif ($item->type == 'membership') {
            // Cek ketersediaan jadwal membership jika ada
            if (!empty($item->membership_sessions)) {
                $sessions = json_decode($item->membership_sessions, true);
                $membership = Membership::find($item->item_id);
                $paymentPeriod = $item->payment_period ?? 'weekly';

                if ($membership) {
                    $fieldId = $membership->field_id;

                    // Untuk pembayaran bulanan, persiapkan semua sesi selama 4 minggu
                    $allSessions = [];

                    if ($paymentPeriod === 'monthly') {
                        // Buat jadwal untuk 4 minggu berdasarkan pola jadwal minggu pertama
                        for ($week = 0; $week < 4; $week++) {
                            foreach ($sessions as $session) {
                                $sessionDate = Carbon::parse($session['date']);

                                // Tambahkan 7 hari untuk setiap minggu berikutnya
                                if ($week > 0) {
                                    $sessionDate->addDays(7 * $week);
                                }

                                $allSessions[] = [
                                    'date' => $sessionDate->format('Y-m-d'),
                                    'start_time' => $session['start_time'],
                                    'end_time' => $session['end_time'],
                                ];
                            }
                        }

                        // Ganti sessions dengan allSessions untuk pemeriksaan
                        $sessionsToCheck = $allSessions;
                    } else {
                        $sessionsToCheck = $sessions;
                    }

                    Log::info('Sessions to check for conflicts', ['sessions' => $sessionsToCheck]);

                    foreach ($sessionsToCheck as $session) {
                        $sessionDate = is_string($session['date']) ? $session['date'] : Carbon::parse($session['date'])->toDateString();

                        // Parse waktu awal & akhir, pastikan format yang benar
                        $startTimeStr = $session['start_time'];
                        $endTimeStr = $session['end_time'];

                        // Jika start_time/end_time adalah objek datetime, konversi ke string
                        if ($startTimeStr instanceof \Carbon\Carbon) {
                            $startTimeStr = $startTimeStr->format('H:i:s');
                        }
                        if ($endTimeStr instanceof \Carbon\Carbon) {
                            $endTimeStr = $endTimeStr->format('H:i:s');
                        }

                        try {
                            // Buat datetime yang benar dengan menggunakan tanggal dari session
                            if (strpos($startTimeStr, ' ') !== false && strpos($startTimeStr, '-') !== false) {
                                // Jika sudah termasuk tanggal, ekstrak waktu saja
                                $tempDateTime = Carbon::parse($startTimeStr);
                                $startTimeStr = $tempDateTime->format('H:i:s');
                            }

                            if (strpos($endTimeStr, ' ') !== false && strpos($endTimeStr, '-') !== false) {
                                // Jika sudah termasuk tanggal, ekstrak waktu saja
                                $tempDateTime = Carbon::parse($endTimeStr);
                                $endTimeStr = $tempDateTime->format('H:i:s');
                            }

                            // Buat datetime yang benar dengan kombinasi tanggal dari session dan waktu
                            $startDateTime = Carbon::parse($sessionDate . ' ' . $startTimeStr);
                            $endDateTime = Carbon::parse($sessionDate . ' ' . $endTimeStr);

                            // Log untuk debugging
                            Log::info('Checking availability for session', [
                                'session_date' => $sessionDate,
                                'start_time' => $startDateTime->format('Y-m-d H:i:s'),
                                'end_time' => $endDateTime->format('Y-m-d H:i:s')
                            ]);
                        } catch (\Exception $e) {
                            // Log error jika gagal parsing datetime
                            Log::error('Error parsing datetime for conflict check', [
                                'sessionDate' => $sessionDate,
                                'startTimeStr' => $startTimeStr,
                                'endTimeStr' => $endTimeStr,
                                'error' => $e->getMessage()
                            ]);

                            $unavailableItems[] = $membership->name . ' (Format tanggal/waktu tidak valid)';
                            continue;
                        }

                        // Cek apakah ada booking lain di waktu yang sama
                        $isBooked = FieldBooking::where('field_id', $fieldId)
                            ->where(function ($query) use ($startDateTime, $endDateTime) {
                                $query
                                    ->where(function ($q) use ($startDateTime, $endDateTime) {
                                        $q->where('start_time', '<=', $startDateTime)->where('end_time', '>', $startDateTime);
                                    })
                                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                                        $q->where('start_time', '<', $endDateTime)->where('end_time', '>=', $endDateTime);
                                    })
                                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                                        $q->where('start_time', '>=', $startDateTime)->where('end_time', '<=', $endDateTime);
                                    });
                            })
                            ->where('status', '!=', 'cancelled')
                            ->exists();

                        if ($isBooked) {
                            $startTimeFormat = $startDateTime->format('d M Y H:i');
                            $endTimeFormat = $endDateTime->format('H:i');
                            $unavailableItems[] = $membership->name . ' - Sesi pada ' . $startTimeFormat . ' - ' . $endTimeFormat;
                        }
                    }
                }
            }
        }
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
    $pointRedemptionId = null;
    $discountAmount = 0;

    // Cek apakah ada diskon reguler yang diterapkan
    if (session()->has('cart_discount')) {
        $cartDiscount = session('cart_discount');
        $discountId = $cartDiscount['id'];
        $discountAmount = $cartDiscount['amount'];

        // Verifikasi ulang diskon biasa
        $discount = Discount::find($discountId);

        if (!$discount || !$discount->isValidForUser(Auth::id())) {
            // Diskon tidak valid, hapus dari session
            session()->forget('cart_discount');
            return redirect()->route('user.cart.view')->with('error', 'Kupon diskon tidak valid atau sudah tidak dapat digunakan');
        }

        // Re-calculate discount (untuk keamanan)
        $discountAmount = $discount->calculateDiscount($subtotal);
    }
    // Cek apakah ada voucher poin yang diterapkan
    elseif (session()->has('cart_point_voucher')) {
        $cartPointVoucher = session('cart_point_voucher');
        $discountId = $cartPointVoucher['id']; // ID dari point_voucher
        $discountAmount = $cartPointVoucher['amount'];
        $pointRedemptionId = $cartPointVoucher['point_redemption_id'];

        // Verifikasi ulang redemption
        $redemption = PointRedemption::find($pointRedemptionId);

        if (!$redemption || $redemption->user_id != Auth::id() || $redemption->status !== 'active') {
            // Voucher tidak valid, hapus dari session
            session()->forget('cart_point_voucher');
            return redirect()->route('user.cart.view')->with('error', 'Voucher poin tidak valid atau sudah digunakan');
        }

        // Verifikasi apakah voucher masih berlaku
        if ($redemption->expires_at && Carbon::parse($redemption->expires_at)->isPast()) {
            session()->forget('cart_point_voucher');
            return redirect()->route('user.cart.view')->with('error', 'Voucher poin sudah kadaluarsa');
        }

        // Re-calculate discount (untuk keamanan)
        $pointVoucher = $redemption->pointVoucher;
        $discountAmount = $pointVoucher->calculateDiscount($subtotal);
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

                    // Tambahkan periode pembayaran ke detail
                    $paymentPeriod = $item->payment_period ?? 'weekly';
                    $periodText = $paymentPeriod === 'monthly' ? 'Bulanan' : 'Mingguan';
                    $detail = $itemName . ' (Periode: ' . $periodText . ')';
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
            $discountName = '';
            if (session()->has('cart_discount')) {
                $discountName = session('cart_discount')['name'] ?? 'Kupon Diskon';
            } elseif (session()->has('cart_point_voucher')) {
                $discountName = session('cart_point_voucher')['name'] ?? 'Voucher Poin';
            }

            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -$discountAmount,
                'quantity' => 1,
                'name' => 'Diskon: ' . $discountName,
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
            'discount_id' => session()->has('cart_discount') ? $discountId : null,
            'point_redemption_id' => $pointRedemptionId,
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
                        // Ambil periode pembayaran (default: weekly)
                        $paymentPeriod = $item->payment_period ?? 'weekly';

                        // Tentukan end_date berdasarkan periode pembayaran
                        $startDate = now();
                        $endDate = null;

                        if ($paymentPeriod === 'monthly') {
                            // Jika bulanan, end_date adalah 28 hari (4 minggu) dari sekarang
                            $endDate = now()->addDays(27)->endOfDay();
                        } else {
                            // Jika mingguan (default), end_date adalah 6 hari dari sekarang
                            $endDate = now()->addDays(6)->endOfDay();
                        }

                        // Jika ini adalah bulanan, invoice_sent langsung true karena tidak butuh invoice perpanjangan
                        $invoiceSent = ($paymentPeriod === 'monthly');

                        $subscription = MembershipSubscription::create([
                            'user_id' => Auth::id(),
                            'membership_id' => $item->item_id,
                            'payment_id' => $payment->id,
                            'price' => $item->price,
                            'status' => 'pending',
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'invoice_sent' => $invoiceSent,
                            'payment_period' => $paymentPeriod,
                        ]);

                        // Jika ada data sesi membership, buat jadwal sesi
                        if (!empty($item->membership_sessions)) {
                            $originalSessions = json_decode($item->membership_sessions, true);

                            // Array untuk menyimpan semua sesi yang akan dibuat
                            $allSessions = [];

                            // Untuk pembayaran bulanan, perlu membuat jadwal untuk 4 minggu
                            if ($paymentPeriod === 'monthly') {
                                // Log untuk debugging
                                Log::info('Creating monthly membership sessions', [
                                    'subscription_id' => $subscription->id,
                                    'original_sessions_count' => count($originalSessions)
                                ]);

                                // Buat jadwal untuk 4 minggu berdasarkan pola jadwal minggu pertama
                                for ($week = 0; $week < 4; $week++) {
                                    foreach ($originalSessions as $session) {
                                        // Parse tanggal dasar
                                        $sessionDate = Carbon::parse($session['date']);

                                        // Tambahkan 7 hari untuk setiap minggu berikutnya
                                        if ($week > 0) {
                                            $sessionDate = $sessionDate->copy()->addDays(7 * $week);
                                        }

                                        // Tambahkan sesi baru dengan tanggal yang diperbarui
                                        $allSessions[] = [
                                            'date' => $sessionDate->format('Y-m-d'),
                                            'start_time' => $session['start_time'],
                                            'end_time' => $session['end_time'],
                                        ];

                                        // Log untuk debugging
                                        Log::info('Added session for week ' . ($week + 1), [
                                            'date' => $sessionDate->format('Y-m-d'),
                                            'start_time' => $session['start_time'],
                                            'end_time' => $session['end_time']
                                        ]);
                                    }
                                }
                            } else {
                                // Untuk pembayaran mingguan, gunakan sesi asli
                                $allSessions = $originalSessions;

                                // Log untuk debugging
                                Log::info('Creating weekly membership sessions', [
                                    'subscription_id' => $subscription->id,
                                    'sessions_count' => count($allSessions)
                                ]);
                            }

                            // Tambahkan counter untuk nomor sesi
                            $sessionNumber = 1;

                            // Buat sesi untuk semua jadwal
                            foreach ($allSessions as $session) {
                                // Ambil tanggal dari session date
                                $sessionDate = $session['date'];

                                // Parse waktu awal & akhir
                                $startTimeStr = $session['start_time'];
                                $endTimeStr = $session['end_time'];

                                try {
                                    // Pastikan format waktu konsisten dengan membuang komponen tanggal jika ada
                                    if (strpos($startTimeStr, ' ') !== false && strpos($startTimeStr, '-') !== false) {
                                        $tempDateTime = Carbon::parse($startTimeStr);
                                        $startTimeStr = $tempDateTime->format('H:i:s');
                                    }

                                    if (strpos($endTimeStr, ' ') !== false && strpos($endTimeStr, '-') !== false) {
                                        $tempDateTime = Carbon::parse($endTimeStr);
                                        $endTimeStr = $tempDateTime->format('H:i:s');
                                    }

                                    // Buat datetime dengan menggabungkan tanggal sesi dan waktu
                                    $startDateTime = Carbon::parse($sessionDate . ' ' . $startTimeStr);
                                    $endDateTime = Carbon::parse($sessionDate . ' ' . $endTimeStr);

                                    // Log untuk debugging
                                    Log::info('Creating membership session', [
                                        'subscription_id' => $subscription->id,
                                        'session_date' => $sessionDate,
                                        'start_time' => $startDateTime->format('Y-m-d H:i:s'),
                                        'end_time' => $endDateTime->format('Y-m-d H:i:s'),
                                        'session_number' => $sessionNumber
                                    ]);

                                    // Buat membership session
                                    $membershipSession = MembershipSession::create([
                                        'membership_subscription_id' => $subscription->id,
                                        'session_date' => $sessionDate,
                                        'start_time' => $startDateTime,
                                        'end_time' => $endDateTime,
                                        'status' => 'scheduled',
                                        'session_number' => $sessionNumber++, // Tambahkan nomor sesi dan increment
                                    ]);

                                    $field = Field::find($membership->field_id);

                                    // Buat juga entri di FieldBooking untuk mencegah double booking
                                    FieldBooking::create([
                                        'user_id' => Auth::id(),
                                        'field_id' => $membership->field_id,
                                        'payment_id' => $payment->id,
                                        'membership_session_id' => $membershipSession->id,
                                        'start_time' => $startDateTime,
                                        'end_time' => $endDateTime,
                                        'total_price' => 0, // 0 karena sudah termasuk dalam membership
                                        'status' => 'pending', // Akan berubah menjadi 'confirmed' ketika payment dikonfirmasi
                                        'is_membership' => true, // Tandai ini sebagai booking dari membership
                                    ]);
                                } catch (\Exception $e) {
                                    // Log error
                                    Log::error('Error creating membership session', [
                                        'session_date' => $sessionDate,
                                        'start_time_str' => $startTimeStr,
                                        'end_time_str' => $endTimeStr,
                                        'error' => $e->getMessage(),
                                        'trace' => $e->getTraceAsString()
                                    ]);

                                    throw $e; // Re-throw exception untuk rollback
                                }
                            }
                        }
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
                // Cek apakah ini diskon dari penukaran poin atau diskon reguler
                if ($pointRedemptionId) {
                    // Jika ini adalah voucher hasil redeem poin, update status point redemption
                    $pointRedemption = PointRedemption::find($pointRedemptionId);
                    if ($pointRedemption) {
                        $pointRedemption->status = 'used';
                        $pointRedemption->used_at = now();
                        $pointRedemption->payment_id = $payment->id;
                        $pointRedemption->save();
                    }
                } else {
                    // Jika ini adalah diskon reguler, catat di discount_usages
                    DiscountUsage::create([
                        'discount_id' => $discountId,
                        'user_id' => Auth::id(),
                        'payment_id' => $payment->id,
                        'discount_amount' => $discountAmount,
                    ]);
                }

                // Hapus diskon dari session setelah digunakan
                if (session()->has('cart_discount')) {
                    session()->forget('cart_discount');
                } elseif (session()->has('cart_point_voucher')) {
                    session()->forget('cart_point_voucher');
                }
            }

            // Hapus items dari cart setelah berhasil membuat booking
            $cartItemsToDelete = $cartItems->pluck('id')->toArray();
            CartItem::whereIn('id', $cartItemsToDelete)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating bookings: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('user.cart.view')
                ->with('error', 'Gagal membuat booking: ' . $e->getMessage());
        }
 // Jika ada diskon atau voucher, hapus dari session setelah digunakan
 if (session()->has('cart_discount')) {
    session()->forget('cart_discount');
} elseif (session()->has('cart_point_voucher')) {
    session()->forget('cart_point_voucher');
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
        Log::error('Checkout Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        // Redirect ke halaman cart dengan pesan error
        return redirect()
            ->route('user.cart.view')
            ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
    }
}
}
