<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class FieldController extends Controller
{
    /**
     * Menampilkan daftar lapangan
     */
    public function index()
    {
        $fields = Field::all();
        return view('admin.fields.index', compact('fields'));
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
            'regular_price' => 'required|numeric|min:0',
            'peak_price' => 'nullable|numeric|min:0',
            'facilities' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/fields');
            $validatedData['image'] = str_replace('public/', 'storage/', $imagePath);
        }

        Field::create($validatedData);

        return redirect()->route('admin.fields.index')
            ->with('success', 'Lapangan berhasil ditambahkan');
    }

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
            'regular_price' => 'required|numeric|min:0',
            'peak_price' => 'nullable|numeric|min:0',
            'facilities' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($field->image && Storage::exists(str_replace('storage/', 'public/', $field->image))) {
                Storage::delete(str_replace('storage/', 'public/', $field->image));
            }

            $imagePath = $request->file('image')->store('public/fields');
            $validatedData['image'] = str_replace('public/', 'storage/', $imagePath);
        }

        $field->update($validatedData);

        return redirect()->route('admin.fields.index')
            ->with('success', 'Lapangan berhasil diperbarui');
    }

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
            return redirect()->route('admin.fields.index')
                ->with('success', 'Lapangan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.fields.index')
                ->with('error', 'Tidak dapat menghapus lapangan');
        }
    }

    /**
     * Mengubah status aktif lapangan
     */
    public function toggleStatus(Field $field)
    {
        $field->update([
            'is_active' => !$field->is_active
        ]);

        return redirect()->route('admin.fields.index')
            ->with('success', 'Status lapangan berhasil diperbarui');
    }
}
