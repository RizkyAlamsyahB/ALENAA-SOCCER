<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk dengan server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::select('*');

            return DataTables::of($products)
                ->addColumn('action', function ($product) {
                    return '<div class="d-flex gap-1">
                            <a href="' .
                        route('admin.products.show', $product->id) .
                        '" class="btn btn-sm btn-info">Show</a>
                            <a href="' .
                        route('admin.products.edit', $product->id) .
                        '" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $product->id .
                        '" data-name="' .
                        $product->name .
                        '">Hapus</button>
                        </div>';
                })

                ->editColumn('category', function ($product) {
                    $badges = [
                        'food' => 'bg-primary',
                        'beverage' => 'bg-info',
                        'equipment' => 'bg-warning',
                        'other' => 'bg-secondary',
                    ];

                    $badge = isset($badges[$product->category]) ? $badges[$product->category] : 'bg-secondary';
                    return '<span class="badge ' . $badge . '">' . ucfirst($product->category) . '</span>';
                })
                ->rawColumns(['action', 'category'])
                ->make(true);
        }

        return view('admin.products.index');
    }

    /**
     * Menampilkan form tambah produk
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'category' => ['required', Rule::in(['food', 'beverage', 'equipment', 'other'])],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
        ]);

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Simpan gambar ke storage/app/public/products
            $path = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        }

        Product::create($validatedData);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Menampilkan detail produk
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Menampilkan form edit produk
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Memperbarui data produk
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'description' => 'nullable|string',
            'category' => ['required', Rule::in(['food', 'beverage', 'equipment', 'other'])],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
        ]);

        // Cek jika ada file gambar yang diupload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::exists('public/products/' . basename($product->image))) {
                Storage::delete('public/products/' . basename($product->image));
            }

            // Simpan gambar baru ke storage/app/public/products
            $path = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $path; // Simpan path di database
        }

        $product->update($validatedData);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Menghapus produk
     */
    public function destroy(Product $product)
    {
        try {
            // Hapus gambar jika ada
            if ($product->image && Storage::exists(str_replace('storage/', 'public/', $product->image))) {
                Storage::delete(str_replace('storage/', 'public/', $product->image));
            }

            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')->with('error', 'Tidak dapat menghapus produk');
        }
    }
}
