@extends('layouts.admin')

@section('title', 'Detail Lapangan')
@section('breadcrumb', 'Detail Lapangan')
@section('header-title', 'Detail Lapangan')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Informasi Lapangan</h4>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">ID</th>
                                <td>{{ $field->id }}</td>
                            </tr>
                            <tr>
                                <th>Nama Lapangan</th>
                                <td>{{ $field->name }}</td>
                            </tr>
                            <tr>
                                <th>Tipe Lapangan</th>
                                <td>{{ $field->type }}</td>
                            </tr>
                            <tr>
                                <th>Harga per Jam</th>
                                <td>Rp {{ number_format($field->price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Fotografer</th>
                                <td>
                                    @if($photographer)
                                        <span class="badge bg-info">{{ $photographer->name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $photographer->email }}</small>
                                        <br>
                                        <small class="text-muted">{{ $photographer->phone_number }}</small>
                                    @else
                                        <span class="badge bg-secondary">Tidak ada fotografer</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $field->description ?? 'Tidak ada deskripsi' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ $field->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Diperbarui Pada</th>
                                <td>{{ $field->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Gambar Lapangan</h5>
                            </div>
                            <div class="card-body text-center">
                                @if($field->image)
                                    <img src="{{ asset('storage/' . $field->image) }}" alt="{{ $field->name }}" class="img-fluid rounded" style="max-height: 300px;">
                                @else
                                    <div class="alert alert-info">
                                        Tidak ada gambar
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.fields.edit', $field->id) }}" class="btn btn-warning">
                        <i class="fa fa-edit"></i> Edit Lapangan
                    </a>
                    <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $field->id }}" data-name="{{ $field->name }}">
                        <i class="fa fa-trash"></i> Hapus Lapangan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // Configure Toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Konfirmasi hapus menggunakan Toastr
        $('.delete-btn').on('click', function() {
            var fieldId = $(this).data('id');
            var fieldName = $(this).data('name');

            toastr.warning(
                `<div>
                    <p>Apakah Anda yakin ingin menghapus lapangan "<b>${fieldName}</b>"?</p>
                    <button class="btn btn-danger btn-sm" id="confirmDelete" data-id="${fieldId}" style="margin-right:10px;">Ya, Hapus!</button>
                    <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                </div>`,
                'Konfirmasi Hapus', {
                    closeButton: true,
                    onShown: function() {
                        // Event listener untuk tombol hapus
                        $('#confirmDelete').on('click', function() {
                            var id = $(this).data('id');
                            hapusLapangan(id);
                        });

                        // Event listener untuk tombol batal
                        $('#cancelDelete').on('click', function() {
                            toastr.clear(); // Hilangkan toastr jika dibatalkan
                        });
                    }
                }
            );
        });

        // Fungsi untuk menghapus data
        function hapusLapangan(fieldId) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.fields.destroy', '') }}/' + fieldId;
            form.style.display = 'none';

            var csrfToken = document.createElement('input');
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            var methodField = document.createElement('input');
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
@endsection
