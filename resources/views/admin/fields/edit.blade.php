@extends('layouts.admin')

@section('title', 'Edit Lapangan')
@section('breadcrumb', 'Edit Lapangan')
@section('header-title', 'Edit Lapangan')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Form Edit Lapangan</h4>

                <form action="{{ route('admin.fields.update', $field->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Fotografer Lapangan</label>
                        <select name="photographer_id" class="form-control @error('photographer_id') is-invalid @enderror">
                            <option value="">-- Pilih Fotografer --</option>
                            @foreach($photographers as $photographer)
                                <option value="{{ $photographer->id }}" {{ (old('photographer_id', $field->photographer_id) == $photographer->id) ? 'selected' : '' }}>
                                    {{ $photographer->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Fotografer yang ditugaskan untuk lapangan ini</small>
                        @error('photographer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nama Lapangan</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $field->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Tipe Lapangan</label>
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">Pilih Tipe Lapangan</option>
                            <option value="Matras Standar" {{ old('type', $field->type) == 'Matras Standar' ? 'selected' : '' }}>
                                Matras Standar
                            </option>
                            <option value="Rumput Sintetis" {{ old('type', $field->type) == 'Rumput Sintetis' ? 'selected' : '' }}>
                                Rumput Sintetis
                            </option>
                            <option value="Matras Premium" {{ old('type', $field->type) == 'Matras Premium' ? 'selected' : '' }}>
                                Matras Premium
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga Per Jam</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $field->price) }}" required>
                        <small class="form-text text-muted">Harga sewa lapangan per jam</small>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $field->description) }}</textarea>
                        <small class="form-text text-muted">Deskripsi detail tentang lapangan</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar Lapangan</label>
                        <input type="file" name="image" id="image" class="form-control-file" accept="image/*" onchange="previewImage(event)">
                        <small class="form-text text-muted">Ukuran maksimal 2MB (JPG, PNG, GIF). Biarkan kosong untuk mempertahankan gambar yang ada.</small>

                        <div class="mt-2">
                            @if($field->image)
                                <div class="mb-2">
                                    <p>Gambar saat ini:</p>
                                    <img src="{{ asset('storage/' . $field->image) }}" alt="{{ $field->name }}" class="img-thumbnail" style="max-width: 200px; height: auto;">
                                </div>
                            @endif
                            <img id="imagePreview" src="#" alt="Preview Gambar" style="display: none; max-width: 200px; height: auto;" class="img-thumbnail">
                        </div>
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary">
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
