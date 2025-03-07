@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/modern-payment.css') }}">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-wrapper" style="margin-top: 50px;">
        <div class="container py-2">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('user.payment.history') }}" class="breadcrumb-link">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Pembayaran</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-file-invoice"></i>
                    <span>Detail Pembayaran</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 fw-bold">Detail Pembayaran</h1>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <h5 class="mb-0 fw-bold">Item yang Dibooking</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="booking-items-list">
                            @foreach($payment->fieldBookings as $booking)
                                <div class="booking-item p-4 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 col-sm-3 mb-3 mb-md-0">
                                            <div class="booking-item-image">
                                                @if(isset($booking->field->image))
                                                    <img src="{{ Storage::url($booking->field->image) }}" alt="{{ $booking->field->name ?? 'Lapangan' }}" class="img-fluid rounded-3">
                                                @else
                                                    <div class="placeholder-image bg-light rounded-3 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-futbol text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-7 col-sm-6 mb-3 mb-md-0">
                                            <div class="booking-item-details">
                                                <h5 class="booking-item-title fw-bold mb-1">{{ $booking->field->name ?? 'Lapangan' }}</h5>
                                                <div class="booking-item-category mb-2">
                                                    <span class="type-badge">{{ $booking->field->type ?? 'Olahraga' }}</span>
                                                    <span class="status-badge
                                                        @if($booking->status == 'confirmed') bg-success
                                                        @elseif($booking->status == 'pending') bg-warning
                                                        @elseif($booking->status == 'cancelled') bg-danger
                                                        @else bg-secondary @endif">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </div>
                                                <div class="booking-item-info">
                                                    <div class="info-badge">
                                                        <i class="far fa-calendar-alt"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                    </div>
                                                    <div class="info-badge">
                                                        <i class="far fa-clock"></i>
                                                        <span>
                                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3 d-flex justify-content-end">
                                            <div class="booking-item-price text-end">
                                                <div class="price">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm hover-shadow summary-card">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <h5 class="mb-0 fw-bold">Detail Pembayaran</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="payment-status text-center mb-4 p-3 rounded-3
                            @if($payment->transaction_status == 'success') bg-success-light
                            @elseif($payment->transaction_status == 'pending') bg-warning-light
                            @elseif($payment->transaction_status == 'failed') bg-danger-light
                            @else bg-secondary-light @endif">
                            <div class="status-icon mb-2">
                                @if($payment->transaction_status == 'success')
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                @elseif($payment->transaction_status == 'pending')
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                @elseif($payment->transaction_status == 'failed')
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                @else
                                    <i class="fas fa-question-circle fa-2x text-secondary"></i>
                                @endif
                            </div>
                            <h5 class="mb-0 fw-bold
                                @if($payment->transaction_status == 'success') text-success
                                @elseif($payment->transaction_status == 'pending') text-warning
                                @elseif($payment->transaction_status == 'failed') text-danger
                                @else text-secondary @endif">
                                @if($payment->transaction_status == 'success')
                                    Pembayaran Berhasil
                                @elseif($payment->transaction_status == 'pending')
                                    Menunggu Pembayaran
                                @elseif($payment->transaction_status == 'failed')
                                    Pembayaran Gagal
                                @else
                                    {{ ucfirst($payment->transaction_status) }}
                                @endif
                            </h5>
                        </div>
                        <div class="payment-details mb-4">
                            <div class="detail-item d-flex justify-content-between mb-3">
                                <span class="label">Order ID</span>
                                <span class="value">{{ $payment->order_id }}</span>
                            </div>
                            <div class="detail-item d-flex justify-content-between mb-3">
                                <span class="label">Tanggal</span>
                                <span class="value">{{ $payment->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($payment->transaction_time)
                            <div class="detail-item d-flex justify-content-between mb-3">
                                <span class="label">Waktu Pembayaran</span>
                                <span class="value">{{ Carbon\Carbon::parse($payment->transaction_time)->format('d M Y H:i') }}</span>
                            </div>
                            @endif
                            @if($payment->payment_type)
                            <div class="detail-item d-flex justify-content-between mb-3">
                                <span class="label">Metode Pembayaran</span>
                                <span class="value">{{ ucfirst($payment->payment_type) }}</span>
                            </div>
                            @endif
                            <div class="detail-item d-flex justify-content-between mb-3">
                                <span class="label">Total</span>
                                <span class="value font-weight-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('user.payment.history') }}" class="btn-back w-100">
                                <i class="fas fa-arrow-left me-2"></i>
                                <span>Kembali ke Riwayat</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Additional styles for payment detail */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            color: white;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 8px;
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-secondary-light {
            background-color: rgba(108, 117, 125, 0.1);
        }

        .payment-details {
            padding: 16px;
            background-color: #f8f9fa;
            border-radius: 12px;
        }

        .detail-item {
            margin-bottom: 12px;
        }

        .detail-item .label {
            color: #6c757d;
            font-weight: 500;
        }

        .detail-item .value {
            font-weight: 600;
            color: #212529;
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 0;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
            color: white;
        }
    </style>
@endsection
