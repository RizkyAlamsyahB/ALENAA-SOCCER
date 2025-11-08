@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Voucher Poin</h3>
                <p class="text-subtitle text-muted">Lihat informasi lengkap voucher poin.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.point_vouchers.index') }}">Data Voucher Poin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Voucher Poin</li>
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
                <h4 class="card-title">Informasi Voucher Poin</h4>
                <div class="card-actions">
                    <a href="{{ route('owner.point_vouchers.edit', $pointVoucher->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('toggle-form').submit();" class="btn {{ $pointVoucher->is_active ? 'btn-warning' : 'btn-success' }} btn-sm">
                        <i class="bi {{ $pointVoucher->is_active ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                        {{ $pointVoucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </a>
                    <form id="toggle-form" action="{{ route('owner.point_vouchers.toggle-status', $pointVoucher->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('PATCH')
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Kode Voucher</h6>
                            <p class="mb-0">{{ $pointVoucher->code }}</p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Nama Voucher</h6>
                            <p class="mb-0">{{ $pointVoucher->name }}</p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Tipe Diskon</h6>
                            <p class="mb-0">
                                {{ $pointVoucher->discount_type == 'percentage' ? 'Persentase (%)' : 'Nominal Tetap (Rp)' }}
                            </p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Nilai Diskon</h6>
                            <p class="mb-0">
                                @if($pointVoucher->discount_type == 'percentage')
                                    {{ $pointVoucher->discount_value }}%
                                @else
                                    Rp {{ number_format($pointVoucher->discount_value, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Poin Dibutuhkan</h6>
                            <p class="mb-0">{{ $pointVoucher->points_required }} poin</p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Minimal Pembelian</h6>
                            <p class="mb-0">Rp {{ number_format($pointVoucher->min_order, 0, ',', '.') }}</p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Maksimum Nilai Diskon</h6>
                            <p class="mb-0">
                                @if($pointVoucher->max_discount)
                                    Rp {{ number_format($pointVoucher->max_discount, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">Tidak ada batas</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Berlaku Untuk</h6>
                            <p class="mb-0">
                                @switch($pointVoucher->applicable_to)
                                    @case('all')
                                        <span class="badge bg-primary">Semua Layanan</span>
                                        @break
                                    @case('field_booking')
                                        <span class="badge bg-success">Booking Lapangan</span>
                                        @break
                                    @case('rental_item')
                                        <span class="badge bg-info">Rental Peralatan</span>
                                        @break
                                    @case('membership')
                                        <span class="badge bg-warning text-dark">Keanggotaan</span>
                                        @break
                                    @case('photographer')
                                        <span class="badge bg-danger">Fotografer</span>
                                        @break
                                    @default
                                        <span class="badge bg-dark">{{ $pointVoucher->applicable_to }}</span>
                                @endswitch
                            </p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Dibuat Oleh</h6>
                            <p class="mb-0">{{ $pointVoucher->createdBy ? $pointVoucher->createdBy->name : 'Tidak Diketahui' }}</p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Status</h6>
                            <p class="mb-0">
                                @if($pointVoucher->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Periode</h6>
                            <p class="mb-0">
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $startDate = $pointVoucher->start_date ? \Carbon\Carbon::parse($pointVoucher->start_date) : null;
                                    $endDate = $pointVoucher->end_date ? \Carbon\Carbon::parse($pointVoucher->end_date) : null;
                                @endphp

                                @if(!$startDate || !$endDate)
                                    <span class="badge bg-secondary">Tidak Ada Periode</span>
                                @elseif($now < $startDate)
                                    <span class="badge bg-warning text-dark">Belum Mulai</span>
                                @elseif($now > $endDate)
                                    <span class="badge bg-danger">Kedaluwarsa</span>
                                @else
                                    <span class="badge bg-success">Sedang Berlangsung</span>
                                @endif
                            </p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Tanggal Mulai</h6>
                            <p class="mb-0">
                                {{ $pointVoucher->start_date ? \Carbon\Carbon::parse($pointVoucher->start_date)->format('d M Y H:i') : 'Tidak Ada' }}
                            </p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Tanggal Berakhir</h6>
                            <p class="mb-0">
                                {{ $pointVoucher->end_date ? \Carbon\Carbon::parse($pointVoucher->end_date)->format('d M Y H:i') : 'Tidak Ada' }}
                            </p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Batas Penggunaan Keseluruhan</h6>
                            <p class="mb-0">
                                {{ $pointVoucher->usage_limit ? $pointVoucher->usage_limit : 'Tidak Terbatas' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Deskripsi</h6>
                            <p class="mb-0">{{ $pointVoucher->description ?? 'Tidak ada deskripsi' }}</p>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Tanggal Dibuat</h6>
                            <p class="mb-0">{{ $pointVoucher->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="info-group mb-3">
                            <h6 class="fw-bold">Terakhir Diperbarui</h6>
                            <p class="mb-0">{{ $pointVoucher->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('owner.point_vouchers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $pointVoucher->id }}" data-name="{{ $pointVoucher->name }}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>

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

            // Konfirmasi hapus menggunakan Toastr dengan tombol interaktif
            $('.delete-btn').on('click', function() {
                var voucherId = $(this).data('id');
                var voucherName = $(this).data('name');

                toastr.warning(
                    `<div>
                        <p>Apakah Anda yakin ingin menghapus voucher poin "<b>${voucherName}</b>"?</p>
                        <button class="btn btn-danger btn-sm" id="confirmDelete" data-id="${voucherId}" style="margin-right:10px;">Ya, Hapus!</button>
                        <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                    </div>`,
                    'Konfirmasi Hapus', {
                        closeButton: true,
                        onShown: function() {
                            // Event listener untuk tombol hapus
                            $('#confirmDelete').on('click', function() {
                                var id = $(this).data('id');
                                hapusVoucher(id);
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
            function hapusVoucher(voucherId) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('owner.point_vouchers.destroy', '') }}/' + voucherId;
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
