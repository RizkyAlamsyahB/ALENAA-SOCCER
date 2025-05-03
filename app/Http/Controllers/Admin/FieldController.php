<?php

namespace App\Http\Controllers\Admin;

use App\Models\Field;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class FieldController extends Controller
{
    /**
     * Menampilkan daftar lapangan dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $fields = Field::select('*');

            return DataTables::of($fields)
                ->addColumn('action', function ($field) {
                    return '<div class="d-flex gap-1">
                            <a href="' .
                        route('admin.fields.show', $field->id) .
                        '" class="btn btn-sm btn-info">Detail</a>
                            <a href="' .
                        route('admin.fields.edit', $field->id) .
                        '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $field->id .
                        '" data-name="' .
                        $field->name .
                        '">Hapus</button>
                        </div>';
                })
                ->addColumn('photographer', function ($field) {
                    if ($field->photographer_id) {
                        $photographer = User::find($field->photographer_id);
                        if ($photographer) {
                            return '<span class="badge bg-info">' . $photographer->name . '</span>';
                        }
                    }
                    return '<span class="badge bg-secondary">Tidak ada fotografer</span>';
                })
                ->editColumn('price', function ($field) {
                    return number_format($field->price, 0, ',', '.');
                })
                ->editColumn('created_at', function ($field) {
                    return $field->created_at->format('d M Y H:i');
                })
                ->editColumn('updated_at', function ($field) {
                    return $field->updated_at->format('d M Y H:i');
                })
                ->rawColumns(['action', 'photographer'])
                ->make(true);
        }

        return view('admin.fields.index');
    }

    /**
     * Menampilkan form tambah lapangan
     */
    public function create()
    {
        // Ambil daftar photographer untuk dropdown
        $photographers = User::where('role', 'photographer')->get();
        return view('admin.fields.create', compact('photographers'));
    }

    /**
     * Menyimpan lapangan baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:fields,name',
            'type' => ['required', Rule::in(['Matras Standar', 'Rumput Sintetis', 'Matras Premium'])],
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
            'photographer_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string|max:1000',
        ]);

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Simpan gambar ke storage/app/public/fields
            $path = $request->file('image')->store('fields', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        } else {
            // Default image jika tidak ada upload
            $validatedData['image'] = 'assets/futsal-field.png';
        }

        Field::create($validatedData);

        return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail lapangan
     */
    public function show(Field $field)
    {
        $photographer = null;
        if ($field->photographer_id) {
            $photographer = User::find($field->photographer_id);
        }
        return view('admin.fields.show', compact('field', 'photographer'));
    }

    /**
     * Menampilkan form edit lapangan
     */
    public function edit(Field $field)
    {
        $photographers = User::where('role', 'photographer')->get();
        return view('admin.fields.edit', compact('field', 'photographers'));
    }

    /**
     * Memperbarui data lapangan
     */
    public function update(Request $request, Field $field)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fields')->ignore($field->id)],
            'type' => ['required', Rule::in(['Matras Standar', 'Rumput Sintetis', 'Matras Premium'])],
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
            'photographer_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string|max:1000',
        ]);

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada dan bukan default
            if ($field->image && $field->image != 'assets/futsal-field.png' && Storage::exists('public/' . $field->image)) {
                Storage::delete('public/' . $field->image);
            }

            // Simpan gambar baru ke storage/app/public/fields
            $path = $request->file('image')->store('fields', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        }

        $field->update($validatedData);

        return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil diperbarui');
    }

    /**
     * Menghapus lapangan
     */
    public function destroy(Field $field)
    {
        try {
            // Hapus gambar jika ada dan bukan default
            if ($field->image && $field->image != 'assets/futsal-field.png' && Storage::exists('public/' . $field->image)) {
                Storage::delete('public/' . $field->image);
            }

            $field->delete();
            return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting field: ' . $e->getMessage());
            return redirect()->route('admin.fields.index')->with('error', 'Tidak dapat menghapus lapangan');
        }
    }
}
