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


/**
 * Mendapatkan voucher yang tersedia untuk ditukar dan yang sudah dimiliki user di modal cart
 */
public function getAvailableVouchersForCart()
{
    try {
        $user = Auth::user();

        // 1. Ambil voucher yang sudah ditukar user dan masih aktif
        $redeemedVouchers = PointRedemption::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('pointVoucher')
            ->get()
            ->map(function ($redemption) {
                $voucher = $redemption->pointVoucher;
                return [
                    'id' => $voucher->id,
                    'name' => $voucher->name,
                    'description' => $voucher->description,
                    'discount_type' => $voucher->discount_type,
                    'discount_value' => $voucher->discount_value,
                    'min_order' => $voucher->min_order,
                    'max_discount' => $voucher->max_discount,
                    'end_date' => $voucher->end_date,
                    'formatted_discount' => $voucher->discount_type === 'percentage'
                        ? $voucher->discount_value . '% OFF'
                        : 'Rp ' . number_format($voucher->discount_value) . ' OFF',
                    'formatted_min_order' => $voucher->min_order > 0
                        ? 'Min. order Rp ' . number_format($voucher->min_order)
                        : null,
                    'formatted_max_discount' => $voucher->max_discount
                        ? 'Maks. diskon Rp ' . number_format($voucher->max_discount)
                        : null,
                    'formatted_end_date' => $voucher->end_date
                        ? \Carbon\Carbon::parse($voucher->end_date)->format('d M Y')
                        : null,
                    'is_owned' => true,
                    'discount_code' => $redemption->discount_code,
                    'redemption_id' => $redemption->id,
                    'status' => 'owned'
                ];
            });

        // 2. Ambil voucher yang bisa dibeli (belum ditukar dan poin cukup)
        $availableVoucherIds = $redeemedVouchers->pluck('id')->toArray();

        $availableVouchers = PointVoucher::where('is_active', true)
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
            ->whereNotIn('id', $availableVoucherIds) // Exclude vouchers user already owns
            ->orderBy('points_required', 'asc')
            ->get()
            ->map(function ($voucher) use ($user) {
                return [
                    'id' => $voucher->id,
                    'name' => $voucher->name,
                    'description' => $voucher->description,
                    'points_required' => $voucher->points_required,
                    'discount_type' => $voucher->discount_type,
                    'discount_value' => $voucher->discount_value,
                    'min_order' => $voucher->min_order,
                    'max_discount' => $voucher->max_discount,
                    'end_date' => $voucher->end_date,
                    'formatted_points' => number_format($voucher->points_required),
                    'formatted_discount' => $voucher->discount_type === 'percentage'
                        ? $voucher->discount_value . '% OFF'
                        : 'Rp ' . number_format($voucher->discount_value) . ' OFF',
                    'formatted_min_order' => $voucher->min_order > 0
                        ? 'Min. order Rp ' . number_format($voucher->min_order)
                        : null,
                    'formatted_max_discount' => $voucher->max_discount
                        ? 'Maks. diskon Rp ' . number_format($voucher->max_discount)
                        : null,
                    'can_afford' => $user->points >= $voucher->points_required,
                    'formatted_end_date' => $voucher->end_date
                        ? \Carbon\Carbon::parse($voucher->end_date)->format('d M Y')
                        : null,
                    'is_owned' => false,
                    'status' => $user->points >= $voucher->points_required ? 'can_buy' : 'insufficient_points'
                ];
            });

        // 3. Gabungkan: voucher yang dimiliki di atas, voucher yang bisa dibeli di bawah
        $allVouchers = $redeemedVouchers->concat($availableVouchers);

        return response()->json([
            'success' => true,
            'user_points' => $user->points,
            'formatted_user_points' => number_format($user->points),
            'vouchers' => $allVouchers,
            'owned_count' => $redeemedVouchers->count(),
            'available_count' => $availableVouchers->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data voucher: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * TAMBAH method baru untuk apply voucher yang sudah dimiliki
 */
public function applyOwnedVoucherToCart($redemptionId)
{
    try {
        $user = Auth::user();

        // Ambil redemption yang sudah ada
        $redemption = PointRedemption::where('id', $redemptionId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('pointVoucher')
            ->first();

        if (!$redemption) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak ditemukan atau sudah tidak valid.'
            ], 404);
        }

        $voucher = $redemption->pointVoucher;

        // Ambil cart user dan hitung subtotal untuk validasi
        $cart = \App\Models\Cart::where('user_id', $user->id)->first();
        $subtotal = 0;

        if ($cart) {
            $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)->get();
            $subtotal = $cartItems->sum('price');
        }

        // Validasi minimum order
        if ($subtotal < $voucher->min_order) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum pembelian Rp ' . number_format($voucher->min_order, 0, ',', '.') . ' untuk menggunakan voucher ini.'
            ], 400);
        }

        // Hitung diskon
        $discountAmount = $voucher->calculateDiscount($subtotal);

        // LANGSUNG APPLY KE CART SESSION (hapus diskon lama jika ada)
        session()->forget(['cart_discount', 'cart_point_voucher']);

        session()->put('cart_point_voucher', [
            'id' => $voucher->id,
            'code' => $redemption->discount_code,
            'name' => $voucher->name,
            'amount' => $discountAmount,
            'subtotal' => $subtotal,
            'total' => $subtotal - $discountAmount,
            'point_redemption_id' => $redemption->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan ke keranjang!',
            'voucher' => [
                'name' => $voucher->name,
                'code' => $redemption->discount_code,
                'discount_amount' => $discountAmount,
                'formatted_discount_amount' => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
                'new_total' => $subtotal - $discountAmount,
                'formatted_new_total' => 'Rp ' . number_format($subtotal - $discountAmount, 0, ',', '.')
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * TAMBAHKAN method ini di PointController.php
 * Tukar voucher dari modal cart dan langsung apply ke cart
 */
public function redeemVoucherFromCart($id)
{
    try {
        $user = Auth::user();
        $voucher = PointVoucher::findOrFail($id);

        // Validasi yang sama seperti redeemVoucher method
        if ($user->points < $voucher->points_required) {
            return response()->json([
                'success' => false,
                'message' => 'Poin Anda tidak mencukupi untuk menukarkan voucher ini.'
            ], 400);
        }

        if (!$voucher->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher ini sudah tidak aktif.'
            ], 400);
        }

        if ($voucher->start_date && Carbon::parse($voucher->start_date)->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher ini belum tersedia.'
            ], 400);
        }

        if ($voucher->end_date && Carbon::parse($voucher->end_date)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher ini sudah kadaluwarsa.'
            ], 400);
        }

        if ($voucher->usage_limit !== null) {
            $usageCount = PointRedemption::where('point_voucher_id', $voucher->id)->count();
            if ($usageCount >= $voucher->usage_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher ini sudah mencapai batas penggunaan.'
                ], 400);
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
                    'discount_code' => $discountCode,
                    'redeemed_from' => 'cart_modal'
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

            // Ambil cart user dan hitung subtotal untuk validasi
            $cart = \App\Models\Cart::where('user_id', $user->id)->first();
            $subtotal = 0;

            if ($cart) {
                $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)->get();
                $subtotal = $cartItems->sum('price');
            }

            // Validasi minimum order
            if ($subtotal < $voucher->min_order) {
                // Rollback transaksi
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Minimum pembelian Rp ' . number_format($voucher->min_order, 0, ',', '.') . ' untuk menggunakan voucher ini.'
                ], 400);
            }

            // Hitung diskon
            $discountAmount = $voucher->calculateDiscount($subtotal);

            // LANGSUNG APPLY KE CART SESSION (hapus diskon lama jika ada)
            session()->forget(['cart_discount', 'cart_point_voucher']);

            session()->put('cart_point_voucher', [
                'id' => $voucher->id,
                'code' => $discountCode,
                'name' => $voucher->name,
                'amount' => $discountAmount,
                'subtotal' => $subtotal,
                'total' => $subtotal - $discountAmount,
                'point_redemption_id' => $redemption->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil ditukar dan diterapkan ke keranjang!',
                'voucher' => [
                    'name' => $voucher->name,
                    'code' => $discountCode,
                    'discount_amount' => $discountAmount,
                    'formatted_discount_amount' => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
                    'new_total' => $subtotal - $discountAmount,
                    'formatted_new_total' => 'Rp ' . number_format($subtotal - $discountAmount, 0, ',', '.')
                ],
                'user_points' => $user->points,
                'formatted_user_points' => number_format($user->points)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Voucher tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage()
        ], 404);
    }
}
}
