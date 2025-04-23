<?php

namespace App\Http\Controllers\Owner;

use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\PhotographerBooking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Field;
use App\Models\OpenMabar;
use App\Models\MembershipSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ReportsController extends Controller
{
    /**
     * Display the main reports dashboard
     */
    public function index()
    {
        // Get summary statistics
        $totalRevenue = Payment::where('transaction_status', 'settlement')->sum('amount');
        $userCount = User::where('role', 'user')->count();
        $bookingCount = FieldBooking::count();
        $fieldCount = Field::count();

        // Today's bookings
        $todayBookings = FieldBooking::whereDate('start_time', Carbon::today())->count();

        // This month's revenue
        $monthlyRevenue = Payment::where('transaction_status', 'settlement')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Active memberships
        $activeMemberships = MembershipSubscription::where('status', 'active')->count();

        return view('owner.reports.index', compact(
            'totalRevenue',
            'userCount',
            'bookingCount',
            'fieldCount',
            'todayBookings',
            'monthlyRevenue',
            'activeMemberships'
        ));
    }

    /**
     * Revenue reports
     */
    public function revenueReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Validate date range
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Convert to start and end of day
        $startDateTime = $start->startOfDay();
        $endDateTime = $end->endOfDay();

        // Get revenue by payment type
        $revenueByType = Payment::where('transaction_status', 'settlement')
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select('payment_type', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_type')
            ->get();

        // Get revenue by day
        $revenueByDay = Payment::where('transaction_status', 'settlement')
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate totals
        $totalRevenue = $revenueByType->sum('total');
        $avgDailyRevenue = $revenueByDay->avg('total');

        // Get revenue by service type (derived from examining relationships)
        $fieldBookingRevenue = DB::table('payments')
            ->join('field_bookings', 'payments.id', '=', 'field_bookings.payment_id')
            ->where('payments.transaction_status', 'settlement')
            ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
            ->sum('payments.amount');

        $rentalRevenue = DB::table('payments')
            ->join('rental_bookings', 'payments.id', '=', 'rental_bookings.payment_id')
            ->where('payments.transaction_status', 'settlement')
            ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
            ->sum('payments.amount');

        $photographerRevenue = DB::table('payments')
            ->join('photographer_bookings', 'payments.id', '=', 'photographer_bookings.payment_id')
            ->where('payments.transaction_status', 'settlement')
            ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
            ->sum('payments.amount');

        $membershipRevenue = DB::table('payments')
            ->join('membership_subscriptions', 'payments.id', '=', 'membership_subscriptions.payment_id')
            ->where('payments.transaction_status', 'settlement')
            ->whereBetween('payments.created_at', [$startDateTime, $endDateTime])
            ->sum('payments.amount');

        return view('owner.reports.revenue', compact(
            'revenueByType',
            'revenueByDay',
            'totalRevenue',
            'avgDailyRevenue',
            'fieldBookingRevenue',
            'rentalRevenue',
            'photographerRevenue',
            'membershipRevenue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Field utilization report
     */
    public function fieldUtilizationReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to start and end of day
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Get fields with booking counts
        $fields = Field::withCount([
            'bookings' => function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('start_time', [$startDateTime, $endDateTime]);
            }
        ])->get();

        // Calculate total hours booked per field
        foreach ($fields as $field) {
            $totalHours = FieldBooking::where('field_id', $field->id)
                ->whereBetween('start_time', [$startDateTime, $endDateTime])
                ->selectRaw('SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours')
                ->first();

            $field->total_hours = $totalHours->total_hours ?? 0;

            // Calculate revenue per field
            $fieldRevenue = DB::table('field_bookings')
                ->join('payments', 'field_bookings.payment_id', '=', 'payments.id')
                ->where('field_bookings.field_id', $field->id)
                ->where('payments.transaction_status', 'settlement')
                ->whereBetween('field_bookings.start_time', [$startDateTime, $endDateTime])
                ->sum('payments.amount');

            $field->revenue = $fieldRevenue;
        }

        // Get bookings by hour of day
        $bookingsByHour = FieldBooking::whereBetween('start_time', [$startDateTime, $endDateTime])
            ->select(DB::raw('HOUR(start_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Get bookings by day of week
        $bookingsByDayOfWeek = FieldBooking::whereBetween('start_time', [$startDateTime, $endDateTime])
            ->select(DB::raw('DAYOFWEEK(start_time) as day'), DB::raw('COUNT(*) as count'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Format day of week for better readability
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($bookingsByDayOfWeek as $booking) {
            $booking->day_name = $daysOfWeek[$booking->day - 1];
        }

        return view('owner.reports.field-utilization', compact(
            'fields',
            'bookingsByHour',
            'bookingsByDayOfWeek',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Membership statistics report
     */
    public function membershipReport(Request $request)
    {
        // Active memberships by type
        $membershipsByType = MembershipSubscription::where('status', 'active')
            ->join('memberships', 'membership_subscriptions.membership_id', '=', 'memberships.id')
            ->select('memberships.type', DB::raw('count(*) as count'))
            ->groupBy('memberships.type')
            ->get();

        // New memberships by month
        $newMembershipsByMonth = MembershipSubscription::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Format month names
        foreach ($newMembershipsByMonth as $item) {
            $date = Carbon::createFromDate($item->year, $item->month, 1);
            $item->month_name = $date->format('F Y');
        }

        // Membership retention rate
        $totalSubscriptions = MembershipSubscription::count();
        $expiredSubscriptions = MembershipSubscription::where('status', 'expired')->count();
        $renewedSubscriptions = MembershipSubscription::where('renewal_status', 'renewed')->count();

        $retentionRate = $totalSubscriptions > 0
            ? round(($renewedSubscriptions / ($expiredSubscriptions + $renewedSubscriptions)) * 100, 2)
            : 0;

        // Membership usage statistics
        $sessionUtilization = DB::table('membership_sessions')
            ->join('membership_subscriptions', 'membership_sessions.membership_subscription_id', '=', 'membership_subscriptions.id')
            ->select(
                'membership_subscriptions.id',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(CASE WHEN membership_sessions.status = "completed" THEN 1 ELSE 0 END) as completed_sessions')
            )
            ->groupBy('membership_subscriptions.id')
            ->get();

        $avgUtilizationRate = $sessionUtilization->avg(function($item) {
            return $item->total_sessions > 0
                ? ($item->completed_sessions / $item->total_sessions) * 100
                : 0;
        });

        return view('owner.reports.membership', compact(
            'membershipsByType',
            'newMembershipsByMonth',
            'retentionRate',
            'avgUtilizationRate'
        ));
    }

    /**
     * Open Mabar (pickup games) statistics
     */
    public function mabarReport(Request $request)
    {
        // Total Mabars and popularity
        $totalMabars = OpenMabar::count();
        $avgParticipants = DB::table('mabar_participants')
            ->select('open_mabar_id', DB::raw('COUNT(*) as participant_count'))
            ->groupBy('open_mabar_id')
            ->avg('participant_count');

        // Mabars by completion status
        $mabarsByStatus = OpenMabar::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Popular skill levels
        $popularLevels = OpenMabar::select('level', DB::raw('COUNT(*) as count'))
            ->groupBy('level')
            ->orderBy('count', 'desc')
            ->get();

        // Create Mabar organizer leaderboard
        $topOrganizers = OpenMabar::select('user_id', DB::raw('COUNT(*) as mabars_organized'))
            ->groupBy('user_id')
            ->orderBy('mabars_organized', 'desc')
            ->limit(10)
            ->with('user:id,name')
            ->get();

        // Most active participants
        $activeParticipants = DB::table('mabar_participants')
            ->select('user_id', DB::raw('COUNT(DISTINCT open_mabar_id) as mabars_joined'))
            ->groupBy('user_id')
            ->orderBy('mabars_joined', 'desc')
            ->limit(10)
            ->get();

        // Fetch user details for active participants
        $userIds = $activeParticipants->pluck('user_id');
        $users = User::whereIn('id', $userIds)->get(['id', 'name'])->keyBy('id');

        foreach ($activeParticipants as $participant) {
            $participant->user_name = $users[$participant->user_id]->name ?? 'Unknown User';
        }

        // Revenue generated from Mabars
        $mabarRevenue = DB::table('mabar_participants')
            ->where('payment_status', 'paid')
            ->sum('amount_paid');

        return view('owner.reports.mabar', compact(
            'totalMabars',
            'avgParticipants',
            'mabarsByStatus',
            'popularLevels',
            'topOrganizers',
            'activeParticipants',
            'mabarRevenue'
        ));
    }

    /**
     * User activity and engagement report
     */
    public function userReport(Request $request)
    {
        // User registration over time
        $usersByMonth = User::where('role', 'user')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Format month names
        foreach ($usersByMonth as $item) {
            $date = Carbon::createFromDate($item->year, $item->month, 1);
            $item->month_name = $date->format('F Y');
        }

        // Top users by bookings
        $topUsersByBookings = User::withCount('fieldBookings')
            ->orderBy('field_bookings_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'field_bookings_count']);

        // Top users by spending
        $topUsersBySpending = Payment::where('transaction_status', 'settlement')
            ->select('user_id', DB::raw('SUM(amount) as total_spent'))
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Fetch user details for top spenders
        $userIds = $topUsersBySpending->pluck('user_id');
        $users = User::whereIn('id', $userIds)->get(['id', 'name'])->keyBy('id');

        foreach ($topUsersBySpending as $spender) {
            $spender->user_name = $users[$spender->user_id]->name ?? 'Unknown User';
        }

        // User points utilization
        $totalPointsIssued = DB::table('points_transactions')
            ->where('amount', '>', 0)
            ->sum('amount');

        $totalPointsRedeemed = DB::table('points_transactions')
            ->where('amount', '<', 0)
            ->sum(DB::raw('ABS(amount)'));

        $pointsUtilizationRate = $totalPointsIssued > 0
            ? round(($totalPointsRedeemed / $totalPointsIssued) * 100, 2)
            : 0;

        // Average bookings per user
        $totalUsers = User::where('role', 'user')->count();
        $totalBookings = FieldBooking::count();
        $avgBookingsPerUser = $totalUsers > 0 ? round($totalBookings / $totalUsers, 2) : 0;

        return view('owner.reports.users', compact(
            'usersByMonth',
            'topUsersByBookings',
            'topUsersBySpending',
            'pointsUtilizationRate',
            'avgBookingsPerUser',
            'totalUsers'
        ));
    }

    /**
     * Generate excel export of data
     */
    public function exportReport(Request $request)
    {
        $reportType = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Convert to start and end of day
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        switch ($reportType) {
            case 'revenue':
                return $this->exportRevenueReport($startDateTime, $endDateTime);

            case 'bookings':
                return $this->exportBookingsReport($startDateTime, $endDateTime);

            case 'memberships':
                return $this->exportMembershipsReport();

            case 'users':
                return $this->exportUsersReport();

            default:
                return redirect()->back()->with('error', 'Report type not recognized');
        }
    }

    /**
     * Export revenue data to Excel
     */
    private function exportRevenueReport($startDate, $endDate)
    {
        // Implementation would use a package like Laravel Excel
        // This is a placeholder for the actual export logic
        $fileName = 'revenue_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        // Example implementation using Laravel Excel would go here

        return redirect()->back()->with('success', 'Report exported successfully!');
    }

    /**
     * Export bookings data to Excel
     */
    private function exportBookingsReport($startDate, $endDate)
    {
        $fileName = 'bookings_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        // Example implementation using Laravel Excel would go here

        return redirect()->back()->with('success', 'Report exported successfully!');
    }

    /**
     * Export memberships data to Excel
     */
    private function exportMembershipsReport()
    {
        $fileName = 'memberships_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        // Example implementation using Laravel Excel would go here

        return redirect()->back()->with('success', 'Report exported successfully!');
    }

    /**
     * Export users data to Excel
     */
    private function exportUsersReport()
    {
        $fileName = 'users_report_' . Carbon::now()->format('Ymd') . '.xlsx';

        // Example implementation using Laravel Excel would go here

        return redirect()->back()->with('success', 'Report exported successfully!');
    }

    /**
     * Dashboard statistics API for charts
     */
    public function dashboardStats()
    {
        // Revenue trend for the last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $revenueTrend = Payment::where('transaction_status', 'settlement')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Bookings trend for the last 30 days
        $bookingsTrend = FieldBooking::where('created_at', '>=', $thirtyDaysAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate peak hours
        $peakHours = FieldBooking::select(
                DB::raw('HOUR(start_time) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Field type popularity
        $fieldPopularity = FieldBooking::join('fields', 'field_bookings.field_id', '=', 'fields.id')
            ->select('fields.type', DB::raw('COUNT(*) as bookings'))
            ->groupBy('fields.type')
            ->orderBy('bookings', 'desc')
            ->get();

        return response()->json([
            'revenue_trend' => $revenueTrend,
            'bookings_trend' => $bookingsTrend,
            'peak_hours' => $peakHours,
            'field_popularity' => $fieldPopularity
        ]);
    }
}
