<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\User\FieldsController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\MembershipController;
use App\Http\Controllers\Admin\RentalItemController;
use App\Http\Controllers\User\RentalItemsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\User\PhotographerController;
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
            return redirect()->route('users.dashboard');
        }
    }

    // Jika tidak login, tampilkan landing page dengan testimonial
    $testimonials = \App\Models\Review::with(['user', 'reviewable'])
        ->where('rating', 5)
        ->whereNotNull('comment')
        ->where('status', 'active')
        ->orderByRaw('LENGTH(comment) DESC')
        ->limit(3)
        ->get();

    return view('welcome', compact('testimonials'));
})->name('welcome');
// Route testing untuk invoice membership
Route::get('/test-membership-invoice', function() {
    $controller = new PaymentController();
    return $controller->scheduleMembershipRenewalInvoices();
})->name('test.membership.invoice');
// User Routes
Route::middleware(['auth', 'verified', 'checkRole:user'])->group(function () {
    // Dashboard
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('users.dashboard');


    // Fields (Lapangan) Management
    Route::prefix('fields')
        ->name('user.fields.')
        ->group(function () {
            Route::get('/', [FieldsController::class, 'index'])->name('index');
            Route::get('/{id}', [FieldsController::class, 'show'])->name('show');
            Route::get('/{fieldId}/available-slots', [FieldsController::class, 'getAvailableSlots'])->name('availableSlots');
            Route::get('/cart-slots', [FieldsController::class, 'getCartSlots'])->name('cartSlots');
            Route::post('/bookings/{bookingId}/cancel', [FieldsController::class, 'cancelBooking'])->name('bookings.cancel');
        });

    // Cart Management
    Route::prefix('cart')
        ->name('user.cart.')
        ->group(function () {
            Route::post('/add', [CartController::class, 'addToCart'])->name('add');
            Route::get('/', [CartController::class, 'viewCart'])->name('view');
            Route::delete('/{itemId}', [CartController::class, 'removeFromCart'])->name('remove');
            Route::delete('/api/{itemId}', [CartController::class, 'apiRemoveFromCart'])->name('api.remove');
            Route::get('/sidebar', [CartController::class, 'getCartSidebar'])->name('sidebar');
            Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
            Route::get('/clear', [CartController::class, 'clearCart'])->name('clear');
            Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
            // Dalam grup cart management, tambahkan ini:
            Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('apply.discount');
            Route::get('/remove-discount', [CartController::class, 'removeDiscount'])->name('remove.discount');
            Route::get('/add-membership/{id}', [CartController::class, 'addMembershipToCartRoute'])->name('add.membership');
        });

    // Rental Management
    Route::prefix('rental')
        ->name('user.rental_items.')
        ->group(function () {
            Route::get('/', [RentalItemsController::class, 'index'])->name('index');
            Route::get('/items/{id}', [RentalItemsController::class, 'show'])->name('show');
            Route::post('/cart/add', [RentalItemsController::class, 'addToCart'])->name('cart.add');
            Route::get('/cart', [RentalItemsController::class, 'viewCart'])->name('cart');
            Route::delete('/cart/{itemId}', [RentalItemsController::class, 'removeFromCart'])->name('cart.remove');
            Route::post('/checkout', [RentalItemsController::class, 'checkout'])->name('checkout');
            Route::get('/history', [RentalItemsController::class, 'history'])->name('history');
            Route::get('/orders/{id}', [RentalItemsController::class, 'orderDetail'])->name('order.detail');
            Route::get('/items/{rentalItemId}/available-slots', [RentalItemsController::class, 'getAvailableSlots'])->name('availableSlots');
        });

    // Photographer Management
    Route::prefix('photographer')
        ->name('user.photographer.')
        ->group(function () {
            Route::get('/', [PhotographerController::class, 'index'])->name('index');
            Route::get('/{id}', [PhotographerController::class, 'show'])->name('show');
            Route::get('/{photographerId}/available-slots', [PhotographerController::class, 'getAvailableSlots'])->name('availableSlots');
            Route::post('/bookings/{bookingId}/cancel', [PhotographerController::class, 'cancelBooking'])->name('bookings.cancel');
        });
// Tambahkan route ini di file routes jika belum ada
Route::get('/my/subscription/{id}', [MembershipController::class, 'subscriptionDetail'])
    ->name('user.membership.subscription-detail'); // sesuaikan dengan nama di template email
    // Membership Management
Route::prefix('membership')
->name('user.membership.')
->middleware(['auth', 'verified', 'checkRole:user'])
->group(function () {
    // Daftar membership
    Route::get('/', [MembershipController::class, 'index'])->name('index');

    // Detail membership
    Route::get('/{id}', [MembershipController::class, 'show'])->name('show')->where('id', '[0-9]+');

    // Pilih jadwal membership
    Route::get('/{id}/schedule', [MembershipController::class, 'selectSchedule'])->name('select.schedule');
    Route::post('/{id}/schedule', [MembershipController::class, 'saveScheduleToCart'])->name('save.schedule');

    // Membership pengguna
    Route::get('/my/memberships', [MembershipController::class, 'myMemberships'])->name('my-memberships');
    Route::get('/my/subscription/{id}', [MembershipController::class, 'subscriptionDetail'])->name('subscription.detail');

    // Slot waktu tersedia
    Route::get('/fields/{fieldId}/available-slots-membership', [MembershipController::class, 'getAvailableTimeSlotsByDate'])->name('availableSlots');

    // Pembayaran perpanjangan
    Route::get('/renewal/pay/{id}', [PaymentController::class, 'showRenewalPayment'])->name('renewal.pay');
    Route::post('/create-renewal/{id}', [PaymentController::class, 'createRenewalInvoice'])->name('create.renewal');
});

    // Payment Management
    Route::prefix('payment')
        ->name('user.payment.')
        ->group(function () {
            Route::get('/success', [PaymentController::class, 'finish'])->name('success');
            Route::get('/unfinish', [PaymentController::class, 'unfinish'])->name('unfinish');
            Route::get('/history', [PaymentController::class, 'history'])->name('history');
            Route::get('/detail/{id}', [PaymentController::class, 'detail'])->name('detail');
            Route::get('/error', [PaymentController::class, 'error'])->name('error');
            Route::get('/{id}/continue', [PaymentController::class, 'continuePayment'])->name('continue');
            Route::get('/{id}/invoice', [PaymentController::class, 'downloadInvoice'])->name('invoice');
        });

    // Review Management
    Route::prefix('review')
        ->name('user.review.')
        ->group(function () {
            Route::post('/store', [ReviewController::class, 'store'])->name('store');
            Route::get('/item', [ReviewController::class, 'getItemReviews'])->name('item');
        });

    // Other Features
    Route::get('/mabar', function () {
        return view('users.mabar');
    })->name('mabar.index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
});
Route::get('/test-renewal-email', function() {
    $subscriptionId = 1; // Ganti dengan ID subscription yang valid
    $subscription = \App\Models\MembershipSubscription::with(['user', 'membership', 'sessions'])
        ->find($subscriptionId);

    if (!$subscription) {
        return 'Subscription tidak ditemukan';
    }

    \Illuminate\Support\Facades\Mail::to($subscription->user->email)->send(
        new \App\Mail\MembershipRenewalSuccess([
            'user' => $subscription->user,
            'subscription' => $subscription,
        ])
    );

    return 'Email test telah dikirim ke ' . $subscription->user->email;
})->middleware('auth');
// Payment Notification Endpoints (Diakses oleh Midtrans, tidak memerlukan auth)
Route::prefix('payment')
    ->name('payment.')
    ->group(function () {
        // Midtrans notification handler
        Route::post('/notification', [PaymentController::class, 'notification'])->name('notification');

        // Recurring payment notification
        Route::post('/recurring-notification', [PaymentController::class, 'recurringNotification'])->name('recurring.notification');

        // Pay account notification
        Route::post('/pay-account-notification', [PaymentController::class, 'payAccountNotification'])->name('pay-account.notification');
    });

// Admin Routes
Route::middleware(['auth', 'checkRole:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        Route::get('/admin/fields', [FieldController::class, 'index'])->name('admin.fields.index');
        // Manajemen Lapangan
        Route::resource('fields', FieldController::class);
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
// Route::middleware(['auth', 'checkRole:owner'])
//     ->prefix('owner')
//     ->name('owner.')
//     ->group(function () {
//         // Dashboard Owner
//         Route::get('/dashboard', function () {
//             return view('owner.dashboard');
//         })->name('dashboard');

//         // Rute khusus owner
//         Route::get('/financial-report', [Owner\FinancialReportController::class, 'index'])->name('financial-report');
//         Route::get('/analytics', [Owner\AnalyticsController::class, 'index'])->name('analytics');
//     });

// Auth Routes
require __DIR__ . '/auth.php';
