<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\RentalItem;
use Illuminate\Support\Str;
use App\Models\FieldBooking;
use App\Models\Photographer;
use Illuminate\Http\Request;
use App\Models\RentalBooking;
use App\Models\ProductSaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\PhotographerBooking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User\PhotographerController;

class POSController extends Controller
{
    /**
     * Menampilkan halaman utama POS
     */
    public function index()
    {
        // Mendapatkan data untuk dropdown
        $fields = Field::all();
        $rentalItems = RentalItem::where('stock_available', '>', 0)->get();
        $products = Product::where('stock', '>', 0)->get();
        $photographers = Photographer::all();

        // Mendapatkan cart POS untuk admin saat ini jika ada
        $posCart = $this->getPOSCart();
        $cartItems = [];

        if ($posCart) {
            $cartItems = CartItem::where('cart_id', $posCart->id)->get();
        }

        return view('admin.pos.index', compact('fields', 'rentalItems', 'products', 'photographers', 'cartItems', 'posCart'));
    }

    /**
     * Mendapatkan atau membuat cart khusus POS untuk admin
     */
    private function getPOSCart()
    {
        $adminId = Auth::id();
        // Gunakan type 'pos' untuk membedakan cart POS dengan cart biasa
        return Cart::firstOrCreate([
            'user_id' => $adminId,
            'type' => 'pos',
        ]);
    }


/**
 * Mengambil slot waktu tersedia untuk lapangan - DENGAN PENGECEKAN PAST TIME
 */
public function getFieldTimeSlots(Request $request)
{
    $request->validate([
        'field_id' => 'required|exists:fields,id',
        'date' => 'required|date',
    ]);

    $fieldId = $request->field_id;
    $date = $request->date;
    $carbonDate = Carbon::parse($date);

    // Get current time untuk comparison
    $now = Carbon::now();
    $isToday = $carbonDate->isToday();

    // Siapkan semua slot waktu dari jam 8 pagi sampai 10 malam
    $startHour = 8;
    $endHour = 22;
    $slotDuration = 60; // dalam menit

    $availableSlots = [];
    $bookedSlots = [];

    // Ambil booking yang sudah ada untuk lapangan dan tanggal tersebut
    $existingBookings = FieldBooking::where('field_id', $fieldId)
        ->whereDate('start_time', $date)
        ->whereIn('status', ['confirmed', 'pending'])
        ->get();

    // Buat array slot waktu yang tersedia
    for ($hour = $startHour; $hour < $endHour; $hour++) {
        $slotStart = Carbon::parse("$date $hour:00:00");
        $slotEnd = Carbon::parse("$date $hour:00:00")->addMinutes($slotDuration);

        $isAvailable = true;
        $isPastTime = false;

        // PERBAIKAN: Cek apakah slot waktu sudah lewat
        if ($isToday) {
            // Slot disable jika waktu END sudah terlewati
            if ($slotEnd <= $now) {
                $isAvailable = false;
                $isPastTime = true;
            }
        }

        // Jika bukan past time, cek bentrok dengan booking yang sudah ada
        if (!$isPastTime) {
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = Carbon::parse($booking->end_time);

                if (($slotStart >= $bookingStart && $slotStart < $bookingEnd) ||
                    ($slotEnd > $bookingStart && $slotEnd <= $bookingEnd) ||
                    ($slotStart <= $bookingStart && $slotEnd >= $bookingEnd)) {
                    $isAvailable = false;
                    $bookedSlots[] = [
                        'start' => $slotStart->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'customer' => $booking->user->name ?? 'Unknown',
                    ];
                    break;
                }
            }
        }

        // Tentukan status slot
        $status = 'available';
        if ($isPastTime) {
            $status = 'past_time';
        } else if (!$isAvailable) {
            $status = 'booked';
        }

        // Tambahkan semua slot (termasuk past time) untuk konsistensi UI
        $availableSlots[] = [
            'start' => $slotStart->format('H:i'),
            'end' => $slotEnd->format('H:i'),
            'label' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
            'is_available' => $isAvailable,
            'is_past_time' => $isPastTime,
            'status' => $status
        ];
    }

    return response()->json([
        'available_slots' => $availableSlots,
        'booked_slots' => $bookedSlots,
    ]);
}

/**
 * Menambahkan booking lapangan ke cart POS - DENGAN VALIDASI PAST TIME
 */
