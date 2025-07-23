<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\POSController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\PointController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\User\FieldsController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Owner\ReportsController;
use App\Http\Controllers\Owner\ReviewsController;
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
use App\Http\Controllers\Owner\UserManagementController;
use App\Http\Controllers\User\BookingReminderController;
use App\Http\Controllers\Photographer\ScheduleController;
use App\Http\Controllers\Photographer\PhotographerDashboardController;

// routes/web.php
Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// If using route groups for auth
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.pos.index');
    Route::get('/owner/dashboard', [DashboardController::class, 'index'])->name('owner.reports.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('users.dashboard');
    Route::get('/photographer/dashboard', [DashboardController::class, 'index'])->name('photographers.schedule');
});

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

    // Tambahkan route ini di dalam grup cart management di routes/web.php

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

            // Discount & Voucher management
            Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('apply.discount');
            Route::post('/apply-point-voucher', [CartController::class, 'applyPointVoucher'])->name('apply.point.voucher');
            Route::get('/remove-discount', [CartController::class, 'removeDiscount'])->name('remove.discount');
            Route::get('/add-membership/{id}', [CartController::class, 'addMembershipToCartRoute'])->name('add.membership');

            // NEW: Item management routes
            Route::get('/item/{itemId}/details', [CartController::class, 'getCartItemDetails'])->name('item.details');
            Route::post('/update-quantity/{itemId}', [CartController::class, 'updateQuantity'])->name('update.quantity');
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

            // Routes untuk modal cart:
            Route::get('/available-for-cart', [PointController::class, 'getAvailableVouchersForCart'])->name('available.for.cart');
            Route::post('/redeem-from-cart/{id}', [PointController::class, 'redeemVoucherFromCart'])->name('redeem.from.cart');

            // TAMBAHAN BARU - untuk apply voucher yang sudah dimiliki:
            Route::post('/apply-owned-voucher/{redemptionId}', [PointController::class, 'applyOwnedVoucherToCart'])->name('apply.owned.voucher');
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
Route::prefix('bookings')
    ->name('user.bookings.')
    ->group(function () {
        Route::get('/upcoming', [BookingReminderController::class, 'upcomingBookings'])->name('upcoming');
        Route::post('/update-reminder-preferences', [BookingReminderController::class, 'updateReminderPreferences'])->name('update.reminder.preferences');
    });
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

        // POS Routes
        Route::prefix('pos')
            ->name('pos.')
            ->group(function () {
                // Halaman utama POS
                Route::get('/', [POSController::class, 'index'])->name('index');

                // APIs untuk menambahkan item ke keranjang
                Route::post('/add-field', [POSController::class, 'addFieldToCart'])->name('add.field');
                Route::post('/add-rental', [POSController::class, 'addRentalItemToCart'])->name('add.rental');
                Route::post('/add-photographer', [POSController::class, 'addPhotographerToCart'])->name('add.photographer');
                Route::post('/add-product', [POSController::class, 'addProductToCart'])->name('add.product');

                // API untuk mendapatkan slot waktu tersedia
                Route::get('/field-timeslots', [POSController::class, 'getFieldTimeSlots'])->name('field.timeslots');
                Route::get('/photographer-timeslots', [POSController::class, 'getPhotographerTimeSlots'])->name('photographer.timeslots');

                // Menghapus item dari keranjang
                Route::delete('/remove-item/{item_id}', [POSController::class, 'removeFromCart'])->name('remove.item');

                // Checkout
                Route::post('/checkout', [POSController::class, 'checkout'])->name('checkout');

                // Struk pembayaran
                Route::get('/receipt/{id}', [POSController::class, 'showReceipt'])->name('receipt');
                Route::get('/receipt/{id}/download', [POSController::class, 'downloadReceipt'])->name('receipt.download');

                // Riwayat transaksi
                Route::get('/history', [POSController::class, 'transactionHistory'])->name('history');

                Route::get('/admin/customers/search', [POSController::class, 'searchCustomers'])->name('customers.search');
            });
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

        // Photographer Tasks Monitoring - NEW FEATURE
        Route::prefix('photographer-tasks')
            ->name('photographer-tasks.')
            ->group(function () {
                Route::get('/', [App\Http\Controllers\Owner\PhotographerTasksController::class, 'index'])->name('index');
                Route::get('/{task}', [App\Http\Controllers\Owner\PhotographerTasksController::class, 'show'])->name('show');
                Route::get('/data/tasks', [App\Http\Controllers\Owner\PhotographerTasksController::class, 'getTasksData'])->name('data');
                Route::get('/analytics/performance', [App\Http\Controllers\Owner\PhotographerTasksController::class, 'photographerPerformance'])->name('performance');
                Route::post('/{task}/reminder', [App\Http\Controllers\Owner\PhotographerTasksController::class, 'sendReminder'])->name('reminder');
            });

        // Diskon
        Route::resource('discounts', App\Http\Controllers\Owner\DiscountsController::class);

        // Reviews
        Route::get('reviews', [App\Http\Controllers\Owner\ReviewsController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{review}', [App\Http\Controllers\Owner\ReviewsController::class, 'show'])->name('reviews.show');
        Route::delete('reviews/{review}', [App\Http\Controllers\Owner\ReviewsController::class, 'destroy'])->name('reviews.destroy');
        Route::post('reviews/{review}/toggle-status', [App\Http\Controllers\Owner\ReviewsController::class, 'toggleStatus'])->name('reviews.toggle-status');

        // Summary and analytics for reviews
        Route::get('reviews-summary', [App\Http\Controllers\Owner\ReviewsController::class, 'reviewSummary'])->name('reviews.summary');
        Route::get('reviews-for-item', [App\Http\Controllers\Owner\ReviewsController::class, 'getItemReviews'])->name('reviews.item-reviews');

        // Voucher Poin
        Route::resource('point_vouchers', App\Http\Controllers\Owner\PointVoucherController::class);
        Route::patch('point-vouchers/{pointVoucher}/toggle-status', [App\Http\Controllers\Owner\PointVoucherController::class, 'toggleStatus'])->name('point_vouchers.toggle-status');

        // Reports & Analytics - Grouped for better organization
        Route::prefix('reports')
            ->name('reports.')
            ->group(function () {
                // Main reports page
                Route::get('/', [App\Http\Controllers\Owner\ReportsController::class, 'index'])->name('index');

                // Legacy revenue report routes (if needed)
                Route::get('/revenue', [App\Http\Controllers\Owner\ReportsController::class, 'revenueReport'])->name('revenue');
                Route::get('/field-revenue', [App\Http\Controllers\Owner\ReportsController::class, 'fieldRevenueReport'])->name('field-revenue');
                Route::get('/rental-revenue', [App\Http\Controllers\Owner\ReportsController::class, 'rentalRevenueReport'])->name('rental-revenue');
                Route::get('/photographer-revenue', [App\Http\Controllers\Owner\ReportsController::class, 'photographerRevenueReport'])->name('photographer-revenue');
                Route::get('/membership-revenue', [App\Http\Controllers\Owner\ReportsController::class, 'membershipRevenueReport'])->name('membership-revenue');
                Route::get('/product-sales', [App\Http\Controllers\Owner\ReportsController::class, 'productSalesRevenueReport'])->name('product-sales-revenue');
                Route::get('/transactions', [App\Http\Controllers\Owner\ReportsController::class, 'transactionHistory'])->name('transactions');

                // API endpoints for charts and data
                Route::get('/dashboard-stats', [App\Http\Controllers\Owner\ReportsController::class, 'dashboardStats'])->name('dashboard-stats');

                // NEW: Table data and export endpoints
                Route::get('/table-data', [App\Http\Controllers\Owner\ReportsController::class, 'getTableData'])->name('table-data');
                Route::get('/export', [App\Http\Controllers\Owner\ReportsController::class, 'exportToExcel'])->name('export');
            });

        // User Management
        Route::resource('users', App\Http\Controllers\Owner\UserManagementController::class);
        Route::get('customers', [App\Http\Controllers\Owner\UserManagementController::class, 'customers'])->name('users.customers');
    });

Route::middleware(['auth', 'checkRole:photographer'])
    ->prefix('photographers')
    ->name('photographers.')
    ->group(function () {
        // Dashboard photographer
        Route::get('/dashboard', [ScheduleController::class, 'dashboard'])->name('dashboard');

        // Rute khusus photographer
        Route::get('/schedule', [ScheduleController::class, 'schedule'])->name('schedule');

        // Route untuk booking details
        Route::get('/booking-details/{bookingId}/{bookingType}', [ScheduleController::class, 'getBookingDetails'])->name('booking-details');

        // Route untuk confirm booking
        Route::post('/confirm-booking/{bookingId}/{bookingType}', [ScheduleController::class, 'confirmBooking'])->name('confirm-booking');

        // Route untuk mark shooting completed
        Route::post('/mark-shooting-completed/{bookingId}', [ScheduleController::class, 'markShootingCompleted'])->name('mark-shooting-completed');

        // Route untuk send photo gallery
        Route::post('/send-photo-gallery/{bookingId}', [ScheduleController::class, 'sendPhotoGallery'])->name('send-photo-gallery');
    });
// Auth Routes
require __DIR__ . '/auth.php';
