@extends('layouts.admin')

@section('title', 'Lapangan Management')
@section('breadcrumb', 'Lapangan Management')
@section('header-title', 'Manage Lapangan')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">List of Lapangan</h4>
                    <a href="{{ route('admin.fields.create') }}" class="btn btn-success mt-2 mb-3">
                        <i class="fas fa-plus"></i> Tambah Lapangan
                    </a>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-striped table-bordered table-responsive-sm mt-2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Gambar</th>
                                <th>Tipe</th>
                                <th>Harga Regular</th>
                                <th>Harga Peak</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fields as $field)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $field->name }}</td>
                                    <td>
                                        @if($field->image)
                                            <img src="{{ asset($field->image) }}" alt="Lapangan {{ $field->name }}" width="80">
                                        @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                        @endif
                                    </td>
                                    <td>{{ $field->type }}</td>
                                    <td>Rp {{ number_format($field->regular_price, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($field->peak_price)
                                            Rp {{ number_format($field->peak_price, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $field->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $field->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.fields.show', $field->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.fields.edit', $field->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.fields.destroy', $field->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus lapangan ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.fields.toggle-status', $field->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                    class="btn btn-{{ $field->is_active ? 'secondary' : 'success' }} btn-sm">
                                                    {{ $field->is_active ? 'Non-Aktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#lapanganTable').DataTable({
                    responsive: true,
                    language: {
                        paginate: {
                            previous: '<i class="fas fa-chevron-left"></i>',
                            next: '<i class="fas fa-chevron-right"></i>'
                        }
                    }
                });
            });
        </script>
    @endpush

@endsection
