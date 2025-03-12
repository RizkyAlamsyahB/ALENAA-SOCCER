<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Field;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\Membership;
use App\Models\RentalItem;
use App\Models\FieldBooking;
use App\Models\Photographer;
use Illuminate\Http\Request;
use App\Models\RentalBooking;
use Illuminate\Support\Facades\DB;
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
 * Menambahkan item rental ke keranjang
 */
private function addRentalItemToCart(Request $request, Cart $cart)
{
    // Validasi input spesifik untuk rental item
    $request->validate([
        'rental_item_id' => 'required|exists:rental_items,id',
        'date' => 'required|date|after_or_equal:today',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'quantity' => 'required|integer|min:1',
    ]);

    $rentalItem = RentalItem::findOrFail($request->rental_item_id);
    $date = $request->date;
    $quantity = $request->quantity;

    // Buat full datetime untuk start dan end time
    $startDateTime = Carbon::parse("{$date} {$request->start_time}");
    $endDateTime = Carbon::parse("{$date} {$request->end_time}");

    // Log untuk debugging
    Log::info('Rental item details', [
        'rental_item_id' => $rentalItem->id,
        'rental_item_name' => $rentalItem->name,
        'date' => $date,
        'start_time' => $startDateTime->format('Y-m-d H:i:s'),
        'end_time' => $endDateTime->format('Y-m-d H:i:s'),
        'quantity' => $quantity,
    ]);

    // Periksa apakah item dengan waktu yang sama sudah ada di cart
    $existingItem = CartItem::where('cart_id', $cart->id)
        ->where('type', 'rental_item')
        ->where('item_id', $rentalItem->id)
        ->where('start_time', $startDateTime)
        ->where('end_time', $endDateTime)
        ->first();

    if ($existingItem) {
        // Jika sudah ada, update quantity (tambahkan)
        $newQuantity = $existingItem->quantity + $quantity;
        $existingItem->quantity = $newQuantity;
        $existingItem->price = $rentalItem->rental_price * $newQuantity;
        $existingItem->save();

        $message = 'Jumlah item berhasil diperbarui di keranjang';
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

        $message = 'Item berhasil ditambahkan ke keranjang';
    }

    // Hitung jumlah item di cart
    $cartCount = CartItem::where('cart_id', $cart->id)->count();

    return response()->json([
        'success' => true,
        'message' => $message,
        'cart_count' => $cartCount,
    ]);
}

    /**
     * Menambahkan membership ke keranjang
     */

    /**
     * Menambahkan booking fotografer ke keranjang
     */

    /**
     * Memeriksa ketersediaan fotografer pada waktu tertentu
     */

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
            // Tambahkan pengecekan untuk jenis item lainnya jika diperlukan
        }

        if (!empty($unavailableItems)) {
            // Ada item yang sudah tidak tersedia
            return redirect()
                ->route('user.cart.view')
                ->with('error', 'Beberapa item yang Anda pilih sudah tidak tersedia: ' . implode(', ', $unavailableItems));
        }

        // Hitung total harga
        $totalPrice = $cartItems->sum('price');

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
                    'price' => ($item->type == 'rental_item') ? ($item->price / $item->quantity) : $item->price,
                    'quantity' => $item->quantity ?? 1,
                    'name' => $detail,
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
                'transaction_status' => 'pending',
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
                'cart_items' => $cartItems,
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
