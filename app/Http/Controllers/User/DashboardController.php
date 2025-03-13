<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\Payment;
use App\Models\Review;
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
        $recentPayments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Review yang diberikan
        $userReviews = Review::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Testimonial terbaik untuk ditampilkan di bagian "Apa Kata Mereka?"
        $testimonials = Review::with(['user', 'reviewable'])
            ->where('rating', 5)
            ->whereNotNull('comment')
            ->where('status', 'active')
            ->orderByRaw('LENGTH(comment) DESC')
            ->limit(3)
            ->get();

        return view('users.dashboard', compact(
            'recentFieldBookings',
            'recentRentalBookings',
            'recentPayments',
            'userReviews',
            'testimonials'
        ));
    }
}
