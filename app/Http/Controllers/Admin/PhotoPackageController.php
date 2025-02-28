<?php

namespace App\Http\Controllers\Admin;

use App\Models\PhotoPackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class PhotoPackageController extends Controller
{
    /**
     * Menampilkan daftar paket foto dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $photoPackages = PhotoPackage::select('*');

            return DataTables::of($photoPackages)
                ->addColumn('action', function ($photoPackage) {
                    return '<div class="d-flex gap-1">
                            <a href="' . route('admin.photo-packages.show', $photoPackage->id) . '" class="btn btn-sm btn-info">Show</a>
                            <a href="' . route('admin.photo-packages.edit', $photoPackage->id) . '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $photoPackage->id . '" data-name="' . $photoPackage->name . '">Hapus</button>
                        </div>';
                })
                ->editColumn('is_active', function ($photoPackage) {
                    return $photoPackage->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>';
                })
                ->editColumn('includes_editing', function ($photoPackage) {
                    return $photoPackage->includes_editing ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>';
                })
                ->editColumn('duration_minutes', function ($photoPackage) {
                    // Format durasi menjadi jam:menit jika lebih dari 60 menit
                    if ($photoPackage->duration_minutes >= 60) {
                        $hours = floor($photoPackage->duration_minutes / 60);
                        $minutes = $photoPackage->duration_minutes % 60;
                        return $hours . ' jam ' . ($minutes > 0 ? $minutes . ' menit' : '');
                    }
                    return $photoPackage->duration_minutes . ' menit';
                })
                ->rawColumns(['action', 'is_active', 'includes_editing'])
                ->make(true);
        }

        return view('admin.photo-packages.index');
    }

    /**
     * Menampilkan form tambah paket foto
     */
    public function create()
    {
        return view('admin.photo-packages.create');
    }

    /**
     * Menyimpan paket foto baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:photo_packages,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|numeric|min:1',
            'number_of_photos' => 'required|numeric|min:1',
            'includes_editing' => 'boolean',
            'is_active' => 'boolean',
        ]);

        PhotoPackage::create($validatedData);

        return redirect()->route('admin.photo-packages.index')->with('success', 'Paket foto berhasil ditambahkan');
    }

    /**
     * Menampilkan detail paket foto
     */
    public function show(PhotoPackage $photoPackage)
    {
        return view('admin.photo-packages.show', compact('photoPackage'));
    }

    /**
     * Menampilkan form edit paket foto
     */
    public function edit(PhotoPackage $photoPackage)
    {
        return view('admin.photo-packages.edit', compact('photoPackage'));
    }

    /**
     * Memperbarui data paket foto
     */
    public function update(Request $request, PhotoPackage $photoPackage)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('photo_packages')->ignore($photoPackage->id)],
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|numeric|min:1',
            'number_of_photos' => 'required|numeric|min:1',
            'includes_editing' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $photoPackage->update($validatedData);

        return redirect()->route('admin.photo-packages.index')->with('success', 'Paket foto berhasil diperbarui');
    }

    /**
     * Menghapus paket foto
     */
    public function destroy(PhotoPackage $photoPackage)
    {
        try {
            // Cek jika paket digunakan dalam booking (implementasi bisa ditambahkan nanti)
            // if ($photoPackage->photographerBookings()->exists()) {
            //     return redirect()->route('admin.photo-packages.index')->with('error', 'Tidak dapat menghapus paket karena sedang digunakan dalam booking');
            // }

            $photoPackage->delete();
            return redirect()->route('admin.photo-packages.index')->with('success', 'Paket foto berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.photo-packages.index')->with('error', 'Tidak dapat menghapus paket foto');
        }
    }

    /**
     * Mengubah status aktif paket foto
     */
    public function toggleStatus(PhotoPackage $photoPackage)
    {
        $photoPackage->update([
            'is_active' => !$photoPackage->is_active,
        ]);

        return redirect()->route('admin.photo-packages.index')->with('success', 'Status paket foto berhasil diperbarui');
    }
}
