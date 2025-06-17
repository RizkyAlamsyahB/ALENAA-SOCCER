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
        <!-- Session Alert -->
        @if(session('alert'))
            <div class="alert alert-{{ session('alert.type') }} alert-dismissible fade show" role="alert" style="margin-bottom: 2rem;">
                <div class="d-flex align-items-center">
                    @if(session('alert.type') == 'success')
                        <i class="fas fa-check-circle me-2"></i>
                    @elseif(session('alert.type') == 'danger')
                        <i class="fas fa-exclamation-triangle me-2"></i>
                    @elseif(session('alert.type') == 'warning')
                        <i class="fas fa-exclamation-circle me-2"></i>
                    @elseif(session('alert.type') == 'info')
                        <i class="fas fa-info-circle me-2"></i>
                    @endif
                    <div>
                        @if(session('alert.title'))
                            <strong>{{ session('alert.title') }}</strong>
                        @endif
                        {{ session('alert.message') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                                <div class="payment-status-header
                                    @if($payment->transaction_status == 'success') bg-success-gradient
                                    @elseif($payment->transaction_status == 'pending') bg-warning-gradient
                                    @elseif($payment->transaction_status == 'failed') bg-danger-gradient
                                    @else bg-secondary-gradient @endif">
                                    <div class="status-icon">
                                        @if($payment->transaction_status == 'success')
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
                                        @if($payment->transaction_status == 'success')
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
                                        @if($payment->transaction_time)
                                            {{ Carbon\Carbon::parse($payment->transaction_time)->format('d M Y, H:i') }}
                                        @else
                                            {{ $payment->created_at->format('d M Y, H:i') }}
                                        @endif
                                    </div>
                                </div>

                                @if($payment->transaction_status == 'pending' && isset($payment->expires_at))
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
                                            <i class="fas fa-receipt"></i>
                                            <span>Order ID</span>
                                        </div>
                                        <div class="item-value">
                                            <span class="order-id-value">{{ $payment->order_id }}</span>
                                        </div>
                                    </div>

                                    <div class="summary-item">
                                        <div class="item-label">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Tanggal Order</span>
                                        </div>
                                        <div class="item-value">
                                            {{ $payment->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>

                                    @if($payment->payment_type)
                                        <div class="summary-item">
                                            <div class="item-label">
                                                <i class="fas fa-credit-card"></i>
                                                <span>Metode Pembayaran</span>
                                            </div>
                                            <div class="item-value payment-method">
                                                @if(strtolower($payment->payment_type) == 'bank_transfer' || strtolower($payment->payment_type) == 'bank transfer')
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

                                    @if($payment->transaction_id)
                                        <div class="summary-item">
                                            <div class="item-label">
                                                <i class="fas fa-hashtag"></i>
                                                <span>Transaction ID</span>
                                            </div>
                                            <div class="item-value">
                                                {{ $payment->transaction_id }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($payment->discount_id && $payment->discount_amount > 0)
                                        <div class="summary-item">
                                            <div class="item-label">
                                                <i class="fas fa-receipt"></i>
                                                <span>Subtotal</span>
                                            </div>
                                            <div class="item-value">
                                                Rp {{ number_format($payment->original_amount, 0, ',', '.') }}
                                            </div>
                                        </div>

                                        <div class="summary-item">
                                            <div class="item-label">
                                                <i class="fas fa-tag text-success"></i>
                                                <span class="text-success">Diskon</span>
                                            </div>
                                            <div class="item-value text-success">
                                                - Rp {{ number_format($payment->discount_amount, 0, ',', '.') }}
                                                @if($payment->discount)
                                                    <div class="discount-code">{{ $payment->discount->code }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="total-amount">
                                        <div class="amount-label">Total Pembayaran</div>
                                        <div class="amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                    </div>

                                    @if($payment->transaction_status == 'success')
                                        <div class="payment-actions mt-4">
                                            <a href="{{ route('user.payment.invoice', ['id' => $payment->id]) }}" class="btn-success-action">
                                                <i class="fas fa-download me-2"></i>
                                                <span>Download Faktur</span>
                                            </a>
                                        </div>
                                    @elseif($payment->transaction_status == 'pending')
                                        <div class="payment-actions mt-4">
                                            <a href="{{ route('user.payment.continue', ['id' => $payment->id]) }}" class="btn-warning-action">
                                                <i class="fas fa-credit-card me-2"></i>
                                                <span>Lanjutkan Pembayaran</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($payment->transaction_status == 'success')
                            <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="points-icon me-3">
                                            <i class="fas fa-coins fa-2x text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Points Diperoleh</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="fs-5 fw-bold text-success">{{ $pointsEarned }}</span>
                                                <span class="badge bg-success ms-2 rounded-pill">
                                                    <i class="fas fa-plus me-1"></i>Points
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ms-auto text-end">
                                            <p class="mb-0 text-muted small">Total Points</p>
                                            <p class="mb-0 fw-bold">{{ Auth::user()->points }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Customer Support Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow support-card">
                            <div class="card-body p-4">
                                <h5 class="section-title mb-3">Butuh Bantuan?</h5>
                                <p class="support-text">Jika Anda memiliki pertanyaan atau masalah terkait pembayaran ini, silakan hubungi tim dukungan kami.</p>
                                <a href="#" class="btn-support">
                                    <i class="fas fa-headset me-2"></i>
                                    <span>Hubungi Kami</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Items Card -->
                    <div class="col-lg-8 order-lg-1">
                        @php
                            $hasMembership = $payment->membershipSubscriptions && count($payment->membershipSubscriptions) > 0;
                        @endphp

                        @if($hasMembership)
                            {{-- MEMBERSHIP PAYMENT - Tampilkan membership dengan detail jadwal --}}
                            <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                    <h5 class="m-0 fw-bold">
                                        <i class="fas fa-crown text-warning me-2"></i>
                                        Paket Membership
                                    </h5>
                                    <span class="booking-count">{{ count($payment->membershipSubscriptions) }} Package</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="booking-list">
                                        @foreach($payment->membershipSubscriptions as $subscription)
                                            <div class="membership-main-card">
                                                {{-- Membership Header --}}
                                                <div class="membership-header">
                                                    <div class="membership-info">
                                                        <div class="membership-icon">
                                                            <i class="fas fa-crown"></i>
                                                        </div>
                                                        <div class="membership-details">
                                                            <h4 class="membership-title">{{ $subscription->membership->name ?? 'Membership Package' }}</h4>
                                                            <div class="membership-meta">
                                                                @if($subscription->start_date && $subscription->end_date)
                                                                    <div class="meta-item">
                                                                        <i class="far fa-clock"></i>
                                                                        <span>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</span>
                                                                    </div>
                                                                @endif
                                                                @if($subscription->membership->field)
                                                                    <div class="meta-item">
                                                                        <i class="fas fa-map-marker-alt"></i>
                                                                        <span>{{ $subscription->membership->field->name }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="membership-price">
                                                            <div class="price-value">Rp {{ number_format($subscription->price, 0, ',', '.') }}</div>
                                                        </div>
                                                    </div>

                                                    <div class="membership-status">
                                                        @if($subscription->status == 'active')
                                                            <span class="status-pill confirmed">
                                                                <i class="fas fa-check-circle me-1"></i> Aktif
                                                            </span>
                                                        @elseif($subscription->status == 'pending')
                                                            <span class="status-pill pending">
                                                                <i class="fas fa-clock me-1"></i> Menunggu
                                                            </span>
                                                        @elseif($subscription->status == 'cancelled')
                                                            <span class="status-pill cancelled">
                                                                <i class="fas fa-times-circle me-1"></i> Dibatalkan
                                                            </span>
                                                        @else
                                                            <span class="status-pill other">
                                                                <i class="fas fa-info-circle me-1"></i> {{ ucfirst($subscription->status) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($subscription->membership->description)
                                                    <div class="membership-description">
                                                        <p class="mb-0">{{ $subscription->membership->description }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- DETAILED SCHEDULE SECTIONS --}}
                            {{-- Field Schedules --}}
                            @if($payment->fieldBookings && count($payment->fieldBookings) > 0)
                                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                        <h5 class="m-0 fw-bold">
                                            Jadwal Lapangan
                                        </h5>
                                        <span class="booking-count">{{ count($payment->fieldBookings) }} Jadwal</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="schedule-list">
                                            @foreach($payment->fieldBookings as $booking)
                                                <div class="schedule-item">
                                                    <div class="schedule-info">
                                                        <div class="schedule-icon field">
                                                            @if(isset($booking->field->image) && $booking->field->image)
                                                                <img src="{{ Storage::url($booking->field->image) }}"
                                                                     alt="{{ $booking->field->name ?? 'Lapangan' }}"
                                                                     class="schedule-image">
                                                            @else
                                                                <i class="fas fa-futbol"></i>
                                                            @endif
                                                        </div>
                                                        <div class="schedule-details">
                                                            <h6 class="schedule-title">{{ $booking->field->name ?? 'Lapangan' }}</h6>
                                                            <div class="schedule-meta">
                                                                <span class="schedule-date">
                                                                    <i class="far fa-calendar-alt me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}
                                                                </span>
                                                                <span class="schedule-time">
                                                                    <i class="far fa-clock me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="schedule-status">
                                                        <span class="included-badge">Termasuk</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Rental Schedules --}}
                            @if($payment->rentalBookings && count($payment->rentalBookings) > 0)
                                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                        <h5 class="m-0 fw-bold">
                                            Jadwal Rental
                                        </h5>
                                        <span class="booking-count">{{ count($payment->rentalBookings) }} Jadwal</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="schedule-list">
                                            @foreach($payment->rentalBookings as $booking)
                                                <div class="schedule-item">
                                                    <div class="schedule-info">
                                                        <div class="schedule-icon rental">
                                                            @if(isset($booking->rentalItem->image) && $booking->rentalItem->image)
                                                                <img src="{{ Storage::url($booking->rentalItem->image) }}"
                                                                     alt="{{ $booking->rentalItem->name ?? 'Rental Item' }}"
                                                                     class="schedule-image">
                                                            @else
                                                                <i class="fas fa-box"></i>
                                                            @endif
                                                        </div>
                                                        <div class="schedule-details">
                                                            <h6 class="schedule-title">{{ $booking->rentalItem->name ?? 'Rental Item' }}</h6>
                                                            <div class="schedule-meta">
                                                                <span class="schedule-quantity">
                                                                    <i class="fas fa-hashtag me-1"></i>
                                                                    Jumlah: {{ $booking->quantity }}
                                                                </span>
                                                                <span class="schedule-date">
                                                                    <i class="far fa-calendar-alt me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}
                                                                </span>
                                                                <span class="schedule-time">
                                                                    <i class="far fa-clock me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="schedule-status">
                                                        <span class="included-badge">Termasuk</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Photographer Schedules --}}
                            @if($payment->photographerBookings && count($payment->photographerBookings) > 0)
                                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                        <h5 class="m-0 fw-bold">
                                            Jadwal Fotografer
                                        </h5>
                                        <span class="booking-count">{{ count($payment->photographerBookings) }} Jadwal</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="schedule-list">
                                            @foreach($payment->photographerBookings as $booking)
                                                <div class="schedule-item">
                                                    <div class="schedule-info">
                                                        <div class="schedule-icon photographer">
                                                            @if(isset($booking->photographer->image) && $booking->photographer->image)
                                                                <img src="{{ Storage::url($booking->photographer->image) }}"
                                                                     alt="{{ $booking->photographer->name ?? 'Fotografer' }}"
                                                                     class="schedule-image">
                                                            @else
                                                                <i class="fas fa-camera"></i>
                                                            @endif
                                                        </div>
                                                        <div class="schedule-details">
                                                            <h6 class="schedule-title">{{ $booking->photographer->name ?? 'Fotografer' }}</h6>
                                                            <div class="schedule-meta">
                                                                <span class="schedule-specialty">
                                                                    <i class="fas fa-tag me-1"></i>
                                                                    {{ $booking->photographer->specialization ?? 'Umum' }}
                                                                </span>
                                                                <span class="schedule-date">
                                                                    <i class="far fa-calendar-alt me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($booking->date ?? $booking->start_time)->format('d M Y') }}
                                                                </span>
                                                                <span class="schedule-time">
                                                                    <i class="far fa-clock me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="schedule-status">
                                                        <span class="included-badge">Termasuk</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @else
                            {{-- NON-MEMBERSHIP PAYMENT - Tampilkan setiap service dengan harga --}}

                            {{-- Field Bookings --}}
                            @if($payment->fieldBookings && count($payment->fieldBookings) > 0)
                                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                        <h5 class="m-0 fw-bold">Lapangan</h5>
                                        <span class="booking-count">{{ count($payment->fieldBookings) }} Item</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="booking-list">
                                            @foreach($payment->fieldBookings as $booking)
                                                <div class="booking-item">
                                                    <div class="booking-item-header">
                                                        <div class="booking-status">
                                                            @if($booking->status == 'confirmed')
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
                                                                    <i class="fas fa-info-circle me-1"></i> {{ ucfirst($booking->status) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="booking-id">#{{ $booking->id }}</div>
                                                    </div>

                                                    <div class="booking-item-content">
                                                        <div class="booking-image">
                                                            @if(isset($booking->field->image))
                                                                <img src="{{ Storage::url($booking->field->image) }}" alt="{{ $booking->field->name ?? 'Lapangan' }}" class="field-image">
                                                            @else
                                                                <div class="field-image-placeholder">
                                                                    <i class="fas fa-futbol"></i>
                                                                </div>
                                                            @endif
                                                            <div class="field-type">{{ $booking->field->type ?? 'Lapangan' }}</div>
                                                        </div>

                                                        <div class="booking-details">
                                                            <h5 class="field-name">{{ $booking->field->name ?? 'Lapangan' }}</h5>
                                                            <div class="booking-info">
                                                                <div class="info-item">
                                                                    <i class="far fa-calendar-alt"></i>
                                                                    <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <i class="far fa-clock"></i>
                                                                    <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="booking-price">
                                                            <div class="price-value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                                            @php
                                                                $startTime = \Carbon\Carbon::parse($booking->start_time);
                                                                $endTime = \Carbon\Carbon::parse($booking->end_time);
                                                                $durationInHours = $startTime->diffInHours($endTime);
                                                            @endphp
                                                            <div class="price-duration">{{ $durationInHours }} jam</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Rental Bookings --}}
                            @if($payment->rentalBookings && count($payment->rentalBookings) > 0)
                                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                        <h5 class="m-0 fw-bold">Penyewaan Peralatan</h5>
                                        <span class="booking-count">{{ count($payment->rentalBookings) }} Item</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="booking-list">
                                            @foreach($payment->rentalBookings as $booking)
                                                <div class="booking-item">
                                                    <div class="booking-item-header">
                                                        <div class="booking-status">
                                                            @if($booking->status == 'confirmed')
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
                                                                    <i class="fas fa-info-circle me-1"></i> {{ ucfirst($booking->status) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="booking-id">#{{ $booking->id }}</div>
                                                    </div>

                                                    <div class="booking-item-content">
                                                        <div class="booking-image">
                                                            @if(isset($booking->rentalItem->image))
                                                                <img src="{{ Storage::url($booking->rentalItem->image) }}" alt="{{ $booking->rentalItem->name ?? 'Penyewaan' }}" class="field-image">
                                                            @else
                                                                <div class="field-image-placeholder">
                                                                    <i class="fas fa-box"></i>
                                                                </div>
                                                            @endif
                                                            <div class="field-type">Peralatan</div>
                                                        </div>

                                                        <div class="booking-details">
                                                            <h5 class="field-name">{{ $booking->rentalItem->name ?? 'Penyewaan' }}</h5>
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
                                                                    <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="booking-price">
                                                            <div class="price-value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                                            @php
                                                                $startTime = \Carbon\Carbon::parse($booking->start_time);
                                                                $endTime = \Carbon\Carbon::parse($booking->end_time);
                                                                $durationInHours = $startTime->diffInHours($endTime);
                                                            @endphp
                                                            <div class="price-duration">{{ $durationInHours }} jam</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Photographer Bookings --}}
                            @if($payment->photographerBookings && count($payment->photographerBookings) > 0)
                                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4 border-0">
                                        <h5 class="m-0 fw-bold">Fotografer</h5>
                                        <span class="booking-count">{{ count($payment->photographerBookings) }} Item</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="booking-list">
                                            @foreach($payment->photographerBookings as $booking)
                                                <div class="booking-item">
                                                    <div class="booking-item-header">
                                                        <div class="booking-status">
                                                            @if($booking->status == 'confirmed')
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
                                                                    <i class="fas fa-info-circle me-1"></i> {{ ucfirst($booking->status) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="booking-id">#{{ $booking->id }}</div>
                                                    </div>

                                                    <div class="booking-item-content">
                                                        <div class="booking-image">
                                                            @if(isset($booking->photographer->image))
                                                                <img src="{{ Storage::url($booking->photographer->image) }}" alt="{{ $booking->photographer->name ?? 'Fotografer' }}" class="field-image">
                                                            @else
                                                                <div class="field-image-placeholder">
                                                                    <i class="fas fa-camera"></i>
                                                                </div>
                                                            @endif
                                                            <div class="field-type">Fotografer</div>
                                                        </div>

                                                        <div class="booking-details">
                                                            <h5 class="field-name">{{ $booking->photographer->name ?? 'Fotografer' }}</h5>
                                                            <div class="booking-info">
                                                                <div class="info-item">
                                                                    <i class="far fa-calendar-alt"></i>
                                                                    <span>{{ \Carbon\Carbon::parse($booking->date ?? $booking->start_time)->format('d M Y') }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <i class="far fa-clock"></i>
                                                                    <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <i class="fas fa-camera-retro"></i>
                                                                    <span>{{ $booking->photographer->specialization ?? 'Umum' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="booking-price">
                                                            <div class="price-value">Rp {{ number_format($booking->price, 0, ',', '.') }}</div>
                                                            @php
                                                                $startTime = \Carbon\Carbon::parse($booking->start_time);
                                                                $endTime = \Carbon\Carbon::parse($booking->end_time);
                                                                $durationInHours = $startTime->diffInHours($endTime);
                                                            @endphp
                                                            <div class="price-duration">{{ $durationInHours }} jam</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Special Section for Membership Renewal --}}
                        @if(strpos($payment->order_id, 'RENEW-MEM-') === 0)
                            <div class="membership-renewal-info p-4 rounded-3 bg-light mt-3">
                                <h5 class="mb-3 fw-bold">
                                    <i class="fas fa-user-tag me-2 text-primary"></i>
                                    Perpanjangan Membership
                                </h5>
                                @php
                                    $subscription = \App\Models\MembershipSubscription::where('user_id', Auth::id())
                                        ->where('status', 'active')
                                        ->first();
                                @endphp
                                @if($subscription)
                                    <div class="membership-item p-3 bg-white rounded shadow-sm">
                                        <div class="membership-details">
                                            <div class="detail-row">
                                                <span class="detail-label">Lapangan:</span>
                                                <span class="detail-value">{{ $subscription->membership->field->name }}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Periode Baru:</span>
                                                <span class="detail-value">
                                                    {{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($subscription->end_date)->addMonths($subscription->membership->duration)->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted">Informasi membership tidak ditemukan.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Review functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Countdown timer functionality
            initCountdownTimer();
        });

        // Fungsi untuk inisialisasi countdown timer
        function initCountdownTimer() {
            const countdownEl = document.getElementById('countdown');

            if (countdownEl) {
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
                        countdownEl.innerHTML = '<span class="text-danger">Waktu pembayaran telah habis</span>';

                        // Reload halaman setelah 3 detik
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);

                        return;
                    }

                    // Hitung menit dan detik
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;

                    // Tampilkan dalam format MM:SS
                    countdownEl.innerHTML = `Sisa waktu: <span class="text-danger">${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}</span>`;
                }, 1000);
            }
        }
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
            align-items: center;
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
            font-size: 1.1rem;
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

        /* ==================== MEMBERSHIP RENEWAL ==================== */
        .membership-renewal-info {
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .membership-item {
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .membership-details {
            margin-top: 0.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .detail-label {
            color: #6c757d;
        }

        .detail-value {
            font-weight: 500;
        }

        /* ==================== DISCOUNT STYLING ==================== */
        .discount-code {
            display: inline-block;
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .text-success {
            color: #28a745 !important;
        }

        /* ==================== RESPONSIVE ADJUSTMENTS ==================== */
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
                align-items: flex-start;
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
                width: 100%;
            }

            .schedule-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .schedule-info {
                width: 100%;
            }

            .schedule-status {
                align-self: flex-end;
            }

            .membership-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .membership-price {
                text-align: left;
            }

            .membership-status {
                justify-content: flex-start;
            }
        }

        /* ==================== EXPIRATION WARNING ==================== */
        .expiration-warning {
            background-color: transparent;
        }

        .countdown-timer {
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>

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

        /* Booking Count */
        .booking-count {
            background-color: #f8f9fa;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* ==================== MEMBERSHIP MAIN CARD ==================== */
        .membership-main-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #f1f3f5;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .membership-main-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .membership-header {
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid #f1f3f5;
        }

        .membership-info {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
        }

        .membership-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffd700, #ffb347);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .membership-details {
            flex: 1;
        }

        .membership-title {
            font-weight: 700;
            color: #343a40;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .membership-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: #9e0620;
            width: 16px;
        }

        .membership-price {
            text-align: right;
        }

        .membership-price .price-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #198754;
        }

        .membership-status {
            display: flex;
            justify-content: flex-end;
        }

        .membership-description {
            margin-top: 12px;
            padding: 16px 20px 20px;
            background-color: rgba(158, 6, 32, 0.02);
            border-left: 3px solid #9e0620;
        }

        .membership-description p {
            color: #495057;
            line-height: 1.6;
        }

        /* ==================== SCHEDULE LISTS ==================== */
        .schedule-list {
            padding: 16px;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: white;
            border: 1px solid #f1f3f5;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .schedule-item:last-child {
            margin-bottom: 0;
        }

        .schedule-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .schedule-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .schedule-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            flex-shrink: 0;
            overflow: hidden;
        }

        .schedule-icon.field {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .schedule-icon.rental {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }

        .schedule-icon.photographer {
            background: linear-gradient(135deg, #fd7e14, #e83e8c);
        }

        .schedule-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .schedule-details {
            flex: 1;
        }

        .schedule-title {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .schedule-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .schedule-date,
        .schedule-time,
        .schedule-quantity,
        .schedule-specialty {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .schedule-status {
            flex-shrink: 0;
        }

        .included-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .included-badge::before {
            content: '✓';
            font-weight: bold;
        }

        /* ==================== REGULAR BOOKING ITEMS ==================== */
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
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .status-pill.pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        .status-pill.cancelled {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .status-pill.other {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.2);
        }

        /* Points Card */
        .points-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffd700, #ffb347);
            border-radius: 12px;
            flex-shrink: 0;
        }

        /* Session Alert */
        .alert {
            border-radius: 12px !important;
            border: none !important;
            padding: 16px 20px !important;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
            color: #28a745 !important;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
            color: #856404 !important;
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.1) !important;
            color: #055160 !important;
        }

        /* Card Headers */
        .card-header {
            background-color: white !important;
            border-bottom: 1px solid #f1f3f5 !important;
            padding: 16px 20px !important;
            font-weight: 600;
        }

        /* Utilities */
        .fw-bold {
            font-weight: 700 !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .rounded-3 {
            border-radius: 12px !important;
        }

        .rounded-4 {
            border-radius: 16px !important;
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
        }

        /* Badge */
        .badge {
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }

        /* Expiration Warning Specific */
        .countdown-timer {
            font-weight: 700;
            font-size: 0.95rem;
            margin-top: 8px;
        }

        .countdown-timer .text-danger {
            color: #dc3545 !important;
            font-weight: 700;
        }

        /* Mobile Responsiveness */
        @media (max-width: 991.98px) {
            .membership-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .membership-price {
                text-align: left;
                width: 100%;
            }

            .membership-status {
                justify-content: flex-start;
                width: 100%;
            }

            .schedule-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .schedule-meta {
                gap: 8px;
            }
        }

        @media (max-width: 767.98px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .breadcrumb-wrapper {
                height: 160px;
                margin-top: 0 !important;
            }

            .breadcrumb-link,
            .breadcrumb-item.active {
                font-size: 1.1rem;
                padding: 4px 8px;
            }

            .page-header {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }

            .page-header h2 {
                font-size: 1.5rem;
            }

            .payment-status-header {
                padding: 20px 16px;
            }

            .status-title {
                font-size: 1.3rem;
            }

            .membership-title {
                font-size: 1.1rem;
            }

            .membership-meta {
                gap: 8px;
            }

            .meta-item {
                font-size: 0.85rem;
            }

            .membership-price .price-value {
                font-size: 1.2rem;
            }

            .schedule-title {
                font-size: 0.95rem;
            }

            .schedule-meta {
                font-size: 0.8rem;
                gap: 8px;
            }

            .booking-item-content {
                padding: 12px;
            }

            .field-name {
                font-size: 1rem;
            }

            .info-item {
                font-size: 0.85rem;
            }

            .price-value {
                font-size: 1rem;
            }

            .amount-value {
                font-size: 1.4rem;
            }

            .summary-item {
                margin-bottom: 12px;
                padding-bottom: 12px;
            }

            .item-label {
                font-size: 0.9rem;
            }

            .item-value {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 575.98px) {
            .breadcrumb-wrapper {
                height: 140px;
            }

            .breadcrumb-link,
            .breadcrumb-item.active {
                font-size: 1rem;
                padding: 4px 6px;
            }

            .breadcrumb-link span,
            .breadcrumb-item.active span {
                display: none;
            }

            .payment-status-header {
                padding: 16px 12px;
            }

            .status-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .status-title {
                font-size: 1.1rem;
            }

            .membership-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .schedule-icon {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }

            .booking-image {
                width: 80px;
                min-width: 80px;
                height: 80px;
            }

            .booking-item-content {
                gap: 12px;
            }

            .schedule-item {
                padding: 12px;
            }

            .membership-header {
                padding: 16px;
            }

            .schedule-list {
                padding: 12px;
            }

            .booking-list {
                padding: 12px;
            }
        }

        /* Animation for hover effects */
        @keyframes slideUp {
            from {
                transform: translateY(10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .card {
            animation: slideUp 0.3s ease-out;
        }

        /* Print styles */
        @media print {
            .breadcrumb-wrapper,
            .btn-outline-sm,
            .payment-actions,
            .support-card {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }

            .page-header {
                border-bottom: 2px solid #dee2e6;
                padding-bottom: 1rem;
                margin-bottom: 2rem;
            }
        }

        /* Focus states for accessibility */
        .btn-outline-sm:focus,
        .btn-success-action:focus,
        .btn-warning-action:focus,
        .btn-support:focus {
            outline: 2px solid #9e0620;
            outline-offset: 2px;
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .card {
                border: 2px solid #000 !important;
            }

            .status-pill {
                border-width: 2px !important;
            }

            .payment-status-header {
                border: 2px solid #000;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .card,
            .booking-item,
            .schedule-item,
            .membership-main-card {
                transition: none;
            }

            .hover-shadow:hover {
                transform: none;
            }

            .btn-outline-sm:hover,
            .btn-success-action:hover,
            .btn-warning-action:hover,
            .btn-support:hover {
                transform: none;
            }
        }
    </style>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

@endsection
