@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Lapangan</h3>
                <p class="text-subtitle text-muted">A sortable, searchable, paginated table without dependencies thanks to
                    simple-datatables.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Lapangan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="card-tools">
                    <a href="{{ route('admin.fields.create') }}" class="btn btn-primary btn-sm rounded-3">
                        Tambah Lapangan Baru
                    </a>
                </div>
            </div>
            <div class="card-body rounded-4">
                <div class="table-responsive">
                    <table id="fieldsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fotografer</th>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Harga</th>
                                <th>Gambar</th>
                                <th>Dibuat Pada</th>
                                <th>Diperbarui Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/static/js/pages/datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert@2"></script>
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configure Toastr options
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right", // Ubah posisi ke bottom-right
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Flash messages
            @if (session('success'))
                toastr.success('{{ session('success') }}', 'Berhasil');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}', 'Error');
            @endif

            @if (session('info'))
                toastr.info('{{ session('info') }}', 'Info');
            @endif

            @if (session('warning'))
                toastr.warning('{{ session('warning') }}', 'Peringatan');
            @endif

            $('#fieldsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.fields.index') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'photographer',
                        name: 'photographer',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'price',
                        name: 'price',
                        render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ')
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: function(data) {
                            return data ? `<img src="/storage/${data}" width="50">` : 'No Image';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Konfirmasi hapus menggunakan Toastr dengan tombol interaktif
            $(document).on('click', '.delete-btn', function() {
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
            // Fungsi untuk menghapus data (versi yang direvisi)
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

                // Kirim form dan biarkan controller mengembalikan flash message
                form.submit();
            }
        });
    </script>
@endsection
