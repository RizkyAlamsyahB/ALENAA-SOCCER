<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Field;
use App\Models\CartItem;
use App\Models\FieldBooking;
use App\Models\Photographer;
use Illuminate\Http\Request;
use App\Models\PhotographerBooking;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PhotographerBookingNotification;

class PhotographerController extends Controller
{
/**
     * Menampilkan daftar fotografer untuk user - DENGAN FILTER BERDASARKAN CART
     */
    public function index(Request $request)
    {
        // Ambil field IDs yang ada di cart user
        $cartFieldIds = $this->getCartFieldIds();

        Log::info('Cart field IDs for photographer filtering', [
            'user_id' => Auth::id(),
            'cart_field_ids' => $cartFieldIds
        ]);

        // Query dasar untuk fotografer aktif
        $query = Photographer::where('status', 'active');

        // FILTER UTAMA: Jika ada lapangan di cart, filter fotografer berdasarkan lapangan tersebut
        if (!empty($cartFieldIds)) {
            $query->where(function($q) use ($cartFieldIds) {
                // Fotografer yang ditugaskan ke lapangan yang ada di cart
                $q->whereIn('field_id', $cartFieldIds)
                  // ATAU fotografer yang tidak terikat dengan lapangan tertentu (bisa untuk semua lapangan)
                  ->orWhereNull('field_id');
            });
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('package_type', 'like', "%{$searchTerm}%");
            });
        }

        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Ambil fotografer
        $photographers = $query->get();

        // Kelompokkan fotografer berdasarkan package_type
        $photographersByType = $photographers->groupBy('package_type');

        // Untuk setiap fotografer, tambahkan informasi rating dan lapangan
        foreach ($photographers as $photographer) {
            $photographer->rating = $photographer->getRatingAttribute();
            $photographer->reviews_count = $photographer->getReviewsCountAttribute();

            // Tambahkan info lapangan - DENGAN LOGIC YANG LEBIH SMART
            if ($photographer->field_id) {
                // Jika fotografer ditugaskan ke lapangan tertentu
                $field = Field::find($photographer->field_id);
                $photographer->assigned_field = $field ? $field->name : 'Lapangan tidak ditemukan';
                $photographer->field_restriction = true;
            } else {
                // Jika fotografer tidak terikat dengan lapangan tertentu
                if (!empty($cartFieldIds)) {
                    // Tampilkan lapangan yang ada di cart
                    $cartFields = Field::whereIn('id', $cartFieldIds)->pluck('name')->toArray();
                    $photographer->assigned_field = 'Tersedia untuk: ' . implode(', ', $cartFields);
                } else {
                    $photographer->assigned_field = 'Tersedia untuk semua lapangan';
                }
                $photographer->field_restriction = false;
            }
        }

        // Info untuk view tentang filtering yang diterapkan
        $filterInfo = $this->getFilterInfo($cartFieldIds);

        return view('users.photographers.index', compact(
            'photographers',
            'photographersByType',
            'filterInfo',
            'cartFieldIds'
        ));
    }

    /**
     * Helper: Ambil field IDs yang ada di cart user
     */
    private function getCartFieldIds()
    {
        if (!Auth::check()) {
            return [];
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return [];
        }

        // Ambil field IDs dari cart items yang bertipe 'field_booking'
        $fieldIds = CartItem::where('cart_id', $cart->id)
            ->where('type', 'field_booking')
            ->pluck('item_id')
            ->unique()
            ->toArray();

        return $fieldIds;
    }

    /**
     * Helper: Buat info tentang filtering yang diterapkan
     */
    private function getFilterInfo($cartFieldIds)
    {
        if (empty($cartFieldIds)) {
            return [
                'has_filter' => false,
                'message' => 'Menampilkan semua fotografer tersedia',
                'cart_fields' => []
            ];
        }

        $cartFields = Field::whereIn('id', $cartFieldIds)->get(['id', 'name']);

        return [
            'has_filter' => true,
            'message' => 'Menampilkan fotografer yang kompatibel dengan lapangan di keranjang Anda',
            'cart_fields' => $cartFields,
            'field_names' => $cartFields->pluck('name')->toArray()
        ];
    }



    /**
     * Method lainnya tetap sama seperti sebelumnya...
     */
    public function show($id)
    {
        $photographer = Photographer::findOrFail($id);

        // Tambahkan rating dan review count
        $photographer->rating = $photographer->getRatingAttribute();
        $photographer->reviews_count = $photographer->getReviewsCountAttribute();

        // Tambahkan info lapangan dengan logic yang sama
        if ($photographer->field_id) {
            $field = Field::find($photographer->field_id);
            $photographer->assigned_field = $field;
            $photographer->field_restriction = true;
        } else {
            $photographer->assigned_field = null;
            $photographer->field_restriction = false;
        }


        return view('users.photographers.show', compact(
            'photographer',
        ));
    }

    /**
     * Helper: Check apakah fotografer kompatibel dengan lapangan di cart
     */
    private function checkPhotographerCompatibility($photographer, $cartFieldIds)
    {
        if (empty($cartFieldIds)) {
            // Jika tidak ada lapangan di cart, semua fotografer kompatibel
            return [
                'is_compatible' => true,
                'message' => 'Fotografer tersedia'
            ];
        }

        if ($photographer->field_id) {
            // Jika fotografer ditugaskan ke lapangan tertentu
            if (in_array($photographer->field_id, $cartFieldIds)) {
                return [
                    'is_compatible' => true,
                    'message' => 'Fotografer kompatibel dengan lapangan di keranjang Anda'
                ];
            } else {
                $assignedField = Field::find($photographer->field_id);
                return [
                    'is_compatible' => false,
                    'message' => "Fotografer ini hanya tersedia untuk {$assignedField->name}. Silakan tambahkan lapangan tersebut ke keranjang atau pilih fotografer lain."
                ];
            }
        } else {
            // Jika fotografer tidak terikat dengan lapangan tertentu
            return [
                'is_compatible' => true,
                'message' => 'Fotografer tersedia untuk semua lapangan di keranjang Anda'
            ];
        }
    }

    /**
     * Mendapatkan slot waktu yang tersedia untuk tanggal tertentu
     */
    public function getAvailableSlots(Request $request, $photographerId)
    {
        try {
            // Validasi input
            $request->validate([
                'date' => 'required|date'
            ]);

            // Cari fotografer
            $photographer = Photographer::findOrFail($photographerId);

            // Cek lapangan yang ditugaskan untuk fotografer ini
            $assignedField = Field::where('photographer_id', $photographer->user_id)->first();

            $date = $request->date;
            $carbonDate = Carbon::parse($date);

            // Get current time untuk comparison
            $now = Carbon::now();
            $isToday = $carbonDate->isToday();

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

            // Get cart items untuk fotografer ini pada tanggal yang dipilih
            $cartSlots = [];
            if (Auth::check()) {
                $userCart = Cart::where('user_id', Auth::id())->first();

                if ($userCart) {
                    $cartItems = CartItem::where('cart_id', $userCart->id)
                        ->where('type', 'photographer')
                        ->where('item_id', $photographerId)
                        ->whereDate('start_time', $date)
                        ->get();

                    foreach ($cartItems as $item) {
                        $startFormatted = Carbon::parse($item->start_time)->format('H:i');
                        $endFormatted = Carbon::parse($item->end_time)->format('H:i');
                        $cartSlots[] = $startFormatted . ' - ' . $endFormatted;
                    }
                }
            }

            // Dapatkan booking fotografer yang sudah ada pada tanggal tersebut
            $bookedSlots = PhotographerBooking::where('photographer_id', $photographerId)
                ->whereDate('start_time', $date)
                ->where('status', '!=', 'cancelled')
                ->get();

            // Jika fotografer ditugaskan ke lapangan, ambil juga booking lapangan
            $fieldBookedSlots = [];
            if ($assignedField) {
                $fieldBookedSlots = FieldBooking::where('field_id', $assignedField->id)
                    ->whereDate('start_time', $date)
                    ->where('status', '!=', 'cancelled')
                    ->get();
            }

            // Filter slot yang tersedia
            $availableSlots = [];
            foreach ($allSlots as $slot) {
                $startTime = Carbon::parse("{$date} {$slot['start']}");
                $endTime = Carbon::parse("{$date} {$slot['end']}");
                $displaySlot = $slot['start'] . ' - ' . $slot['end'];

                $isAvailable = true;
                $isInCart = in_array($displaySlot, $cartSlots);
                $bookingType = '';
                $isPastTime = false;

                // PERBAIKAN: Cek apakah slot waktu sudah lewat
                if ($isToday) {
                    // Jika tanggal yang dipilih adalah hari ini, cek apakah waktu AKHIR slot sudah lewat
                    // Slot disable jika waktu end sudah terlewati
                    if ($endTime <= $now) {
                        $isAvailable = false;
                        $isPastTime = true;
                    }
                }

                // Cek booking fotografer yang sudah ada (hanya jika bukan past time)
                if (!$isPastTime) {
                    foreach ($bookedSlots as $bookedBooking) {
                        if (
                            ($startTime >= $bookedBooking->start_time && $startTime < $bookedBooking->end_time) ||
                            ($endTime > $bookedBooking->start_time && $endTime <= $bookedBooking->end_time) ||
                            ($startTime <= $bookedBooking->start_time && $endTime >= $bookedBooking->end_time)
                        ) {
                            $isAvailable = false;
                            $bookingType = 'photographer';
                            break;
                        }
                    }

                    // Jika masih tersedia dan fotografer ditugaskan ke lapangan, cek slot lapangan
                    if ($isAvailable && $assignedField) {
                        foreach ($fieldBookedSlots as $fieldBooking) {
                            if (
                                ($startTime >= $fieldBooking->start_time && $startTime < $fieldBooking->end_time) ||
                                ($endTime > $fieldBooking->start_time && $endTime <= $fieldBooking->end_time) ||
                                ($startTime <= $fieldBooking->start_time && $endTime >= $fieldBooking->end_time)
                            ) {
                                $isAvailable = false;
                                $bookingType = 'field';
                                break;
                            }
                        }
                    }
                }

                // Harga booking fotografer
                $slotPrice = $photographer->price;

                // Tentukan status slot
                $status = 'available';
                if ($isPastTime) {
                    $status = 'past_time';
                } else if ($isInCart) {
                    $status = 'in_cart';
                } else if (!$isAvailable) {
                    $status = 'booked';
                }

                $availableSlots[] = [
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'display' => $displaySlot,
                    'is_available' => $isAvailable,
                    'in_cart' => $isInCart,
                    'price' => $slotPrice,
                    'status' => $status,
                    'booking_type' => $isAvailable ? '' : $bookingType,
                    'assigned_field' => $assignedField ? $assignedField->name : null,
                    'is_past_time' => $isPastTime
                ];
            }

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in getAvailableSlots for photographer', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to retrieve available slots',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membatalkan booking fotografer
     */
    public function cancelBooking($bookingId)
    {
        $booking = PhotographerBooking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status === 'pending' || $booking->status === 'confirmed') {
            $booking->status = 'cancelled';
            $booking->save();

            return back()->with('success', 'Booking fotografer berhasil dibatalkan');
        }

        return back()->with('error', 'Maaf, booking fotografer tidak dapat dibatalkan');
    }

    /**
     * Send booking notification to photographer
     */
    public function sendBookingNotification(PhotographerBooking $booking)
    {
        try {
            // Get the photographer record
            $photographer = Photographer::findOrFail($booking->photographer_id);

            // Get the photographer's user record
            $photographerUser = \App\Models\User::find($photographer->user_id);

            // Get the booking user
            $user = $booking->user;

            if ($photographerUser && $photographerUser->email) {
                // Pass both photographer user and booking user to the notification
                Mail::to($photographerUser->email)
                    ->send(new PhotographerBookingNotification($booking, $photographerUser, $user));

                $booking->notification_sent_at = now();
                $booking->save();

                Log::info('Photographer booking notification sent to: ' . $photographerUser->email);
                return true;
            } else {
                Log::warning('Photographer user or email not found for booking: ' . $booking->id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send photographer notification: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }

        return false;
    }
}
