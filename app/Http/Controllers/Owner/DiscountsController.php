<?php

namespace App\Http\Controllers\Owner;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DiscountsController extends Controller
{
    /**
     * Menampilkan daftar diskon dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $discounts = Discount::select('*');

            return DataTables::of($discounts)
                ->addColumn('action', function ($discount) {
                    return '<div class="d-flex gap-1">
                            <a href="' . route('owner.discounts.show', $discount->id) . '" class="btn btn-sm btn-info">Detail</a>
                            <a href="' . route('owner.discounts.edit', $discount->id) . '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $discount->id . '" data-name="' . $discount->name . '">Hapus</button>
                        </div>';
                })
                ->editColumn('value', function ($discount) {
                    if ($discount->type == 'percentage') {
                        return $discount->value . '%';
                    }
                    return 'Rp ' . number_format($discount->value, 0, ',', '.');
                })
                ->editColumn('min_order', function ($discount) {
                    return 'Rp ' . number_format($discount->min_order, 0, ',', '.');
                })
                ->editColumn('max_discount', function ($discount) {
                    return $discount->max_discount ? 'Rp ' . number_format($discount->max_discount, 0, ',', '.') : '-';
                })
                ->editColumn('is_active', function ($discount) {
                    return $discount->is_active
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-danger">Tidak Aktif</span>';
                })
                ->editColumn('start_date', function ($discount) {
                    return $discount->start_date ? Carbon::parse($discount->start_date)->format('d M Y') : '-';
                })
                ->editColumn('end_date', function ($discount) {
                    return $discount->end_date ? Carbon::parse($discount->end_date)->format('d M Y') : '-';
                })
                ->addColumn('status_periode', function ($discount) {
                    $now = Carbon::now();
                    if (!$discount->start_date || !$discount->end_date) {
                        return '<span class="badge bg-secondary">Tidak Ada Periode</span>';
                    }

                    $startDate = Carbon::parse($discount->start_date);
                    $endDate = Carbon::parse($discount->end_date);

                    if ($now < $startDate) {
                        return '<span class="badge bg-warning">Belum Mulai</span>';
                    } else if ($now > $endDate) {
                        return '<span class="badge bg-danger">Kedaluwarsa</span>';
                    } else {
                        return '<span class="badge bg-success">Sedang Berlangsung</span>';
                    }
                })
                ->rawColumns(['action', 'is_active', 'status_periode'])
                ->make(true);
        }

        return view('owner.discounts.index');
    }

    /**
     * Menampilkan form tambah diskon
     */
    public function create()
    {
        return view('owner.discounts.create');
    }

    /**
     * Menyimpan diskon baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => 'required|numeric|min:0',
            'min_order' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|string|max:255',
            'usage_limit' => 'nullable|integer|min:1',
            'user_usage_limit' => 'required|integer|min:1',
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

        Discount::create($validatedData);

        return redirect()->route('owner.discounts.index')->with('success', 'Diskon berhasil ditambahkan');
    }

    /**
     * Menampilkan detail diskon
     */
    public function show(Discount $discount)
    {
        return view('owner.discounts.show', compact('discount'));
    }

    /**
     * Menampilkan form edit diskon
     */
    public function edit(Discount $discount)
    {
        return view('owner.discounts.edit', compact('discount'));
    }

    /**
     * Memperbarui data diskon
     */
    public function update(Request $request, Discount $discount)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'string', 'max:255', Rule::unique('discounts')->ignore($discount->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => 'required|numeric|min:0',
            'min_order' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|string|max:255',
            'usage_limit' => 'nullable|integer|min:1',
            'user_usage_limit' => 'required|integer|min:1',
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

        $discount->update($validatedData);

        return redirect()->route('owner.discounts.index')->with('success', 'Diskon berhasil diperbarui');
    }

    /**
     * Mengubah status aktif/nonaktif diskon
     */
    public function toggleStatus(Discount $discount)
    {
        $discount->is_active = !$discount->is_active;
        $discount->save();

        $status = $discount->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('owner.discounts.index')->with('success', "Diskon berhasil $status");
    }

    /**
     * Menghapus diskon
     */
    public function destroy(Discount $discount)
    {
        try {
            $discount->delete();
            return redirect()->route('owner.discounts.index')->with('success', 'Diskon berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting discount: ' . $e->getMessage());
            return redirect()->route('owner.discounts.index')->with('error', 'Tidak dapat menghapus diskon. Diskon mungkin sedang digunakan.');
        }
    }
}
