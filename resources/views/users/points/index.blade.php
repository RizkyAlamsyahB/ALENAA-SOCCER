@extends('layouts.app')
@section('content')
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.js">
    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Poin Alena</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i>
                                    Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Poin Alena</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Points Balance Card -->
        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-5">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="fw-bold text-dark mb-1">Poin Alena Anda</h3>
                        <p class="text-muted mb-0">Tukarkan poin Anda dengan berbagai promo menarik</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <div class="me-3">
                                <span class="d-block text-muted fs-6">Poin Anda</span>
                                <span class="fs-2 fw-bold text-danger">{{ number_format($user->points) }}</span>
                            </div>
                            <i class="fas fa-coins fa-2x" style="color: #FFD700;"></i>
                        </div>
                    </div>
                </div>

                <div class="progress mt-4" style="height: 12px; border-radius: 10px; overflow: hidden;">
                    <div class="progress-bar" role="progressbar"
                        style="width: {{ min(100, ($user->points / 1000) * 100) }}%; background: linear-gradient(135deg, #d00f25 0%, #9e0620 100%);"
                        aria-valuenow="{{ $user->points }}" aria-valuemin="0" aria-valuemax="1000">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">0 poin</small>
                    <small class="text-muted">1000 poin</small>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('user.points.history') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="fas fa-history me-2"></i> Riwayat Poin
                    </a>
                    <a href="{{ route('user.payment.history') }}" class="btn btn-primary rounded-pill" style="background: linear-gradient(135deg, #d00f25 0%, #9e0620 100%); border: none;">
                        <i class="fas fa-shopping-cart me-2"></i> Belanja Lagi
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Vouchers Section -->
        @if($activeRedemptions->count() > 0)
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Voucher Aktif Anda</h4>
            </div>

            <div class="row g-4">
                @foreach($activeRedemptions as $redemption)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow h-100">
                        <!-- Status Badge -->
                        <div class="position-absolute" style="top: 15px; right: 15px; z-index: 10;">
                            @if($redemption->status === 'used')
                                <div class="badge bg-success bg-opacity-10 text-success p-2">
                                    <i class="fas fa-check-circle me-1"></i>Digunakan
                                </div>
                            @elseif($redemption->expires_at && \Carbon\Carbon::parse($redemption->expires_at)->isPast())
                                <div class="badge bg-danger bg-opacity-10 text-danger p-2">
                                    <i class="fas fa-times-circle me-1"></i>Kadaluarsa
                                </div>
                            @else
                                <div class="badge bg-primary bg-opacity-10 text-white p-2">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </div>
                            @endif
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-3 me-3" style="background-color: rgba(158, 6, 32, 0.1);">
                                    <i class="fas fa-ticket-alt" style="color: #9E0620;"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold">{{ $redemption->pointVoucher->name }}</h5>
                            </div>

                            <div class="mb-3">
                                <span class="d-block fw-bold fs-5 text-danger">
                                    @if($redemption->pointVoucher->discount_type === 'percentage')
                                    {{ $redemption->pointVoucher->discount_value }}% OFF
                                    @else
                                    Rp {{ number_format($redemption->pointVoucher->discount_value) }} OFF
                                    @endif
                                </span>

                                <span class="text-muted small">
                                    @if($redemption->pointVoucher->min_order > 0)
                                    Min. order Rp {{ number_format($redemption->pointVoucher->min_order) }}
                                    @endif
                                </span>
                            </div>

                            <div class="py-3 px-3 bg-light rounded-3 mb-3">
                                <span class="d-block text-muted small">Kode Voucher:</span>
                                <div class="d-flex justify-content-between align-items-center">
                                    <code class="fs-6 fw-bold">{{ $redemption->discount_code }}</code>
                                    <button class="btn btn-sm copy-btn" data-clipboard-text="{{ $redemption->discount_code }}" title="Salin kode">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            @if($redemption->expires_at)
                            <div class="small text-muted mb-3">
                                <i class="far fa-clock me-1"></i> Berlaku hingga: {{ \Carbon\Carbon::parse($redemption->expires_at)->format('d M Y') }}
                            </div>
                            @endif

                            <a href="{{ route('user.points.redemption-detail', $redemption->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill w-100 mt-2">
                                Lihat Detail <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Available Vouchers Section -->
        <div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Voucher Tersedia</h4>
            </div>

            @if($vouchers->count() > 0)
            <div class="row g-4">
                @foreach($vouchers as $voucher)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-3 me-3" style="background-color: rgba(158, 6, 32, 0.1);">
                                    <i class="fas fa-tag" style="color: #9E0620;"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 fw-bold">{{ $voucher->name }}</h5>
                                    <span class="badge {{ $user->points >= $voucher->points_required ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} p-2 mt-1">
                                        <i class="fas fa-coins me-1"></i>{{ number_format($voucher->points_required) }} Poin
                                    </span>
                                </div>
                            </div>

                            <p class="card-text small">{{ $voucher->description }}</p>

                            <div class="mb-3">
                                <span class="d-block fw-bold fs-5 text-danger">
                                    @if($voucher->discount_type === 'percentage')
                                    {{ $voucher->discount_value }}% OFF
                                    @else
                                    Rp {{ number_format($voucher->discount_value) }} OFF
                                    @endif
                                </span>

                                @if($voucher->min_order > 0)
                                <span class="text-muted small d-block">
                                    <i class="fas fa-info-circle me-1"></i>Min. order Rp {{ number_format($voucher->min_order) }}
                                </span>
                                @endif

                                @if($voucher->max_discount)
                                <span class="text-muted small d-block">
                                    <i class="fas fa-info-circle me-1"></i>Maks. diskon Rp {{ number_format($voucher->max_discount) }}
                                </span>
                                @endif
                            </div>

                            @if($voucher->end_date)
                            <div class="small text-muted mb-3">
                                <i class="far fa-clock me-1"></i> Berakhir: {{ \Carbon\Carbon::parse($voucher->end_date)->format('d M Y') }}
                            </div>
                            @endif

                            <div class="mt-3">
                                @if($user->points >= $voucher->points_required)
                                <form action="{{ route('user.points.redeem', $voucher->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary rounded-pill w-100" style="background: linear-gradient(135deg, #d00f25 0%, #9e0620 100%); border: none;">
                                        Tukarkan Sekarang <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-secondary rounded-pill w-100 mb-2" disabled>
                                    Poin Tidak Cukup <i class="fas fa-lock ms-2"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <img src="{{ asset('assets/images/empty-state.svg') }}" alt="No vouchers" style="max-width: 200px;">
                </div>
                <p class="text-muted mb-4">Belum ada voucher yang tersedia saat ini.</p>
                <a href="{{ route('user.fields.index') }}" class="btn btn-primary rounded-pill" style="background: linear-gradient(135deg, #d00f25 0%, #9e0620 100%); border: none;">
                    Mulai Booking <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            @endif
        </div>
    </div>

    <style>
        /* Hero Section */
        .hero-section {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            height: 220px;
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-content {
            color: white;
            text-align: center;
            width: 100%;
        }

        .hero-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .breadcrumb-wrapper {
            display: flex;
            justify-content: center;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            display: inline-flex;
            margin-bottom: 0;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .breadcrumb-item.active {
            color: white;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Card Styling */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Button Styling */
        .btn-primary {
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #9e0620 !important;
            color: white;
        }

        .btn-outline-secondary {
            border-color: #dee2e6;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #343a40;
            border-color: #dee2e6;
        }

        /* Progress Bar */
        .progress {
            background-color: #f8f9fa;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Copy Button */
        .copy-btn {
            border: none;
            background: transparent;
            color: #6c757d;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background-color: #f8f9fa;
            color: #343a40;
        }

        /* Badge Styling */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-section {
                height: 180px;
            }

            .hero-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }
        }

        /* Font and General Styling */
        body {
            font-family: 'Poppins', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }

        .text-danger {
            color: #9E0620 !important;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }

        .rounded-4 {
            border-radius: 0.75rem !important;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }
    </style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    $(document).ready(function() {
        // Pastikan jQuery sudah dimuat sebelum menginisialisasi tooltip
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        // Inisialisasi tooltip pada semua tombol copy
        $('.copy-btn').tooltip({
            trigger: 'manual',
            placement: 'top'
        });

        // Inisialisasi clipboard.js
        var clipboard = new ClipboardJS('.copy-btn');

        clipboard.on('success', function(e) {
            // Mengubah teks tooltip menjadi 'Tersalin!'
            $(e.trigger).attr('data-original-title', 'Tersalin!').tooltip('show');

            // Menghilangkan tooltip setelah 1 detik
            setTimeout(function() {
                $(e.trigger).tooltip('hide');
                // Reset tooltip title kembali
                $(e.trigger).attr('data-original-title', 'Salin kode');
            }, 1000);

            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);

            // Menampilkan pesan error
            $(e.trigger).attr('data-original-title', 'Gagal menyalin!').tooltip('show');

            setTimeout(function() {
                $(e.trigger).tooltip('hide');
                $(e.trigger).attr('data-original-title', 'Salin kode');
            }, 1000);
        });
    });
</script>
@endpush
@endsection
