<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PointVoucher;
use App\Models\PointRedemption;
use App\Models\PointsTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PointController extends Controller
{
    /**
     * Menampilkan halaman daftar voucher yang tersedia untuk ditukarkan
     */
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil daftar voucher yang tersedia dan aktif
        $vouchers = PointVoucher::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function($query) {
                $query->whereNull('usage_limit')
                    ->orWhere('usage_limit', '>', 0);
            })
            ->orderBy('points_required', 'asc')
            ->get();

        // Ambil voucher yang sudah ditukarkan oleh user dan masih aktif
        $activeRedemptions = PointRedemption::with('pointVoucher')
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'used'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Tampilkan view dengan data yang diperlukan
        return view('users.points.index', compact('user', 'vouchers', 'activeRedemptions'));
    }

    /**
     * Menampilkan halaman detail voucher
     */
    public function showVoucher($id)
    {
        $user = Auth::user();
        $voucher = PointVoucher::findOrFail($id);

        return view('users.points.voucher_detail', compact('user', 'voucher'));
    }

    /**
     * Proses penukaran poin dengan voucher
     */
    public function redeemVoucher(Request $request, $id)
    {
        $user = Auth::user();
        $voucher = PointVoucher::findOrFail($id);

        // Validasi apakah user memiliki cukup poin
        if ($user->points < $voucher->points_required) {
            return redirect()->back()->with('error', 'Poin Anda tidak mencukupi untuk menukarkan voucher ini.');
        }

        // Validasi apakah voucher masih aktif
        if (!$voucher->is_active) {
            return redirect()->back()->with('error', 'Voucher ini sudah tidak aktif.');
        }

        // Validasi apakah voucher masih dalam periode aktif
        if ($voucher->start_date && Carbon::parse($voucher->start_date)->isFuture()) {
            return redirect()->back()->with('error', 'Voucher ini belum tersedia.');
        }

        if ($voucher->end_date && Carbon::parse($voucher->end_date)->isPast()) {
            return redirect()->back()->with('error', 'Voucher ini sudah kadaluwarsa.');
        }

        // Validasi apakah voucher masih tersedia (usage_limit)
        if ($voucher->usage_limit !== null) {
            $usageCount = PointRedemption::where('point_voucher_id', $voucher->id)->count();
            if ($usageCount >= $voucher->usage_limit) {
                return redirect()->back()->with('error', 'Voucher ini sudah mencapai batas penggunaan.');
            }
        }

        DB::beginTransaction();
        try {
            // Buat kode diskon unik
            $discountCode = strtoupper(Str::random(8));

            // Kurangi poin user
            $user->points -= $voucher->points_required;
            $user->save();

            // Buat catatan transaksi poin
            PointsTransaction::create([
                'user_id' => $user->id,
                'type' => 'redeem',
                'amount' => -$voucher->points_required,
                'description' => 'Penukaran poin untuk ' . $voucher->name,
                'reference_type' => 'App\\Models\\PointVoucher',
                'reference_id' => $voucher->id,
                'metadata' => json_encode([
                    'voucher_name' => $voucher->name,
                    'discount_code' => $discountCode
                ])
            ]);

            // Buat catatan penukaran voucher
            $redemption = PointRedemption::create([
                'user_id' => $user->id,
                'point_voucher_id' => $voucher->id,
                'points_used' => $voucher->points_required,
                'discount_code' => $discountCode,
                'status' => 'active',
                'expires_at' => $voucher->end_date ?? Carbon::now()->addDays(30),
            ]);

            DB::commit();

            return redirect()->route('user.points.index')->with('success', 'Selamat! Voucher berhasil ditukarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat penukaran poin
     */
    public function history()
    {
        $user = Auth::user();

        // Ambil riwayat penukaran poin oleh user
        $redemptions = PointRedemption::with('pointVoucher')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Ambil riwayat transaksi poin
        $transactions = PointsTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.points.history', compact('user', 'redemptions', 'transactions'));
    }

    /**
     * Menampilkan detail penukaran poin
     */
    public function showRedemption($id)
    {
        $user = Auth::user();
        $redemption = PointRedemption::with('pointVoucher')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return view('users.points.redemption_detail', compact('user', 'redemption'));
    }
}
