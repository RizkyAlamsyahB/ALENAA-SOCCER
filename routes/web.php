<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\RentalItemController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\PhotoPackageController;
use App\Http\Controllers\Admin\UserManagementController;

// Public Routes
Route::get('/', function () {
    // Jika sudah login, redirect berdasarkan role
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'owner') {
            return redirect()->route('owner.dashboard');
        } elseif ($user->role === 'user') {
            return redirect()->route('user.dashboard');
        }
    }
    return view('welcome');
})->name('welcome');

// User Routes
Route::middleware(['auth', 'checkRole:user'])->group(function () {
    Route::get('/dashboard', function () {
        return view('users.dashboard');
    })->name('user.dashboard');

    // Rute user lainnya (Mabar, Lapangan, dll.)
    Route::get('/mabar', function () {
        return view('users.mabar');
    })->name('mabar.index');

    Route::get('/lapangan', function () {
        return view('users.lapangan');
    })->name('lapangan');

    Route::get('/membership', function () {
        return view('users.membership');
    })->name('membership');

    Route::get('/rental', function () {
        return view('users.rental');
    })->name('rental.index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::get('/admin/fields', [FieldController::class, 'index'])->name('admin.fields.index');
    // Manajemen Lapangan
    Route::resource('fields', FieldController::class);
    Route::put('/fields/{field}/toggle-status', [FieldController::class, 'toggleStatus'])
        ->name('fields.toggle-status');

    // Rute admin lainnya untuk CRUD
    Route::resources([
        'products' => ProductController::class,
        'rental-items' => RentalItemController::class,
        'memberships' => MembershipController::class,
        'transactions' => TransactionController::class,
        'users' => UserManagementController::class,
        'discounts' => DiscountController::class,
        'photo-packages' => PhotoPackageController::class,
    ]);
});

// Owner Routes
Route::middleware(['auth', 'checkRole:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Dashboard Owner
    Route::get('/dashboard', function () {
        return view('owner.dashboard');
    })->name('dashboard');

    // Rute khusus owner
    Route::get('/financial-report', [Owner\FinancialReportController::class, 'index'])
        ->name('financial-report');
    Route::get('/analytics', [Owner\AnalyticsController::class, 'index'])
        ->name('analytics');
});

// Auth Routes
require __DIR__ . '/auth.php';
