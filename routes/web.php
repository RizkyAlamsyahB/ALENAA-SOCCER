<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\PointController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\User\FieldsController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Owner\ReviewsController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\OpenMabarController;
use App\Http\Controllers\Admin\SchedulesController;
use App\Http\Controllers\Owner\DiscountsController;
use App\Http\Controllers\User\MembershipController;
use App\Http\Controllers\Admin\RentalItemController;
use App\Http\Controllers\User\RentalItemsController;
use App\Http\Controllers\Admin\MembershipsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\User\PhotographerController;
use App\Http\Controllers\Admin\PhotoPackageController;
use App\Http\Controllers\Owner\PointVoucherController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Photographer\ScheduleController;
use App\Http\Controllers\Photographer\PhotographerDashboardController;

// Public Routes
Route::get('/', function () {
    // Jika sudah login, redirect berdasarkan role
    // Di route utama
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'owner') {
            return redirect()->route('owner.dashboard');
        } elseif ($user->role === 'user') {
            return redirect()->route('users.dashboard');
        } elseif ($user->role === 'photographer') {
            return redirect()->route('photographers.dashboard');
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

Route::get('/test/renewal-failed-email', [App\Http\Controllers\User\MembershipController::class, 'testRenewalFailedEmail'])
    ->middleware(['auth']) // Cukup auth saja untuk testing
    ->name('test.renewal-failed-email');
e('test.renewal-failed-email');

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
            Route::get('/checkout', [CartController::class, 'showCheckout'])->name('show.checkout');
            Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
            // Dalam grup cart management, tambahkan ini:
            Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('apply.discount');
            Route::post('/apply-point-voucher', [CartController::class, 'applyPointVoucher'])->name('apply.point.voucher');
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
    Route::get('/my/subscription/{id}', [MembershipController::class, 'subscriptionDetail'])->name('user.membership.subscription-detail'); // sesuaikan dengan nama di template email

    // Membership Management - Update rute yang menggunakan method dari PaymentController
    Route::prefix('membership')
        ->name('user.membership.')
        ->middleware(['auth', 'verified', 'checkRole:user'])
        ->group(function () {
            // Daftar membership
            Route::get('/', [MembershipController::class, 'index'])->name('index');

            // Detail membership
            Route::get('/{id}', [MembershipController::class, 'show'])
                ->name('show')
                ->where('id', '[0-9]+');

            // Pilih jadwal membership
            Route::get('/{id}/schedule', [MembershipController::class, 'selectSchedule'])->name('select.schedule');
            Route::post('/{id}/schedule', [MembershipController::class, 'saveScheduleToCart'])->name('save.schedule');

            // Membership pengguna
            Route::get('/my/memberships', [MembershipController::class, 'myMemberships'])->name('my-memberships');
            Route::get('/my/subscription/{id}', [MembershipController::class, 'subscriptionDetail'])->name('subscription.detail');

            // Slot waktu tersedia
            Route::get('/fields/{fieldId}/available-slots-membership', [MembershipController::class, 'getAvailableTimeSlotsByDate'])->name('availableSlots');

            // Pembayaran perpanjangan - UPDATED: sekarang menggunakan MembershipController
            Route::get('/renewal/pay/{id}', [MembershipController::class, 'showRenewalPayment'])->name('renewal.pay');
            Route::post('/create-renewal/{id}', [MembershipController::class, 'createRenewalInvoice'])->name('create.renewal');

            // Jadwalkan invoice perpanjangan - MOVED dari PaymentController
            Route::get('/schedule-renewal-invoices', [MembershipController::class, 'scheduleMembershipRenewalInvoices'])->name('schedule.renewal.invoices');

            // Cek membership yang kedaluwarsa - MOVED dari PaymentController
            Route::get('/check-expired-renewals', [MembershipController::class, 'checkExpiredMembershipRenewals'])->name('check.expired.renewals');

            Route::get('/manual-renewal/{id}', [MembershipController::class, 'manualRenewal'])->name('manual.renewal');
        });

    // Payment Management

    // Payment Management - PASTIKAN RUTE INI TETAP ADA
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

    // Points Management
    Route::prefix('points')
        ->name('user.points.')
        ->middleware(['auth', 'verified', 'checkRole:user'])
        ->group(function () {
            // Daftar voucher yang tersedia
            Route::get('/', [PointController::class, 'index'])->name('index');

            // Detail voucher
            Route::get('/voucher/{id}', [PointController::class, 'showVoucher'])->name('voucher-detail');

            // Proses penukaran voucher
            Route::post('/redeem/{id}', [PointController::class, 'redeemVoucher'])->name('redeem');

            // Riwayat poin
            Route::get('/history', [PointController::class, 'history'])->name('history');

            // Detail penukaran
            Route::get('/redemption/{id}', [PointController::class, 'showRedemption'])->name('redemption-detail');
        });

    Route::prefix('mabar')
        ->name('user.mabar.')
        ->middleware(['auth', 'verified', 'checkRole:user'])
        ->group(function () {
            // Menampilkan daftar open mabar
            Route::get('/', [OpenMabarController::class, 'index'])->name('index');

            // Menampilkan detail open mabar
            Route::get('/{id}', [OpenMabarController::class, 'show'])->name('show');

            // Form pembuatan open mabar baru
            Route::get('/create/new', [OpenMabarController::class, 'create'])->name('create');

            // Menyimpan open mabar baru
            Route::post('/store', [OpenMabarController::class, 'store'])->name('store');

            // Bergabung dengan open mabar
            Route::post('/{id}/join', [OpenMabarController::class, 'join'])->name('join');

            // Batalkan keikutsertaan
            Route::post('/{id}/cancel', [OpenMabarController::class, 'cancel'])->name('cancel');

            // Tandai peserta hadir (untuk pembuat mabar)
            Route::post('/{mabarId}/participants/{participantId}/attended', [OpenMabarController::class, 'markAttended'])->name('mark.attended');

            // Daftar mabar yang diikuti user
            Route::get('/my/mabars', [OpenMabarController::class, 'myMabars'])->name('my');

            // Chat grup mabar
            Route::get('/{id}/chat', [OpenMabarController::class, 'showChat'])->name('chat');
            Route::post('/{id}/send-message', [OpenMabarController::class, 'sendMessage'])->name('send.message');

            Route::get('/{id}/broadcast', [OpenMabarController::class, 'showBroadcastForm'])->name('broadcast.form');
            Route::post('/{id}/broadcast', [OpenMabarController::class, 'sendBroadcast'])->name('broadcast.send');

            Route::delete('/{id}/delete', [OpenMabarController::class, 'destroy'])->name('delete');
        });

    // Other Features
    // Route::get('/mabar', function () {
    //     return view('users.mabar');
    // })->name('mabar.index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
});

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
            'memberships' => MembershipsController::class,
            'transactions' => TransactionController::class,
            'users' => UserManagementController::class,
            'photo-packages' => PhotoPackageController::class,
            'schedules' => SchedulesController::class,
        ]);
        Route::get('schedule', [SchedulesController::class, 'index'])->name('schedule.index');
        Route::get('schedule/events', [SchedulesController::class, 'getScheduleEvents'])->name('schedule.events');
        Route::get('schedule/all-bookings', [SchedulesController::class, 'allBookingsTable'])->name('schedule.all-bookings');
        Route::get('schedule/field/{id}', [SchedulesController::class, 'fieldSchedule'])->name('schedule.field');
        Route::get('schedule/membership', [SchedulesController::class, 'membershipSchedule'])->name('schedule.membership');
        Route::get('schedule/membership/{id}', [SchedulesController::class, 'membershipDetail'])->name('schedule.membership.detail');
        Route::get('schedule/booking/{id}', [SchedulesController::class, 'getBookingDetail'])->name('schedule.booking');
        Route::get('schedule/booking/{id}/edit', [SchedulesController::class, 'editBooking'])->name('schedule.booking.edit');
        Route::put('schedule/booking/{id}', [SchedulesController::class, 'updateBooking'])->name('schedule.booking.update');
    });

