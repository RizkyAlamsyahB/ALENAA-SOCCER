<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\RentalItem;
use App\Models\RentalBooking; // Tambahkan import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentalItemsController extends Controller
{
    /**
     * Menampilkan daftar item rental untuk user
     */
    public function index(Request $request)
    {
        // Inisialisasi query
        $query = RentalItem::query();

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('rental_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('rental_price', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        // Hitung jumlah item per kategori untuk filter
        $categoryCounts = [
            'jersey' => RentalItem::where('category', 'jersey')->count(),
            'shoes' => RentalItem::where('category', 'shoes')->count(),
            'ball' => RentalItem::where('category', 'ball')->count(),
            'other' => RentalItem::where('category', 'other')->count()
        ];

        // Pagination
        $rentalItems = $query->paginate(12);

        return view('users.rental_items.index', compact('rentalItems', 'categoryCounts'));
    }


    /**
     * Menampilkan detail item rental
     */
    public function show($id)
    {
        $rentalItem = RentalItem::findOrFail($id);
        return view('users.rental_items.show', compact('rentalItem'));
    }

    /**
     * Mendapatkan slot waktu yang tersedia untuk tanggal tertentu
     */
    public function getAvailableSlots(Request $request, $rentalItemId)
    {
        try {
            // Validasi input
            $request->validate([
                'date' => 'required|date'
            ]);

            // Cari item rental
            $rentalItem = RentalItem::findOrFail($rentalItemId);

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

            // Get cart items for current user, rental item, and date
            $cartSlots = [];
            $cartQuantities = []; // Track quantities in cart per slot
            if (Auth::check()) {
                $userCart = Cart::where('user_id', Auth::id())->first();

                if ($userCart) {
                    $cartItems = CartItem::where('cart_id', $userCart->id)
                        ->where('type', 'rental_item')
                        ->where('item_id', $rentalItemId)
                        ->whereDate('start_time', $date)
                        ->get();

                    foreach ($cartItems as $item) {
                        $startFormatted = Carbon::parse($item->start_time)->format('H:i');
                        $endFormatted = Carbon::parse($item->end_time)->format('H:i');
                        $slotKey = $startFormatted . ' - ' . $endFormatted;

                        if (!isset($cartQuantities[$slotKey])) {
                            $cartQuantities[$slotKey] = 0;
                            $cartSlots[] = $slotKey;
                        }

                        $cartQuantities[$slotKey] += $item->quantity;
                    }
                }
            }

            // Dapatkan peminjaman yang sudah ada pada tanggal tersebut
            // Asumsi ada tabel rental_bookings untuk menyimpan peminjaman yang sudah terjadi
// Dapatkan peminjaman yang sudah ada pada tanggal tersebut dari CART_ITEMS
$rentedItemsFromCart = DB::table('cart_items')
    ->where('type', 'rental_item')
    ->where('item_id', $rentalItemId)
    ->whereDate('start_time', $date)
    ->whereNotNull('start_time')
    ->whereNotNull('end_time')
    ->get();

// Dapatkan peminjaman yang sudah ada pada tanggal tersebut dari RENTAL_BOOKINGS
$rentedItemsFromBookings = RentalBooking::where('rental_item_id', $rentalItemId)
    ->whereDate('start_time', $date)
    ->whereNotIn('status', ['cancelled'])
    ->get();

// Gabungkan kedua hasil
$rentedItems = $rentedItemsFromCart->merge($rentedItemsFromBookings);

            // Hitung jumlah item yang disewa per slot waktu
            $rentedQuantities = [];
            foreach ($rentedItems as $item) {
                $startTime = Carbon::parse($item->start_time);
                $endTime = Carbon::parse($item->end_time);

                foreach ($allSlots as $slot) {
                    $slotStart = Carbon::parse("{$date} {$slot['start']}");
                    $slotEnd = Carbon::parse("{$date} {$slot['end']}");

                    // Jika slot waktu bertabrakan dengan waktu rental
                    if (
                        ($slotStart >= $startTime && $slotStart < $endTime) ||
                        ($slotEnd > $startTime && $slotEnd <= $endTime) ||
                        ($slotStart <= $startTime && $slotEnd >= $endTime)
                    ) {
                        $slotKey = $slot['start'] . ' - ' . $slot['end'];
                        if (!isset($rentedQuantities[$slotKey])) {
                            $rentedQuantities[$slotKey] = 0;
                        }
                        $rentedQuantities[$slotKey] += $item->quantity;
                    }
                }
            }

            // Filter slot yang tersedia
            $availableSlots = [];
            foreach ($allSlots as $slot) {
                $displaySlot = $slot['start'] . ' - ' . $slot['end'];

                // Hitung jumlah item yang tersedia pada slot ini
                $rentedCount = isset($rentedQuantities[$displaySlot]) ? $rentedQuantities[$displaySlot] : 0;
                $cartCount = isset($cartQuantities[$displaySlot]) ? $cartQuantities[$displaySlot] : 0;
                $availableCount = $rentalItem->stock_total - $rentedCount;

                $isInCart = in_array($displaySlot, $cartSlots);
                $isAvailable = $availableCount > 0;

                // Calculate price (1 hour per slot)
                $slotPrice = $rentalItem->rental_price;

                $availableSlots[] = [
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'display' => $displaySlot,
                    'is_available' => $isAvailable,
                    'in_cart' => $isInCart,
                    'price' => $slotPrice,
                    'available_quantity' => max(0, $availableCount),
                    'cart_quantity' => $cartCount,
                    'status' => $isInCart ? 'in_cart' : ($isAvailable ? 'available' : 'fully_booked')
                ];
            }

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            // Log full error
            Log::error('Error in getAvailableSlots for rental items', [
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
     * Mendapatkan slot yang ada di keranjang user
     */
    public function getCartSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'rental_item_id' => 'required|exists:rental_items,id'
        ]);

        $rentalItemId = $request->rental_item_id;
        $date = $request->date;
        $reservedSlots = [];

        // Get slots from user's cart for this specific rental item and date
        if (Auth::check()) {
            $userCart = Cart::where('user_id', Auth::id())->first();

            if ($userCart) {
                $cartItems = CartItem::where('cart_id', $userCart->id)
                    ->where('type', 'rental_item')
                    ->where('item_id', $rentalItemId)
                    ->whereDate('start_time', $date)
                    ->get();

                foreach ($cartItems as $item) {
                    $startFormatted = Carbon::parse($item->start_time)->format('H:i');
                    $endFormatted = Carbon::parse($item->end_time)->format('H:i');
                    $slotKey = $startFormatted . ' - ' . $endFormatted;

                    $reservedSlots[] = [
                        'slot' => $slotKey,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ];
                }
            }
        }

        return response()->json($reservedSlots);
    }

    /**
     * Mendapatkan ketersediaan item rental berdasarkan kategori
     */
    public function getByCategory($category)
    {
        $validCategories = ['ball', 'jersey', 'shoes', 'other'];

        if (!in_array($category, $validCategories)) {
            return response()->json([
                'error' => 'Kategori tidak valid'
            ], 400);
        }

        $items = RentalItem::where('category', $category)
            ->where('stock_available', '>', 0)
            ->get();

        return response()->json($items);
    }

    /**
     * Mencari item rental berdasarkan nama
     */
    public function search(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2'
        ]);

        $searchTerm = $request->term;

        $items = RentalItem::where('name', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%")
            ->get();

        return response()->json($items);
    }
}
