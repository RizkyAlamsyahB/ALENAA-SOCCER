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
                            <li class="breadcrumb-item"><a href="{{ route('user.points.history') }}">Riwayat Poin</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Voucher</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ route('user.points.history') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
                    </a>
                </div>

                <!-- Voucher Card -->
                <div class="card border-0 rounded-4 shadow-sm mb-4 hover-shadow">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 fw-bold">Detail Voucher</h4>

                            <!-- Status Badge -->
                            @if($redemption->status === 'used')
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success p-2">
                                <i class="fas fa-check-double me-1"></i>Digunakan
                            </span>
                            @elseif($redemption->status === 'expired' || ($redemption->expires_at && \Carbon\Carbon::parse($redemption->expires_at)->isPast()))
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2">
                                <i class="fas fa-calendar-times me-1"></i>Kadaluarsa
                            </span>
                            @else
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success p-2">
                                <i class="fas fa-check-circle me-1"></i>Aktif
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Voucher Info -->
                        <div class="text-center mb-4 p-4 voucher-highlight">
                            <div class="voucher-icon mb-3">
                                <i class="fas fa-ticket-alt fa-3x"></i>
                            </div>
                            <h3 class="fw-bold mb-2">{{ $redemption->pointVoucher->name }}</h3>
                            <p class="text-muted mb-3">{{ $redemption->pointVoucher->description }}</p>

                            <div class="d-flex justify-content-center mb-3">
                                <div class="discount-badge">
                                    @if($redemption->pointVoucher->discount_type === 'percentage')
                                    {{ $redemption->pointVoucher->discount_value }}% OFF
                                    @else
                                    Rp {{ number_format($redemption->pointVoucher->discount_value) }} OFF
                                    @endif
                                </div>
                            </div>

                            <!-- Kode Voucher -->
                            <div class="voucher-code-container mx-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block mb-1">Kode Voucher</small>
                                        <code class="fs-4 fw-bold">{{ $redemption->discount_code }}</code>
                                    </div>
                                    <button class="btn copy-btn" data-clipboard-text="{{ $redemption->discount_code }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Voucher -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 section-title">
                                <i class="fas fa-info-circle me-2"></i>Detail Voucher
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless custom-table">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%;" class="text-muted">Tanggal Penukaran</td>
                                            <td class="fw-medium">{{ \Carbon\Carbon::parse($redemption->created_at)->format('d M Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Poin Digunakan</td>
                                            <td class="fw-medium">
                                                <span class="text-danger">-{{ number_format($redemption->points_used) }}</span> poin
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Masa Berlaku</td>
                                            <td class="fw-medium">
                                                @if($redemption->expires_at)
                                                {{ \Carbon\Carbon::parse($redemption->expires_at)->format('d M Y H:i') }}
                                                @else
                                                Tidak ada batas waktu
                                                @endif
                                            </td>
                                        </tr>
                                        @if($redemption->used_at)
                                        <tr>
                                            <td class="text-muted">Digunakan Pada</td>
                                            <td class="fw-medium">{{ \Carbon\Carbon::parse($redemption->used_at)->format('d M Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($redemption->payment_id)
                                        <tr>
                                            <td class="text-muted">Order ID</td>
                                            <td class="fw-medium">
                                                <a href="{{ route('user.payment.detail', $redemption->payment_id) }}" class="text-primary">
                                                    {{ $redemption->payment->order_id }}
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Ketentuan dan Syarat -->
                        <div>
                            <h5 class="fw-bold mb-3 section-title">
                                <i class="fas fa-list-ul me-2"></i>Syarat dan Ketentuan
                            </h5>
                            <ul class="terms-list">
                                @if($redemption->pointVoucher->min_order > 0)
                                <li class="mb-2">
                                    <i class="fas fa-check me-2 text-success"></i>
                                    Minimum pembelian Rp {{ number_format($redemption->pointVoucher->min_order) }}
                                </li>
                                @endif

                                @if($redemption->pointVoucher->max_discount)
                                <li class="mb-2">
                                    <i class="fas fa-check me-2 text-success"></i>
                                    Maksimum diskon Rp {{ number_format($redemption->pointVoucher->max_discount) }}
                                </li>
                                @endif

                                @if($redemption->pointVoucher->applicable_to !== 'all')
                                <li class="mb-2">
                                    <i class="fas fa-check me-2 text-success"></i>
                                    Hanya berlaku untuk
                                    @if($redemption->pointVoucher->applicable_to === 'field_booking')
                                    booking lapangan
                                    @elseif($redemption->pointVoucher->applicable_to === 'rental_item')
                                    sewa peralatan
                                    @else
                                    {{ $redemption->pointVoucher->applicable_to }}
                                    @endif
                                </li>
                                @endif

                                <li class="mb-2">
                                    <i class="fas fa-check me-2 text-success"></i>
                                    Voucher berlaku {{ $redemption->expires_at ? 'hingga ' . \Carbon\Carbon::parse($redemption->expires_at)->format('d M Y') : 'tanpa batas waktu' }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check me-2 text-success"></i>
                                    Voucher hanya dapat digunakan sekali
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check me-2 text-success"></i>
                                    Voucher tidak dapat digabung dengan promo lainnya
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4">
                        <div class="d-grid">
                            @if($redemption->status === 'active' && (!$redemption->expires_at || !\Carbon\Carbon::parse($redemption->expires_at)->isPast()))
                            <a href="{{ route('user.fields.index') }}" class="btn btn-primary rounded-pill">
                                <i class="fas fa-shopping-cart me-2"></i>Gunakan Sekarang
                            </a>
                            @elseif($redemption->status === 'used')
                            <button class="btn btn-secondary rounded-pill" disabled>
                                <i class="fas fa-ban me-2"></i>Voucher Sudah Digunakan
                            </button>
                            @else
                            <button class="btn btn-secondary rounded-pill" disabled>
                                <i class="fas fa-ban me-2"></i>Voucher Sudah Tidak Berlaku
                            </button>
                            @endif
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

        /* Card Styles */
        .card {
            transition: all 0.3s ease;
            border-radius: 15px !important;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Voucher Highlight Section */
        .voucher-highlight {
            background-color: #f9f9f9;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        .voucher-highlight::before,
        .voucher-highlight::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            z-index: 1;
        }

        .voucher-highlight::before {
            top: 50%;
            left: -10px;
            transform: translateY(-50%);
        }

        .voucher-highlight::after {
            top: 50%;
            right: -10px;
            transform: translateY(-50%);
        }

        .voucher-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            background-color: rgba(158, 6, 32, 0.1);
            border-radius: 50%;
            color: #9E0620;
        }

        .discount-badge {
            background: #9E0620;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            display: inline-block;
        }

        .voucher-code-container {
            background-color: white;
            border: 1px dashed #ddd;
            border-radius: 10px;
            padding: 1rem;
            max-width: 300px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .copy-btn {
            background-color: #f8f9fa;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background-color: #e9ecef;
        }

        /* Section Titles */
        .section-title {
            padding-bottom: 0.5rem;
            position: relative;
            color: #333;
        }

        /* Custom Table Styles */
        .custom-table td {
            padding: 0.75rem 0;
        }

        /* Terms List */
        .terms-list {
            list-style-type: none;
            padding-left: 0;
        }

        .terms-list li {
            background-color: #f8f9fa;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .terms-list li:hover {
            background-color: #f1f3f5;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Button Styling */
        .btn-primary {
            background-color: #9E0620;
            border-color: #9E0620;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #850519;
            border-color: #850519;
        }

        .btn-outline-primary {
            color: #9E0620;
            border-color: #9E0620;
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: #9E0620;
            border-color: #9E0620;
            color: white;
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

            .discount-badge {
                font-size: 1.2rem;
                padding: 0.4rem 1.2rem;
            }
        }
    </style>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <script>
        $(document).ready(function() {
            var clipboard = new ClipboardJS('.copy-btn');

            clipboard.on('success', function(e) {
                $(e.trigger).html('<i class="fas fa-check"></i>');

                setTimeout(function() {
                    $(e.trigger).html('<i class="far fa-copy"></i>');
                }, 1000);

                e.clearSelection();
            });
        });
    </script>
    @endpush
@endsection