// Owner Routes
Route::middleware(['auth', 'checkRole:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        // Dashboard Owner
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        //Diskon
        Route::resource('discounts', DiscountsController::class);
        //Reviews
        Route::get('reviews', [ReviewsController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{review}', [ReviewsController::class, 'show'])->name('reviews.show');
        Route::delete('reviews/{review}', [ReviewsController::class, 'destroy'])->name('reviews.destroy');
        Route::post('reviews/{review}/toggle-status', [ReviewsController::class, 'toggleStatus'])->name('reviews.toggle-status');

        // Summary and analytics
        Route::get('reviews-summary', [ReviewsController::class, 'reviewSummary'])->name('reviews.summary');

        // Get reviews for specific item
        Route::get('reviews-for-item', [ReviewsController::class, 'getItemReviews'])->name('reviews.item-reviews');
        // Voucher Poin
        Route::resource('point_vouchers', PointVoucherController::class);
        Route::patch('point-vouchers/{pointVoucher}/toggle-status', [PointVoucherController::class, 'toggleStatus'])->name('point_vouchers.toggle-status');


        // Rute khusus owner
        // Route::get('/financial-report', [Owner\FinancialReportController::class, 'index'])->name('financial-report');
        // Route::get('/analytics', [Owner\AnalyticsController::class, 'index'])->name('analytics');
    });

// Owner Routes
Route::middleware(['auth', 'checkRole:photographer'])
    ->prefix('photographers')
    ->name('photographers.')
    ->group(function () {
        // Dashboard photographer
        Route::get('/dashboard', [ScheduleController::class, 'dashboard'])->name('dashboard');

        // Rute khusus photographer
        Route::get('/schedule', [ScheduleController::class, 'schedule'])->name('schedule');
    });
// Auth Routes
require __DIR__ . '/auth.php';
