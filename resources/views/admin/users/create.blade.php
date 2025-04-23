@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')
@section('breadcrumb', 'Tambah Pengguna')
@section('header-title', 'Tambah Pengguna Baru')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Form Tambah Pengguna</h4>

                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                <small class="form-text text-muted">Minimal 8 karakter</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Customer</option>
                                    <option value="photographer" {{ old('role') == 'photographer' ? 'selected' : '' }}>Fotografer</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
                                    value="{{ old('phone_number') }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Alamat <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                    rows="3" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror"
                                    value="{{ old('birthdate') }}" required>
                                @error('birthdate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Points</label>
                                <input type="number" name="points" class="form-control @error('points') is-invalid @enderror"
                                    value="{{ old('points', 0) }}">
                                <small class="form-text text-muted">Points reward untuk user</small>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="profile_picture">Foto Profil</label>
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror"
                                    accept="image/*" onchange="previewImage(event)">
                                <small class="form-text text-muted">Format: JPG, PNG, GIF (Maks. 2MB)</small>
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <img id="imagePreview" src="#" alt="Preview Gambar" style="display: none; max-width: 200px; height: auto;" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Field Assignment Section (only shown for photographers) -->
<div class="col-md-12" id="fieldAssignmentSection" style="display: none;">
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Assign Fotografer ke Lapangan</h5>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label>Pilih Lapangan <span class="text-danger">*</span></label>
                <select name="field_id" class="form-control @error('field_id') is-invalid @enderror" id="fieldSelect">
                    <option value="">-- Pilih Lapangan --</option>
                    @foreach($fields as $field)
                        <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>
                            {{ $field->name }} ({{ $field->type }})
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Pilih lapangan untuk fotografer ini (1 fotografer hanya bisa diassign ke 1 lapangan)</small>
                @error('field_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

                    <div class="form-group mb-3 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Pengguna
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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
</script>

@endsection
