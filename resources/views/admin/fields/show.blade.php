@extends('layouts.admin')

@section('title', 'Detail Lapangan')
@section('breadcrumb', 'Detail Lapangan')
@section('header-title', 'Informasi Detail Lapangan')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Detail Lapangan</h4>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Nama Lapangan:</strong>
                        <p>{{ $field->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipe Lapangan:</strong>
                        <p>{{ $field->type }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Harga Regular:</strong>
                        <p>Rp. {{ number_format($field->regular_price, 0, ',', '.') }}/jam</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Harga Peak:</strong>
                        <p>
                            {{ $field->peak_price ? 'Rp. ' . number_format($field->peak_price, 0, ',', '.') . '/jam' : 'Tidak ada harga peak' }}
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Fasilitas:</strong>
                        <p>{{ $field->facilities ?? 'Tidak ada informasi fasilitas' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>
                            <span class="badge {{ $field->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $field->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Terakhir Diperbarui:</strong>
                        <p>{{ $field->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <a href="{{ route('admin.fields.edit', $field->id) }}" class="btn btn-warning">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
