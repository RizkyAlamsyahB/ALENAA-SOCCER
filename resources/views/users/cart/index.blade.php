@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/modern-cart.css') }}">

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
                    <i class="fas fa-shopping-cart"></i>
                    <span>Keranjang</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 fw-bold">Keranjang Booking</h1>
            </div>
        </div>

        @if (count($cartItems) > 0)
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                        <div class="card-header bg-white py-3 border-0 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">Item Keranjang ({{ count($cartItems) }})</h5>
                                <a href="{{ route('user.cart.clear') }}" class="btn-clear"
                                    onclick="return confirm('Apakah Anda yakin ingin mengosongkan keranjang?')">
                                    <i class="fas fa-trash-alt me-2"></i>
                                    <span>Kosongkan</span>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="cart-items-list">
                                @foreach ($cartItems as $item)
                                <div class="cart-item p-4 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 col-sm-3 mb-3 mb-md-0">
                                            <div class="cart-item-image">
                                                @if (isset($item->image))
                                                    <img src="{{ Storage::url($item->image) }}"
                                                        alt="{{ $item->name ?? 'Item' }}"
                                                        class="img-fluid rounded-3">
                                                @else
                                                    <div class="placeholder-image bg-light rounded-3 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-futbol text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-7 col-sm-6 mb-3 mb-md-0">
                                            <div class="cart-item-details">
                                                <h5 class="cart-item-title fw-bold mb-1">
                                                    {{ $item->name ?? 'Item' }}
                                                </h5>
                                                <div class="cart-item-category mb-2">
                                                    <span class="type-badge">{{ $item->type_name ?? $item->type }}</span>
                                                </div>
                                                <div class="cart-item-info">
                                                    @if($item->type == 'field_booking' || $item->type == 'photographer')
                                                        <div class="info-badge">
                                                            <i class="far fa-calendar-alt"></i>
                                                            <span>{{ $item->formatted_date ?? \Carbon\Carbon::parse($item->start_time)->format('d M Y') }}</span>
                                                        </div>
                                                        <div class="info-badge">
                                                            <i class="far fa-clock"></i>
                                                            <span>
                                                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                            </span>
                                                        </div>
                                                    @elseif($item->type == 'rental_item')
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
                                                    @elseif($item->type == 'membership')
                                                        <div class="info-badge">
                                                            <i class="fas fa-id-card"></i>
                                                            <span>{{ $item->details }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3 d-flex justify-content-between align-items-center">
                                            <div class="cart-item-price text-end">
                                                <div class="price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="cart-item-actions">
                                                <form action="{{ route('user.cart.remove', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-remove" title="Hapus">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
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
                            <h5 class="mb-0 fw-bold">Ringkasan Booking</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="summary-items mb-4">
                                <div class="summary-item d-flex justify-content-between mb-3">
                                    <span class="label">Subtotal</span>
                                    <span class="value">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                </div>
                                <div class="summary-item d-flex justify-content-between mb-3">
                                    <span class="label">Jumlah Item</span>
                                    <span class="value">{{ count($cartItems) }} item</span>
                                </div>
                            </div>
                            <div class="summary-total d-flex justify-content-between align-items-center p-3 rounded-3">
                                <span class="fw-bold">Total</span>
                                <span class="total-price">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-4">
                                <form action="{{ route('user.cart.checkout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-checkout">
                                        <span>Checkout</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="{{ route('user.fields.index') }}" class="continue-shopping-link">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Lanjutkan Booking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4 text-center py-5 px-4">
                        <div class="empty-cart mb-4">
                            <i class="fas fa-shopping-cart fa-4x"></i>
                        </div>
                        <h4 class="mb-3 fw-bold">Keranjang Anda Kosong</h4>
                        <p class="text-muted mb-4">Anda belum memiliki item di keranjang. Mulai booking lapangan sekarang.
                        </p>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('user.fields.index') }}" class="btn-explore">
                                <i class="fas fa-futbol me-2"></i>
                                <span>Cari Lapangan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script>
        $(document).ready(function() {
            // Configure toastr options if needed
            toastr.options = {
                "closeButton": true,
                "positionClass": "toast-top-right",
                "opacity": 1
            };

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif
        });
    </script>



<!-- Add these in the head section of your layout -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        /* Modern Cart Styling */

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

        /* Cart Items */
        .cart-items-list {
            border-radius: 0 0 16px 16px;
            overflow: hidden;
        }

        .cart-item {
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f8f9fa;
        }

        .cart-item:last-child {
            border-bottom: none !important;
        }

        .cart-item-image {
            width: 100%;
            height: 90px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .placeholder-image {
            width: 100%;
            height: 90px;
            color: #adb5bd;
        }

        .cart-item-title {
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #212529;
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

        .cart-item:hover .type-badge {
            background-color: #e9ecef;
        }

        .cart-item-info {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
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

        .cart-item:hover .info-badge {
            background-color: #e9ecef;
        }

        .cart-item-price {
            text-align: right;
        }

        .cart-item-price .price {
            font-weight: 700;
            color: #9e0620;
            font-size: 1.1rem;
        }

        .btn-remove {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f8f9fa;
            color: #9e0620;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-remove:hover {
            background-color: #9e0620;
            color: white;
            transform: rotate(90deg);
        }

        /* Summary Card */
        .summary-card {
            position: sticky;
            top: 1.5rem;
        }

        .summary-items {
            padding: 16px;
            background-color: #f8f9fa;
            border-radius: 12px;
        }

        .summary-item {
            margin-bottom: 12px;
        }

        .summary-item .label {
            color: #6c757d;
            font-weight: 500;
        }

        .summary-item .value {
            font-weight: 600;
            color: #212529;
        }

        .summary-total {
            background-color: #fff8f8;
        }

        .total-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #9e0620;
        }

        /* Buttons */
        .btn-checkout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 0;
            background-color: #9e0620;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-checkout:hover {
            background-color: #7d0318;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(158, 6, 32, 0.2);
        }

        .continue-shopping-link {
            display: inline-flex;
            align-items: center;
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .continue-shopping-link:hover {
            color: #9e0620;
            transform: translateX(-5px);
        }

        .btn-clear {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            background-color: #f8f9fa;
            color: #9e0620;
            border: none;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-clear:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        /* Empty Cart */
        .empty-cart {
            margin: 2rem 0;
            color: #9e0620;
            opacity: 0.2;
        }

        .btn-explore {
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
        }

        .btn-explore:hover {
            background-color: #7d0318;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(158, 6, 32, 0.2);
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

            .cart-item {
                padding: 16px !important;
            }

            .cart-item-price {
                text-align: left;
                margin-top: 10px;
            }

            .cart-item-actions {
                margin-top: 10px;
            }

            .summary-card {
                position: static !important;
                margin-top: 1.5rem;
            }

            .btn-clear span {
                display: none;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <!-- Add this near the end of your content section, before closing body tag -->
    <style>
        /* Make toastr notifications fully opaque by default */
        #toast-container>div {
            opacity: 1 !important;
        }

        /* Remove any hover-specific opacity changes */
        #toast-container>div:hover {
            opacity: 1 !important;
        }
    </style>

@endsection
