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
                <li class="breadcrumb-item active">
                    <i class="fas fa-user-tag"></i>
                    <span>Membership Saya</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 fw-bold">Membership Saya</h1>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($subscriptions->isEmpty())
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-user-tag fa-3x text-muted"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Belum Ada Membership Aktif</h5>
                    <p class="text-muted mb-4">Anda belum memiliki paket membership aktif. Dapatkan keuntungan dengan membeli paket membership kami.</p>
                    <a href="{{ route('user.membership.index') }}" class="btn-payment">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span>Pilih Paket Membership</span>
                    </a>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($subscriptions as $subscription)
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow h-100">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold">{{ $subscription->membership->name }}</h5>
                                    <div class="membership-badge
                                        @if($subscription->status === 'active') badge-success
                                        @elseif($subscription->status === 'expired') badge-danger
                                        @else badge-warning @endif">
                                        <i class="fas fa-circle me-1 small"></i>
                                        <span>{{ ucfirst($subscription->status) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <div class="membership-info-item">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <span>{{ $subscription->membership->field->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="membership-info-item">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                        <span>Mulai: {{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}</span>
                                    </div>
                                    <div class="membership-info-item">
                                        <i class="fas fa-hourglass-end text-primary"></i>
                                        <span>Berakhir: {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</span>
                                    </div>
                                    <div class="membership-info-item">
                                        <i class="fas fa-sync-alt text-primary"></i>
                                        <span>Status Perpanjangan: {{ ucfirst($subscription->renewal_status) }}</span>
                                    </div>
                                </div>

                                <div class="membership-actions">
                                    <a href="{{ route('user.membership.subscription.detail', $subscription->id) }}" class="btn-outline">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span>Detail</span>
                                    </a>
                                    @if($subscription->status === 'active')
                                    @php
                                        $pendingPayment = App\Models\Payment::where('user_id', Auth::id())
                                            ->where('payment_type', 'membership_renewal')
                                            ->where('transaction_status', 'pending')
                                            ->first();
                                    @endphp

                                    @if($pendingPayment)
                                        <a href="{{ route('user.membership.renewal.pay', $pendingPayment->id) }}" class="btn-primary">
                                            <i class="fas fa-sync me-2"></i>
                                            <span>Lanjutkan Pembayaran</span>
                                        </a>
                                    @elseif($subscription->renewal_status !== 'renewed')
                                        <button onclick="renewMembership({{ $subscription->id }})" class="btn-primary">
                                            <i class="fas fa-sync me-2"></i>
                                            <span>Perpanjang</span>
                                        </button>
                                    @endif
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function renewMembership(subscriptionId) {
            if (confirm('Anda yakin ingin memperpanjang membership ini?')) {
                // Tampilkan loading state
                const button = event.currentTarget;
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>Memproses...</span>';

                // Kirim request ke endpoint untuk membuat invoice perpanjangan
                fetch('/membership/create-renewal/' + subscriptionId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect ke halaman pembayaran
                        window.location.href = data.payment_url;
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat membuat invoice perpanjangan');
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan pada sistem. Silakan coba lagi nanti.');
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
            }
        }
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
<style>
    /* Tambahan styles khusus halaman membership */
    .membership-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .badge-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .membership-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 8px 12px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .membership-info-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .membership-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        background-color: white;
        color: #6c757d;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-outline:hover {
        background-color: #f8f9fa;
        color: #495057;
        text-decoration: none;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        background-color: #9e0620;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #7d0318;
        color: white;
        text-decoration: none;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background-color: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
</style>
@endsection
