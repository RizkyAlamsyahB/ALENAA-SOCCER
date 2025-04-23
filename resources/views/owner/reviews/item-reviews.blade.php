@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Review untuk {{ $item->name }}</h3>
                <p class="text-subtitle text-muted">Daftar semua review pelanggan untuk {{ $itemType === 'field' ? 'lapangan' : ($itemType === 'rental_item' ? 'penyewaan' : 'fotografer') }} ini.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reviews.index') }}">Data Review</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Review {{ $item->name }}</li>
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
                <h4 class="card-title">Informasi {{ $itemType === 'field' ? 'Lapangan' : ($itemType === 'rental_item' ? 'Penyewaan' : 'Fotografer') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid rounded mb-3">
                        @else
                            <div class="no-image bg-light text-center p-5 rounded mb-3">
                                <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                <p class="mt-2">Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="item-info">
                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Nama</h6>
                                <p class="mb-0">{{ $item->name }}</p>
                            </div>

                            @if ($itemType === 'field')
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Tipe Lapangan</h6>
                                    <p class="mb-0">{{ $item->type }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Harga</h6>
                                    <p class="mb-0">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            @elseif ($itemType === 'rental_item')
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Kategori</h6>
                                    <p class="mb-0">{{ $item->category }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Harga Sewa</h6>
                                    <p class="mb-0">Rp {{ number_format($item->rental_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Stok Tersedia</h6>
                                    <p class="mb-0">{{ $item->stock_available }} dari {{ $item->stock_total }}</p>
                                </div>
                            @elseif ($itemType === 'photographer')
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Paket</h6>
                                    <p class="mb-0">{{ $item->package_type }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Harga</h6>
                                    <p class="mb-0">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <h6 class="fw-bold">Durasi</h6>
                                    <p class="mb-0">{{ $item->duration }} menit</p>
                                </div>
                            @endif

                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Deskripsi</h6>
                                <p class="mb-0">{{ $item->description ?? 'Tidak ada deskripsi' }}</p>
                            </div>

                            <div class="info-group mb-3">
                                <h6 class="fw-bold">Rating Rata-rata</h6>
                                <div class="rating-stars">
                                    @php
                                        $avgRating = $reviews->avg('rating');
                                    @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= round($avgRating))
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-secondary"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2">({{ number_format($avgRating, 1) }} dari 5)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review List -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Daftar Review</h4>
            </div>
            <div class="card-body">
                @forelse ($reviews as $review)
                    <div class="review-item mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="review-header">
                                <h5 class="mb-0">{{ $review->user ? $review->user->name : 'User tidak ditemukan' }}</h5>
                                <div class="rating-stars mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-secondary"></i>
                                        @endif
                                    @endfor
                                    <small class="text-muted ms-2">{{ $review->created_at->format('d M Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="review-actions">
                                <div class="badge {{ $review->status === 'active' ? 'bg-success' : 'bg-danger' }} mb-2">
                                    {{ $review->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('owner.reviews.show', $review->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <button type="button" class="btn btn-sm btn-{{ $review->status === 'active' ? 'warning' : 'success' }} toggle-btn"
                                        data-id="{{ $review->id }}"
                                        data-status="{{ $review->status }}">
                                        {{ $review->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="review-content mt-2">
                            <p class="mb-0">{{ $review->comment ?? 'Tidak ada komentar' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-frown text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Belum Ada Review</h5>
                        <p class="text-muted">Belum ada review yang diberikan untuk {{ $itemType === 'field' ? 'lapangan' : ($itemType === 'rental_item' ? 'penyewaan' : 'fotografer') }} ini.</p>
                    </div>
                @endforelse

                <div class="d-flex justify-content-center mt-4">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>

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

                            // Update badge
                            $button.closest('.review-actions').find('.badge')
                                .removeClass('bg-success bg-danger')
                                .addClass(response.new_status === 'active' ? 'bg-success' : 'bg-danger')
                                .text(response.new_status_label);
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan. Silakan coba lagi.', 'Error');
                    }
                });
            });
        });
    </script>
@endsection
