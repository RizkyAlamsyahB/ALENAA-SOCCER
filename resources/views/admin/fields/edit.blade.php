@extends('layouts.admin')

@section('title', 'Edit Lapangan')
@section('breadcrumb', 'Edit Lapangan')
@section('header-title', 'Edit Data Lapangan')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Form Edit Lapangan</h4>

                <form action="{{ route('admin.fields.update', $field->id) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                        <label>Harga Regular</label>
                        <input type="number" name="regular_price" class="form-control @error('regular_price') is-invalid @enderror"
                               value="{{ old('regular_price', $field->regular_price) }}" required>
                        @error('regular_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga Peak (Opsional)</label>
                        <input type="number" name="peak_price" class="form-control @error('peak_price') is-invalid @enderror"
                               value="{{ old('peak_price', $field->peak_price) }}">
                        @error('peak_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Fasilitas</label>
                        <textarea name="facilities" class="form-control @error('facilities') is-invalid @enderror"
                                  rows="3">{{ old('facilities', $field->facilities) }}</textarea>
                        @error('facilities')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active"
                                   name="is_active" value="1"
                                   {{ old('is_active', $field->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                Lapangan Aktif
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Perbarui Lapangan
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
