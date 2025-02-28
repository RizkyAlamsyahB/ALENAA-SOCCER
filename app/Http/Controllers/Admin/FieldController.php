<?php

namespace App\Http\Controllers\Admin;

use App\Models\Field;
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
                            <a href="' . route('admin.fields.show', $field->id) . '" class="btn btn-sm btn-info">Show</a>
                            <a href="' . route('admin.fields.edit', $field->id) . '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $field->id . '" data-name="' . $field->name . '">Hapus</button>
                        </div>';
                })
                ->editColumn('is_active', function ($field) {
                    return $field->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>';
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        return view('admin.fields.index');
    }
    /**
     * Menampilkan form tambah lapangan
     */
    public function create()
    {
        return view('admin.fields.create');
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
        ]);

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Simpan gambar ke storage/app/public/fields
            $path = $request->file('image')->store('fields', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        }

        Field::create($validatedData);

        return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil ditambahkan');    }

    /**
     * Menampilkan detail lapangan
     */
    public function show(Field $field)
    {
        return view('admin.fields.show', compact('field'));
    }

    /**
     * Menampilkan form edit lapangan
     */
    public function edit(Field $field)
    {
        return view('admin.fields.edit', compact('field'));
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
        ]);

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($field->image && Storage::exists('public/fields/' . basename($field->image))) {
                Storage::delete('public/fields/' . basename($field->image));
            }

            // Simpan gambar baru ke storage/app/public/fields
            $path = $request->file('image')->store('fields', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        }

        $field->update($validatedData);

        return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil diperbarui');    }

    /**
     * Menghapus lapangan
     */
    public function destroy(Field $field)
    {
        try {
            // Hapus gambar jika ada
            if ($field->image && Storage::exists(str_replace('storage/', 'public/', $field->image))) {
                Storage::delete(str_replace('storage/', 'public/', $field->image));
            }

            $field->delete();
            return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.fields.index')->with('error', 'Tidak dapat menghapus lapangan');
        }
    }

}
