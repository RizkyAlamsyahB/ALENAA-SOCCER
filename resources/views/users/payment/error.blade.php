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
                    <i class="fas fa-times-circle"></i>
                    <span>Pembayaran Gagal</span>
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
                        <div class="failed-header text-center mb-4">
                            <div class="failed-icon mb-3">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h2 class="fw-bold mb-3">Pembayaran Gagal</h2>
                            <p class="lead text-muted">Maaf, pembayaran Anda tidak dapat diproses. Terjadi kesalahan selama proses pembayaran.</p>
                        </div>

                        @if(isset($errorMessage))
                        <div class="error-message p-4 rounded-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                <h5 class="fw-bold mb-0">Pesan Error</h5>
                            </div>
                            <p class="mb-0">{{ $errorMessage }}</p>
                        </div>
                        @endif

                        <div class="failure-reasons p-4 rounded-3 mb-4">
                            <h5 class="reason-title fw-bold mb-3">Kemungkinan penyebab kegagalan:</h5>
                            <div class="reason-list">
                                <div class="reason-item">
                                    <div class="reason-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="reason-text">
                                        Kartu kredit atau rekening bank Anda tidak memiliki saldo yang cukup
                                    </div>
                                </div>
                                <div class="reason-item">
                                    <div class="reason-icon">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div class="reason-text">
                                        Metode pembayaran yang Anda pilih sedang mengalami gangguan
                                    </div>
                                </div>
                                <div class="reason-item">
                                    <div class="reason-icon">
                                        <i class="fas fa-hourglass-end"></i>
                                    </div>
                                    <div class="reason-text">
                                        Waktu pembayaran telah habis (timeout)
                                    </div>
                                </div>
                                <div class="reason-item">
                                    <div class="reason-icon">
                                        <i class="fas fa-wifi"></i>
                                    </div>
                                    <div class="reason-text">
                                        Masalah koneksi internet selama proses pembayaran
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="solution-message p-4 rounded-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <h5 class="fw-bold mb-0">Apa yang bisa Anda lakukan?</h5>
                            </div>
                            <p class="mb-0">Anda dapat mencoba pembayaran kembali dengan metode pembayaran yang sama atau berbeda. Pastikan Anda memiliki koneksi internet yang stabil dan saldo yang mencukupi sebelum melakukan pembayaran ulang.</p>
                        </div>

                        <div class="action-buttons text-center">
                            <a href="{{ route('user.cart.view') }}" class="btn-outline">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span>Kembali ke Keranjang</span>
                            </a>
                            <a href="{{ route('user.cart.checkout') }}" class="btn-primary">
                                <i class="fas fa-redo me-2"></i>
                                <span>Coba Bayar Lagi</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern Payment Failed Styling */

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

        /* Failed Status Header */
        .failed-header {
            padding: 20px 0;
        }

        .failed-icon {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background-color: #dc3545;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 3.5rem;
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.2);
        }

        /* Error Message */
        .error-message {
            background-color: rgba(220, 53, 69, 0.1);
            border-radius: 12px;
            border-left: 4px solid #dc3545;
        }

        /* Failure Reasons */
        .failure-reasons {
            background-color: #f8f9fa;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .reason-title {
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            margin-bottom: 16px;
        }

        .reason-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .reason-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .reason-icon {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #9e0620;
            flex-shrink: 0;
        }

        .reason-text {
            flex: 1;
            padding-top: 10px;
            color: #495057;
        }

        /* Solution Message */
        .solution-message {
            background-color: rgba(255, 193, 7, 0.1);
            border-radius: 12px;
            border-left: 4px solid #ffc107;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 2rem;
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

            .failed-icon {
                width: 90px;
                height: 90px;
                font-size: 2.8rem;
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
