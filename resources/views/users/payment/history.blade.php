@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Riwayat Pembayaran</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-shopping-bag"></i> Riwayat Pembayaran
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0">Riwayat Pembayaran</h2>
                    <a href="{{ route('user.fields.index') }}" class="btn-outline-sm">
                        <i class="fas fa-futbol me-2"></i>
                        <span>Cari Lapangan</span>
                    </a>
                </div>

                @if (count($payments) > 0)
                    <div class="payment-filter mb-4">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="search-box">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="form-control" placeholder="Cari Order ID...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select">
                                    <option selected>Semua Status</option>
                                    <option value="success">Sukses</option>
                                    <option value="pending">Menunggu</option>
                                    <option value="failed">Gagal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select">
                                    <option selected>Semua Metode</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="credit_card">Kartu Kredit</option>
                                    <option value="gopay">GoPay</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn-filter w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="payment-list">
                        @foreach ($payments as $payment)
                            <div class="payment-card">
                                <div class="payment-card-header">
                                    <div class="order-info">
                                        <div class="order-id">{{ $payment->order_id }}</div>
                                        <div class="order-date">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $payment->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                    <div class="payment-status">
                                        @if ($payment->transaction_status == 'success')
                                            <span class="status-badge success">
                                                <i class="fas fa-check-circle me-1"></i> Sukses
                                            </span>
                                        @elseif($payment->transaction_status == 'pending')
                                            <span class="status-badge pending">
                                                <i class="fas fa-clock me-1"></i> Menunggu
                                            </span>
                                        @elseif($payment->transaction_status == 'failed')
                                            <span class="status-badge failed">
                                                <i class="fas fa-times-circle me-1"></i> Gagal
                                            </span>
                                        @elseif($payment->transaction_status == 'challenge')
                                            <span class="status-badge info">
                                                <i class="fas fa-exclamation-circle me-1"></i> Challenge
                                            </span>
                                        @else
                                            <span class="status-badge secondary">
                                                <i class="fas fa-question-circle me-1"></i>
                                                {{ $payment->transaction_status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="payment-card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="payment-info">
                                                <div class="payment-method">
                                                    @if ($payment->payment_type == 'bank_transfer')
                                                        <i class="fas fa-university payment-icon bank"></i>
                                                    @elseif($payment->payment_type == 'credit_card')
                                                        <i class="fas fa-credit-card payment-icon card"></i>
                                                    @elseif($payment->payment_type == 'gopay')
                                                        <i class="fas fa-wallet payment-icon ewallet"></i>
                                                    @else
                                                        <i class="fas fa-money-bill-wave payment-icon default"></i>
                                                    @endif
                                                    <div class="payment-details">
                                                        <div class="payment-method-name">
                                                            {{ $payment->payment_type ?? '' }}</div>
                                                        <div class="payment-items">
                                                            @php
                                                                $totalItems = 0;
                                                                if (isset($payment->fieldBookings)) {
                                                                    $totalItems += count($payment->fieldBookings);
                                                                }
                                                                if (isset($payment->rentalBookings)) {
                                                                    $totalItems += count($payment->rentalBookings);
                                                                }
                                                                if (isset($payment->membershipSubscriptions)) {
                                                                    $totalItems += count(
                                                                        $payment->membershipSubscriptions,
                                                                    );
                                                                }
                                                                if (isset($payment->photographerBookings)) {
                                                                    $totalItems += count(
                                                                        $payment->photographerBookings,
                                                                    );
                                                                }
                                                            @endphp
                                                            {{ $totalItems }} item
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <div class="payment-amount">Rp
                                                {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                            <div class="mt-2">
                                                <a href="{{ route('user.payment.detail', $payment->id) }}"
                                                    class="btn-view-detail">
                                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach


                    </div>
                @else
                    <div class="empty-payment-state">
                        <div class="empty-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3 class="empty-title">Belum Ada Pembayaran</h3>
                        <p class="empty-description">Anda belum memiliki riwayat pembayaran saat ini.</p>
                        <a href="{{ route('user.fields.index') }}" class="btn-primary">
                            <i class="fas fa-futbol me-2"></i>
                            <span>Cari Lapangan Sekarang</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="pagination-wrapper mt-4">
                {{ $payments->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

    <style>
        /* Modern Payment History Styling */
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


        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        /* Buttons */
        .btn-outline-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: transparent;
            color: #9e0620;
            border: 1.5px solid #9e0620;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-outline-sm:hover {
            background-color: rgba(158, 6, 32, 0.1);
            transform: translateY(-2px);
            color: #9e0620;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: #9e0620;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(158, 6, 32, 0.2);
        }

        .btn-primary:hover {
            background-color: #7d0318;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(158, 6, 32, 0.25);
            color: white;
        }

        .btn-filter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 20px;
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-filter:hover {
            background-color: #e9ecef;
        }

        .btn-view-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            background-color: rgba(158, 6, 32, 0.1);
            color: #9e0620;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-view-detail:hover {
            background-color: rgba(158, 6, 32, 0.2);
            color: #9e0620;
        }

        /* Filter Section */
        .payment-filter {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .search-box {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-box input {
            padding-left: 35px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            height: 42px;
        }

        .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            height: 42px;
            padding-left: 12px;
        }

        /* Payment Cards */
        .payment-list {
            margin-bottom: 2rem;
        }

        .payment-card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 1.2rem;
            transition: all 0.3s ease;
        }

        .payment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .payment-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background-color: #f8f9fa;
        }

        .order-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .order-id {
            font-weight: 700;
            color: #343a40;
            font-family: 'Courier New', monospace;
        }

        .order-date {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .payment-status {
            display: flex;
            align-items: center;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge.success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-badge.pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .status-badge.failed {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .status-badge.info {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .status-badge.secondary {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .payment-card-body {
            padding: 20px;
        }

        .payment-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .payment-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .payment-icon.bank {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .payment-icon.card {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .payment-icon.ewallet {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .payment-icon.default {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .payment-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .payment-method-name {
            font-weight: 600;
            color: #343a40;
        }

        .payment-items {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .payment-amount {
            font-weight: 700;
            color: #198754;
            font-size: 1.2rem;
        }

        /* Empty State */
        .empty-payment-state {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 4rem 2rem;
            text-align: center;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            background-color: rgba(158, 6, 32, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: #9e0620;
        }

        .empty-title {
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #343a40;
        }

        .empty-description {
            color: #6c757d;
            max-width: 450px;
            margin: 0 auto 2rem;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {

            .breadcrumb-link,
            .breadcrumb-item.active {
                padding: 6px;
                font-size: 1rem;
            }

            .breadcrumb-item i {
                font-size: 1rem;
            }

            .payment-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .payment-status {
                align-self: flex-start;
            }

            .payment-method {
                margin-bottom: 15px;
            }

            .payment-amount {
                text-align: left;
                margin-top: 15px;
            }

            .col-md-4.text-md-end {
                text-align: left !important;
            }
        }

    </style>
    <style>
        <!-- Add this style to your blade file -->
<style>
/* Custom Pagination Styling */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    list-style: none;
    gap: 0.5rem;
    margin: 0;
    padding: 0;
}

.page-item {
    margin: 0 2px;
}

.page-item .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0 12px;
    font-size: 0.95rem;
    font-weight: 600;
    border-radius: 10px;
    color: #495057;
    background-color: #fff;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.page-item .page-link:hover {
    background-color: #f8f9fa;
    color: #9e0620;
    border-color: #dee2e6;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    z-index: 3;
}

.page-item.active .page-link {
    background-color: #9e0620;
    color: white;
    border-color: #9e0620;
    box-shadow: 0 4px 10px rgba(158, 6, 32, 0.2);
    z-index: 3;
}

/* For disabled items */
.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #f8f9fa;
    border-color: #dee2e6;
    opacity: 0.6;
}

/* Previous and Next buttons */
.page-item:first-child .page-link,
.page-item:last-child .page-link {
    padding: 0 12px;
    font-size: 0.85rem;
}

/* Ellipsis styling */
.page-item.disabled span.page-link {
    border: none;
    background: transparent;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .pagination {
        gap: 0.2rem;
    }

    .page-item .page-link {
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        font-size: 0.85rem;
    }

    /* Hide some page numbers on very small screens */
    .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.next):not(.prev) {
        display: none;
    }

    /* But keep immediate siblings of active visible if possible */
    .pagination .active + .page-item,
    .pagination .page-item + .active {
        display: flex;
    }
}
</style>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

@endsection
