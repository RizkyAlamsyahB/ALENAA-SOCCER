@extends('layouts.admin')

@section('page-title')
<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Detail Lapangan</h3>
            <p class="text-subtitle text-muted">Informasi lengkap tentang lapangan.</p>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.fields.index') }}">Lapangan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card rounded-4">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Nama</th>
                    <td>{{ $field->name }}</td>
                </tr>
                <tr>
                    <th>Tipe</th>
                    <td>{{ $field->type }}</td>
                </tr>
                <tr>
                    <th>Harga Normal</th>
                    <td>Rp {{ number_format($field->price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Gambar</th>
                    <td>
                        @if($field->image)
                            <img src="{{ asset('storage/' . $field->image) }}" width="200" class="img-thumbnail">
                        @else
                            Tidak ada gambar
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $field->created_at }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Pada</th>
                    <td>{{ $field->updated_at }}</td>
                </tr>
            </table>
            <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary rounded-3">Kembali</a>
        </div>
    </div>
</div>
@endsection
