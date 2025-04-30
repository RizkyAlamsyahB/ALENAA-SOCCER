<?php

namespace App\Http\Controllers\Owner;

use App\Models\PointVoucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PointVoucherController extends Controller
{
    /**
     * Menampilkan daftar voucher poin dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pointVouchers = PointVoucher::select('*');

            return DataTables::of($pointVouchers)
                ->addColumn('action', function ($voucher) {
                    return '<div class="d-flex gap-1">
                            <a href="' .
                        route('owner.point_vouchers.show', $voucher->id) .
                        '" class="btn btn-sm btn-info">Detail</a>
                            <a href="' .
                        route('owner.point_vouchers.edit', $voucher->id) .
                        '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $voucher->id .
                        '" data-name="' .
                        $voucher->name .
                        '">Hapus</button>
                        </div>';
                })
                ->editColumn('discount_value', function ($voucher) {
                    if ($voucher->discount_type == 'percentage') {
                        return $voucher->discount_value . '%';
                    }
                    return 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
                })
                ->editColumn('min_order', function ($voucher) {
                    return 'Rp ' . number_format($voucher->min_order, 0, ',', '.');
                })
                ->editColumn('max_discount', function ($voucher) {
                    return $voucher->max_discount ? 'Rp ' . number_format($voucher->max_discount, 0, ',', '.') : '-';
                })
                ->editColumn('is_active', function ($voucher) {
                    return $voucher->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
                })
                ->editColumn('start_date', function ($voucher) {
                    return $voucher->start_date ? Carbon::parse($voucher->start_date)->format('d M Y') : '-';
                })
                ->editColumn('end_date', function ($voucher) {
                    return $voucher->end_date ? Carbon::parse($voucher->end_date)->format('d M Y') : '-';
                })
                ->addColumn('status_periode', function ($voucher) {
                    $now = Carbon::now();
                    if (!$voucher->start_date || !$voucher->end_date) {
                        return '<span class="badge bg-secondary">Tidak Ada Periode</span>';
                    }

                    $startDate = Carbon::parse($voucher->start_date);
                    $endDate = Carbon::parse($voucher->end_date);

                    if ($now < $startDate) {
                        return '<span class="badge bg-warning">Belum Mulai</span>';
                    } elseif ($now > $endDate) {
                        return '<span class="badge bg-danger">Kedaluwarsa</span>';
                    } else {
                        return '<span class="badge bg-success">Sedang Berlangsung</span>';
                    }
                })
                ->rawColumns(['action', 'is_active', 'status_periode'])
                ->make(true);
        }

        return view('owner.point_vouchers.index');
    }

    /**
     * Menampilkan form tambah voucher poin
     */
    public function create()
    {
        return view('owner.point_vouchers.create');
    }

    /**
     * Menyimpan voucher poin baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:point_vouchers,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => 'required|numeric|min:0',
            'points_required' => 'required|integer|min:1',
            'min_order' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|string|max:255',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Konversi tanggal ke format yang benar
        if ($request->has('start_date') && $request->start_date) {
            $validatedData['start_date'] = Carbon::parse($request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $validatedData['end_date'] = Carbon::parse($request->end_date);
        }

        // Set nilai default untuk is_active jika tidak ada
        $validatedData['is_active'] = $request->has('is_active') ? $request->is_active : true;

        PointVoucher::create($validatedData);

        return redirect()->route('owner.point_vouchers.index')->with('success', 'Voucher poin berhasil ditambahkan');
    }

    /**
     * Menampilkan detail voucher poin
     */
    public function show(PointVoucher $pointVoucher)
    {
        // Load data pembuat voucher
        $pointVoucher->load('createdBy');
        return view('owner.point_vouchers.show', compact('pointVoucher'));
    }

    /**
     * Menampilkan form edit voucher poin
     */
    public function edit(PointVoucher $pointVoucher)
    {
        return view('owner.point_vouchers.edit', compact('pointVoucher'));
    }

    /**
     * Memperbarui data voucher poin
     */
    public function update(Request $request, PointVoucher $pointVoucher)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'string', 'max:255', Rule::unique('point_vouchers')->ignore($pointVoucher->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => 'required|numeric|min:0',
            'points_required' => 'required|integer|min:1',
            'min_order' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|string|max:255',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Konversi tanggal ke format yang benar
        if ($request->has('start_date') && $request->start_date) {
            $validatedData['start_date'] = Carbon::parse($request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $validatedData['end_date'] = Carbon::parse($request->end_date);
        }

        // Set nilai default untuk is_active jika tidak ada
        $validatedData['is_active'] = $request->has('is_active') ? $request->is_active : true;

        $pointVoucher->update($validatedData);

        return redirect()->route('owner.point_vouchers.index')->with('success', 'Voucher poin berhasil diperbarui');
    }

    /**
     * Mengubah status aktif/nonaktif voucher poin
     */
    public function toggleStatus(PointVoucher $pointVoucher)
    {
        $pointVoucher->is_active = !$pointVoucher->is_active;
        $pointVoucher->save();

        $status = $pointVoucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()
            ->route('owner.point_vouchers.index')
            ->with('success', "Voucher poin berhasil $status");
    }

    /**
     * Menghapus voucher poin
     */
    public function destroy(PointVoucher $pointVoucher)
    {
        try {
            $pointVoucher->delete();
            return redirect()->route('owner.point_vouchers.index')->with('success', 'Voucher poin berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting point voucher: ' . $e->getMessage());
            return redirect()->route('owner.point_vouchers.index')->with('error', 'Tidak dapat menghapus voucher poin. Voucher mungkin sedang digunakan.');
        }
    }
}
