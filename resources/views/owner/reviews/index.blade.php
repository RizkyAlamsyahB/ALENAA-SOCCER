@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Review</h3>
                <p class="text-subtitle text-muted">Kelola semua review dari pelanggan dalam satu tempat.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Review</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Review</h4>
                <a href="{{ route('owner.reviews.summary') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-bar-chart"></i> Ringkasan Review
                </a>
            </div>
            <div class="card-body rounded-4">
                <div class="table-responsive">
                    <table id="reviewsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pengguna</th>
                                <th>Item</th>
                                <th>Tipe</th>
                                <th>Pembayaran</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Diperbarui</th>
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

            var table = $('#reviewsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('owner.reviews.index') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'item_type_label',
                        name: 'item_type_label'
                    },
                    {
                        data: 'payment_info',
                        name: 'payment_info'
                    },
                    {
                        data: 'rating_stars',
                        name: 'rating_stars'
                    },
                    {
                        data: 'status',
                        name: 'status'
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

            // Toggle status (aktif/nonaktif)
            $(document).on('click', '.toggle-btn', function() {
                var reviewId = $(this).data('id');
                var currentStatus = $(this).data('status');
                var $button = $(this);

                $.ajax({
                    url: '{{ url('owner/reviews') }}/' + reviewId + '/toggle-status',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'Berhasil');

                            // Update button text, class, and data-status
                            $button.text(response.new_button_text);
                            $button.removeClass('btn-success btn-warning').addClass('btn-' + response.new_button_class);
                            $button.data('status', response.new_status);

                            // Reload table to refresh status column
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan. Silakan coba lagi.', 'Error');
                    }
                });
            });

            // Konfirmasi hapus menggunakan Toastr dengan tombol interaktif
            $(document).on('click', '.delete-btn', function() {
                var reviewId = $(this).data('id');

                toastr.warning(
                    `<div>
                        <p>Apakah Anda yakin ingin menghapus review ini?</p>
                        <button class="btn btn-danger btn-sm" id="confirmDelete" data-id="${reviewId}" style="margin-right:10px;">Ya, Hapus!</button>
                        <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                    </div>`,
                    'Konfirmasi Hapus', {
                        closeButton: true,
                        onShown: function() {
                            // Event listener untuk tombol hapus
                            $('#confirmDelete').on('click', function() {
                                var id = $(this).data('id');
                                hapusReview(id);
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
            function hapusReview(reviewId) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('owner.reviews.destroy', '') }}/' + reviewId;
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
