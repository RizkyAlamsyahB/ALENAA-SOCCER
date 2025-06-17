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
use Illuminate\Support\Facades\Log;
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

    /**
     * Get detailed transaction data for table report - CORRECTED for actual database schema
     */
    public function getTableData(Request $request)
    {
        try {
            $serviceType = $request->get('service_type', 'all');
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
            $perPage = (int) $request->get('per_page', 15);
            $currentPage = (int) $request->get('page', 1);

            Log::info('getTableData called', [
                'service_type' => $serviceType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $perPage,
                'current_page' => $currentPage
            ]);

            $transactions = collect();

            // Field Bookings - using actual database columns
            if ($serviceType === 'all' || $serviceType === 'field') {
                try {
                    $fieldTransactions = DB::table('field_bookings')
                        ->leftJoin('fields', 'field_bookings.field_id', '=', 'fields.id')
                        ->leftJoin('payments', 'field_bookings.payment_id', '=', 'payments.id')
                        ->leftJoin('users', 'field_bookings.user_id', '=', 'users.id')
                        ->where('field_bookings.status', 'confirmed')
                        ->whereBetween(DB::raw('DATE(field_bookings.created_at)'), [$startDate, $endDate])
                        ->select(
                            DB::raw('"Lapangan" as service_type'),
                            'field_bookings.created_at as transaction_date',
                            DB::raw('CONCAT(IFNULL(fields.name, "Lapangan"), " - ", IFNULL(fields.type, "Unknown")) as description'),
                            DB::raw('IFNULL(users.name, "Guest") as customer_name'),
                            'field_bookings.total_price as original_amount',
                            DB::raw('IFNULL(payments.discount_amount, 0) as discount_amount'),
                            DB::raw('field_bookings.total_price - IFNULL(payments.discount_amount, 0) as net_amount'),
                            DB::raw('CASE WHEN field_bookings.is_membership = 1 THEN "Membership" ELSE "Reguler" END as booking_type'),
                            DB::raw('DATE(field_bookings.start_time) as booking_date'),
                            DB::raw('TIME(field_bookings.start_time) as start_time'),
                            DB::raw('TIME(field_bookings.end_time) as end_time')
                        )
                        ->get();

                    Log::info('Field transactions found: ' . $fieldTransactions->count());
                    $transactions = $transactions->merge($fieldTransactions);
                } catch (\Exception $e) {
                    Log::error('Error fetching field transactions: ' . $e->getMessage());
                }
            }

            // Rental Bookings - using actual database columns
            if ($serviceType === 'all' || $serviceType === 'rental') {
                try {
                    $rentalTransactions = DB::table('rental_bookings')
                        ->leftJoin('rental_items', 'rental_bookings.rental_item_id', '=', 'rental_items.id')
                        ->leftJoin('payments', 'rental_bookings.payment_id', '=', 'payments.id')
                        ->leftJoin('users', 'rental_bookings.user_id', '=', 'users.id')
                        ->where('rental_bookings.status', 'confirmed')
                        ->whereBetween(DB::raw('DATE(rental_bookings.created_at)'), [$startDate, $endDate])
                        ->select(
                            DB::raw('"Rental" as service_type'),
                            'rental_bookings.created_at as transaction_date',
                            DB::raw('CONCAT(IFNULL(rental_items.name, "Rental Item"), " (", IFNULL(rental_bookings.quantity, 1), " unit)") as description'),
                            DB::raw('IFNULL(users.name, "Guest") as customer_name'),
                            'rental_bookings.total_price as original_amount',
                            DB::raw('IFNULL(payments.discount_amount, 0) as discount_amount'),
                            DB::raw('rental_bookings.total_price - IFNULL(payments.discount_amount, 0) as net_amount'),
                            DB::raw('CASE WHEN rental_bookings.is_membership = 1 THEN "Membership" ELSE "Reguler" END as booking_type'),
                            DB::raw('DATE(rental_bookings.start_time) as booking_date'),
                            DB::raw('TIME(rental_bookings.start_time) as start_time'),
                            DB::raw('TIME(rental_bookings.end_time) as end_time')
                        )
                        ->get();

                    Log::info('Rental transactions found: ' . $rentalTransactions->count());
                    $transactions = $transactions->merge($rentalTransactions);
                } catch (\Exception $e) {
                    Log::error('Error fetching rental transactions: ' . $e->getMessage());
                }
            }

            // Photographer Bookings - using actual database columns
            if ($serviceType === 'all' || $serviceType === 'photographer') {
                try {
                    $photographerTransactions = DB::table('photographer_bookings')
                        ->leftJoin('photographers', 'photographer_bookings.photographer_id', '=', 'photographers.id')
                        ->leftJoin('payments', 'photographer_bookings.payment_id', '=', 'payments.id')
                        ->leftJoin('users', 'photographer_bookings.user_id', '=', 'users.id')
                        ->where('photographer_bookings.status', 'confirmed')
                        ->whereBetween(DB::raw('DATE(photographer_bookings.created_at)'), [$startDate, $endDate])
                        ->select(
                            DB::raw('"Fotografer" as service_type'),
                            'photographer_bookings.created_at as transaction_date',
                            DB::raw('IFNULL(photographers.name, "Photographer") as description'),
                            DB::raw('IFNULL(users.name, "Guest") as customer_name'),
                            'photographer_bookings.price as original_amount',
                            DB::raw('IFNULL(payments.discount_amount, 0) as discount_amount'),
                            DB::raw('photographer_bookings.price - IFNULL(payments.discount_amount, 0) as net_amount'),
                            DB::raw('CASE WHEN photographer_bookings.is_membership = 1 THEN "Membership" ELSE "Reguler" END as booking_type'),
                            DB::raw('DATE(photographer_bookings.start_time) as booking_date'),
                            DB::raw('TIME(photographer_bookings.start_time) as start_time'),
                            DB::raw('TIME(photographer_bookings.end_time) as end_time')
                        )
                        ->get();

                    Log::info('Photographer transactions found: ' . $photographerTransactions->count());
                    $transactions = $transactions->merge($photographerTransactions);
                } catch (\Exception $e) {
                    Log::error('Error fetching photographer transactions: ' . $e->getMessage());
                }
            }

            // Product Sales - no time columns
            if ($serviceType === 'all' || $serviceType === 'product') {
                try {
                    $productTransactions = DB::table('product_sale_items')
                        ->leftJoin('products', 'product_sale_items.product_id', '=', 'products.id')
                        ->leftJoin('payments', 'product_sale_items.payment_id', '=', 'payments.id')
                        ->leftJoin('users', 'payments.user_id', '=', 'users.id')
                        ->where(function ($query) {
                            $query->where('payments.transaction_status', 'settlement')
                                ->orWhere('payments.transaction_status', 'success');
                        })
                        ->whereBetween(DB::raw('DATE(payments.created_at)'), [$startDate, $endDate])
                        ->select(
                            DB::raw('"Produk" as service_type'),
                            'payments.created_at as transaction_date',
                            DB::raw('CONCAT(IFNULL(products.name, "Product"), " (", IFNULL(product_sale_items.quantity, 1), " pcs)") as description'),
                            DB::raw('IFNULL(users.name, "Guest") as customer_name'),
                            DB::raw('product_sale_items.price * product_sale_items.quantity as original_amount'),
                            DB::raw('IFNULL(payments.discount_amount, 0) as discount_amount'),
                            DB::raw('(product_sale_items.price * product_sale_items.quantity) - IFNULL(payments.discount_amount, 0) as net_amount'),
                            DB::raw('"Reguler" as booking_type'),
                            DB::raw('DATE(payments.created_at) as booking_date'),
                            DB::raw('NULL as start_time'),
                            DB::raw('NULL as end_time')
                        )
                        ->get();

                    Log::info('Product transactions found: ' . $productTransactions->count());
                    $transactions = $transactions->merge($productTransactions);
                } catch (\Exception $e) {
                    Log::error('Error fetching product transactions: ' . $e->getMessage());
                }
            }

            // Membership Subscriptions - using start_date
            if ($serviceType === 'all' || $serviceType === 'membership') {
                try {
                    $membershipTransactions = DB::table('membership_subscriptions')
                        ->leftJoin('memberships', 'membership_subscriptions.membership_id', '=', 'memberships.id')
                        ->leftJoin('payments', 'membership_subscriptions.payment_id', '=', 'payments.id')
                        ->leftJoin('users', 'membership_subscriptions.user_id', '=', 'users.id')
                        ->where('membership_subscriptions.status', 'active')
                        ->whereBetween(DB::raw('DATE(membership_subscriptions.created_at)'), [$startDate, $endDate])
                        ->select(
                            DB::raw('"Membership" as service_type'),
                            'membership_subscriptions.created_at as transaction_date',
                            DB::raw('IFNULL(memberships.name, "Membership") as description'),
                            DB::raw('IFNULL(users.name, "Guest") as customer_name'),
                            'membership_subscriptions.price as original_amount',
                            DB::raw('IFNULL(payments.discount_amount, 0) as discount_amount'),
                            DB::raw('membership_subscriptions.price - IFNULL(payments.discount_amount, 0) as net_amount'),
                            DB::raw('"Membership" as booking_type'),
                            DB::raw('DATE(membership_subscriptions.start_date) as booking_date'),
                            DB::raw('NULL as start_time'),
                            DB::raw('NULL as end_time')
                        )
                        ->get();

                    Log::info('Membership transactions found: ' . $membershipTransactions->count());
                    $transactions = $transactions->merge($membershipTransactions);
                } catch (\Exception $e) {
                    Log::error('Error fetching membership transactions: ' . $e->getMessage());
                }
            }

            Log::info('Total transactions before sort: ' . $transactions->count());

            // Sort by transaction date (newest first)
            $transactions = $transactions->sortByDesc('transaction_date');

            // Calculate totals
            $totalOriginalAmount = $transactions->sum('original_amount');
            $totalDiscountAmount = $transactions->sum('discount_amount');
            $totalNetAmount = $transactions->sum('net_amount');

            // Manual pagination
            $total = $transactions->count();
            $offset = ($currentPage - 1) * $perPage;
            $paginatedTransactions = $transactions->slice($offset, $perPage)->values();

            Log::info('Final result', [
                'total_transactions' => $total,
                'paginated_count' => $paginatedTransactions->count(),
                'total_net_amount' => $totalNetAmount
            ]);

            return response()->json([
                'data' => $paginatedTransactions,
                'pagination' => [
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / max($perPage, 1)),
                    'from' => $total > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $total)
                ],
                'summary' => [
                    'total_transactions' => $total,
                    'total_original_amount' => $totalOriginalAmount,
                    'total_discount_amount' => $totalDiscountAmount,
                    'total_net_amount' => $totalNetAmount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getTableData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data',
                'message' => $e->getMessage(),
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 15,
                    'total' => 0,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0
                ],
                'summary' => [
                    'total_transactions' => 0,
                    'total_original_amount' => 0,
                    'total_discount_amount' => 0,
                    'total_net_amount' => 0
                ]
            ], 500);
        }
    }

    /**
     * Export transaction data to CSV
     */
    public function exportToExcel(Request $request)
    {
        try {
            $serviceType = $request->get('service_type', 'all');
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            // Use the same logic as getTableData but without pagination
            $request->merge(['per_page' => 10000]); // Get all data
            $result = $this->getTableData($request);
            $data = $result->getData();

            if ($data->error ?? false) {
                return redirect()->back()->with('error', 'Error exporting data: ' . $data->message);
            }

            $transactions = collect($data->data);

            $filename = 'laporan_transaksi_' . $serviceType . '_' . $startDate . '_to_' . $endDate . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($transactions) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Headers
                fputcsv($file, [
                    'Tanggal Transaksi',
                    'Jenis Layanan',
                    'Deskripsi',
                    'Customer',
                    'Tipe Booking',
                    'Harga Asli',
                    'Diskon',
                    'Harga Bersih',
                    'Tanggal Booking',
                    'Waktu Mulai',
                    'Waktu Selesai'
                ]);

                foreach ($transactions as $transaction) {
                    fputcsv($file, [
                        Carbon::parse($transaction->transaction_date)->format('d/m/Y H:i'),
                        $transaction->service_type,
                        $transaction->description,
                        $transaction->customer_name ?? '-',
                        $transaction->booking_type,
                        number_format($transaction->original_amount, 0, ',', '.'),
                        number_format($transaction->discount_amount, 0, ',', '.'),
                        number_format($transaction->net_amount, 0, ',', '.'),
                        $transaction->booking_date ? Carbon::parse($transaction->booking_date)->format('d/m/Y') : '-',
                        $transaction->start_time ?? '-',
                        $transaction->end_time ?? '-'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error in exportToExcel: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
}
