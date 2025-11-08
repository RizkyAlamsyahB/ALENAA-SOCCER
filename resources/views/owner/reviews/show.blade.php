@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Review</h3>
                <p class="text-subtitle text-muted">Lihat informasi lengkap review dari pelanggan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reviews.index') }}">Data Review</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Review</li>
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
                <h4 class="card-title">Informasi Review #{{ $review->id }}</h4>
                <div class="card-actions">
                    <button type="button"
                        class="btn {{ $review->status === 'active' ? 'btn-warning' : 'btn-success' }} btn-sm toggle-btn"
                        data-id="{{ $review->id }}" data-status="{{ $review->status }}">
                        <i class="bi {{ $review->status === 'active' ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                        {{ $review->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $review->id }}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="review-section mb-4">
                            <h5 class="section-title border-bottom pb-2">Detail Review</h5>
                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Rating</h6>
                                <div class="rating-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-secondary"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2">({{ $review->rating }} dari 5)</span>
                                </div>
                            </div>
                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Komentar</h6>
                                <p class="mb-0">{{ $review->comment ?? 'Tidak ada komentar' }}</p>
                            </div>
                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Status</h6>
                                <p class="mb-0">
                                    @if ($review->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </p>
                            </div>
                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Tanggal Review</h6>
                                <p class="mb-0">{{ $review->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Terakhir Diperbarui</h6>
                                <p class="mb-0">{{ $review->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="review-section mb-4">
                            <h5 class="section-title border-bottom pb-2">Informasi Pengguna</h5>
                            @if ($review->user)
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Nama Pengguna</h6>
                                    <p class="mb-0">{{ $review->user->name }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Email</h6>
                                    <p class="mb-0">{{ $review->user->email }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">No. Telepon</h6>
                                    <p class="mb-0">{{ $review->user->phone_number ?? 'Tidak tersedia' }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Alamat</h6>
                                    <p class="mb-0">{{ $review->user->address ?? 'Tidak tersedia' }}</p>
                                </div>
                            @else
                                <p class="text-muted">Data pengguna tidak ditemukan.</p>
                            @endif
                        </div>

                        <div class="review-section mb-4">
                            <h5 class="section-title border-bottom pb-2">Item yang Direview</h5>
                            @if ($item)
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Tipe Item</h6>
                                    <p class="mb-0">
                                        @php
                                            $itemTypes = [
                                                'App\\Models\\Field' => 'Lapangan',
                                                'App\\Models\\RentalItem' => 'Penyewaan',
                                                'App\\Models\\Photographer' => 'Fotografer',
                                            ];
                                        @endphp
                                        {{ $itemTypes[$review->item_type] ?? $review->item_type }}
                                    </p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Nama Item</h6>
                                    <p class="mb-0">{{ $item->name }}</p>
                                </div>
                                @if ($review->item_type === 'App\\Models\\Field')
                                    <div class="info-group mb-3">
                                        <h6 class="fw-bold">Tipe Lapangan</h6>
                                        <p class="mb-0">{{ $item->type }}</p>
                                    </div>
                                @elseif ($review->item_type === 'App\\Models\\RentalItem')
                                    <div class="info-group mb-3">
                                        <h6 class="fw-bold">Kategori</h6>
                                        <p class="mb-0">{{ $item->category }}</p>
                                    </div>
                                @elseif ($review->item_type === 'App\\Models\\Photographer')
                                    <div class="info-group mb-3">
                                        <h6 class="fw-bold">Paket</h6>
                                        <p class="mb-0">{{ $item->package_type }}</p>
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">Data item tidak ditemukan.</p>
                            @endif
                        </div>

                        <div class="review-section mb-4">
                            <h5 class="section-title border-bottom pb-2">Informasi Pembayaran</h5>
                            @if ($review->payment)
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Order ID</h6>
                                    <p class="mb-0">{{ $review->payment->order_id }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Total Pembayaran</h6>
                                    <p class="mb-0">Rp {{ number_format($review->payment->amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Status Transaksi</h6>
                                    <p class="mb-0">
                                        @if ($review->payment->transaction_status === 'success')
                                            <span class="badge bg-success">Sukses</span>
                                        @elseif($review->payment->transaction_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($review->payment->transaction_status === 'failed')
                                            <span class="badge bg-danger">Gagal</span>
                                        @else
                                            <span
                                                class="badge bg-secondary">{{ $review->payment->transaction_status }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Tanggal Transaksi</h6>
                                    <p class="mb-0">{{ $review->payment->created_at->format('d M Y H:i') }}</p>
                                </div>
                            @else
                                <p class="text-muted">Data pembayaran tidak ditemukan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('owner.reviews.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert@2"></script>
@endsection
