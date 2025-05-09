<?php

namespace App\Http\Controllers\Admin;

use App\Models\RentalItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class RentalItemController extends Controller
{
    /**
     * Menampilkan daftar item sewa dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rentalItems = RentalItem::select('*');

            return DataTables::of($rentalItems)
                ->addColumn('action', function ($rentalItem) {
                    return '<div class="d-flex gap-1">
                            <a href="' .
                        route('admin.rental-items.show', $rentalItem->id) .
                        '" class="btn btn-sm btn-info">Show</a>
                            <a href="' .
                        route('admin.rental-items.edit', $rentalItem->id) .
                        '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $rentalItem->id .
                        '" data-name="' .
                        $rentalItem->name .
                        '">Hapus</button>
                        </div>';
                })

                ->editColumn('category', function ($rentalItem) {
                    $badges = [
                        'ball' => 'bg-primary',
                        'jersey' => 'bg-info',
                        'shoes' => 'bg-warning',
                        'other' => 'bg-secondary',
                    ];

                    $badge = isset($badges[$rentalItem->category]) ? $badges[$rentalItem->category] : 'bg-secondary';
                    return '<span class="badge ' . $badge . '">' . ucfirst($rentalItem->category) . '</span>';
                })
                ->editColumn('stock_available', function ($rentalItem) {
                    $percentage = $rentalItem->stock_total > 0 ? ($rentalItem->stock_available / $rentalItem->stock_total) * 100 : 0;

                    if ($percentage <= 20) {
                        $badgeClass = 'bg-danger';
                    } elseif ($percentage <= 50) {
                        $badgeClass = 'bg-warning';
                    } else {
                        $badgeClass = 'bg-success';
                    }

                    return '<span class="badge ' . $badgeClass . '">' . $rentalItem->stock_available . ' / ' . $rentalItem->stock_total . '</span>';
                })
                ->rawColumns(['action', 'category', 'stock_available'])
                ->make(true);
        }

        return view('admin.rental-items.index');
    }

    /**
     * Menampilkan form tambah item sewa
     */
    public function create()
    {
        return view('admin.rental-items.create');
    }

    /**
     * Menyimpan item sewa baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:rental_items,name',
            'description' => 'nullable|string',
            'category' => ['required', Rule::in(['ball', 'jersey', 'shoes', 'other'])],
            'rental_price' => 'required|numeric|min:0',
            'stock_total' => 'required|numeric|min:0',
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
        ]);

        // Set stok tersedia sama dengan stok total awal
        $validatedData['stock_available'] = $validatedData['stock_total'];

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Simpan gambar ke storage/app/public/rental-items
            $path = $request->file('image')->store('rental-items', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        }

        RentalItem::create($validatedData);

        return redirect()->route('admin.rental-items.index')->with('success', 'Item sewa berhasil ditambahkan');
    }

    /**
     * Menampilkan detail item sewa
     */
    public function show(RentalItem $rentalItem)
    {
        return view('admin.rental-items.show', compact('rentalItem'));
    }

    /**
     * Menampilkan form edit item sewa
     */
    public function edit(RentalItem $rentalItem)
    {
        return view('admin.rental-items.edit', compact('rentalItem'));
    }

    /**
     * Memperbarui data item sewa
     */
    public function update(Request $request, RentalItem $rentalItem)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('rental_items')->ignore($rentalItem->id)],
            'description' => 'nullable|string',
            'category' => ['required', Rule::in(['ball', 'jersey', 'shoes', 'other'])],
            'rental_price' => 'required|numeric|min:0',
            'stock_total' => 'required|numeric|min:' . ($rentalItem->stock_total - $rentalItem->stock_available),
            'stock_available' => 'required|numeric|min:0|max:' . $request->input('stock_total', $rentalItem->stock_total),
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
        ]);

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($rentalItem->image && Storage::exists('public/rental-items/' . basename($rentalItem->image))) {
                Storage::delete('public/rental-items/' . basename($rentalItem->image));
            }

            // Simpan gambar baru ke storage/app/public/rental-items
            $path = $request->file('image')->store('rental-items', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        }

        $rentalItem->update($validatedData);

        return redirect()->route('admin.rental-items.index')->with('success', 'Item sewa berhasil diperbarui');
    }

/**
 * Menghapus item sewa (soft delete)
 */
public function destroy(RentalItem $rentalItem)
{
    try {
        // Cek apakah item sedang disewa (stock_available tidak sama dengan stock_total)
        if ($rentalItem->stock_available < $rentalItem->stock_total) {
            return redirect()->route('admin.rental-items.index')
                ->with('error', 'Tidak dapat menghapus item karena sedang disewa');
        }

        // Cek booking aktif
        $activeBookings = $rentalItem->bookings()->where('status', '!=', 'cancelled')->where('end_time', '>', now())->exists();
        if ($activeBookings) {
            return redirect()->route('admin.rental-items.index')
                ->with('error', 'Tidak dapat menghapus item karena masih ada booking aktif terkait.');
        }

        // Hapus gambar jika ada
        if ($rentalItem->image && Storage::exists('public/' . $rentalItem->image)) {
            Storage::delete('public/' . $rentalItem->image);
        }

        // Soft delete
        $rentalItem->delete();

        return redirect()->route('admin.rental-items.index')->with('success', 'Item sewa berhasil dihapus');
    } catch (\Exception $e) {
        Log::error('Error deleting rental item: ' . $e->getMessage());
        return redirect()->route('admin.rental-items.index')
            ->with('error', 'Tidak dapat menghapus item sewa: ' . $e->getMessage());
    }
}
}
