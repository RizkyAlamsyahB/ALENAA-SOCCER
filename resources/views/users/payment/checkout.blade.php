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
                    <a href="{{ route('user.cart.view') }}" class="breadcrumb-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Keranjang</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-credit-card"></i>
                    <span>Pembayaran</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 fw-bold">Pembayaran</h1>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Ringkasan Pesanan</h5>
                            <div class="order-badge">
                                <i class="fas fa-receipt me-2"></i>
                                <span>Order ID: {{ $order_id ?? $payment->order_id }}</span>
                            </div>
                        </div>

                    </div>
                    <div class="card-body p-4">
                        <div class="payment-header mb-4 text-center">
                            <div class="payment-icon mb-3">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h5 class="fw-bold">Detail Pembayaran</h5>
                            <!-- Menambahkan countdown timer di bawah total pembayaran -->
                            <div class="payment-expires-wrapper mt-2 mb-3">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="payment-expire-notice">
                                        <i class="fas fa-clock text-warning me-2"></i>
                                        <span>Mohon selesaikan pembayaran sebelum:</span>
                                    </div>
                                    <div id="payment-countdown" class="payment-countdown ms-2">
                                        <span id="countdown-minutes">00</span>:<span id="countdown-seconds">00</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="detail-items mb-4">
                            <h6 class="detail-title fw-bold mb-3">Item yang Dibeli</h6>

                            {{-- Jika data berasal dari cart_items --}}
                            @if (isset($cart_items))
                                @foreach ($cart_items as $item)
                                    <div class="detail-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8 col-sm-7 mb-2 mb-md-0">
                                                <div class="item-details">
                                                    @if ($item->type == 'field_booking')
                                                        <?php
                                                        $field = App\Models\Field::find($item->item_id);
                                                        $fieldName = $field ? $field->name : 'Booking Lapangan';
                                                        $fieldType = $field ? $field->type : 'Olahraga';
                                                        ?>
                                                        <h5 class="item-title fw-bold mb-1">{{ $fieldName }}</h5>
                                                        <div class="item-category mb-2">
                                                            <span class="type-badge">{{ $fieldType }}</span>
                                                        </div>
                                                        <div class="item-info">
                                                            <div class="info-badge">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($item->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @elseif($item->type == 'rental_item')
                                                        <?php
                                                        $rentalItem = App\Models\RentalItem::find($item->item_id);
                                                        $itemName = $rentalItem ? $rentalItem->name : 'Penyewaan Peralatan';
                                                        ?>
                                                        <h5 class="item-title fw-bold mb-1">{{ $itemName }}</h5>
                                                        <div class="item-category mb-2">
                                                            <span class="type-badge">Penyewaan Peralatan</span>
                                                        </div>
                                                        <div class="item-info">
                                                            <div class="info-badge">
                                                                <i class="fas fa-box"></i>
                                                                <span>Jumlah: {{ $item->quantity }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($item->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @elseif($item->type == 'membership')
    <?php
    $membership = App\Models\Membership::find($item->item_id);
    $membershipName = $membership ? $membership->name : 'Keanggotaan';
    $membershipType = $membership ? $membership->type : 'regular';
    $sessionDuration = $membership ? $membership->session_duration : '1';
    ?>
    <h5 class="item-title fw-bold mb-1">{{ $membershipName }}</h5>
    <div class="item-category mb-2">
        <span class="type-badge">Keanggotaan</span>
    </div>
    <div class="item-info">
        <div class="info-badge">
            <i class="fas fa-user-tag"></i>
            <span>{{ ucfirst($membershipType) }} Package</span>
        </div>
        <div class="info-badge">
            <i class="fas fa-calendar-week"></i>
            <span>3x permainan/minggu</span>
        </div>
        <div class="info-badge">
            <i class="fas fa-clock"></i>
            <span>{{ $sessionDuration }} jam/sesi</span>
        </div>
        @if(!empty($item->membership_sessions))
            <?php $sessions = json_decode($item->membership_sessions, true); ?>
            <div class="info-badge">
                <i class="fas fa-list"></i>
                <span>{{ count($sessions) }} jadwal tetap</span>
            </div>

            <!-- Tambahkan kode berikut untuk menampilkan detail jadwal -->
            <div class="mt-2">
                <small class="text-muted">Jadwal yang dipilih:</small>
                <ul class="list-unstyled ms-3 small">
                    @foreach($sessions as $index => $session)
                        <li class="mt-1">
                            <i class="fas fa-calendar-day text-primary"></i>
                            {{ \Carbon\Carbon::parse($session['date'])->format('d M Y') }}
                            <i class="fas fa-clock ms-1 text-primary"></i>
                            {{ \Carbon\Carbon::parse($session['start_time'])->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($session['end_time'])->format('H:i') }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
                                                        @elseif($item->type == 'photographer')
                                                        <?php
                                                        $photographer = App\Models\Photographer::find($item->item_id);
                                                        $photographerName = $photographer ? $photographer->name : 'Jasa Fotografer';
                                                        ?>
                                                        <h5 class="item-title fw-bold mb-1">{{ $photographerName }}</h5>
                                                        <div class="item-category mb-2">
                                                            <span class="type-badge">Jasa Fotografer</span>
                                                        </div>
                                                        <div class="item-info">
                                                            <div class="info-badge">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($item->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-5 text-end">
                                                <div class="item-price">
                                                    <span class="price">Rp
                                                        {{ number_format($item->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Jika data berasal dari allBookings (dari controller yang baru) --}}
                            @elseif(isset($allBookings))
                                {{-- Field Bookings --}}
                                @if (isset($allBookings['field_bookings']))
                                    @foreach ($allBookings['field_bookings'] as $booking)
                                        <div class="detail-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-7 mb-2 mb-md-0">
                                                    <div class="item-details">
                                                        <h5 class="item-title fw-bold mb-1">{{ $booking->field->name }}
                                                        </h5>
                                                        <div class="item-category mb-2">
                                                            <span class="type-badge">{{ $booking->field->type }}</span>
                                                        </div>
                                                        <div class="item-info">
                                                            <div class="info-badge">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-5 text-end">
                                                    <div class="item-price">
                                                        <span class="price">Rp
                                                            {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- Rental Bookings --}}
                                @if (isset($allBookings['rental_bookings']))
                                    @foreach ($allBookings['rental_bookings'] as $booking)
                                        <div class="detail-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-7 mb-2 mb-md-0">
                                                    <div class="item-details">
                                                        <h5 class="item-title fw-bold mb-1">
                                                            {{ $booking->rentalItem->name }}</h5>
                                                        <div class="item-category mb-2">
                                                            <span class="type-badge">Penyewaan Peralatan</span>
                                                        </div>
                                                        <div class="item-info">
                                                            <div class="info-badge">
                                                                <i class="fas fa-box"></i>
                                                                <span>Jumlah: {{ $booking->quantity }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-5 text-end">
                                                    <div class="item-price">
                                                        <span class="price">Rp
                                                            {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- Membership Subscriptions --}}
                                @if (isset($allBookings['membership_subscriptions']))
                                    @foreach ($allBookings['membership_subscriptions'] as $subscription)
                                        <div class="detail-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-7 mb-2 mb-md-0">
                                                    <div class="item-details">
                                                        <h5 class="item-title fw-bold mb-1">{{ $subscription->membership->name }}</h5>
                                                        <div class="item-category mb-2">
                                                            <span class="type-badge">Keanggotaan</span>
                                                        </div>
                                                        <div class="item-info">
                                                            <div class="info-badge">
                                                                <i class="fas fa-user-tag"></i>
                                                                <span>{{ ucfirst($subscription->membership->type) }} Package</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="fas fa-calendar-week"></i>
                                                                <span>3x permainan/minggu</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="fas fa-clock"></i>
                                                                <span>{{ $subscription->membership->session_duration }} jam/sesi</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-5 text-end">
                                                    <div class="item-price">
                                                        <span class="price">Rp {{ number_format($subscription->price, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- Photographer Bookings --}}
                                @if (isset($allBookings['photographer_bookings']))
                                    @foreach ($allBookings['photographer_bookings'] as $booking)
                                        <div class="detail-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-8 col-sm-7 mb-2 mb-md-0">
                                                    <div class="item-details">
                                                        <h5 class="item-title fw-bold mb-1">
                                                            {{ $booking->photographer->name }}</h5>
                                                        <div class="item-category mb-2">
                                                            <span class="type-badge">Jasa Fotografer</span>
                                                        </div>
                                                        <div class="item-info">
                                                            <div class="info-badge">
                                                                <i class="fas fa-camera"></i>
                                                                <span>{{ $booking->photographer->specialization ?? 'Umum' }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-5 text-end">
                                                    <div class="item-price">
                                                        <span class="price">Rp
                                                            {{ number_format($booking->price, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                {{-- Jika menggunakan format data lama via bookings --}}
                            @elseif(isset($bookings))
                                @foreach ($bookings as $booking)
                                    <div class="detail-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8 col-sm-7 mb-2 mb-md-0">
                                                <div class="item-details">
                                                    <h5 class="item-title fw-bold mb-1">{{ $booking->field->name }}</h5>
                                                    <div class="item-category mb-2">
                                                        <span class="type-badge">{{ $booking->field->type }}</span>
                                                    </div>
                                                    <div class="item-info">
                                                        <div class="info-badge">
                                                            <i class="far fa-calendar-alt"></i>
                                                            <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</span>
                                                        </div>
                                                        <div class="info-badge">
                                                            <i class="far fa-clock"></i>
                                                            <span>
                                                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-5 text-end">
                                                <div class="item-price">
                                                    <span class="price">Rp
                                                        {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="payment-summary p-4 rounded-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="summary-label">Subtotal</div>
                                <div class="summary-value">Rp
                                    {{ number_format(isset($original_amount) ? $original_amount : $total_price, 0, ',', '.') }}
                                </div>
                            </div>

                            @if (isset($discount_amount) && $discount_amount > 0)
                                <div class="d-flex justify-content-between align-items-center mb-2 text-success">
                                    <div class="summary-label">
                                        <i class="fas fa-tag me-2"></i>Diskon
                                    </div>
                                    <div class="summary-value">- Rp {{ number_format($discount_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Total Pembayaran</h5>
                                <h5 class="fw-bold total-price mb-0">
                                    Rp
                                    {{ number_format(isset($total_price) ? $total_price : $payment->amount, 0, ',', '.') }}
                                </h5>
                            </div>
                        </div>

                        <div class="payment-action text-center">
                            <button id="pay-button" class="btn-payment">
                                <i class="fas fa-lock me-2"></i>
                                <span>Bayar Sekarang</span>
                            </button>
                            <div class="mt-3">
                                <a href="{{ route('user.cart.view') }}" class="back-link">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Kembali ke Keranjang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');

            payButton.addEventListener('click', function() {
                // Tampilkan snap payment page
                snap.pay('{{ $snap_token }}', {
                    onSuccess: function(result) {
                        @if (isset($is_continue_payment) && $is_continue_payment)
                            window.location.href =
                                '{{ route('user.payment.success') }}?order_id={{ $original_order_id }}&payment_id={{ $payment_id }}';
                        @else
                            window.location.href =
                                '{{ route('user.payment.success') }}?order_id={{ $order_id }}';
                        @endif
                    },
                    onPending: function(result) {
                        @if (isset($is_continue_payment) && $is_continue_payment)
                            window.location.href =
                                '{{ route('user.payment.unfinish') }}?order_id={{ $original_order_id }}&payment_id={{ $payment_id }}';
                        @else
                            window.location.href =
                                '{{ route('user.payment.unfinish') }}?order_id={{ $order_id }}';
                        @endif
                    },
                    onError: function(result) {
                        @if (isset($is_continue_payment) && $is_continue_payment)
                            window.location.href =
                                '{{ route('user.payment.error') }}?order_id={{ $original_order_id }}&payment_id={{ $payment_id }}';
                        @else
                            window.location.href =
                                '{{ route('user.payment.error') }}?order_id={{ $order_id }}';
                        @endif
                    },
                    onClose: function() {
                        // Jika user menutup popup tanpa menyelesaikan pembayaran
                        alert('Anda menutup popup pembayaran tanpa menyelesaikan transaksi');
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get expiration time from server
            const expiresAt = new Date("{{ $expires_at }}").getTime();

            // Update countdown every second
            const countdownTimer = setInterval(function() {
                // Current time
                const now = new Date().getTime();

                // Time difference
                const distance = expiresAt - now;

                // Calculate minutes and seconds
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display countdown
                document.getElementById("countdown-minutes").innerHTML = minutes.toString().padStart(2,
                    '0');
                document.getElementById("countdown-seconds").innerHTML = seconds.toString().padStart(2,
                    '0');

                // If time has expired
                if (distance < 0) {
                    clearInterval(countdownTimer);
                    document.getElementById("countdown-minutes").innerHTML = "00";
                    document.getElementById("countdown-seconds").innerHTML = "00";

                    // Disable payment button
                    document.getElementById("pay-button").disabled = true;
                    document.getElementById("pay-button").innerHTML =
                        "<i class='fas fa-times-circle me-2'></i><span>Waktu Pembayaran Habis</span>";

                    // Show expiration message with Bootstrap alert
                    const alertContainer = document.createElement('div');
                    alertContainer.className = 'alert alert-danger alert-dismissible fade show';
                    alertContainer.setAttribute('role', 'alert');
                    alertContainer.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                Waktu pembayaran telah habis. Silakan kembali ke keranjang dan checkout ulang.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

                    // Insert alert at the top of the card
                    const cardBody = document.querySelector('.card-body');
                    cardBody.insertBefore(alertContainer, cardBody.firstChild);

                    // Redirect to cart page after 3 seconds
                    setTimeout(function() {
                        window.location.href =
                            "{{ route('user.payment.error') }}?order_id={{ $order_id ?? $payment->order_id }}";
                    }, 3000);
                }
            }, 1000);
        });
    </script>
    <style>
        /* Modern Payment Styling */

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

/* Card Styling */
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

.card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
}

/* Order Badge */
.order-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background-color: #f8f9fa;
    color: #6c757d;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Payment Header */
.payment-header {
    padding: 20px 0;
}

.payment-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color: #9e0620;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2rem;
    box-shadow: 0 10px 20px rgba(158, 6, 32, 0.2);
}

/* Detail Items */
.detail-title {
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    margin-bottom: 16px;
}

.detail-item {
    padding: 16px;
    background-color: #fff;
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.detail-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.item-title {
    font-size: 1.1rem;
    margin-bottom: 8px;
    color: #212529;
}

.item-category {
    margin-bottom: 8px;
}

.type-badge {
    display: inline-block;
    padding: 5px 12px;
    background-color: #f8f9fa;
    color: #6c757d;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.item-info {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
}

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background-color: #f8f9fa;
    color: #495057;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.info-badge i {
    color: #9e0620;
}

.detail-item:hover .info-badge {
    background-color: #e9ecef;
}

.detail-item:hover .type-badge {
    background-color: #e9ecef;
}

.item-price {
    font-weight: 700;
    color: #9e0620;
    font-size: 1.1rem;
}

/* Payment Summary */
.payment-summary {
    background-color: #fff8f8;
    border: 1px dashed rgba(158, 6, 32, 0.2);
}

.total-price {
    font-size: 1.25rem;
    color: #9e0620;
}

/* Payment Button */
.btn-payment {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 36px;
    background-color: #9e0620;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(158, 6, 32, 0.2);
}

.btn-payment:hover {
    background-color: #7d0318;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(158, 6, 32, 0.25);
}

.back-link {
    display: inline-flex;
    align-items: center;
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.back-link:hover {
    color: #9e0620;
    transform: translateX(-5px);
}

/* Payment Expires Styling */
.payment-expires-wrapper {
    text-align: center;
    padding: 8px;
    background-color: #fff8e1;
    border-radius: 8px;
}

.payment-expire-notice {
    font-size: 14px;
    color: #555;
}

.payment-countdown {
    font-weight: bold;
    font-size: 16px;
    color: #FF5722;
}

/* Alert Styling */
.alert {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.btn-close {
    font-size: 0.8rem;
}

/* Summary Items */
.summary-label {
    color: #6c757d;
    font-weight: 500;
}

.summary-value {
    font-weight: 600;
    color: #212529;
}

/* Membership Sessions */
.membership-sessions {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px dashed #dee2e6;
}

.membership-sessions .info-badge {
    margin-bottom: 5px;
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

    .item-price {
        text-align: left;
        margin-top: 10px;
    }

    .payment-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }

    .item-info {
        flex-direction: column;
        gap: 5px;
    }

    .info-badge {
        width: 100%;
    }

    .detail-item {
        padding: 12px;
    }

    .payment-summary {
        padding: 12px !important;
    }

    .btn-payment {
        width: 100%;
    }
}

/* Membership Type Colors */
.badge-bronze {
    background-color: #cd7f32 !important;
    color: white !important;
}

.badge-silver {
    background-color: #c0c0c0 !important;
    color: white !important;
}

.badge-gold {
    background-color: #ffd700 !important;
    color: #212529 !important;
}

/* Animation for Selected Items */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(158, 6, 32, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(158, 6, 32, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(158, 6, 32, 0);
    }
}

.detail-item:hover {
    animation: pulse 1.5s infinite;
}
    </style>
@endsection
