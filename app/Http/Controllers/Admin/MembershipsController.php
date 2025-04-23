<?php

namespace App\Http\Controllers\Admin;

use App\Models\Field;
use App\Models\Membership;
use App\Models\User;
use App\Models\Photographer;
use App\Models\RentalItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MembershipsController extends Controller
{
    /**
     * Menampilkan daftar paket membership dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $memberships = Membership::with('field', 'photographer')->select('*');

            return DataTables::of($memberships)
                ->addColumn('action', function ($membership) {
                    return '<div class="d-flex gap-1">
                            <a href="' . route('admin.memberships.show', $membership->id) . '" class="btn btn-sm btn-info">Detail</a>
                            <a href="' . route('admin.memberships.edit', $membership->id) . '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $membership->id . '" data-name="' . $membership->name . '">Hapus</button>
                        </div>';
                })
                ->addColumn('field_name', function ($membership) {
                    return $membership->field ? $membership->field->name : 'N/A';
                })
                ->editColumn('type', function ($membership) {
                    $badgeClass = '';
                    switch ($membership->type) {
                        case 'bronze':
                            $badgeClass = 'bg-secondary';
                            break;
                        case 'silver':
                            $badgeClass = 'bg-light text-dark';
                            break;
                        case 'gold':
                            $badgeClass = 'bg-warning';
                            break;
                        default:
                            $badgeClass = 'bg-info';
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($membership->type) . '</span>';
                })
                ->editColumn('price', function ($membership) {
                    return 'Rp ' . number_format($membership->price, 0, ',', '.');
                })
                ->editColumn('status', function ($membership) {
                    $statusClass = $membership->status === 'active' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' . $statusClass . '">' . ucfirst($membership->status) . '</span>';
                })
                ->editColumn('image', function ($membership) {
                    if ($membership->image) {
                        return '<img src="' . asset('storage/' . $membership->image) . '" alt="Membership" class="img-thumbnail" width="50">';
                    }
                    return '<span class="badge bg-secondary">No Image</span>';
                })
                ->addColumn('photographer', function ($membership) {
                    if ($membership->includes_photographer && $membership->photographer_id) {
                        $photographer = Photographer::find($membership->photographer_id);
                        if ($photographer) {
                            return '<span class="badge bg-info">' . $photographer->name . '</span>';
                        }
                    }
                    return '<span class="badge bg-secondary">Tidak termasuk</span>';
                })
                ->addColumn('details', function ($membership) {
                    return '<span class="badge bg-primary">' . $membership->sessions_per_week . ' sesi/minggu</span>
                            <span class="badge bg-info">' . $membership->session_duration . ' jam/sesi</span>';
                })
                ->rawColumns(['action', 'type', 'status', 'image', 'photographer', 'details'])
                ->make(true);
        }

        return view('admin.memberships.index');
    }

    /**
     * Menampilkan form tambah paket membership
     */
    public function create()
    {
        $fields = Field::all();
        $photographers = Photographer::all();
        $rentalItems = RentalItem::all();
        return view('admin.memberships.create', compact('fields', 'photographers', 'rentalItems'));
    }

    /**
     * Menyimpan paket membership baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['bronze', 'silver', 'gold', 'platinum'])],
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'sessions_per_week' => 'required|integer|min:1',
            'session_duration' => 'required|integer|min:1',
            'photographer_duration' => 'nullable|integer|min:0',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'includes_photographer' => 'boolean',
            'photographer_id' => 'nullable|required_if:includes_photographer,1|exists:photographers,id',
            'includes_rental_item' => 'boolean',
            'rental_item_id' => 'nullable|required_if:includes_rental_item,1|exists:rental_items,id',
            'rental_item_quantity' => 'nullable|integer|min:1',
        ]);

        // Process boolean checkboxes
        $validatedData['includes_photographer'] = $request->has('includes_photographer');
        $validatedData['includes_rental_item'] = $request->has('includes_rental_item');

        // If photographer not included, set id and duration to null
        if (!$validatedData['includes_photographer']) {
            $validatedData['photographer_id'] = null;
            $validatedData['photographer_duration'] = null;
        }

        // If rental item not included, set id and quantity to null
        if (!$validatedData['includes_rental_item']) {
            $validatedData['rental_item_id'] = null;
            $validatedData['rental_item_quantity'] = null;
        }

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('memberships', 'public');
            $validatedData['image'] = $path;
        }

        Membership::create($validatedData);

        return redirect()->route('admin.memberships.index')->with('success', 'Paket membership berhasil ditambahkan');
    }

    /**
     * Menampilkan detail paket membership
     */
    public function show($id)
    {
        $membership = Membership::with(['field', 'photographer', 'rentalItem'])->findOrFail($id);
        $rentalItem = null;

        if ($membership->includes_rental_item && $membership->rental_item_id) {
            $rentalItem = RentalItem::find($membership->rental_item_id);
        }

        return view('admin.memberships.show', compact('membership', 'rentalItem'));
    }

    /**
     * Menampilkan form edit paket membership
     */
    public function edit($id)
    {
        $membership = Membership::findOrFail($id);
        $fields = Field::all();
        $photographers = Photographer::all();
        $rentalItems = RentalItem::all();

        return view('admin.memberships.edit', compact('membership', 'fields', 'photographers', 'rentalItems'));
    }

    /**
     * Memperbarui data paket membership
     */
    public function update(Request $request, $id)
    {
        $membership = Membership::findOrFail($id);

        $validatedData = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['bronze', 'silver', 'gold', 'platinum'])],
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'sessions_per_week' => 'required|integer|min:1',
            'session_duration' => 'required|integer|min:1',
            'photographer_duration' => 'nullable|integer|min:0',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'includes_photographer' => 'boolean',
            'photographer_id' => 'nullable|exists:photographers,id',
            'includes_rental_item' => 'boolean',
            'rental_item_id' => 'nullable|exists:rental_items,id',
            'rental_item_quantity' => 'nullable|integer|min:1',
        ]);

        // Process boolean checkboxes
        $validatedData['includes_photographer'] = $request->has('includes_photographer');
        $validatedData['includes_rental_item'] = $request->has('includes_rental_item');

        // If photographer not included, set id and duration to null
        if (!$validatedData['includes_photographer']) {
            $validatedData['photographer_id'] = null;
            $validatedData['photographer_duration'] = null;
        }

        // If rental item not included, set id and quantity to null
        if (!$validatedData['includes_rental_item']) {
            $validatedData['rental_item_id'] = null;
            $validatedData['rental_item_quantity'] = null;
        }

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($membership->image && Storage::exists('public/' . $membership->image)) {
                Storage::delete('public/' . $membership->image);
            }

            $path = $request->file('image')->store('memberships', 'public');
            $validatedData['image'] = $path;
        }

        $membership->update($validatedData);

        return redirect()->route('admin.memberships.index')->with('success', 'Paket membership berhasil diperbarui');
    }

    /**
     * Menghapus paket membership
     */
    public function destroy($id)
    {
        try {
            $membership = Membership::findOrFail($id);

            // Hapus gambar jika ada
            if ($membership->image && Storage::exists('public/' . $membership->image)) {
                Storage::delete('public/' . $membership->image);
            }

            $membership->delete();
            return redirect()->route('admin.memberships.index')->with('success', 'Paket membership berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting membership: ' . $e->getMessage());
            return redirect()->route('admin.memberships.index')->with('error', 'Tidak dapat menghapus paket membership');
        }
    }
}
