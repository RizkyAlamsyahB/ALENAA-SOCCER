@extends('layouts.app')
@section('content')
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Detail Voucher</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i>
                                    Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user.points.index') }}">Poin Alena</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Voucher</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('user.points.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Voucher
                    </a>
                </div>

                <!-- Voucher Card -->
                <div class="card border-0 rounded-4 shadow-sm hover-shadow">
                    <!-- Voucher Card Header with Badge -->
                    <div class="card-header bg-white border-0 py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 fw-bold">Detail Voucher</h4>
                            <div class="badge bg-primary bg-opacity-10 text-white p-2">
                                <i class="fas fa-coins me-1"></i> {{ number_format($voucher->points_required) }} Poin
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Voucher Info Box -->
                        <div class="text-center mb-4 p-4 rounded-4" style="background-color: #f8f9fa;">
                            <div class="rounded-circle mx-auto mb-3 p-3 d-inline-flex justify-content-center align-items-center" style="background-color: rgba(158, 6, 32, 0.1); width: 80px; height: 80px;">
                                <i class="fas fa-tag fa-2x" style="color: #9E0620;"></i>
                            </div>

                            <h3 class="card-title fw-bold mb-2">{{ $voucher->name }}</h3>
                            <p class="text-muted mb-3">{{ $voucher->description }}</p>

                            <div class="mb-2">
                                <span class="fs-2 fw-bold text-danger">
                                    @if($voucher->discount_type === 'percentage')
                                    {{ $voucher->discount_value }}% OFF
                                    @else
                                    Rp {{ number_format($voucher->discount_value) }} OFF
                                    @endif
                                </span>
                            </div>

                            @if($voucher->min_order > 0)
                            <div class="badge bg-light text-dark p-2">
                                <i class="fas fa-info-circle me-1"></i> Min. pembelian Rp {{ number_format($voucher->min_order) }}
                            </div>
                            @endif
                        </div>

                        <!-- Detail Voucher -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">Detail Voucher</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%;" class="text-muted border-0">Jenis Diskon</td>
                                            <td class="fw-medium border-0">
                                                @if($voucher->discount_type === 'percentage')
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 p-2" style="background-color: rgba(158, 6, 32, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-percent" style="color: #9E0620;"></i>
                                                    </div>
                                                    Persentase ({{ $voucher->discount_value }}%)
                                                </div>
                                                @else
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 p-2" style="background-color: rgba(158, 6, 32, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-money-bill" style="color: #9E0620;"></i>
                                                    </div>
                                                    Nominal (Rp {{ number_format($voucher->discount_value) }})
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted border-0">Poin Dibutuhkan</td>
                                            <td class="fw-medium border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 p-2" style="background-color: rgba(158, 6, 32, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-coins" style="color: #9E0620;"></i>
                                                    </div>
                                                    {{ number_format($voucher->points_required) }} poin
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted border-0">Minimum Pembelian</td>
                                            <td class="fw-medium border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 p-2" style="background-color: rgba(158, 6, 32, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-cart-shopping" style="color: #9E0620;"></i>
                                                    </div>
                                                    @if($voucher->min_order > 0)
                                                    Rp {{ number_format($voucher->min_order) }}
                                                    @else
                                                    Tidak ada minimum
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted border-0">Maksimum Diskon</td>
                                            <td class="fw-medium border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 p-2" style="background-color: rgba(158, 6, 32, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-tag" style="color: #9E0620;"></i>
                                                    </div>
                                                    @if($voucher->max_discount)
                                                    Rp {{ number_format($voucher->max_discount) }}
                                                    @else
                                                    Tidak ada batas
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted border-0">Berlaku Untuk</td>
                                            <td class="fw-medium border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 p-2" style="background-color: rgba(158, 6, 32, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-check-circle" style="color: #9E0620;"></i>
                                                    </div>
                                                    @if($voucher->applicable_to === 'all')
                                                    Semua jenis layanan
                                                    @elseif($voucher->applicable_to === 'field_booking')
                                                    Booking lapangan
                                                    @elseif($voucher->applicable_to === 'rental_item')
                                                    Sewa peralatan
                                                    @else
                                                    {{ ucfirst($voucher->applicable_to) }}
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted border-0">Periode Berlaku</td>
                                            <td class="fw-medium border-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle me-2 p-2" style="background-color: rgba(158, 6, 32, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-calendar-alt" style="color: #9E0620;"></i>
                                                    </div>
                                                    @if($voucher->start_date && $voucher->end_date)
                                                    {{ \Carbon\Carbon::parse($voucher->start_date)->format('d M Y') }} s/d
                                                    {{ \Carbon\Carbon::parse($voucher->end_date)->format('d M Y') }}
                                                    @elseif($voucher->end_date)
                                                    Hingga {{ \Carbon\Carbon::parse($voucher->end_date)->format('d M Y') }}
                                                    @elseif($voucher->start_date)
                                                    Mulai {{ \Carbon\Carbon::parse($voucher->start_date)->format('d M Y') }}
                                                    @else
                                                    Tidak ada batas waktu
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Ketentuan dan Syarat -->
                        <div class="p-4 rounded-4" style="background-color: #f8f9fa;">
                            <h5 class="fw-bold mb-3">Syarat dan Ketentuan</h5>
                            <ul class="ps-3 mb-0">
                                <li class="mb-2">
                                    Voucher dapat ditukarkan dengan {{ number_format($voucher->points_required) }} poin
                                </li>
                                @if($voucher->min_order > 0)
                                <li class="mb-2">
                                    Minimum pembelian Rp {{ number_format($voucher->min_order) }}
                                </li>
                                @endif

                                @if($voucher->max_discount)
                                <li class="mb-2">
                                    Maksimum diskon Rp {{ number_format($voucher->max_discount) }}
                                </li>
                                @endif

                                @if($voucher->applicable_to !== 'all')
                                <li class="mb-2">
                                    Hanya berlaku untuk
                                    @if($voucher->applicable_to === 'field_booking')
                                    booking lapangan
                                    @elseif($voucher->applicable_to === 'rental_item')
                                    sewa peralatan
                                    @else
                                    {{ $voucher->applicable_to }}
                                    @endif
                                </li>
                                @endif

                                @if($voucher->end_date)
                                <li class="mb-2">
                                    Voucher berlaku hingga {{ \Carbon\Carbon::parse($voucher->end_date)->format('d M Y') }}
                                </li>
                                @endif

                                <li class="mb-2">
                                    Voucher hanya dapat digunakan sekali
                                </li>
                                <li class="mb-2">
                                    Voucher tidak dapat digabung dengan promo lainnya
                                </li>
                                <li class="mb-2">
                                    Poin yang sudah ditukarkan tidak dapat dikembalikan
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-footer bg-white p-4 border-top-0">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="d-block text-muted small">Poin Anda</span>
                                        <span class="fs-5 fw-bold text-danger">{{ number_format($user->points) }}</span>
                                    </div>
                                    <div>
                                        @if($user->points >= $voucher->points_required)
                                        <div class="badge bg-success bg-opacity-10 text-success p-2">
                                            <i class="fas fa-check-circle me-1"></i>Cukup
                                        </div>
                                        @else
                                        <div class="badge bg-danger bg-opacity-10 text-danger p-2">
                                            <i class="fas fa-times-circle me-1"></i>Tidak Cukup
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-grid">
                                    @if($user->points >= $voucher->points_required)
                                    <form action="{{ route('user.points.redeem', $voucher->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary rounded-pill w-100" style="background: linear-gradient(135deg, #d00f25 0%, #9e0620 100%); border: none;">
                                            Tukarkan Sekarang <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-secondary rounded-pill w-100" disabled>
                                        Poin Tidak Cukup <i class="fas fa-lock ms-2"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

        /* Badge Styling */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Text Colors */
        .text-danger {
            color: #9E0620 !important;
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

        .rounded-4 {
            border-radius: 0.75rem !important;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }
    </style>
@endsection
