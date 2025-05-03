@extends('layouts.admin')

@section('title', 'Edit Paket Membership')
@section('breadcrumb', 'Edit Paket Membership')
@section('header-title', 'Edit Paket Membership')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Form Edit Paket Membership</h4>

                    <form action="{{ route('admin.memberships.update', $membership->id) }}" method="POST" enctype="multipart/form-data"
                        id="membershipForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Lapangan <span class="text-danger">*</span></label>
                                    <select name="field_id" class="form-control @error('field_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Lapangan --</option>
                                        @foreach ($fields as $field)
                                            <option value="{{ $field->id }}"
                                                {{ old('field_id', $membership->field_id) == $field->id ? 'selected' : '' }}>
                                                {{ $field->name }} ({{ $field->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Pilih lapangan untuk paket membership ini</small>
                                    @error('field_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Nama Paket <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $membership->name) }}"
                                        required>
                                    <small class="form-text text-muted">Contoh: Bronze, Silver, Gold</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Tipe Paket <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tipe Paket --</option>
                                        <option value="bronze" {{ old('type', $membership->type) == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                        <option value="silver" {{ old('type', $membership->type) == 'silver' ? 'selected' : '' }}>Silver</option>
                                        <option value="gold" {{ old('type', $membership->type) == 'gold' ? 'selected' : '' }}>Gold</option>
                                        <option value="platinum" {{ old('type', $membership->type) == 'platinum' ? 'selected' : '' }}>Platinum</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Harga per Minggu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="price"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ old('price', $membership->price) }}" required>
                                    </div>
                                    <small class="form-text text-muted">Harga paket per minggu dalam Rupiah</small>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Deskripsi <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $membership->description) }}</textarea>
                                    <small class="form-text text-muted">Deskripsi detail tentang paket membership</small>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Jumlah Sesi per Minggu <span class="text-danger">*</span></label>
                                    <input type="number" name="sessions_per_week"
                                        class="form-control @error('sessions_per_week') is-invalid @enderror"
                                        value="{{ old('sessions_per_week', $membership->sessions_per_week) }}" min="1" required>
                                    <small class="form-text text-muted">Jumlah sesi permainan dalam satu minggu</small>
                                    @error('sessions_per_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Durasi per Sesi (jam) <span class="text-danger">*</span></label>
                                    <input type="number" name="session_duration"
                                        class="form-control @error('session_duration') is-invalid @enderror"
                                        value="{{ old('session_duration', $membership->session_duration) }}" min="1" required>
                                    <small class="form-text text-muted">Durasi setiap sesi dalam jam</small>
                                    @error('session_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        <option value="active" {{ old('status', $membership->status) == 'active' ? 'selected' : '' }}>
                                            Aktif</option>
                                        <option value="inactive" {{ old('status', $membership->status) == 'inactive' ? 'selected' : '' }}>Tidak
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
                                        @if($membership->image)
                                            <img id="imagePreview" src="{{ asset('storage/' . $membership->image) }}"
                                                alt="Preview Gambar" class="img-thumbnail" style="max-width: 200px; height: auto;">
                                        @else
                                            <img id="imagePreview" src="#" alt="Preview Gambar"
                                                style="display: none; max-width: 200px; height: auto;" class="img-thumbnail">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="includes_photographer"
                                                id="includes_photographer" value="1"
                                                {{ old('includes_photographer', $membership->includes_photographer) ? 'checked' : '' }}
                                                onchange="togglePhotographerOptions()">
                                            <label class="form-check-label" for="includes_photographer">
                                                <strong>Termasuk Fotografer</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body" id="photographer_options">
                                        <div class="form-group mb-3">
                                            <label>Pilih Paket Fotografer</label>
                                            <select name="photographer_id"
                                                class="form-control @error('photographer_id') is-invalid @enderror">
                                                <option value="">-- Pilih Paket Fotografer --</option>
                                                @foreach ($photographers as $photographer)
                                                    <option value="{{ $photographer->id }}"
                                                        {{ old('photographer_id', $membership->photographer_id) == $photographer->id ? 'selected' : '' }}>
                                                        {{ $photographer->name }} ({{ ucfirst($photographer->package_type) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('photographer_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>Total Jam Fotografer</label>
                                            <input type="number" name="photographer_duration"
                                                class="form-control @error('photographer_duration') is-invalid @enderror"
                                                value="{{ old('photographer_duration', $membership->photographer_duration) }}" min="1">
                                            <small class="form-text text-muted">Total jam layanan fotografer untuk seluruh paket</small>
                                            @error('photographer_duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="includes_rental_item"
                                                id="includes_rental_item" value="1"
                                                {{ old('includes_rental_item', $membership->includes_rental_item) ? 'checked' : '' }}
                                                onchange="toggleRentalItemOptions()">
                                            <label class="form-check-label" for="includes_rental_item">
                                                <strong>Termasuk Barang Sewaan</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body" id="rental_item_options">
                                        <div class="form-group mb-3">
                                            <label>Pilih Barang</label>
                                            <select name="rental_item_id"
                                                class="form-control @error('rental_item_id') is-invalid @enderror">
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach ($rentalItems as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('rental_item_id', $membership->rental_item_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }} (Rp {{ number_format($item->rental_price, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('rental_item_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>Jumlah Barang</label>
                                            <input type="number" name="rental_item_quantity"
                                                class="form-control @error('rental_item_quantity') is-invalid @enderror"
                                                value="{{ old('rental_item_quantity', $membership->rental_item_quantity) }}" min="1">
                                            <small class="form-text text-muted">Jumlah barang yang termasuk dalam paket</small>
                                            @error('rental_item_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.memberships.index') }}" class="btn btn-secondary">
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

        function togglePhotographerOptions() {
            var checkbox = document.getElementById('includes_photographer');
            var options = document.getElementById('photographer_options');

            if (checkbox.checked) {
                options.style.display = 'block';
            } else {
                options.style.display = 'none';
            }
        }

        function toggleRentalItemOptions() {
            var checkbox = document.getElementById('includes_rental_item');
            var options = document.getElementById('rental_item_options');

            if (checkbox.checked) {
                options.style.display = 'block';
            } else {
                options.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            togglePhotographerOptions();
            toggleRentalItemOptions();
        });
    </script>
@endsection
