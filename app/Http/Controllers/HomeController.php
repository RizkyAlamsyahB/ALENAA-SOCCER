<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda
     */
    public function index()
    {
        // Ambil 3 review terbaik (rating 5) dengan komentar terlengkap
        $testimonials = Review::with(['user', 'reviewable'])
            ->where('rating', 5)
            ->whereNotNull('comment')
            ->where('status', 'active')
            ->orderByRaw('LENGTH(comment) DESC')
            ->limit(3)
            ->get();

        return view('welcome', compact('testimonials'));
    }
}
