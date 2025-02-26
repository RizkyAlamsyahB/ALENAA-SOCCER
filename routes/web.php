<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    // Jika sudah login, redirect berdasarkan role
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin' || $user->role === 'owner') {
            return redirect()->route('dashboard');
        }
    }
    return view('welcome');
})->name('welcome');

// User Routes - Berdasarkan file blade yang ada
Route::middleware(['auth', 'role:user'])->group(function () {
    // Dashboard User
    Route::get('/dashboard', function () {
        return view('users.dashboard');
    })->name('user.dashboard');

    // Mabar Routes
    Route::get('/mabar', function () {
        return view('users.mabar');
    })->name('mabar.index');

    Route::get('/detail-mabar', function () {
        return view('users.detail-mabar');
    })->name('mabar.detail');

    // Lapangan Routes
    Route::get('/lapangan', function () {
        return view('users.lapangan');
    })->name('lapangan');

    // Maincourt Route
    Route::get('/maincourt', function () {
        return view('users.maincourt');
    })->name('maincourt');

    // Membership Routes
    Route::get('/membership', function () {
        return view('users.membership');
    })->name('membership');

    Route::get('/payment-membership', function () {
        return view('users.peyment-membership');
    })->name('payment.membership');

    // Payment Route
    Route::get('/payment', function () {
        return view('users.payment');
    })->name('payment');

    // Rental Routes
    Route::get('/rental', function () {
        return view('users.rental');
    })->name('rental.index');

    Route::get('/product-rental', function () {
        return view('users.product-rental');
    })->name('rental.products');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin/Owner Routes
Route::middleware(['auth', 'role:admin,owner'])
    ->prefix('admin')
    ->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Tambahkan route admin lainnya di sini
    });

// Auth Routes
require __DIR__ . '/auth.php';
