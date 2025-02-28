@extends('layouts.admin')

@section('title', 'Edit Item Sewa')
@section('breadcrumb', 'Edit Item Sewa')
@section('header-title', 'Edit Item Sewa')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Form Edit Item Sewa</h4>

                <form action="{{ route('admin.rental-items.update', $rentalItem->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Item</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $rentalItem->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar Item</label>
                        <input type="file" name="image" id="image" class="form-control-file" accept="image/*" onchange="previewImage(event)">
                        <div class="mt-2">
                            @if($rentalItem->image)
                                <img id="imagePreview" src="{{ asset('storage/' . $rentalItem->image) }}" alt="Gambar Item" style="max-width: 200px; height: auto;" class="img-thumbnail">
                            @else
                                <img id="imagePreview" src="#" alt="Preview Gambar" style="display: none; max-width: 200px; height: auto;" class="img-thumbnail">
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kategori Item</label>
                        <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                            <option value="">Pilih Kategori Item</option>
                            <option value="ball" {{ old('category', $rentalItem->category) == 'ball' ? 'selected' : '' }}>
                                Bola
                            </option>
                            <option value="jersey" {{ old('category', $rentalItem->category) == 'jersey' ? 'selected' : '' }}>
                                Jersey
                            </option>
                            <option value="shoes" {{ old('category', $rentalItem->category) == 'shoes' ? 'selected' : '' }}>
                                Sepatu
                            </option>
                            <option value="other" {{ old('category', $rentalItem->category) == 'other' ? 'selected' : '' }}>
                                Lainnya
                            </option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga Sewa</label>
                        <input type="number" name="rental_price" class="form-control @error('rental_price') is-invalid @enderror"
                               value="{{ old('rental_price', $rentalItem->rental_price) }}" required>
                        @error('rental_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Total Stok</label>
                        <input type="number" name="stock_total" class="form-control @error('stock_total') is-invalid @enderror"
                               value="{{ old('stock_total', $rentalItem->stock_total) }}" required>
                        <small class="form-text text-muted">Jumlah total item yang tersedia untuk disewa.</small>
                        @error('stock_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Stok Tersedia</label>
                        <input type="number" name="stock_available" class="form-control @error('stock_available') is-invalid @enderror"
                               value="{{ old('stock_available', $rentalItem->stock_available) }}" required>
                        <small class="form-text text-muted">Jumlah item yang tersedia untuk disewa saat ini.</small>
                        @error('stock_available')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Kondisi</label>
                        <select name="condition" class="form-control @error('condition') is-invalid @enderror">
                            <option value="">Pilih Kondisi Item</option>
                            <option value="Baru" {{ old('condition', $rentalItem->condition) == 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Sangat Baik" {{ old('condition', $rentalItem->condition) == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                            <option value="Baik" {{ old('condition', $rentalItem->condition) == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Cukup Baik" {{ old('condition', $rentalItem->condition) == 'Cukup Baik' ? 'selected' : '' }}>Cukup Baik</option>
                        </select>
                        @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description', $rentalItem->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active"
                                   name="is_active" value="1"
                                   {{ old('is_active', $rentalItem->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                Item Aktif
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.rental-items.index') }}" class="btn btn-secondary">
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
