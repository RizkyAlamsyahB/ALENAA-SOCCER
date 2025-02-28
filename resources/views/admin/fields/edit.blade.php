@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Lapangan</h3>
                <p class="text-subtitle text-muted">Perbarui data lapangan yang sudah ada.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.fields.index') }}">Lapangan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                <form action="{{ route('admin.fields.update', $field->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $field->name }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe</label>
                        <input type="text" name="type" id="type" class="form-control" value="{{ $field->type }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga </label>
                        <input type="number" name="price" id="price" class="form-control"
                            value="{{ $field->price }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img id="imagePreview"
                                src="{{ $field->image ? asset('storage/' . $field->image) : 'https://via.placeholder.com/150' }}"
                                width="150" class="img-thumbnail">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-3">Simpan Perubahan</button>
                    <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('imagePreview').src = reader.result;
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    </script>
@endsection
