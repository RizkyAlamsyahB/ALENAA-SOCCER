<?php

namespace App\Http\Controllers\Owner;

use App\Models\Field;
use App\Models\Payment;
use App\Models\FieldBooking;
use Illuminate\Http\Request;
use App\Models\RentalBooking;
use Illuminate\Support\Carbon;
use App\Models\ProductSaleItem;
use Illuminate\Support\Facades\DB;
use App\Models\PhotographerBooking;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
/**
 * Display the main reports dashboard
 */
public function index()
{
    // Pendapatan lapangan bersih (hanya non-membership)
    $fieldNetRevenue =
        DB::table('field_bookings')
            ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
            ->where('field_bookings.status', 'confirmed')
            ->where('field_bookings.is_membership', 0)
            ->select(
                DB::raw('SUM(field_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
        (field_bookings.total_price / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Pendapatan sewa perlengkapan bersih (hanya non-membership)
    $rentalNetRevenue =
        DB::table('rental_bookings')
            ->leftJoin('payments', 'rental_bookings.payment_id', '=', 'payments.id')
            ->where('rental_bookings.status', 'confirmed')
            ->where('rental_bookings.is_membership', 0)
            ->select(
                DB::raw('SUM(rental_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
        (rental_bookings.total_price / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Pendapatan fotografer bersih (hanya non-membership)
    $photographerNetRevenue =
        DB::table('photographer_bookings')
            ->leftJoin('payments', 'photographer_bookings.payment_id', '=', 'payments.id')
            ->where('photographer_bookings.status', 'confirmed')
            ->where('photographer_bookings.is_membership', 0)
            ->select(
                DB::raw('SUM(photographer_bookings.price) - COALESCE(SUM(payments.discount_amount *
        (photographer_bookings.price / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Pendapatan product sales bersih - Perbaikan untuk menangani transaksi POS
    $productSalesNetRevenue =
        DB::table('product_sale_items')
            ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
            ->where(function($query) {
                $query->where('payments.transaction_status', 'settlement')
                      ->orWhere('payments.transaction_status', 'success');
            })
            ->select(
                DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) - COALESCE(SUM(payments.discount_amount *
        ((product_sale_items.price * product_sale_items.quantity) / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Pendapatan membership bersih dari subscription
    $membershipNetRevenue =
        DB::table('membership_subscriptions')
            ->leftJoin('payments', 'membership_subscriptions.payment_id', '=', 'payments.id')
            ->where('membership_subscriptions.status', 'active')
            ->select(
                DB::raw('SUM(membership_subscriptions.price) - COALESCE(SUM(payments.discount_amount *
        (membership_subscriptions.price / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Hitung total pendapatan bersih termasuk membership dan product sales
    $totalNetRevenue = $fieldNetRevenue + $rentalNetRevenue + $photographerNetRevenue + $membershipNetRevenue + $productSalesNetRevenue;

    // Get count of fields for display
    $fieldCount = Field::count();

    // This month's net revenue (all types including membership)
    $monthlyNetRevenue =
        DB::table('payments')
            ->where(function($query) {
                $query->where('transaction_status', 'settlement')
                      ->orWhere('transaction_status', 'success');
            })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('SUM(amount - COALESCE(discount_amount, 0)) as net_revenue')
            ->value('net_revenue') ?? 0;

    // Get membership stats
    $activeMemberships = DB::table('membership_subscriptions')->where('status', 'active')->where('end_date', '>', Carbon::now())->count();

    $membershipUsageCount = DB::table('field_bookings')->where('is_membership', 1)->where('status', 'confirmed')->count() + DB::table('rental_bookings')->where('is_membership', 1)->where('status', 'confirmed')->count() + DB::table('photographer_bookings')->where('is_membership', 1)->where('status', 'confirmed')->count();

    return view('owner.reports.index', compact(
        'totalNetRevenue',
        'fieldCount',
        'monthlyNetRevenue',
        'fieldNetRevenue',
        'rentalNetRevenue',
        'photographerNetRevenue',
        'membershipNetRevenue',
        'productSalesNetRevenue',
        'activeMemberships',
        'membershipUsageCount'
    ));
}

/**
 * Revenue reports
 */
/**
 * Revenue reports
 */
public function revenueReport(Request $request)
{
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

    // Convert to start and end of day
    $startDateTime = Carbon::parse($startDate)->startOfDay();
    $endDateTime = Carbon::parse($endDate)->endOfDay();

    // Pendapatan bersih booking lapangan (non-membership) - setelah diskon
    $fieldBookingNetRevenue =
        DB::table('field_bookings')
            ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
            ->where('field_bookings.status', 'confirmed')
            ->where('field_bookings.is_membership', 0)
            ->whereBetween('field_bookings.created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('SUM(field_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
            (field_bookings.total_price / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Pendapatan bersih sewa perlengkapan (non-membership) - setelah diskon
    $rentalNetRevenue =
        DB::table('rental_bookings')
            ->leftJoin('payments', 'rental_bookings.payment_id', '=', 'payments.id')
            ->where('rental_bookings.status', 'confirmed')
            ->where('rental_bookings.is_membership', 0)
            ->whereBetween('rental_bookings.created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('SUM(rental_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
            (rental_bookings.total_price / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Pendapatan bersih fotografer (non-membership) - setelah diskon
    $photographerNetRevenue =
        DB::table('photographer_bookings')
            ->leftJoin('payments', 'photographer_bookings.payment_id', '=', 'payments.id')
            ->where('photographer_bookings.status', 'confirmed')
            ->where('photographer_bookings.is_membership', 0)
            ->whereBetween('photographer_bookings.created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('SUM(photographer_bookings.price) - COALESCE(SUM(payments.discount_amount *
            (photographer_bookings.price / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Pendapatan product sales bersih - PERBAIKAN: Menambahkan kondisi 'success'
    $productSalesNetRevenue =
        DB::table('product_sale_items')
            ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
            ->where(function($query) {
                $query->where('payments.transaction_status', 'settlement')
                    ->orWhere('payments.transaction_status', 'success');
            })
            ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) - COALESCE(SUM(payments.discount_amount *
            ((product_sale_items.price * product_sale_items.quantity) / payments.original_amount)), 0) as net_revenue'),
            )
            ->value('net_revenue') ?? 0;

    // Total pendapatan bersih termasuk product sales
    $totalNetRevenue = $fieldBookingNetRevenue + $rentalNetRevenue + $photographerNetRevenue + $productSalesNetRevenue;

    // Pendapatan berdasarkan hari
    $revenueByDay = collect();
    $currentDate = clone $startDateTime;

    while ($currentDate <= $endDateTime) {
        $dayStart = clone $currentDate->startOfDay();
        $dayEnd = clone $currentDate->endOfDay();

        // Pendapatan dan diskon per kategori per hari
        $dayFieldRevenue = DB::table('field_bookings')
            ->where('status', 'confirmed')
            ->where('is_membership', 0)
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->sum('total_price');

        $dayRentalRevenue = DB::table('rental_bookings')
            ->where('status', 'confirmed')
            ->where('is_membership', 0)
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->sum('total_price');

        $dayPhotographerRevenue = DB::table('photographer_bookings')
            ->where('status', 'confirmed')
            ->where('is_membership', 0)
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->sum('price');

        // Pendapatan produk per hari - PERBAIKAN: Menambahkan kondisi 'success'
        $dayProductRevenue = DB::table('product_sale_items')
            ->join('payments', 'product_sale_items.payment_id', '=', 'payments.id')
            ->where(function($query) {
                $query->where('payments.transaction_status', 'settlement')
                    ->orWhere('payments.transaction_status', 'success');
            })
            ->whereBetween('payments.created_at', [$dayStart, $dayEnd])
            ->sum(DB::raw('product_sale_items.price * product_sale_items.quantity'));

        // Diskon per hari - PERBAIKAN: Menambahkan kondisi 'success'
        $dayDiscountAmount = DB::table('payments')
            ->where(function($query) {
                $query->where('transaction_status', 'settlement')
                    ->orWhere('transaction_status', 'success');
            })
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->sum('discount_amount');

        $dayGrossTotal = $dayFieldRevenue + $dayRentalRevenue + $dayPhotographerRevenue + $dayProductRevenue;
        $dayNetTotal = $dayGrossTotal - $dayDiscountAmount;

        $revenueByDay->push(
            (object) [
                'date' => $currentDate->format('Y-m-d'),
                'total_gross' => $dayGrossTotal,
                'total_net' => $dayNetTotal,
                'discount_amount' => $dayDiscountAmount,
                'field_revenue' => $dayFieldRevenue,
                'rental_revenue' => $dayRentalRevenue,
                'photographer_revenue' => $dayPhotographerRevenue,
                'product_revenue' => $dayProductRevenue,
            ],
        );

        $currentDate->addDay();
    }

    // We'll still calculate these for the chart
    $fieldBookingRevenue = DB::table('field_bookings')
        ->where('status', 'confirmed')
        ->where('is_membership', 0)
        ->whereBetween('created_at', [$startDateTime, $endDateTime])
        ->sum('total_price');

    $rentalRevenue = DB::table('rental_bookings')
        ->where('status', 'confirmed')
        ->where('is_membership', 0)
        ->whereBetween('created_at', [$startDateTime, $endDateTime])
        ->sum('total_price');

    $photographerRevenue = DB::table('photographer_bookings')
        ->where('status', 'confirmed')
        ->where('is_membership', 0)
        ->whereBetween('created_at', [$startDateTime, $endDateTime])
        ->sum('price');

    // Tambahkan untuk pendapatan produk - PERBAIKAN: Menambahkan kondisi 'success'
    $productRevenue = DB::table('product_sale_items')
        ->join('payments', 'product_sale_items.payment_id', '=', 'payments.id')
        ->where(function($query) {
            $query->where('payments.transaction_status', 'settlement')
                ->orWhere('payments.transaction_status', 'success');
        })
        ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
        ->sum(DB::raw('product_sale_items.price * product_sale_items.quantity'));

    return view(
        'owner.reports.revenue',
        compact(
            'revenueByDay',
            'totalNetRevenue',
            'fieldBookingRevenue', // Still needed for pie chart
            'fieldBookingNetRevenue',
            'rentalRevenue', // Still needed for pie chart
            'rentalNetRevenue',
            'photographerRevenue', // Still needed for pie chart
            'photographerNetRevenue',
            'productRevenue', // Tambahkan untuk pie chart
            'productSalesNetRevenue', // Tambahkan net revenue produk
            'startDate',
            'endDate',
        ),
    );
}

    /**
     * Field revenue report
     */
    public function fieldRevenueReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to start and end of day
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Get fields with revenue
        $fields = Field::withTrashed()->all(); // Ganti Field::all()

        // Calculate net revenue per field (hanya non-membership)
        foreach ($fields as $field) {
            // Get net revenue for this field after discounts
            $fieldNetRevenue =
                DB::table('field_bookings')
                    ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
                    ->where('field_bookings.field_id', $field->id)
                    ->where('field_bookings.status', 'confirmed')
                    ->where('field_bookings.is_membership', 0)
                    ->whereBetween('field_bookings.created_at', [$startDateTime, $endDateTime])
                    ->select(
                        DB::raw('SUM(field_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
                    (field_bookings.total_price / payments.original_amount)), 0) as net_revenue'),
                    )
                    ->value('net_revenue') ?? 0;

            $field->revenue = $fieldNetRevenue;

            // Get booking count for this field (semua booking, termasuk membership)
            $bookingCount = FieldBooking::where('field_id', $field->id)
                ->where('status', 'confirmed')
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->count();

            $field->booking_count = $bookingCount;

            // Get membership usage count for this field
            $membershipCount = FieldBooking::where('field_id', $field->id)
                ->where('status', 'confirmed')
                ->where('is_membership', 1)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->count();

            $field->membership_count = $membershipCount;
        }

        // Get total field net revenue
        $totalFieldNetRevenue = $fields->sum('revenue');

        // Get bookings by day of week with net revenue
        $bookingsByDayOfWeek = DB::table('field_bookings')
            ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
            ->whereBetween('field_bookings.start_time', [$startDateTime, $endDateTime])
            ->where('field_bookings.status', 'confirmed')
            ->where('field_bookings.is_membership', 0)
            ->select(
                DB::raw('DAYOFWEEK(field_bookings.start_time) as day'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(field_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
                    (field_bookings.total_price / payments.original_amount)), 0) as revenue'),
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Get daily revenue data for chart
        $dailyRevenue = DB::table('field_bookings')
        ->join('fields', 'field_bookings.field_id', '=', 'fields.id')
        ->withTrashed()
            ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
            ->whereBetween('field_bookings.start_time', [$startDateTime, $endDateTime])
            ->where('field_bookings.status', 'confirmed')
            ->where('field_bookings.is_membership', 0)
            ->select(
                DB::raw('DATE(field_bookings.start_time) as date'),
                DB::raw('SUM(field_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
                    (field_bookings.total_price / payments.original_amount)), 0) as revenue'),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Format day of week for better readability
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($bookingsByDayOfWeek as $booking) {
            $booking->day_name = $daysOfWeek[$booking->day - 1];
        }

        return view(
            'owner.reports.field-revenue',
            compact(
                'fields',
                'totalFieldNetRevenue',
                'bookingsByDayOfWeek',
                'startDate',
                'endDate',
                'dailyRevenue', // Menambahkan dailyRevenue ke compact
            ),
        );
    }

    /**
     * Rental revenue report
     */
    public function rentalRevenueReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to start and end of day
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Get rental net revenue by item (hanya non-membership)
        $rentalRevenueByItem = DB::table('rental_bookings')
            ->join('rental_items', 'rental_bookings.rental_item_id', '=', 'rental_items.id')
            ->withTrashed()
            ->leftJoin('payments', 'rental_bookings.payment_id', '=', 'payments.id')
            ->where('rental_bookings.status', 'confirmed')
            ->where('rental_bookings.is_membership', 0)
            ->whereBetween('rental_bookings.created_at', [$startDateTime, $endDateTime])
            ->select(
                'rental_items.id',
                'rental_items.name',
                'rental_items.category',
                DB::raw('SUM(rental_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
                    (rental_bookings.total_price / payments.original_amount)), 0) as revenue'),
                DB::raw('COUNT(rental_bookings.id) as booking_count'),
                DB::raw('SUM(rental_bookings.quantity) as total_quantity'),
            )
            ->groupBy('rental_items.id', 'rental_items.name', 'rental_items.category')
            ->orderBy('revenue', 'desc')
            ->get();

        // Get total rental net revenue
        $totalRentalNetRevenue = $rentalRevenueByItem->sum('revenue');

        // Get total membership usage for rental
        $membershipRentalCount = DB::table('rental_bookings')
            ->where('status', 'confirmed')
            ->where('is_membership', 1)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->count();

        // Get rental net revenue by day
        $rentalRevenueByDay = DB::table('rental_bookings')
            ->leftJoin('payments', 'rental_bookings.payment_id', '=', 'payments.id')
            ->where('rental_bookings.status', 'confirmed')
            ->where('rental_bookings.is_membership', 0)
            ->whereBetween('rental_bookings.created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('DATE(rental_bookings.created_at) as date'),
                DB::raw('SUM(rental_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
                    (rental_bookings.total_price / payments.original_amount)), 0) as revenue'),
                DB::raw('COUNT(*) as booking_count'),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('owner.reports.rental-revenue', compact('rentalRevenueByItem', 'totalRentalNetRevenue', 'membershipRentalCount', 'rentalRevenueByDay', 'startDate', 'endDate'));
    }

    /**
     * Photographer revenue report
     */
    public function photographerRevenueReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to start and end of day
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Get photographer net revenue (hanya non-membership)
        $photographerRevenue = DB::table('photographer_bookings')
            ->join('photographers', 'photographer_bookings.photographer_id', '=', 'photographers.id')
            ->withTrashed()
            ->leftJoin('payments', 'photographer_bookings.payment_id', '=', 'payments.id')
            ->where('photographer_bookings.status', 'confirmed')
            ->where('photographer_bookings.is_membership', 0)
            ->whereBetween('photographer_bookings.created_at', [$startDateTime, $endDateTime])
            ->select(
                'photographers.id',
                'photographers.name',
                'photographers.package_type',
                DB::raw('SUM(photographer_bookings.price) - COALESCE(SUM(payments.discount_amount *
                    (photographer_bookings.price / payments.original_amount)), 0) as revenue'),
                DB::raw('COUNT(photographer_bookings.id) as booking_count'),
            )
            ->groupBy('photographers.id', 'photographers.name', 'photographers.package_type')
            ->orderBy('revenue', 'desc')
            ->get();

        // Get total photographer net revenue
        $totalPhotographerNetRevenue = $photographerRevenue->sum('revenue');

        // Get total membership usage for photographers
        $membershipPhotographerCount = DB::table('photographer_bookings')
            ->where('status', 'confirmed')
            ->where('is_membership', 1)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->count();

        // Get photographer net revenue by day
        $photographerRevenueByDay = DB::table('photographer_bookings')
            ->leftJoin('payments', 'photographer_bookings.payment_id', '=', 'payments.id')
            ->where('photographer_bookings.status', 'confirmed')
            ->where('photographer_bookings.is_membership', 0)
            ->whereBetween('photographer_bookings.created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('DATE(photographer_bookings.created_at) as date'),
                DB::raw('SUM(photographer_bookings.price) - COALESCE(SUM(payments.discount_amount *
                    (photographer_bookings.price / payments.original_amount)), 0) as revenue'),
                DB::raw('COUNT(*) as booking_count'),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('owner.reports.photographer-revenue', compact('photographerRevenue', 'totalPhotographerNetRevenue', 'membershipPhotographerCount', 'photographerRevenueByDay', 'startDate', 'endDate'));
    }

/**
 * Dashboard statistics API for charts
 */
public function dashboardStats()
{
    // Revenue trend for the last 30 days
    $thirtyDaysAgo = Carbon::now()->subDays(30);

    // Field net revenue trend (hanya non-membership)
    $fieldRevenueTrend = DB::table('field_bookings')
        ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
        ->where('field_bookings.status', 'confirmed')
        ->where('field_bookings.is_membership', 0)
        ->where('field_bookings.created_at', '>=', $thirtyDaysAgo)
        ->select(
            DB::raw('DATE(field_bookings.created_at) as date'),
            DB::raw('SUM(field_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
                (field_bookings.total_price / payments.original_amount)), 0) as total'),
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Rental net revenue trend (hanya non-membership)
    $rentalRevenueTrend = DB::table('rental_bookings')
        ->leftJoin('payments', 'rental_bookings.payment_id', '=', 'payments.id')
        ->where('rental_bookings.status', 'confirmed')
        ->where('rental_bookings.is_membership', 0)
        ->where('rental_bookings.created_at', '>=', $thirtyDaysAgo)
        ->select(
            DB::raw('DATE(rental_bookings.created_at) as date'),
            DB::raw('SUM(rental_bookings.total_price) - COALESCE(SUM(payments.discount_amount *
                (rental_bookings.total_price / payments.original_amount)), 0) as total'),
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Photographer net revenue trend (hanya non-membership)
    $photographerRevenueTrend = DB::table('photographer_bookings')
        ->leftJoin('payments', 'photographer_bookings.payment_id', '=', 'payments.id')
        ->where('photographer_bookings.status', 'confirmed')
        ->where('photographer_bookings.is_membership', 0)
        ->where('photographer_bookings.created_at', '>=', $thirtyDaysAgo)
        ->select(
            DB::raw('DATE(photographer_bookings.created_at) as date'),
            DB::raw('SUM(photographer_bookings.price) - COALESCE(SUM(payments.discount_amount *
                (photographer_bookings.price / payments.original_amount)), 0) as total'),
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Product sales net revenue trend
    $productRevenueTrend = DB::table('product_sale_items')
        ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
        ->where(function($query) {
            $query->where('payments.transaction_status', 'settlement')
                  ->orWhere('payments.transaction_status', 'success');
        })
        ->where('payments.created_at', '>=', $thirtyDaysAgo)
        ->select(
            DB::raw('DATE(payments.created_at) as date'),
            DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) - COALESCE(SUM(payments.discount_amount *
                ((product_sale_items.price * product_sale_items.quantity) / payments.original_amount)), 0) as total'),
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Field type popularity (semua booking termasuk membership)
    $fieldPopularity = FieldBooking::join('fields', 'field_bookings.field_id', '=', 'fields.id')
    ->withTrashed() // Tambahkan di sini
    ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
        ->select(
            'fields.type',
            DB::raw('COUNT(*) as bookings'),
            DB::raw('SUM(CASE WHEN field_bookings.is_membership = 0 THEN field_bookings.total_price - COALESCE(payments.discount_amount *
                (field_bookings.total_price / payments.original_amount), 0) ELSE 0 END) as revenue'),
            DB::raw('SUM(CASE WHEN field_bookings.is_membership = 1 THEN 1 ELSE 0 END) as membership_bookings'),
        )
        ->groupBy('fields.type')
        ->orderBy('revenue', 'desc')
        ->get();

    return response()->json([
        'field_revenue_trend' => $fieldRevenueTrend,
        'rental_revenue_trend' => $rentalRevenueTrend,
        'photographer_revenue_trend' => $photographerRevenueTrend,
        'product_revenue_trend' => $productRevenueTrend, // Tambahkan product revenue trend
        'field_popularity' => $fieldPopularity,
    ]);
}
    /**
     * Membership revenue report
     */
    public function membershipRevenueReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to start and end of day
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Get membership revenue data (pendapatan bersih setelah diskon)
        $membershipRevenueByType = DB::table('membership_subscriptions')
            ->join('memberships', 'membership_subscriptions.membership_id', '=', 'memberships.id')
            ->leftJoin('payments', 'membership_subscriptions.payment_id', '=', 'payments.id')
            ->where('membership_subscriptions.status', 'active')
            ->whereBetween('membership_subscriptions.created_at', [$startDateTime, $endDateTime])
            ->select(
                'memberships.id',
                'memberships.name',
                'memberships.type',
                DB::raw('memberships.sessions_per_week * 4 as sessions_per_month'),
                'memberships.session_duration as duration',
                DB::raw('COUNT(membership_subscriptions.id) as purchase_count'),
                DB::raw('SUM(membership_subscriptions.price) - COALESCE(SUM(payments.discount_amount *
                (membership_subscriptions.price / payments.original_amount)), 0) as revenue'),
                DB::raw('SUM(CASE WHEN membership_subscriptions.end_date > NOW() THEN 1 ELSE 0 END) as active_count'),
            )
            ->groupBy('memberships.id', 'memberships.name', 'memberships.type', 'memberships.sessions_per_week', 'memberships.session_duration')
            ->orderBy('revenue', 'desc')
            ->get();

        // Get total membership net revenue
        $totalMembershipNetRevenue = $membershipRevenueByType->sum('revenue');

        // Get total active memberships
        $activeMemberships = DB::table('membership_subscriptions')->where('status', 'active')->where('end_date', '>', Carbon::now())->count();

        // Get membership usage data by category (lapangan, rental, fotografer)
        $membershipUsageByCategory = [
            (object) [
                'category' => 'Lapangan',
                'usage_count' => DB::table('field_bookings')
                    ->where('is_membership', 1)
                    ->where('status', 'confirmed')
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->count(),
            ],
            (object) [
                'category' => 'Rental',
                'usage_count' => DB::table('rental_bookings')
                    ->where('is_membership', 1)
                    ->where('status', 'confirmed')
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->count(),
            ],
            (object) [
                'category' => 'Fotografer',
                'usage_count' => DB::table('photographer_bookings')
                    ->where('is_membership', 1)
                    ->where('status', 'confirmed')
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->count(),
            ],
        ];

        // Get membership revenue by day
        $membershipRevenueByDay = DB::table('membership_subscriptions')
            ->leftJoin('payments', 'membership_subscriptions.payment_id', '=', 'payments.id')
            ->where('membership_subscriptions.status', 'active')
            ->whereBetween('membership_subscriptions.created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('DATE(membership_subscriptions.created_at) as date'),
                DB::raw('SUM(membership_subscriptions.price) - COALESCE(SUM(payments.discount_amount *
                (membership_subscriptions.price / payments.original_amount)), 0) as revenue'),
                DB::raw('COUNT(*) as purchase_count'),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('owner.reports.membership-revenue', compact('membershipRevenueByType', 'totalMembershipNetRevenue', 'activeMemberships', 'membershipUsageByCategory', 'membershipRevenueByDay', 'startDate', 'endDate'));
    }
/**
 * Menampilkan riwayat transaksi POS
 */
public function transactionHistory(Request $request)
{
    // Set default filter tanggal untuk 30 hari terakhir jika tidak ada filter
    $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate = $request->input('end_date', now()->format('Y-m-d'));

    // Tambahkan waktu ke end_date agar mencakup seluruh hari
    $endDateTime = Carbon::parse($endDate)->endOfDay();

    // Filter untuk pencarian
    $search = $request->input('search');
    $paymentType = $request->input('payment_type');

    // Query dasar untuk pembayaran POS
    $query = Payment::with(['customer', 'fieldBookings.field', 'rentalBookings.rentalItem', 'photographerBookings.photographer', 'productItems.product'])
        ->where(function($q) {
            // Cari berdasarkan order_id yang mengandung 'POS-'
            $q->where('order_id', 'like', 'POS-%')
              // Atau payment dibuat oleh admin (user_id yang tersimpan adalah admin)
              ->orWhereHas('user', function($userQuery) {
                  $userQuery->where('role', 'admin');
              });
        })
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->orderBy('created_at', 'desc');

    // Filter berdasarkan metode pembayaran
    if ($paymentType) {
        $query->where('payment_type', $paymentType);
    }

    // Filter berdasarkan pencarian
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('order_id', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($customerQuery) use ($search) {
                  $customerQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('phone_number', 'like', "%{$search}%");
              });
        });
    }

    // Ambil data dengan pagination
    $transactions = $query->paginate(15)->appends($request->all());

    // Hitung total pendapatan berdasarkan filter
    $totalRevenue = $query->sum('amount');

    // Hitung pendapatan per metode pembayaran
    $revenueByPaymentType = Payment::where(function($q) {
            // Cari berdasarkan order_id yang mengandung 'POS-'
            $q->where('order_id', 'like', 'POS-%')
              // Atau payment dibuat oleh admin (user_id yang tersimpan adalah admin)
              ->orWhereHas('user', function($userQuery) {
                  $userQuery->where('role', 'admin');
              });
        })
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->selectRaw('payment_type, sum(amount) as total')
        ->groupBy('payment_type')
        ->get();

    // Hitung jumlah transaksi per hari dalam rentang filter
    $dailyTransactions = Payment::where(function($q) {
            // Cari berdasarkan order_id yang mengandung 'POS-'
            $q->where('order_id', 'like', 'POS-%')
              // Atau payment dibuat oleh admin (user_id yang tersimpan adalah admin)
              ->orWhereHas('user', function($userQuery) {
                  $userQuery->where('role', 'admin');
              });
        })
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->selectRaw('DATE(created_at) as date, count(*) as count, sum(amount) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Format data untuk chart
    $chartData = [
        'dates' => $dailyTransactions->pluck('date')->toArray(),
        'counts' => $dailyTransactions->pluck('count')->toArray(),
        'totals' => $dailyTransactions->pluck('total')->toArray(),
    ];

    return view('owner.reports.transactions', compact(
        'transactions',
        'totalRevenue',
        'revenueByPaymentType',
        'chartData',
        'startDate',
        'endDate',
        'search',
        'paymentType'
    ));
}

/**
 * Product sales revenue report
 */
public function productSalesRevenueReport(Request $request)
{
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

    // Convert to start and end of day
    $startDateTime = Carbon::parse($startDate)->startOfDay();
    $endDateTime = Carbon::parse($endDate)->endOfDay();

    // Get product net revenue by item using Eloquent models
    $productRevenueByItem = ProductSaleItem::with(['product' => function($query) {
            $query->withTrashed(); // Menambahkan withTrashed untuk eager loading
        }])
        ->join('products', 'product_sale_items.product_id', '=', 'products.id')
        ->withTrashed() // Menambahkan withTrashed untuk join
        ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
        ->where(function($query) {
            $query->where('payments.transaction_status', 'settlement')
                  ->orWhere('payments.transaction_status', 'success');
        })
        ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
        ->select(
            'products.id',
            'products.name',
            'products.category',
            DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) - COALESCE(SUM(payments.discount_amount *
                ((product_sale_items.price * product_sale_items.quantity) / payments.original_amount)), 0) as revenue'),
            DB::raw('COUNT(DISTINCT product_sale_items.id) as sale_count'),
            DB::raw('SUM(product_sale_items.quantity) as total_quantity')
        )
        ->groupBy('products.id', 'products.name', 'products.category')
        ->orderBy('revenue', 'desc')
        ->get();

    // Get total product net revenue
    $totalProductNetRevenue = $productRevenueByItem->sum('revenue');

    // Get product net revenue by day
    $productRevenueByDay = ProductSaleItem::leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
        ->where(function($query) {
            $query->where('payments.transaction_status', 'settlement')
                  ->orWhere('payments.transaction_status', 'success');
        })
        ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
        ->select(
            DB::raw('DATE(payments.created_at) as date'),
            DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) - COALESCE(SUM(payments.discount_amount *
                ((product_sale_items.price * product_sale_items.quantity) / payments.original_amount)), 0) as revenue'),
            DB::raw('COUNT(DISTINCT product_sale_items.id) as sale_count')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Get product revenue by category
    $productRevenueByCategory = ProductSaleItem::join('products', 'product_sale_items.product_id', '=', 'products.id')
        ->withTrashed() // Menambahkan withTrashed untuk join
        ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
        ->where(function($query) {
            $query->where('payments.transaction_status', 'settlement')
                  ->orWhere('payments.transaction_status', 'success');
        })
        ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
        ->select(
            'products.category',
            DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) - COALESCE(SUM(payments.discount_amount *
                ((product_sale_items.price * product_sale_items.quantity) / payments.original_amount)), 0) as revenue'),
            DB::raw('SUM(product_sale_items.quantity) as total_quantity'),
            DB::raw('COUNT(DISTINCT product_sale_items.id) as sale_count')
        )
        ->groupBy('products.category')
        ->orderBy('revenue', 'desc')
        ->get();

    // Get top selling products for this period
    $topSellingProducts = ProductSaleItem::with(['product' => function($query) {
            $query->withTrashed(); // Menambahkan withTrashed untuk eager loading
        }])
        ->join('products', 'product_sale_items.product_id', '=', 'products.id')
        ->withTrashed() // Menambahkan withTrashed untuk join
        ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
        ->where(function($query) {
            $query->where('payments.transaction_status', 'settlement')
                  ->orWhere('payments.transaction_status', 'success');
        })
        ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
        ->select(
            'products.id',
            'products.name',
            'products.category',
            'products.price as unit_price',
            DB::raw('SUM(product_sale_items.quantity) as total_quantity'),
            DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) as total_revenue')
        )
        ->groupBy('products.id', 'products.name', 'products.category', 'products.price')
        ->orderBy('total_quantity', 'desc')
        ->take(5)
        ->get();

    return view('owner.reports.product-sales-revenue', compact(
        'productRevenueByItem',
        'totalProductNetRevenue',
        'productRevenueByDay',
        'productRevenueByCategory',
        'topSellingProducts',
        'startDate',
        'endDate'
    ));
}

}
