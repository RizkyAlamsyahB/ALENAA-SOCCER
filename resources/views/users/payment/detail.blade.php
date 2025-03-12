@extends('layouts.app')
@section('content')
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
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0">Detail Pembayaran</h2>
                    <a href="{{ route('user.payment.history') }}" class="btn-outline-sm">
                        <i class="fas fa-arrow-left me-2"></i>
                        <span>Kembali ke Riwayat</span>
                    </a>
                </div>

                <div class="row g-4">
                    <!-- Payment Summary Card -->
                    <div class="col-lg-4 order-lg-2">
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow summary-card mb-4">
                            <div class="card-body p-0">
                                <div
                                    class="payment-status-header
@if ($payment->transaction_status == 'success') bg-success-gradient
@elseif($payment->transaction_status == 'pending') bg-warning-gradient
@elseif($payment->transaction_status == 'failed') bg-danger-gradient
@else bg-secondary-gradient @endif">
                                    <div class="status-icon">
                                        @if ($payment->transaction_status == 'success')
                                            <i class="fas fa-check-circle"></i>
                                        @elseif($payment->transaction_status == 'pending')
                                            <i class="fas fa-clock"></i>
                                        @elseif($payment->transaction_status == 'failed')
                                            <i class="fas fa-times-circle"></i>
                                        @else
                                            <i class="fas fa-question-circle"></i>
                                        @endif
                                    </div>
                                    <h4 class="status-title">
                                        @if ($payment->transaction_status == 'success')
                                            Pembayaran Berhasil
                                        @elseif($payment->transaction_status == 'pending')
                                            Menunggu Pembayaran
                                        @elseif($payment->transaction_status == 'failed')
                                            Pembayaran Gagal
                                        @else
                                            {{ ucfirst($payment->transaction_status) }}
                                        @endif
                                    </h4>
                                    <div class="status-date">
                                        @if ($payment->transaction_time)
                                            {{ Carbon\Carbon::parse($payment->transaction_time)->format('d M Y, H:i') }}
                                        @else
                                            {{ $payment->created_at->format('d M Y, H:i') }}
                                        @endif
                                    </div>
                                </div>

                                @if ($payment->transaction_status == 'pending' && isset($payment->expires_at))
                                    <div class="expiration-warning px-4 pt-3">
                                        <div class="alert alert-warning p-3 rounded-3 mb-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                                <div>
                                                    <strong>Perhatian:</strong> Pembayaran ini akan kedaluwarsa pada
                                                    {{ \Carbon\Carbon::parse($payment->expires_at)->format('d M Y H:i:s') }}

                                                    <div id="countdown"
                                                        data-expires="{{ \Carbon\Carbon::parse($payment->expires_at)->timestamp }}"
                                                        data-now="{{ now()->timestamp }}"
                                                        class="countdown-timer mt-1 fw-bold">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="payment-summary p-4">
                                    <h5 class="section-title mb-3">Informasi Pembayaran</h5>

                                    <div class="summary-item">
                                        <div class="item-label">
                                            <i class="fas fa-receipt text-primary"></i>
                                            <span>Order ID</span>
                                        </div>
                                        <div class="item-value">
                                            <span class="order-id-value">{{ $payment->order_id }}</span>
                                        </div>
                                    </div>

                                    <div class="summary-item">
                                        <div class="item-label">
                                            <i class="fas fa-calendar-alt text-info"></i>
                                            <span>Tanggal Order</span>
                                        </div>
                                        <div class="item-value">
                                            {{ $payment->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>

                                    @if ($payment->payment_type)
                                        <div class="summary-item">
                                            <div class="item-label">
                                                <i class="fas fa-credit-card text-secondary"></i>
                                                <span>Metode Pembayaran</span>
                                            </div>
                                            <div class="item-value payment-method">
                                                @if (strtolower($payment->payment_type) == 'bank_transfer' || strtolower($payment->payment_type) == 'bank transfer')
                                                    <i class="fas fa-university method-icon bank"></i>
                                                @elseif(strtolower($payment->payment_type) == 'credit_card' || strtolower($payment->payment_type) == 'credit card')
                                                    <i class="fas fa-credit-card method-icon card"></i>
                                                @elseif(strtolower($payment->payment_type) == 'gopay')
                                                    <i class="fas fa-wallet method-icon ewallet"></i>
                                                @else
                                                    <i class="fas fa-money-bill-wave method-icon default"></i>
                                                @endif
                                                {{ ucwords(str_replace('_', ' ', $payment->payment_type)) }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($payment->transaction_id)
                                        <div class="summary-item">
                                            <div class="item-label">
                                                <i class="fas fa-hashtag text-warning"></i>
                                                <span>Transaction ID</span>
                                            </div>
                                            <div class="item-value">
                                                {{ $payment->transaction_id }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="total-amount">
                                        <div class="amount-label">Total Pembayaran</div>
                                        <div class="amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    @if ($payment->transaction_status == 'success')
                                        <div class="payment-actions mt-4">
                                            <a href="{{ route('user.payment.invoice', ['id' => $payment->id]) }}"
                                                class="btn-success-action">
                                                <i class="fas fa-download me-2"></i>
                                                <span>Download Invoice</span>
                                            </a>
                                        </div>
                                    @elseif($payment->transaction_status == 'pending')
                                        <div class="payment-actions mt-4">
                                            <a href="{{ route('user.payment.continue', ['id' => $payment->id]) }}"
                                                class="btn-warning-action">
                                                <i class="fas fa-credit-card me-2"></i>
                                                <span>Lanjutkan Pembayaran</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Customer Support Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow support-card">
                            <div class="card-body p-4">
                                <h5 class="section-title mb-3">Butuh Bantuan?</h5>
                                <p class="support-text">Jika Anda memiliki pertanyaan atau masalah terkait pembayaran ini,
                                    silakan hubungi tim dukungan kami.</p>
                                <a href="#" class="btn-support">
                                    <i class="fas fa-headset me-2"></i>
                                    <span>Hubungi Kami</span>
                                </a>
                            </div>
                        </div>
                    </div>


                    <!-- Booking Items Card -->
                    <div class="col-lg-8 order-lg-1">
                        <!-- Field Bookings -->
                        @if (count($payment->fieldBookings) > 0)
                            <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                <div
                                    class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                    <h5 class="m-0 fw-bold">Lapangan</h5>
                                    <span class="booking-count">{{ count($payment->fieldBookings) }} Item</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="booking-list">
                                        @foreach ($payment->fieldBookings as $booking)
                                            <div class="booking-item">
                                                <div class="booking-item-header">
                                                    <div class="booking-status">
                                                        @if ($booking->status == 'confirmed')
                                                            <span class="status-pill confirmed">
                                                                <i class="fas fa-check-circle me-1"></i> Terkonfirmasi
                                                            </span>
                                                        @elseif($booking->status == 'pending')
                                                            <span class="status-pill pending">
                                                                <i class="fas fa-clock me-1"></i> Menunggu
                                                            </span>
                                                        @elseif($booking->status == 'cancelled')
                                                            <span class="status-pill cancelled">
                                                                <i class="fas fa-times-circle me-1"></i> Dibatalkan
                                                            </span>
                                                        @else
                                                            <span class="status-pill other">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="booking-id">
                                                        #{{ $booking->id }}
                                                    </div>
                                                </div>

                                                <div class="booking-item-content">
                                                    <div class="booking-image">
                                                        @if (isset($booking->field->image))
                                                            <img src="{{ Storage::url($booking->field->image) }}"
                                                                alt="{{ $booking->field->name ?? 'Lapangan' }}"
                                                                class="field-image">
                                                        @else
                                                            <div class="field-image-placeholder">
                                                                <i class="fas fa-futbol"></i>
                                                            </div>
                                                        @endif
                                                        <div class="field-type">
                                                            {{ $booking->field->type ?? 'Lapangan' }}
                                                        </div>
                                                    </div>

                                                    <div class="booking-details">
                                                        <h5 class="field-name">{{ $booking->field->name ?? 'Lapangan' }}
                                                        </h5>
                                                        <div class="booking-info">
                                                            <div class="info-item">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-item">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                            <div class="info-item">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                                <span>{{ $booking->field->location ?? 'Lokasi tidak tersedia' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="booking-price">
                                                        <div class="price-value">Rp
                                                            {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                                        <div class="price-duration">
                                                            @php
                                                                $startTime = \Carbon\Carbon::parse(
                                                                    $booking->start_time,
                                                                );
                                                                $endTime = \Carbon\Carbon::parse($booking->end_time);
                                                                $durationInHours = $startTime->diffInHours($endTime);
                                                            @endphp
                                                            {{ $durationInHours }} jam
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($booking->status == 'confirmed')
                                                    <div class="booking-item-actions">
                                                        <a href="#" class="btn-outline-action">
                                                            <i class="far fa-calendar-check me-2"></i>
                                                            <span>Lihat Tiket</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Rental Bookings -->
                        @if (count($payment->rentalBookings) > 0)
                            <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                <div
                                    class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                    <h5 class="m-0 fw-bold">Penyewaan Peralatan</h5>
                                    <span class="booking-count">{{ count($payment->rentalBookings) }} Item</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="booking-list">
                                        @foreach ($payment->rentalBookings as $booking)
                                            <div class="booking-item">
                                                <div class="booking-item-header">
                                                    <div class="booking-status">
                                                        @if ($booking->status == 'confirmed')
                                                            <span class="status-pill confirmed">
                                                                <i class="fas fa-check-circle me-1"></i> Terkonfirmasi
                                                            </span>
                                                        @elseif($booking->status == 'pending')
                                                            <span class="status-pill pending">
                                                                <i class="fas fa-clock me-1"></i> Menunggu
                                                            </span>
                                                        @elseif($booking->status == 'cancelled')
                                                            <span class="status-pill cancelled">
                                                                <i class="fas fa-times-circle me-1"></i> Dibatalkan
                                                            </span>
                                                        @else
                                                            <span class="status-pill other">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>

                                                <div class="booking-item-content">
                                                    <div class="booking-image">
                                                        @if (isset($booking->rentalItem->image))
                                                            <img src="{{ Storage::url($booking->rentalItem->image) }}"
                                                                alt="{{ $booking->rentalItem->name ?? 'Penyewaan' }}"
                                                                class="field-image">
                                                        @else
                                                            <div class="field-image-placeholder">
                                                                <i class="fas fa-box"></i>
                                                            </div>
                                                        @endif
                                                        <div class="field-type">
                                                            Peralatan
                                                        </div>
                                                    </div>

                                                    <div class="booking-details">
                                                        <h5 class="field-name">
                                                            {{ $booking->rentalItem->name ?? 'Penyewaan' }}</h5>
                                                        <div class="booking-info">
                                                            <div class="info-item">
                                                                <i class="fas fa-box"></i>
                                                                <span>Jumlah: {{ $booking->quantity }}</span>
                                                            </div>
                                                            <div class="info-item">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-item">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="booking-price">
                                                        <div class="price-value">Rp
                                                            {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                                        <div class="price-duration">
                                                            @php
                                                                $startTime = \Carbon\Carbon::parse(
                                                                    $booking->start_time,
                                                                );
                                                                $endTime = \Carbon\Carbon::parse($booking->end_time);
                                                                $durationInHours = $startTime->diffInHours($endTime);
                                                            @endphp
                                                            {{ $durationInHours }} jam
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($booking->status == 'confirmed')
                                                    <div class="booking-item-actions">
                                                        <a href="#" class="btn-outline-action">
                                                            <i class="fas fa-receipt me-2"></i>
                                                            <span>Lihat Detail</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cari element countdown
            const countdownEl = document.getElementById('countdown');

            // Jika elemen countdown ada
            if (countdownEl) {
                // Ambil timestamp kedaluwarsa dan waktu sekarang
                const expiresTimestamp = parseInt(countdownEl.dataset.expires);
                let serverNowTimestamp = parseInt(countdownEl.dataset.now);

                // Ambil waktu client sekarang untuk menghitung offset
                const clientNowTimestamp = Math.floor(Date.now() / 1000);
                const timeOffset = clientNowTimestamp - serverNowTimestamp;

                // Update countdown setiap detik
                const countdownInterval = setInterval(function() {
                    // Update server time berdasarkan waktu client dengan offset
                    serverNowTimestamp = Math.floor(Date.now() / 1000) - timeOffset;

                    // Hitung sisa waktu
                    const remainingTime = expiresTimestamp - serverNowTimestamp;

                    // Jika waktu sudah habis
                    if (remainingTime <= 0) {
                        clearInterval(countdownInterval);
                        countdownEl.innerHTML =
                            '<span class="text-danger">Waktu pembayaran telah habis</span>';

                        // Opsional: reload halaman setelah 3 detik untuk menampilkan status terbaru
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                        return;
                    }

                    // Hitung menit dan detik
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;

                    // Tampilkan dalam format MM:SS
                    countdownEl.innerHTML =
                        `Sisa waktu: <span class="text-danger">${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}</span>`;
                }, 1000);
            }
        });
    </script>



    <style>
        /* Modern Payment Detail Styling */

        /* Breadcrumb */
        .breadcrumb-wrapper {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            position: relative;
            overflow: hidden;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-breadcrumb {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
            align-items: center;
            justify-content: center;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 700;
            font-size: 1.3rem;
        }

        .breadcrumb-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 700;
            font-size: 1.3rem;
        }

        .breadcrumb-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .breadcrumb-item.active {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.15);
            font-weight: 700;
            font-size: 1.3rem;
        }

        /* Cards */
        .card {
            border-radius: 16px !important;
            border: none !important;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

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

        /* Payment Status Header */
        .payment-status-header {
            padding: 30px 20px;
            text-align: center;
            color: white;
            border-radius: 16px 16px 0 0;
        }

        .bg-success-gradient {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .bg-warning-gradient {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .bg-danger-gradient {
            background: linear-gradient(135deg, #dc3545, #c71f37);
        }

        .bg-secondary-gradient {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .status-icon {
            width: 70px;
            height: 70px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2rem;
        }

        .status-title {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .status-date {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Payment Summary */
        .section-title {
            font-weight: 700;
            margin-bottom: 1.2rem;
            color: #343a40;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 0.8rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f8f9fa;
        }

        .item-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6c757d;
            font-weight: 500;
        }

        .item-value {
            font-weight: 600;
            color: #343a40;
            text-align: right;
        }

        .order-id-value {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .method-icon {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .method-icon.bank {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .method-icon.card {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .method-icon.ewallet {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .method-icon.default {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .total-amount {
            background-color: #f8f9fa;
            padding: 16px;
            border-radius: 12px;
            margin-top: 16px;
        }

        .amount-label {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 8px;
        }

        .amount-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #198754;
        }

        /* Payment Actions */
        .btn-success-action,
        .btn-warning-action,
        .btn-support {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-success-action {
            background-color: #28a745;
            color: white;
        }

        .btn-success-action:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
            color: white;
        }

        .btn-warning-action {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning-action:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
            color: #212529;
        }

        .btn-support {
            background-color: transparent;
            color: #9e0620;
            border: 1.5px solid #9e0620;
        }

        .btn-support:hover {
            background-color: rgba(158, 6, 32, 0.1);
            transform: translateY(-2px);
            color: #9e0620;
        }

        /* Support Card */
        .support-card {
            background-color: white;
        }

        .support-text {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        /* Booking List */
        .booking-count {
            background-color: #f8f9fa;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        .booking-list {
            padding: 16px;
        }

        .booking-item {
            background-color: white;
            border-radius: 16px;
            border: 1px solid #f1f3f5;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .booking-item:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .booking-item:last-child {
            margin-bottom: 0;
        }

        .booking-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #f1f3f5;
        }

        .booking-status {
            display: flex;
            align-items: center;
        }

        .status-pill {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .status-pill.confirmed {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-pill.pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .status-pill.cancelled {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .status-pill.other {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .booking-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .booking-item-content {
            padding: 16px;
            display: flex;
            gap: 16px;
        }

        .booking-image {
            position: relative;
            width: 100px;
            min-width: 100px;
            height: 100px;
            border-radius: 12px;
            overflow: hidden;
        }

        .field-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .field-image-placeholder {
            width: 100%;
            height: 100%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 2rem;
        }

        .field-type {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            font-size: 0.8rem;
            padding: 4px 8px;
            text-align: center;
            font-weight: 500;
        }

        .booking-details {
            flex: 1;
        }

        .field-name {
            font-weight: 700;
            color: #343a40;
            margin-bottom: 10px;
        }

        .booking-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .booking-price {
            min-width: 120px;
            text-align: right;
        }

        .price-value {
            font-weight: 700;
            color: #198754;
            font-size: 1.1rem;
        }

        .price-duration {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 4px;
        }

        .booking-item-actions {
            padding: 12px 16px;
            border-top: 1px solid #f1f3f5;
            display: flex;
            justify-content: flex-end;
        }

        .btn-outline-action {
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

        .btn-outline-action:hover {
            background-color: rgba(158, 6, 32, 0.1);
            transform: translateY(-2px);
            color: #9e0620;
        }

        /* Responsive Adjustments */
        @media (max-width: 991.98px) {
            .order-lg-1 {
                order: 2;
            }

            .order-lg-2 {
                order: 1;
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 767.98px) {

            .breadcrumb-link,
            .breadcrumb-item.active {
                padding: 6px;
                font-size: 1rem;
            }

            .breadcrumb-item i {
                font-size: 1rem;
            }

            .booking-item-content {
                flex-direction: column;
            }

            .booking-image {
                width: 100%;
                height: 160px;
            }

            .booking-price {
                text-align: left;
                margin-top: 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

@endsection