public function addFieldToCart(Request $request)
{
    $request->validate([
        'field_id' => 'required|exists:fields,id',
        'date' => 'required|date',
        'time_slot' => 'required',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:20',
    ]);

    try {
        DB::beginTransaction();

        // Parsing time slot (format: "08:00 - 09:00")
        [$startTime, $endTime] = explode(' - ', $request->time_slot);

        // Create full datetime
        $startDateTime = Carbon::parse("{$request->date} {$startTime}");
        $endDateTime = Carbon::parse("{$request->date} {$endTime}");

        // PERBAIKAN: Cek past time sebelum validasi lainnya
        $now = Carbon::now();
        if ($endDateTime <= $now) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Slot waktu yang dipilih sudah lewat. Silakan pilih slot waktu yang masih tersedia.',
                ],
                400,
            );
        }

        // Cek kembali ketersediaan slot waktu
        $conflictingBooking = FieldBooking::where('field_id', $request->field_id)
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
            ->whereIn('status', ['confirmed', 'pending'])
            ->first();

        if ($conflictingBooking) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Slot waktu sudah dibooking',
                ],
                400,
            );
        }

        // Cari atau buat data customer berdasarkan no telepon
        $customer = null;
        if (!empty($request->customer_phone)) {
            $customer = Customer::where('phone_number', $request->customer_phone)->first();
        }

        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->customer_name,
                'phone_number' => $request->customer_phone,
            ]);
        }

        // Ambil field untuk mendapatkan harga
        $field = Field::findOrFail($request->field_id);

        // Ambil atau buat cart POS
        $posCart = $this->getPOSCart();

        // Periksa apakah item sudah ada di cart
        $existingItem = CartItem::where('cart_id', $posCart->id)
            ->where('type', 'field_booking')
            ->where('item_id', $field->id)
            ->where('start_time', $startDateTime)
            ->where('end_time', $endDateTime)
            ->first();

        if ($existingItem) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Item sudah ada di cart',
                ],
                400,
            );
        }

        // Tambahkan ke cart
        $cartItem = CartItem::create([
            'cart_id' => $posCart->id,
            'type' => 'field_booking',
            'item_id' => $field->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'price' => $field->price,
            'notes' => "Customer: {$customer->name} " . ($customer->phone_number ? "({$customer->phone_number})" : ''),
            'customer_id' => $customer->id,
        ]);

        DB::commit();

        // Reload cart items
        $cartItems = CartItem::where('cart_id', $posCart->id)->get();
        $htmlContent = view('admin.pos.partials.cart_items', compact('cartItems', 'posCart'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Booking lapangan berhasil ditambahkan ke cart',
            'html_content' => $htmlContent,
            'cart_total' => $cartItems->sum('price'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error adding field to POS cart: ' . $e->getMessage());

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
 * Mengambil slot waktu tersedia untuk fotografer - DENGAN PENGECEKAN PAST TIME
 */
public function getPhotographerTimeSlots(Request $request)
{
    $request->validate([
        'photographer_id' => 'required|exists:photographers,id',
        'date' => 'required|date',
    ]);

    $photographerId = $request->photographer_id;
    $date = $request->date;
    $carbonDate = Carbon::parse($date);

    // Get current time untuk comparison
    $now = Carbon::now();
    $isToday = $carbonDate->isToday();

    // Siapkan semua slot waktu
    $startHour = 8;
    $endHour = 22;
    $slotDuration = 60; // dalam menit

    $availableSlots = [];
    $bookedSlots = [];

    // Ambil booking yang sudah ada untuk fotografer dan tanggal tersebut
    $existingBookings = PhotographerBooking::where('photographer_id', $photographerId)
        ->whereDate('start_time', $date)
        ->whereIn('status', ['confirmed', 'pending'])
        ->get();

    // Buat array slot waktu yang tersedia
    for ($hour = $startHour; $hour < $endHour; $hour++) {
        $slotStart = Carbon::parse("$date $hour:00:00");
        $slotEnd = Carbon::parse("$date $hour:00:00")->addMinutes($slotDuration);

        $isAvailable = true;
        $isPastTime = false;

        // PERBAIKAN: Cek apakah slot waktu sudah lewat
        if ($isToday) {
            // Slot disable jika waktu END sudah terlewati
            if ($slotEnd <= $now) {
                $isAvailable = false;
                $isPastTime = true;
            }
        }

        // Jika bukan past time, cek bentrok dengan booking yang sudah ada
        if (!$isPastTime) {
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = Carbon::parse($booking->end_time);

                if (($slotStart >= $bookingStart && $slotStart < $bookingEnd) ||
                    ($slotEnd > $bookingStart && $slotEnd <= $bookingEnd) ||
                    ($slotStart <= $bookingStart && $slotEnd >= $bookingEnd)) {
                    $isAvailable = false;
                    $bookedSlots[] = [
                        'start' => $slotStart->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'customer' => $booking->user->name ?? 'Unknown',
                    ];
                    break;
                }
            }
        }

        // Tentukan status slot
        $status = 'available';
        if ($isPastTime) {
            $status = 'past_time';
        } else if (!$isAvailable) {
            $status = 'booked';
        }

        $availableSlots[] = [
            'start' => $slotStart->format('H:i'),
            'end' => $slotEnd->format('H:i'),
            'label' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
            'is_available' => $isAvailable,
            'is_past_time' => $isPastTime,
            'status' => $status
        ];
    }

    return response()->json([
        'available_slots' => $availableSlots,
        'booked_slots' => $bookedSlots,
    ]);
}

/**
 * Menambahkan booking fotografer ke cart POS - DENGAN VALIDASI PAST TIME
 */
public function addPhotographerToCart(Request $request)
{
    $request->validate([
        'photographer_id' => 'required|exists:photographers,id',
        'date' => 'required|date',
        'time_slot' => 'required',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:20',
    ]);

    try {
        DB::beginTransaction();

        // Parsing time slot (format: "08:00 - 09:00")
        [$startTime, $endTime] = explode(' - ', $request->time_slot);

        // Create full datetime
        $startDateTime = Carbon::parse("{$request->date} {$startTime}");
        $endDateTime = Carbon::parse("{$request->date} {$endTime}");

        // PERBAIKAN: Cek past time sebelum validasi lainnya
        $now = Carbon::now();
        if ($endDateTime <= $now) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Slot waktu yang dipilih sudah lewat. Silakan pilih slot waktu yang masih tersedia.',
                ],
                400,
            );
        }

        // Cek kembali ketersediaan slot waktu
        $conflictingBooking = PhotographerBooking::where('photographer_id', $request->photographer_id)
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
            ->whereIn('status', ['confirmed', 'pending'])
            ->first();

        if ($conflictingBooking) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Slot waktu sudah dibooking',
                ],
                400,
            );
        }

        // Cari atau buat data customer
        $customer = null;
        if (!empty($request->customer_phone)) {
            $customer = Customer::where('phone_number', $request->customer_phone)->first();
        }

        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->customer_name,
                'phone_number' => $request->customer_phone,
            ]);
        }

        // Ambil photographer untuk mendapatkan harga
        $photographer = Photographer::findOrFail($request->photographer_id);

        // Ambil atau buat cart POS
        $posCart = $this->getPOSCart();

        // Periksa apakah item sudah ada di cart
        $existingItem = CartItem::where('cart_id', $posCart->id)
            ->where('type', 'photographer')
            ->where('item_id', $photographer->id)
            ->where('start_time', $startDateTime)
            ->where('end_time', $endDateTime)
            ->first();

        if ($existingItem) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Item sudah ada di cart',
                ],
                400,
            );
        }

        // Tambahkan ke cart
        $cartItem = CartItem::create([
            'cart_id' => $posCart->id,
            'type' => 'photographer',
            'item_id' => $photographer->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'price' => $photographer->price,
            'notes' => "Customer: {$customer->name} " . ($customer->phone_number ? "({$customer->phone_number})" : ''),
            'customer_id' => $customer->id,
        ]);

        DB::commit();

        // Reload cart items
        $cartItems = CartItem::where('cart_id', $posCart->id)->get();
        $htmlContent = view('admin.pos.partials.cart_items', compact('cartItems', 'posCart'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Booking fotografer berhasil ditambahkan ke cart',
            'html_content' => $htmlContent,
            'cart_total' => $cartItems->sum('price'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error adding photographer to POS cart: ' . $e->getMessage());

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
 * Menambahkan item rental ke cart POS - DENGAN VALIDASI PAST TIME
 */
public function addRentalItemToCart(Request $request)
{
    $request->validate([
        'rental_item_id' => 'required|exists:rental_items,id',
        'date' => 'required|date',
        'time_slot' => 'required',
        'quantity' => 'required|integer|min:1',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:20',
    ]);

    try {
        DB::beginTransaction();

        // Parsing time slot (format: "08:00 - 09:00")
        [$startTime, $endTime] = explode(' - ', $request->time_slot);

        // Create full datetime
        $startDateTime = Carbon::parse("{$request->date} {$startTime}");
        $endDateTime = Carbon::parse("{$request->date} {$endTime}");

        // PERBAIKAN: Cek past time sebelum validasi lainnya
        $now = Carbon::now();
        if ($endDateTime <= $now) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Slot waktu yang dipilih sudah lewat. Silakan pilih slot waktu yang masih tersedia.',
                ],
                400,
            );
        }

        // Cari item rental
        $rentalItem = RentalItem::findOrFail($request->rental_item_id);

        // Hitung jumlah yang sudah dipesan dalam rentang waktu yang sama
        $bookedQuantity = RentalBooking::where('rental_item_id', $rentalItem->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query
                    ->where(function ($q) use ($startDateTime, $endDateTime) {
                        $q->where('start_time', '>=', $startDateTime)->where('start_time', '<', $endDateTime);
                    })
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        $q->where('end_time', '>', $startDateTime)->where('end_time', '<=', $endDateTime);
                    })
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        $q->where('start_time', '<=', $startDateTime)->where('end_time', '>=', $endDateTime);
                    });
            })
            ->sum('quantity');

        // Cek ketersediaan berdasarkan stok
        $availableQuantity = $rentalItem->stock_total - $bookedQuantity;

        if ($request->quantity > $availableQuantity) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => "Stok tidak cukup. Tersedia: {$availableQuantity}, Diminta: {$request->quantity}",
                ],
                400,
            );
        }

        // Cari atau buat data customer
        $customer = null;
        if (!empty($request->customer_phone)) {
            $customer = Customer::where('phone_number', $request->customer_phone)->first();
        }

        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->customer_name,
                'phone_number' => $request->customer_phone,
            ]);
        }

        // Ambil atau buat cart POS
        $posCart = $this->getPOSCart();

        // Periksa apakah item sudah ada di cart dengan waktu yang sama
        $existingItem = CartItem::where('cart_id', $posCart->id)
            ->where('type', 'rental_item')
            ->where('item_id', $rentalItem->id)
            ->where('start_time', $startDateTime)
            ->where('end_time', $endDateTime)
            ->first();

        if ($existingItem) {
            // Update jumlah jika sudah ada
            $newQuantity = $existingItem->quantity + $request->quantity;

            // Cek lagi ketersediaan stok
            if ($newQuantity > $availableQuantity) {
                DB::rollBack();
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Stok tidak cukup. Tersedia: {$availableQuantity}, Total diminta: {$newQuantity}",
                    ],
                    400,
                );
            }

            $existingItem->quantity = $newQuantity;
            $existingItem->price = $rentalItem->rental_price * $newQuantity;
            $existingItem->save();
        } else {
            // Tambahkan ke cart jika belum ada
            $cartItem = CartItem::create([
                'cart_id' => $posCart->id,
                'type' => 'rental_item',
                'item_id' => $rentalItem->id,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'quantity' => $request->quantity,
                'price' => $rentalItem->rental_price * $request->quantity,
                'notes' => "Customer: {$customer->name} " . ($customer->phone_number ? "({$customer->phone_number})" : ''),
                'customer_id' => $customer->id,
            ]);
        }

        DB::commit();

        // Reload cart items
        $cartItems = CartItem::where('cart_id', $posCart->id)->get();
        $htmlContent = view('admin.pos.partials.cart_items', compact('cartItems', 'posCart'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Item rental berhasil ditambahkan ke cart',
            'html_content' => $htmlContent,
            'cart_total' => $cartItems->sum('price'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error adding rental item to POS cart: ' . $e->getMessage());

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
 * Proses checkout untuk POS - DENGAN VALIDASI PAST TIME
 */
public function checkout(Request $request)
{
    $request->validate([
        'payment_method' => 'required|in:cash,transfer,other',
        'customer_id' => 'required|exists:customers,id',
        'cash_amount' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string|max:500',
    ]);

    try {
        DB::beginTransaction();

        // Ambil cart POS
        $posCart = $this->getPOSCart();
        $cartItems = CartItem::where('cart_id', $posCart->id)->get();

        if ($cartItems->isEmpty()) {
            DB::rollBack();
            return redirect()->route('admin.pos.index')->with('error', 'Keranjang kosong');
        }

        // PERBAIKAN: Cek past time untuk item yang memiliki waktu
        $pastTimeItems = [];
        $now = Carbon::now();

        foreach ($cartItems as $item) {
            // Cek past time untuk item yang memiliki waktu
            if (in_array($item->type, ['field_booking', 'rental_item', 'photographer']) && $item->end_time) {
                $endTime = Carbon::parse($item->end_time);

                // Jika waktu akhir slot sudah lewat, tandai sebagai past time
                if ($endTime <= $now) {
                    $startTime = Carbon::parse($item->start_time)->format('d M Y H:i');
                    $endTimeFormatted = $endTime->format('H:i');

                    switch ($item->type) {
                        case 'field_booking':
                            $field = Field::find($item->item_id);
                            $itemName = $field ? $field->name : 'Booking Lapangan';
                            break;
                        case 'rental_item':
                            $rentalItem = RentalItem::find($item->item_id);
                            $itemName = $rentalItem ? $rentalItem->name : 'Rental Item';
                            break;
                        case 'photographer':
                            $photographer = Photographer::find($item->item_id);
                            $itemName = $photographer ? $photographer->name : 'Fotografer';
                            break;
                    }

                    $pastTimeItems[] = $itemName . ' (' . $startTime . ' - ' . $endTimeFormatted . ')';
                }
            }
        }

        // Jika ada item past time, hapus otomatis dan beri pesan error
        if (!empty($pastTimeItems)) {
            // Hapus item yang past time dari keranjang secara otomatis
            foreach ($cartItems as $item) {
                if (in_array($item->type, ['field_booking', 'rental_item', 'photographer']) && $item->end_time) {
                    $endTime = Carbon::parse($item->end_time);
                    if ($endTime <= $now) {
                        $item->delete();
                    }
                }
            }

            DB::rollBack();
            return redirect()
                ->route('admin.pos.index')
                ->with('error', 'Beberapa item waktu bookingnya sudah lewat dan telah dihapus dari keranjang: ' . implode(', ', $pastTimeItems) . '. Silakan refresh halaman dan coba lagi.');
        }

        // Generate unique order ID
        $orderId = 'POS-' . now()->format('Ymd-His') . '-' . mt_rand(1000, 9999);

        // Hitung total pembayaran
        $totalAmount = $cartItems->sum('price');

        // Ambil atau buat user_id untuk booking yang memerlukan user_id
        $adminId = Auth::id();

        // Buat record pembayaran
        $payment = Payment::create([
            'order_id' => $orderId,
            'customer_id' => $request->customer_id,
            'user_id' => $adminId,
            'amount' => $totalAmount,
            'original_amount' => $totalAmount,
            'payment_type' => $request->payment_method,
            'transaction_status' => 'success',
            'transaction_time' => now(),
            'payment_details' => json_encode([
                'method' => $request->payment_method,
                'cash_amount' => $request->cash_amount,
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name,
                'notes' => $request->notes,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Proses setiap item di cart
        foreach ($cartItems as $item) {
            switch ($item->type) {
                case 'field_booking':
                    $fieldBooking = FieldBooking::create([
                        'customer_id' => $request->customer_id,
                        'user_id' => $adminId,
                        'field_id' => $item->item_id,
                        'payment_id' => $payment->id,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'total_price' => $item->price,
                        'status' => 'confirmed',
                        'notes' => $item->notes,
                    ]);
                    break;

                case 'rental_item':
                    $rentalBooking = RentalBooking::create([
                        'customer_id' => $request->customer_id,
                        'user_id' => $adminId,
                        'rental_item_id' => $item->item_id,
                        'payment_id' => $payment->id,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'quantity' => $item->quantity,
                        'total_price' => $item->price,
                        'status' => 'confirmed',
                    ]);

                    // Update stok yang tersedia
                    $rentalItem = RentalItem::find($item->item_id);
                    if ($rentalItem) {
                        $rentalItem->stock_available -= $item->quantity;
                        $rentalItem->save();
                    }
                    break;

                case 'photographer':
                    $photographerBooking = PhotographerBooking::create([
                        'customer_id' => $request->customer_id,
                        'user_id' => $adminId,
                        'photographer_id' => $item->item_id,
                        'payment_id' => $payment->id,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'price' => $item->price,
                        'status' => 'confirmed',
                        'notes' => $item->notes,
                    ]);

                    // Kirim notifikasi ke fotografer
                    app(PhotographerController::class)->sendBookingNotification($photographerBooking);
                    break;

                case 'product':
                    // Update stok produk
                    $product = Product::find($item->item_id);
                    if ($product) {
                        $product->stock -= $item->quantity;
                        $product->save();
                    }

                    // Buat product sale item
                    ProductSaleItem::create([
                        'payment_id' => $payment->id,
                        'product_id' => $item->item_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price / $item->quantity,
                    ]);
                    break;
            }
        }

        // Bersihkan cart POS
        CartItem::where('cart_id', $posCart->id)->delete();

        DB::commit();

        // Redirect ke halaman sukses
        return redirect()
            ->route('admin.pos.receipt', ['id' => $payment->id])
            ->with('success', 'Transaksi berhasil. Nomor Order: ' . $orderId);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error during POS checkout: ' . $e->getMessage());

        return redirect()
            ->route('admin.pos.index')
            ->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
    }
}







    /**
     * Menambahkan produk ke cart POS
     */
    /**
     * Menambahkan produk ke cart POS
     */
    public function addProductToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Cari produk
            $product = Product::findOrFail($request->product_id);

            // Cek ketersediaan stok
            if ($request->quantity > $product->stock) {
                DB::rollBack();
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Stok tidak cukup. Tersedia: {$product->stock}, Diminta: {$request->quantity}",
                    ],
                    400,
                );
            }

            // Cari atau buat data customer berdasarkan no telepon
            $customer = null;
            if (!empty($request->customer_phone)) {
                $customer = Customer::where('phone_number', $request->customer_phone)->first();
            }

            if (!$customer) {
                // Buat customer baru jika tidak ditemukan
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'phone_number' => $request->customer_phone,
                ]);
            }

            // Ambil atau buat cart POS
            $posCart = $this->getPOSCart();

            // Periksa apakah produk sudah ada di cart
            $existingItem = CartItem::where('cart_id', $posCart->id)->where('type', 'product')->where('item_id', $product->id)->first();

            if ($existingItem) {
                // Update jumlah jika sudah ada
                $newQuantity = $existingItem->quantity + $request->quantity;

                // Cek lagi ketersediaan stok
                if ($newQuantity > $product->stock) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Stok tidak cukup. Tersedia: {$product->stock}, Total diminta: {$newQuantity}",
                        ],
                        400,
                    );
                }

                $existingItem->quantity = $newQuantity;
                $existingItem->price = $product->price * $newQuantity;
                $existingItem->save();
            } else {
                // Tambahkan ke cart jika belum ada
                $cartItem = CartItem::create([
                    'cart_id' => $posCart->id,
                    'type' => 'product',
                    'item_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price * $request->quantity,
                    'notes' => "Customer: {$customer->name} " . ($customer->phone_number ? "({$customer->phone_number})" : ''),
                    'customer_id' => $customer->id,
                ]);
            }

            DB::commit();

            // Reload cart items
            $cartItems = CartItem::where('cart_id', $posCart->id)->get();
            $htmlContent = view('admin.pos.partials.cart_items', compact('cartItems', 'posCart'))->render();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke cart',
                'html_content' => $htmlContent,
                'cart_total' => $cartItems->sum('price'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding product to POS cart: ' . $e->getMessage());

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
     * Menghapus item dari cart POS
     */
    /**
     * Menghapus item dari cart POS
     */
    public function removeFromCart($item_id)
    {
        try {
            // Ambil cart POS
            $posCart = $this->getPOSCart();

            // Cari dan hapus item
            $cartItem = CartItem::where('id', $item_id)->where('cart_id', $posCart->id)->firstOrFail();

            $cartItem->delete();

            // Reload cart items
            $cartItems = CartItem::where('cart_id', $posCart->id)->get();
            $htmlContent = view('admin.pos.partials.cart_items', compact('cartItems', 'posCart'))->render();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari cart',
                'html_content' => $htmlContent,
                'cart_total' => $cartItems->sum('price'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing item from POS cart: ' . $e->getMessage());

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
     * Download struk pembayaran
     */
    public function downloadReceipt($id)
    {
        // Ambil data payment beserta relasinya
        $payment = Payment::with([
            'customer',
            'fieldBookings.field',
            'rentalBookings.rentalItem',
            'photographerBookings.photographer',
            'productItems.product', // Gunakan relasi baru untuk product items
        ])->findOrFail($id);

        // Ambil informasi cash dan kembalian jika pembayaran tunai
        $cashAmount = null;
        $change = null;

        if ($payment->payment_type == 'cash') {
            $paymentDetails = json_decode($payment->payment_details, true);
            if (isset($paymentDetails['cash_amount'])) {
                $cashAmount = $paymentDetails['cash_amount'];
                $change = $cashAmount - $payment->amount;
            }
        }

        // Konfigurasi DomPDF
        $config = [
            'fontDir' => public_path('fonts'),
            'fontCache' => storage_path('fonts'),
            'defaultFont' => 'sans-serif',
            'isRemoteEnabled' => true,
        ];

        // Load view dengan data
        $pdf = Pdf::loadView('admin.pos.receipt_pdf', compact('payment', 'cashAmount', 'change'));

        // Atur options
        $dompdf = $pdf->getDomPDF();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->set('defaultFont', 'sans-serif');

        // Tentukan lokasi font (pastikan direktori ada)
        if (is_dir(public_path('fonts/poppins'))) {
            $options->set('fontDir', [public_path('fonts/poppins')]);
        }

        // Atur kembali options
        $dompdf->setOptions($options);

        // Set PDF options
        $pdf->setPaper('a4', 'portrait');

        // Return the PDF for download
        return $pdf->download('Struk-Pembayaran-' . $payment->order_id . '.pdf');
    }

    /**
     * Menampilkan struk pembayaran
     */
    public function showReceipt($id)
    {
        $payment = Payment::with(['customer', 'productItems.product'])->findOrFail($id);

        // Ambil informasi cash dan kembalian jika pembayaran tunai
        $cashAmount = null;
        $change = null;

        if ($payment->payment_type == 'cash') {
            $paymentDetails = json_decode($payment->payment_details, true);
            if (isset($paymentDetails['cash_amount'])) {
                $cashAmount = $paymentDetails['cash_amount'];
                $change = $cashAmount - $payment->amount;
            }
        }

        return view('admin.pos.receipt', compact('payment', 'cashAmount', 'change'));
    }

    /**
     * Menampilkan riwayat transaksi POS
     */
    public function transactionHistory(Request $request)
    {
        // Set default filter tanggal untuk 30 hari terakhir jika tidak ada filter
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Tambahkan waktu ke end_date agar mencakup seluruh hari
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Filter untuk pencarian
        $search = $request->input('search');
        $paymentType = $request->input('payment_type');

        // Query dasar untuk pembayaran POS
        $query = Payment::with(['user', 'fieldBookings.field', 'rentalBookings.rentalItem', 'photographerBookings.photographer'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan metode pembayaran
        if ($paymentType) {
            $query->where('payment_type', $paymentType);
        }

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")->orWhere('phone_number', 'like', "%{$search}%");
                });
            });
        }

        // Ambil data dengan pagination
        $transactions = $query->paginate(15)->appends($request->all());

        // Hitung total pendapatan berdasarkan filter
        $totalRevenue = $query->sum('amount');

        // Hitung pendapatan per metode pembayaran
        $revenueByPaymentType = Payment::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->selectRaw('payment_type, sum(amount) as total')->groupBy('payment_type')->get();

        // Hitung jumlah transaksi per hari dalam rentang filter
        $dailyTransactions = Payment::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->selectRaw('DATE(created_at) as date, count(*) as count, sum(amount) as total')->groupBy('date')->orderBy('date')->get();

        // Format data untuk chart
        $chartData = [
            'dates' => $dailyTransactions->pluck('date')->toArray(),
            'counts' => $dailyTransactions->pluck('count')->toArray(),
            'totals' => $dailyTransactions->pluck('total')->toArray(),
        ];

        return view('admin.pos.history', compact('transactions', 'totalRevenue', 'revenueByPaymentType', 'chartData', 'startDate', 'endDate', 'search', 'paymentType'));
    }

    /**
     * Mencari pelanggan berdasarkan nama atau nomor telepon
     */
    /**
     * Mencari pelanggan berdasarkan nama atau nomor telepon
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->input('query');

        if (empty($query) || strlen($query) < 3) {
            return response()->json([
                'customers' => [],
            ]);
        }

        $customers = Customer::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")->orWhere('phone_number', 'like', "%{$query}%");
        })
            ->select('id', 'name', 'phone_number')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json([
            'customers' => $customers,
        ]);
    }
}
