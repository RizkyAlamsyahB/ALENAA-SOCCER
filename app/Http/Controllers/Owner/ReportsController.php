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
    public function index()
    {
        // Pendapatan lapangan bersih (hanya non-membership)
        $fieldNetRevenue =
            DB::table('field_bookings')
                ->leftJoin('fields', 'field_bookings.field_id', '=', 'fields.id')
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
                ->leftJoin('rental_items', 'rental_bookings.rental_item_id', '=', 'rental_items.id')
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
                ->leftJoin('photographers', 'photographer_bookings.photographer_id', '=', 'photographers.id')
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
                ->leftJoin('products', 'product_sale_items.product_id', '=', 'products.id')
                ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
                ->where(function ($query) {
                    $query->where('payments.transaction_status', 'settlement')->orWhere('payments.transaction_status', 'success');
                })
                ->select(
                    DB::raw('SUM(product_sale_items.price * product_sale_items.quantity) - COALESCE(SUM(payments.discount_amount *
        ((product_sale_items.price * product_sale_items.quantity) / payments.original_amount)), 0) as net_revenue'),
                )
                ->value('net_revenue') ?? 0;

        // Pendapatan membership bersih dari subscription
        $membershipNetRevenue =
            DB::table('membership_subscriptions')
                ->leftJoin('memberships', 'membership_subscriptions.membership_id', '=', 'memberships.id')
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
                ->where(function ($query) {
                    $query->where('transaction_status', 'settlement')->orWhere('transaction_status', 'success');
                })
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->selectRaw('SUM(amount - COALESCE(discount_amount, 0)) as net_revenue')
                ->value('net_revenue') ?? 0;

        // Get membership stats
        $activeMemberships = DB::table('membership_subscriptions')->where('status', 'active')->where('end_date', '>', Carbon::now())->count();

        $membershipUsageCount = DB::table('field_bookings')->where('is_membership', 1)->where('status', 'confirmed')->count() + DB::table('rental_bookings')->where('is_membership', 1)->where('status', 'confirmed')->count() + DB::table('photographer_bookings')->where('is_membership', 1)->where('status', 'confirmed')->count();

        return view('owner.reports.index', compact('totalNetRevenue', 'fieldCount', 'monthlyNetRevenue', 'fieldNetRevenue', 'rentalNetRevenue', 'photographerNetRevenue', 'membershipNetRevenue', 'productSalesNetRevenue', 'activeMemberships', 'membershipUsageCount'));
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
            ->leftJoin('fields', 'field_bookings.field_id', '=', 'fields.id')
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
            ->leftJoin('rental_items', 'rental_bookings.rental_item_id', '=', 'rental_items.id')
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
            ->leftJoin('photographers', 'photographer_bookings.photographer_id', '=', 'photographers.id')
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
            ->leftJoin('products', 'product_sale_items.product_id', '=', 'products.id')
            ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
            ->where(function ($query) {
                $query->where('payments.transaction_status', 'settlement')->orWhere('payments.transaction_status', 'success');
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

        // Field type popularity menggunakan Eloquent untuk akses withTrashed
        $fieldPopularity = Field::withTrashed()
            ->join('field_bookings', 'fields.id', '=', 'field_bookings.field_id')
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
            'product_revenue_trend' => $productRevenueTrend,
            'field_popularity' => $fieldPopularity,
        ]);
    }
}
