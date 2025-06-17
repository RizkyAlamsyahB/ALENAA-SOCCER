<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PhotographerBooking;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhotographerTasksController extends Controller
{
    /**
     * Display photographer tasks overview
     */
    public function index(Request $request)
    {
        $query = PhotographerBooking::with(['photographer.user', 'user', 'payment'])
            ->where('status', 'confirmed'); // Hanya yang sudah confirmed

        // Tambahkan filter tanggal yang lebih fleksibel
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            // Jika tidak ada filter tanggal, tampilkan semua tugas yang belum lewat 30 hari
            $query->whereDate('start_time', '>=', Carbon::today()->subDays(30));
        }

        // Filter berdasarkan completion status
        if ($request->filled('completion_status')) {
            $query->where('completion_status', $request->completion_status);
        }

        // Filter berdasarkan fotografer
        if ($request->filled('photographer_id')) {
            $query->where('photographer_id', $request->photographer_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        // Filter berdasarkan status urgent (booking hari ini tapi belum selesai)
        if ($request->filled('urgent_only') && $request->urgent_only == '1') {
            $query->whereDate('start_time', '<=', Carbon::today())
                  ->whereIn('completion_status', ['confirmed', 'shooting_completed']);
        }

        $photographerTasks = $query->orderBy('start_time', 'desc')->paginate(20);

        // Get photographers untuk dropdown filter
        $photographers = Photographer::with('user')->get();

        // Get statistics untuk dashboard cards
        $stats = $this->getTasksStatistics($request);

        return view('owner.photographer-tasks.index', compact(
            'photographerTasks',
            'photographers',
            'stats'
        ));
    }

    /**
     * Get tasks statistics for dashboard - DIPERBAIKI SESUAI SKEMA
     */
    private function getTasksStatistics($request = null)
    {
        // Base query untuk semua tugas yang confirmed (booking yang sudah dikonfirmasi)
        $baseQuery = PhotographerBooking::where('status', 'confirmed');

        // Jika ada filter tanggal dari request, gunakan filter yang sama
        if ($request) {
            if ($request->filled('date_from')) {
                $baseQuery->whereDate('start_time', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $baseQuery->whereDate('start_time', '<=', $request->date_to);
            }

            // Jika tidak ada filter tanggal, gunakan range default
            if (!$request->filled('date_from') && !$request->filled('date_to')) {
                $baseQuery->whereDate('start_time', '>=', Carbon::today()->subDays(30));
            }
        } else {
            // Default: tampilkan tugas 30 hari terakhir sampai 30 hari ke depan
            $baseQuery->whereDate('start_time', '>=', Carbon::today()->subDays(30))
                     ->whereDate('start_time', '<=', Carbon::today()->addDays(30));
        }

        // PERBAIKAN: Hitung statistik berdasarkan skema yang benar
        $totalTasks = (clone $baseQuery)->count();

        // Siap Shooting: status=confirmed DAN completion_status masih pending/confirmed (belum mulai shooting)
        $readyToShoot = (clone $baseQuery)->whereIn('completion_status', ['pending', 'confirmed'])->count();

        // Editing Foto: completion_status = shooting_completed (sudah selesai shooting, sedang edit)
        $shootingCompleted = (clone $baseQuery)->where('completion_status', 'shooting_completed')->count();

        // Selesai: completion_status = delivered (foto sudah dikirim)
        $delivered = (clone $baseQuery)->where('completion_status', 'delivered')->count();

        // Urgent: tugas yang tanggalnya hari ini atau sudah lewat tapi belum selesai
        $urgentTasks = (clone $baseQuery)
            ->whereDate('start_time', '<=', Carbon::today())
            ->whereIn('completion_status', ['pending', 'confirmed', 'shooting_completed'])
            ->count();

        // Terlambat: tugas yang sudah shooting_completed lebih dari 3 hari tapi belum delivered
        $overdueTasks = (clone $baseQuery)
            ->where('completion_status', 'shooting_completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '<=', Carbon::now()->subDays(3))
            ->count();

        return [
            'total_tasks' => $totalTasks,
            'ready_to_shoot' => $readyToShoot,
            'shooting_completed' => $shootingCompleted,
            'delivered' => $delivered,
            'urgent_tasks' => $urgentTasks,
            'overdue_delivery' => $overdueTasks
        ];
    }

    // COMMENT OUT OTHER METHODS FOR NOW
    /*
    public function show($taskId)
    {
        $task = PhotographerBooking::with(['photographer.user', 'user', 'payment'])
            ->findOrFail($taskId);

        return view('owner.photographer-tasks.show', compact('task'));
    }

    public function photographerPerformance(Request $request)
    {
        // Method commented out for now
    }

    public function getTasksData(Request $request)
    {
        // Method commented out for now
    }

    private function getStatusLabel($status)
    {
        // Method commented out for now
    }

    public function sendReminder(Request $request, $taskId)
    {
        // Method commented out for now
    }
    */
}
