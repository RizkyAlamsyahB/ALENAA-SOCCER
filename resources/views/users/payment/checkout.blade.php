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
                                <span>Order ID: {{ $order_id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="payment-header mb-4 text-center">
                            <div class="payment-icon mb-3">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h5 class="fw-bold">Detail Pembayaran</h5>
                        </div>

                        <div class="detail-items mb-4">
                            <h6 class="detail-title fw-bold mb-3">Item yang Dibeli</h6>
                            @foreach($cart_items as $item)
                                <div class="detail-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-8 col-sm-7 mb-2 mb-md-0">
                                            <div class="item-details">
                                                @if($item->type == 'field_booking')
                                                    <?php
                                                        $field = App\Models\Field::find($item->item_id);
                                                        $fieldName = $field ? $field->name : 'Booking Lapangan';
                                                    ?>
                                                    <h5 class="item-title fw-bold mb-1">{{ $fieldName }}</h5>
                                                    <div class="item-info">
                                                        <div class="info-badge">
                                                            <i class="far fa-calendar-alt"></i>
                                                            <span>{{ \Carbon\Carbon::parse($item->start_time)->format('d M Y') }}</span>
                                                        </div>
                                                        <div class="info-badge">
                                                            <i class="far fa-clock"></i>
                                                            <span>
                                                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} -
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
                                                    <div class="item-info">
                                                        <div class="info-badge">
                                                            <i class="fas fa-box"></i>
                                                            <span>Jumlah: {{ $item->quantity }}</span>
                                                        </div>
                                                    </div>
                                                @elseif($item->type == 'membership')
                                                    <?php
                                                        $membership = App\Models\Membership::find($item->item_id);
                                                        $membershipName = $membership ? $membership->name : 'Keanggotaan';
                                                    ?>
                                                    <h5 class="item-title fw-bold mb-1">{{ $membershipName }}</h5>
                                                    <div class="item-info">
                                                        <div class="info-badge">
                                                            <i class="fas fa-user-tag"></i>
                                                            <span>Keanggotaan</span>
                                                        </div>
                                                    </div>
                                                @elseif($item->type == 'photographer')
                                                    <?php
                                                        $photographer = App\Models\Photographer::find($item->item_id);
                                                        $photographerName = $photographer ? $photographer->name : 'Jasa Fotografer';
                                                    ?>
                                                    <h5 class="item-title fw-bold mb-1">{{ $photographerName }}</h5>
                                                    <div class="item-info">
                                                        <div class="info-badge">
                                                            <i class="far fa-calendar-alt"></i>
                                                            <span>{{ \Carbon\Carbon::parse($item->start_time)->format('d M Y') }}</span>
                                                        </div>
                                                        <div class="info-badge">
                                                            <i class="far fa-clock"></i>
                                                            <span>
                                                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} -
                                                                {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-5 text-end">
                                            <div class="item-price">
                                                <span class="price">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="payment-summary p-4 rounded-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Total Pembayaran</h5>
                                <h5 class="fw-bold total-price mb-0">Rp {{ number_format($total_price, 0, ',', '.') }}</h5>
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
        }
    </style>

    <!-- Midtrans JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('pay-button');

        payButton.addEventListener('click', function() {
            // Tampilkan snap payment page
            snap.pay('{{ $snap_token }}', {
                onSuccess: function(result) {
                    window.location.href = '{{ route("user.payment.success") }}?order_id={{ $order_id }}';
                },
                onPending: function(result) {
                    window.location.href = '{{ route("user.payment.unfinish") }}?order_id={{ $order_id }}';
                },
                onError: function(result) {
                    window.location.href = '{{ route("user.payment.error") }}?order_id={{ $order_id }}';
                },
                onClose: function() {
                    // Jika user menutup popup tanpa menyelesaikan pembayaran
                    alert('Anda menutup popup pembayaran tanpa menyelesaikan transaksi');
                }
            });
        });
    });
    </script>
@endsection
