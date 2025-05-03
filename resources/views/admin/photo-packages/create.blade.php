@extends('layouts.admin')

@section('title', 'Tambah Paket Fotografer')
@section('breadcrumb', 'Tambah Paket Fotografer')
@section('header-title', 'Tambah Paket Fotografer')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Form Tambah Paket Fotografer</h4>

                    <form action="{{ route('admin.photo-packages.store') }}" method="POST" enctype="multipart/form-data"
                        id="photographerForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group mb-3">
                                    <label>Nama Fotografer <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-control @error('user_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Fotografer --</option>
                                        @foreach ($photographers as $photographer)
                                            <option value="{{ $photographer->id }}"
                                                {{ old('user_id') == $photographer->id ? 'selected' : '' }}>
                                                {{ $photographer->name }} ({{ $photographer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Pilih fotografer yang akan menggunakan paket
                                        ini</small>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Nama Paket <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required>
                                    <small class="form-text text-muted">Contoh: Paket Favorite, Paket Plus, Paket
                                        Exclusive</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Deskripsi <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                                    <small class="form-text text-muted">Deskripsi detail tentang paket fotografer</small>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="price"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ old('price') }}" required>
                                    </div>
                                    <small class="form-text text-muted">Harga paket dalam Rupiah</small>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Jenis Paket <span class="text-danger">*</span></label>
                                    <select name="package_type"
                                        class="form-control @error('package_type') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis Paket --</option>
                                        <option value="basic" {{ old('package_type') == 'basic' ? 'selected' : '' }}>Basic
                                        </option>
                                        <option value="favorite" {{ old('package_type') == 'favorite' ? 'selected' : '' }}>
                                            Favorite</option>
                                        <option value="plus" {{ old('package_type') == 'plus' ? 'selected' : '' }}>Plus
                                        </option>
                                        <option value="exclusive"
                                            {{ old('package_type') == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                                    </select>
                                    @error('package_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Durasi (jam) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration"
                                        class="form-control @error('duration') is-invalid @enderror"
                                        value="{{ old('duration', 1) }}" min="1" required>
                                    <small class="form-text text-muted">Durasi layanan fotografer dalam jam</small>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                            Aktif</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak
                                            Aktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="image">Gambar Paket</label>
                                    <input type="file" name="image" id="image"
                                        class="form-control @error('image') is-invalid @enderror" accept="image/*"
                                        onchange="previewImage(event)">
                                    <small class="form-text text-muted">Format: JPG, PNG, GIF (Maks. 2MB)</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <img id="imagePreview" src="#" alt="Preview Gambar"
                                            style="display: none; max-width: 200px; height: auto;" class="img-thumbnail">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Fitur-fitur Paket <span class="text-danger">*</span></label>
                                    <div id="featuresContainer">
                                        @if (old('features'))
                                            @foreach (old('features') as $index => $feature)
                                                <div class="input-group mb-2 feature-item">
                                                    <input type="text" name="features[]"
                                                        class="form-control @if ($errors->has('features.' . $index)) is-invalid @endif"
                                                        value="{{ $feature }}" placeholder="Masukkan fitur paket">
                                                    <button type="button" class="btn btn-danger remove-feature"><i
                                                            class="bi bi-trash"></i></button>
                                                    @if ($errors->has('features.' . $index))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('features.' . $index) }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2 feature-item">
                                                <input type="text" name="features[]" class="form-control"
                                                    placeholder="Masukkan fitur paket" value="1 Fotografer">
                                                <button type="button" class="btn btn-danger remove-feature"><i
                                                        class="bi bi-trash"></i></button>
                                            </div>
                                            <div class="input-group mb-2 feature-item">
                                                <input type="text" name="features[]" class="form-control"
                                                    placeholder="Masukkan fitur paket" value="1 Kamera Mirrorless/DSLR">
                                                <button type="button" class="btn btn-danger remove-feature"><i
                                                        class="bi bi-trash"></i></button>
                                            </div>
                                            <div class="input-group mb-2 feature-item">
                                                <input type="text" name="features[]" class="form-control"
                                                    placeholder="Masukkan fitur paket" value="Unlimited Photo">
                                                <button type="button" class="btn btn-danger remove-feature"><i
                                                        class="bi bi-trash"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success mt-2" id="addFeature">
                                        <i class="bi bi-plus"></i> Tambah Fitur
                                    </button>
                                    @error('features')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Lapangan</label>
                                <select name="field_id" class="form-control @error('field_id') is-invalid @enderror">
                                    <option value="">-- Pilih Lapangan (Opsional) --</option>
                                    @foreach ($fields as $field)
                                        <option value="{{ $field->id }}"
                                            {{ old('field_id') == $field->id ? 'selected' : '' }}>
                                            {{ $field->name }} ({{ $field->type }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih lapangan jika paket fotografer spesifik untuk
                                    lapangan tertentu</small>
                                @error('field_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Paket
                            </button>
                            <a href="{{ route('admin.photo-packages.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
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

        document.addEventListener('DOMContentLoaded', function() {
            // Add feature
            document.getElementById('addFeature').addEventListener('click', function() {
                var container = document.getElementById('featuresContainer');
                var newItem = document.createElement('div');
                newItem.className = 'input-group mb-2 feature-item';
                newItem.innerHTML = `
            <input type="text" name="features[]" class="form-control" placeholder="Masukkan fitur paket">
            <button type="button" class="btn btn-danger remove-feature"><i class="bi bi-trash"></i></button>
        `;
                container.appendChild(newItem);

                // Add event listener to the new remove button
                newItem.querySelector('.remove-feature').addEventListener('click', function() {
                    container.removeChild(newItem);
                });
            });

            // Remove feature (for existing items)
            document.querySelectorAll('.remove-feature').forEach(function(button) {
                button.addEventListener('click', function() {
                    var container = document.getElementById('featuresContainer');
                    var item = this.closest('.feature-item');

                    // Don't remove if it's the last item
                    if (container.querySelectorAll('.feature-item').length > 1) {
                        container.removeChild(item);
                    } else {
                        alert('Minimal harus ada 1 fitur');
                    }
                });
            });

            // Form validation
            document.getElementById('photographerForm').addEventListener('submit', function(e) {
                var features = document.querySelectorAll('input[name="features[]"]');
                var valid = true;

                features.forEach(function(feature) {
                    if (feature.value.trim() === '') {
                        valid = false;
                        feature.classList.add('is-invalid');
                    } else {
                        feature.classList.remove('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Semua fitur harus diisi');
                }
            });
        });
    </script>
@endsection
