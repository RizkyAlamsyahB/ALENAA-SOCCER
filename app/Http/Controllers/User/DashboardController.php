<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Discount; // Tambahkan model Discount
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard user
     */
    public function index()
    {
        $user = Auth::user();

        // Data booking lapangan terbaru
        $recentFieldBookings = FieldBooking::with(['field'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();

        // Data booking rental terbaru
        $recentRentalBookings = RentalBooking::with(['rentalItem'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();

        // Riwayat pembayaran terbaru
        $recentPayments = Payment::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(5)->get();

        // Review yang diberikan
        $userReviews = Review::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(3)->get();

        $testimonials = Review::with(['user', 'reviewable'])
            ->where('rating', '>=', 4) // <-- Ubah kriteria rating untuk mengambil rating 4 dan 5
            ->whereNotNull('comment')
            ->where('status', 'active')
            ->orderByRaw('LENGTH(comment) DESC')
            ->limit(3)
            ->get();

        // Diskon aktif untuk promo banner - disesuaikan dengan skema database
        $activeDiscounts = Discount::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->orderBy('value', 'desc') // Menggunakan value, bukan discount_percent
            ->limit(4)
            ->get();

// Stats data for counter
$fieldCount = \App\Models\Field::count(); // Count of available fields

// Count active members based on active membership subscriptions
$activeMemberCount = \App\Models\MembershipSubscription::where('status', 'active')
    ->whereDate('end_date', '>=', now())
    ->distinct('user_id')
    ->count('user_id');


return view('users.dashboard', compact(
    'recentFieldBookings',
    'recentRentalBookings',
    'recentPayments',
    'userReviews',
    'testimonials',
    'activeDiscounts',
    'fieldCount',
    'activeMemberCount',
));
    }
}
