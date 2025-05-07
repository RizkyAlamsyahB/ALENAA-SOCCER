<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Field;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PhotoPackageController extends Controller
{
    /**
     * Menampilkan daftar paket fotografer dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $photographers = Photographer::with('user')->select('*');

            return DataTables::of($photographers)
                ->addColumn('action', function ($photographer) {
                    return '<div class="d-flex gap-1">
                            <a href="' .
                        route('admin.photo-packages.show', $photographer->id) .
                        '" class="btn btn-sm btn-info">Detail</a>
                            <a href="' .
                        route('admin.photo-packages.edit', $photographer->id) .
                        '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $photographer->id .
                        '" data-name="' .
                        $photographer->name .
                        '">Hapus</button>
                        </div>';
                })
                ->addColumn('photographer_name', function ($photographer) {
                    return $photographer->user ? $photographer->user->name : 'N/A';
                })
                ->editColumn('package_type', function ($photographer) {
                    $badgeClass = '';
                    switch ($photographer->package_type) {
                        case 'favorite':
                            $badgeClass = 'bg-warning';
                            break;
                        case 'plus':
                            $badgeClass = 'bg-info';
                            break;
                        case 'exclusive':
                            $badgeClass = 'bg-primary';
                            break;
                        default:
                            $badgeClass = 'bg-secondary';
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($photographer->package_type) . '</span>';
                })
                ->editColumn('price', function ($photographer) {
                    return 'Rp ' . number_format($photographer->price, 0, ',', '.');
                })
                ->editColumn('status', function ($photographer) {
                    $statusClass = $photographer->status === 'active' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' . $statusClass . '">' . ucfirst($photographer->status) . '</span>';
                })
                ->editColumn('image', function ($photographer) {
                    if ($photographer->image) {
                        return '<img src="' . asset('storage/' . $photographer->image) . '" alt="Package" class="img-thumbnail" width="50">';
                    }
                    return '<span class="badge bg-secondary">No Image</span>';
                })
                ->rawColumns(['action', 'package_type', 'status', 'image'])
                ->make(true);
        }

        return view('admin.photo-packages.index');
    }

    /**
     * Menampilkan form tambah paket fotografer
     */
    public function create()
    {
        $photographers = User::where('role', 'photographer')->get();
        $fields = Field::all(); // Tambahkan ini
        return view('admin.photo-packages.create', compact('photographers', 'fields'));
    }
    /**
     * Menyimpan paket fotografer baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'package_type' => ['required', Rule::in(['basic', 'favorite', 'plus', 'exclusive'])],
            'duration' => 'required|integer|min:1',
            'field_id' => 'nullable|exists:fields,id', // Tambahkan ini
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'features' => 'required|array',
            'features.*' => 'required|string',
        ]);
        // Encode features array ke json
        $validatedData['features'] = json_encode($validatedData['features']);

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('photographers', 'public');
            $validatedData['image'] = $path;
        }

        Photographer::create($validatedData);

        return redirect()->route('admin.photo-packages.index')->with('success', 'Paket fotografer berhasil ditambahkan');
    }

    /**
     * Menampilkan detail paket fotografer
     */
    public function show($id)
    {
        $photographer = Photographer::findOrFail($id);
        $user = $photographer->user;

        // Correctly handle features - check if it's already decoded
        if (is_string($photographer->features)) {
            $photographer->features = json_decode($photographer->features);
        }

        return view('admin.photo-packages.show', compact('photographer', 'user'));
    }

    /**
     * Menampilkan form edit paket fotografer
     */
    public function edit($id)
    {
        $photographer = Photographer::findOrFail($id);
        $photographers = User::where('role', 'photographer')->get();
        $fields = Field::all(); // Tambahkan ini

        // Only decode if it's a string and not already decoded
        if (is_string($photographer->features)) {
            $photographer->features = json_decode($photographer->features);
        }

        return view('admin.photo-packages.edit', compact('photographer', 'photographers', 'fields'));
    }

    /**
     * Memperbarui data paket fotografer
     */
    public function update(Request $request, $id)
    {
        $photographer = Photographer::findOrFail($id);

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'package_type' => ['required', Rule::in(['basic', 'favorite', 'plus', 'exclusive'])],
            'duration' => 'required|integer|min:1',
            'field_id' => 'nullable|exists:fields,id', // Tambahkan ini
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'features' => 'required|array',
            'features.*' => 'required|string',
        ]);

        // We're receiving features as an array, so we need to encode it to JSON
        // No need to decode first since it's already an array from form input
        $validatedData['features'] = json_encode($validatedData['features']);

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($photographer->image && Storage::exists('public/' . $photographer->image)) {
                Storage::delete('public/' . $photographer->image);
            }

            $path = $request->file('image')->store('photographers', 'public');
            $validatedData['image'] = $path;
        }

        $photographer->update($validatedData);

        return redirect()->route('admin.photo-packages.index')->with('success', 'Paket fotografer berhasil diperbarui');
    }
    /**
     * Menghapus paket fotografer (soft delete)
     */
    public function destroy($id)
    {
        try {
            $photographer = Photographer::findOrFail($id);

            // Cek apakah fotografer memiliki booking aktif
            $activeBookings = $photographer->bookings()->where('status', '!=', 'cancelled')->where('end_time', '>', now())->exists();
            if ($activeBookings) {
                return redirect()->route('admin.photo-packages.index')->with('error', 'Tidak dapat menghapus paket fotografer karena masih ada booking aktif terkait.');
            }

            // Cek apakah fotografer digunakan dalam membership aktif
            $activeInMembership = $photographer
                ->memberships()
                ->whereHas('subscriptions', function ($query) {
                    $query->where('status', 'active')->where('end_date', '>', now());
                })
                ->exists();

            if ($activeInMembership) {
                return redirect()->route('admin.photo-packages.index')->with('error', 'Tidak dapat menghapus paket fotografer karena masih digunakan dalam paket membership aktif.');
            }

            // Hapus gambar jika ada
            if ($photographer->image && Storage::exists('public/' . $photographer->image)) {
                Storage::delete('public/' . $photographer->image);
            }

            // Soft delete
            $photographer->delete();

            return redirect()->route('admin.photo-packages.index')->with('success', 'Paket fotografer berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting photographer package: ' . $e->getMessage());
            return redirect()
                ->route('admin.photo-packages.index')
                ->with('error', 'Tidak dapat menghapus paket fotografer: ' . $e->getMessage());
        }
    }
}
