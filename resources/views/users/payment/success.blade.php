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
                    <i class="fas fa-check-circle"></i>
                    <span>Pembayaran Berhasil</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm hover-shadow">
                    <div class="card-body p-5">
                        <div class="success-header text-center mb-4">
                            <div class="success-icon mb-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2 class="fw-bold mb-3">Pembayaran Berhasil</h2>
                            <p class="lead text-muted">Terima kasih! Pembayaran Anda telah berhasil diproses dan booking Anda telah dikonfirmasi.</p>
                        </div>

                        @if(isset($payment))
                        <div class="payment-details p-4 rounded-3 mb-4">
                            <h5 class="detail-title fw-bold mb-3">Detail Pembayaran</h5>
                            <div class="detail-item">
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-6">
                                        <div class="detail-label">
                                            <i class="fas fa-receipt text-primary me-2"></i>
                                            <span>Order ID</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="detail-value">
                                            <span class="order-id">{{ $payment->order_id }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-6">
                                        <div class="detail-label">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            <span>Total Pembayaran</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="detail-value">
                                            <span class="payment-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-3">
                                    <div class="col-sm-6">
                                        <div class="detail-label">
                                            <i class="fas fa-credit-card text-info me-2"></i>
                                            <span>Metode Pembayaran</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="detail-value">
                                            <span class="payment-method">{{ $payment->payment_type ?? 'Midtrans' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-sm-6">
                                        <div class="detail-label">
                                            <i class="fas fa-calendar-check text-warning me-2"></i>
                                            <span>Tanggal Pembayaran</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="detail-value">
                                            <span class="payment-date">{{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="action-buttons text-center">
                            <a href="{{ route('user.payment.history') }}" class="btn-outline">
                                <i class="fas fa-history me-2"></i>
                                <span>Lihat Riwayat Pembayaran</span>
                            </a>
                            <a href="{{ route('users.dashboard') }}" class="btn-primary">
                                <i class="fas fa-home me-2"></i>
                                <span>Kembali ke Dashboard</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern Payment Success Styling */

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

        /* Success Header */
        .success-header {
            padding: 20px 0;
        }

        .success-icon {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background-color: #28a745;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 3.5rem;
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
        }

        /* Payment Details */
        .payment-details {
            background-color: #f8f9fa;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

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
            transition: all 0.3s ease;
        }

        .detail-label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #495057;
        }

        .detail-value {
            font-weight: 700;
            text-align: right;
        }

        .order-id {
            color: #495057;
            font-family: monospace;
            padding: 4px 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .payment-amount {
            color: #28a745;
        }

        .payment-method, .payment-date {
            color: #495057;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: transparent;
            color: #9e0620;
            border: 2px solid #9e0620;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-outline:hover {
            background-color: rgba(158, 6, 32, 0.1);
            transform: translateY(-2px);
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

            .success-icon {
                width: 90px;
                height: 90px;
                font-size: 2.8rem;
            }

            .detail-value {
                text-align: left;
                margin-top: 5px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-outline, .btn-primary {
                width: 100%;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

@endsection
