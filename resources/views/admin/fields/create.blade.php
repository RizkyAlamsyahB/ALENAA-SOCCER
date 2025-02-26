@extends('layouts.admin')

@section('title', 'Tambah Lapangan Baru')
@section('breadcrumb', 'Tambah Lapangan')
@section('header-title', 'Tambah Lapangan Baru')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Form Tambah Lapangan</h4>

                <form action="{{ route('admin.fields.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Nama Lapangan</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar Lapangan</label>
                        <input type="file" name="image" class="form-control-file">
                    </div>

                    <div class="form-group">
                        <label>Tipe Lapangan</label>
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">Pilih Tipe Lapangan</option>
                            <option value="Matras Standar" {{ old('type') == 'Matras Standar' ? 'selected' : '' }}>
                                Matras Standar
                            </option>
                            <option value="Rumput Sintetis" {{ old('type') == 'Rumput Sintetis' ? 'selected' : '' }}>
                                Rumput Sintetis
                            </option>
                            <option value="Matras Premium" {{ old('type') == 'Matras Premium' ? 'selected' : '' }}>
                                Matras Premium
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga Regular</label>
                        <input type="number" name="regular_price" class="form-control @error('regular_price') is-invalid @enderror"
                               value="{{ old('regular_price') }}" required>
                        @error('regular_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga Peak (Opsional)</label>
                        <input type="number" name="peak_price" class="form-control @error('peak_price') is-invalid @enderror"
                               value="{{ old('peak_price') }}">
                        @error('peak_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Fasilitas</label>
                        <textarea name="facilities" class="form-control @error('facilities') is-invalid @enderror"
                                  rows="3">{{ old('facilities') }}</textarea>
                        @error('facilities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active"
                                   name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                Lapangan Aktif
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Lapangan
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
@endsection
