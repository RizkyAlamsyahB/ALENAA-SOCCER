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
                    <a href="{{ route('user.membership.my-memberships') }}" class="breadcrumb-link">
                        <i class="fas fa-user-tag"></i>
                        <span>Membership Saya</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-sync"></i>
                    <span>Perpanjangan Membership</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <!-- Header dengan ikon -->
                        <div class="text-center bg-light p-4 rounded-top-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle p-3 mb-3" style="width: 70px; height: 70px;">
                                <i class="fas fa-sync fa-2x"></i>
                            </div>
                            <h4 class="fw-bold mb-0">Detail Perpanjangan Membership</h4>
                        </div>

                        <!-- Notification countdown -->
                        <div class="bg-warning-subtle p-3 border-top border-bottom">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span>Mohon selesaikan pembayaran sebelum: <span id="countdown-timer" class="fw-bold">{{ \Carbon\Carbon::parse($payment->expires_at)->format('d M Y H:i') }}</span></span>
                            </div>
                        </div>

                        <!-- Informasi Paket -->
                        <div class="p-4">
                            <h5 class="fw-bold mb-3">Informasi Paket</h5>

                            <div class="border border-1 rounded-4 p-4 mb-4 position-relative">
                                <h5 class="fw-bold mb-0">{{ $membership->name }}</h5>
                                <p class="text-muted small mb-3">Perpanjangan Keanggotaan</p>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="text-danger me-2">
                                                <i class="fas fa-user-tag"></i>
                                            </div>
                                            <div>{{ ucfirst($membership->type) }} Package</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="text-danger me-2">
                                                <i class="fas fa-calendar-week"></i>
                                            </div>
                                            <div>3x permainan/minggu</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="text-danger me-2">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div>{{ $membership->session_duration }} jam/sesi</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="text-danger me-2">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div>Durasi {{ $membership->duration }} bulan</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="position-absolute end-0 top-50 translate-middle-y pe-4">
                                    <div class="fw-bold text-danger fs-5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Detail Pembayaran -->
                            <div class="border border-1 rounded-4 p-4 mb-4">
                                <h5 class="fw-bold mb-3">Detail Pembayaran</h5>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>No. Invoice</div>
                                    <div class="fw-bold">{{ $payment->order_id }}</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>Tanggal Invoice</div>
                                    <div>{{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>Status</div>
                                    <div>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>
                                            Menunggu Pembayaran
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>Total Pembayaran</div>
                                    <div class="fw-bold text-danger fs-5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Tombol pembayaran -->
                            <div class="text-center mt-4">
                                <button id="pay-button" class="btn btn-danger btn-lg px-5 py-3 rounded-3 fw-bold">
                                    <i class="fas fa-lock me-2"></i>
                                    Bayar Sekarang
                                </button>
                                <p class="text-muted small mt-3">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Pembayaran aman &amp; terenkripsi
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Countdown function
            const expiresAt = new Date("{{ $payment->expires_at }}").getTime();

            // Update countdown every second
            const countdownTimer = setInterval(function() {
                // Current time
                const now = new Date().getTime();

                // Time difference
                const distance = expiresAt - now;

                // If time has expired
                if (distance < 0) {
                    clearInterval(countdownTimer);
                    document.getElementById("countdown-timer").innerHTML = "<span class='text-danger'>Waktu Habis</span>";

                    // Disable payment button
                    document.getElementById("pay-button").disabled = true;
                    document.getElementById("pay-button").innerHTML = "<i class='fas fa-times-circle me-2'></i>Waktu Pembayaran Habis";

                    // Show alert
                    alert("Waktu pembayaran telah habis. Silakan kembali ke halaman membership untuk proses perpanjangan baru.");

                    // Redirect after 3 seconds
                    setTimeout(function() {
                        window.location.href = "{{ route('user.membership.my-memberships') }}";
                    }, 3000);
                }
            }, 1000);

            // Payment button handler
            const payButton = document.getElementById('pay-button');

            payButton.addEventListener('click', function() {
                // Tampilkan snap payment page
                snap.pay('{{ $snap_token }}', {
                    onSuccess: function(result) {
                        window.location.href = '{{ route("user.payment.success") }}?order_id={{ $payment->order_id }}';
                    },
                    onPending: function(result) {
                        window.location.href = '{{ route("user.payment.unfinish") }}?order_id={{ $payment->order_id }}';
                    },
                    onError: function(result) {
                        window.location.href = '{{ route("user.payment.error") }}?order_id={{ $payment->order_id }}';
                    },
                    onClose: function() {
                        // Jika user menutup popup tanpa menyelesaikan pembayaran
                        alert('Anda menutup popup pembayaran tanpa menyelesaikan transaksi');
                    }
                });
            });
        });
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
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
