@extends('layouts.app')
@section('content')
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Riwayat Poin</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i>
                                    Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Riwayat Poin</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Points Balance Card -->
        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-1">Riwayat Poin</h3>
                        <p class="text-muted mb-0">Catatan transaksi poin pada akun Anda</p>
                    </div>
                    <div class="text-end">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="d-block text-muted fs-6">Poin Anda</span>
                                <span class="fs-2 fw-bold" style="color: #9E0620;">{{ number_format($user->points) }}</span>
                            </div>
                            <div class="points-icon">
                                <i class="fas fa-coins fa-2x" style="color: #FFD700;"></i>
                            </div>
                        </div>
                        <a href="{{ route('user.points.index') }}" class="btn btn-primary btn-sm rounded-pill mt-2">
                            <i class="fas fa-gift me-1"></i> Tukar Poin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nav tabs -->
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4">
                <ul class="nav nav-tabs custom-tabs mb-4" id="pointsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions"
                            type="button" role="tab" aria-controls="transactions" aria-selected="true">
                            <i class="fas fa-exchange-alt me-2"></i>Transaksi Poin
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="redemptions-tab" data-bs-toggle="tab" data-bs-target="#redemptions"
                            type="button" role="tab" aria-controls="redemptions" aria-selected="false">
                            <i class="fas fa-ticket-alt me-2"></i>Penukaran Voucher
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pointsTabContent">
                    <!-- Transactions Tab -->
                    <div class="tab-pane fade show active" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                        @if($transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover custom-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Deskripsi</th>
                                            <th>Tipe</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}</td>
                                                <td>{{ $transaction->description }}</td>
                                                <td>
                                                    @if($transaction->type == 'earn')
                                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-white p-2">
                                                            <i class="fas fa-plus-circle me-1"></i>Earn
                                                        </span>
                                                    @elseif($transaction->type == 'redeem')
                                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-white p-2">
                                                            <i class="fas fa-minus-circle me-1"></i>Redeem
                                                        </span>
                                                    @elseif($transaction->type == 'expired')
                                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-white p-2">
                                                            <i class="fas fa-times-circle me-1"></i>Expired
                                                        </span>
                                                    @elseif($transaction->type == 'admin')
                                                        <span class="badge rounded-pill bg-info bg-opacity-10 text-info p-2">
                                                            <i class="fas fa-user-shield me-1"></i>Admin
                                                        </span>
                                                    @else
                                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary p-2">
                                                            <i class="fas fa-circle me-1"></i>{{ $transaction->type }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold {{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $transactions->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-history fa-4x text-muted"></i>
                                </div>
                                <p class="mb-3 text-muted">Belum ada riwayat transaksi poin.</p>
                                <a href="{{ route('user.fields.index') }}" class="btn btn-primary rounded-pill">
                                    <i class="fas fa-calendar-plus me-2"></i>Mulai Booking
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Redemptions Tab -->
                    <div class="tab-pane fade" id="redemptions" role="tabpanel" aria-labelledby="redemptions-tab">
                        @if($redemptions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover custom-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Voucher</th>
                                            <th>Kode</th>
                                            <th>Status</th>
                                            <th class="text-end">Poin</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($redemptions as $redemption)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($redemption->created_at)->format('d M Y H:i') }}</td>
                                                <td>{{ $redemption->pointVoucher->name }}</td>
                                                <td><code class="voucher-code">{{ $redemption->discount_code }}</code></td>
                                                <td>
                                                    @if($redemption->status == 'active')
                                                        @if($redemption->expires_at && \Carbon\Carbon::parse($redemption->expires_at)->isPast())
                                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2">
                                                                <i class="fas fa-calendar-times me-1"></i>Kadaluarsa
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-white p-2">
                                                                <i class="fas fa-check-circle me-1"></i>Aktif
                                                            </span>
                                                        @endif
                                                    @elseif($redemption->status == 'used')
                                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-white p-2">
                                                            <i class="fas fa-check-double me-1"></i>Digunakan
                                                        </span>
                                                    @elseif($redemption->status == 'expired')
                                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-white p-2">
                                                            <i class="fas fa-calendar-times me-1"></i>Kadaluarsa
                                                        </span>
                                                    @elseif($redemption->status == 'cancelled')
                                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary p-2">
                                                            <i class="fas fa-ban me-1"></i>Dibatalkan
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end text-danger">-{{ number_format($redemption->points_used) }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('user.points.redemption-detail', $redemption->id) }}"
                                                       class="btn btn-outline-primary btn-sm rounded-pill">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $redemptions->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-ticket-alt fa-4x text-muted"></i>
                                </div>
                                <p class="mb-3 text-muted">Anda belum pernah menukarkan poin.</p>
                                <a href="{{ route('user.points.index') }}" class="btn btn-primary rounded-pill">
                                    <i class="fas fa-gift me-2"></i>Tukar Poin Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
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

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Points Icon Styling */
        .points-icon {
            background-color: rgba(255, 215, 0, 0.15);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Tab Navigation */
        .tab-navigation {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .tab-link {
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            padding: 0.75rem 0;
            position: relative;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .tab-link.active {
            color: #9E0620;
            font-weight: 600;
        }

        .tab-link.active::after {
            content: '';
            position: absolute;
            bottom: -11px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #9E0620;
        }

        .tab-link:hover:not(.active) {
            color: #495057;
        }

        /* Table Styling */
        .custom-table thead th {
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .custom-table tbody td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }

        /* Badge Styling */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
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

        /* Voucher Code Styling */
        .voucher-code {
            background-color: #f8f9fa;
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            letter-spacing: 1px;
            font-family: monospace;
            font-size: 0.9rem;
        }

        /* Empty State Styling */
        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
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
    </style>
@endsection
