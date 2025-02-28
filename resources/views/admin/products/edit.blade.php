@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('breadcrumb', 'Edit Produk')
@section('header-title', 'Edit Produk')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Form Edit Produk</h4>

                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar Produk</label>
                        <input type="file" name="image" id="image" class="form-control-file" accept="image/*" onchange="previewImage(event)">
                        <div class="mt-2">
                            @if($product->image)
                                <img id="imagePreview" src="{{ asset('storage/' . $product->image) }}" alt="Gambar Produk" style="max-width: 200px; height: auto;" class="img-thumbnail">
                            @else
                                <img id="imagePreview" src="#" alt="Preview Gambar" style="display: none; max-width: 200px; height: auto;" class="img-thumbnail">
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kategori Produk</label>
                        <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                            <option value="">Pilih Kategori Produk</option>
                            <option value="food" {{ old('category', $product->category) == 'food' ? 'selected' : '' }}>
                                Makanan
                            </option>
                            <option value="beverage" {{ old('category', $product->category) == 'beverage' ? 'selected' : '' }}>
                                Minuman
                            </option>
                            <option value="equipment" {{ old('category', $product->category) == 'equipment' ? 'selected' : '' }}>
                                Peralatan
                            </option>
                            <option value="other" {{ old('category', $product->category) == 'other' ? 'selected' : '' }}>
                                Lainnya
                            </option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $product->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                               value="{{ old('stock', $product->stock) }}" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active"
                                   name="is_active" value="1"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                Produk Aktif
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var imagePreview = document.getElementById('imagePreview');
        imagePreview.src = reader.result;
        imagePreview.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
