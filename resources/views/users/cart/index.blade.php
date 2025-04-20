@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/modern-cart.css') }}">

 <!-- Hero Section -->
<div class="hero-section" style="margin-top: 50px;">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Keranjang</h1>
            <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-shopping-cart"></i> Keranjang
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>


    <!-- Main Content -->
    <div class="container mt-4 mb-5">
      <!-- Bootstrap Alert for Session Messages -->
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    {{ session('info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

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
                                                            alt="{{ $item->name ?? 'Item' }}" class="img-fluid rounded-3">
                                                    @else
                                                        <div
                                                            class="placeholder-image bg-light rounded-3 d-flex align-items-center justify-content-center">
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
                                                        <span
                                                            class="type-badge">{{ $item->type_name ?? $item->type }}</span>
                                                    </div>
                                                    <div class="cart-item-info">
                                                        @if ($item->type == 'field_booking' || $item->type == 'photographer')
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
                                                                <span>{{ $item->details ?? 'Membership' }}</span>
                                                            </div>

                                                            <!-- Tambahkan bagian ini untuk menampilkan periode pembayaran -->
                                                            <div class="info-badge">
                                                                <i class="fas fa-credit-card"></i>
                                                                <span>
                                                                    Periode:
                                                                    @if (isset($item->payment_period) && $item->payment_period == 'monthly')
                                                                        Bulanan (4 Minggu)
                                                                    @else
                                                                        Mingguan
                                                                    @endif
                                                                </span>
                                                            </div>

                                                            @if (!empty($item->membership_sessions))
                                                                @php
                                                                    $sessions = json_decode(
                                                                        $item->membership_sessions,
                                                                        true,
                                                                    );
                                                                @endphp


                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-3 col-sm-3 d-flex justify-content-between align-items-center">
                                                <div class="cart-item-price text-end">
                                                    <div class="price">Rp {{ number_format($item->price, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                <div class="cart-item-actions">
                                                    <form action="{{ route('user.cart.remove', $item->id) }}"
                                                        method="POST" class="d-inline">
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
<!-- Form Diskon dan Voucher Poin yang dipisahkan -->
<div class="card border-0 rounded-4 shadow-sm hover-shadow summary-card">
    <div class="card-header bg-white py-3 border-0 px-4">
        <h5 class="mb-0 fw-bold">Ringkasan Booking</h5>
    </div>
    <div class="card-body p-4">
        <!-- Tampilkan jika ada diskon atau voucher yang diterapkan -->
        @if (session()->has('cart_discount') || session()->has('cart_point_voucher'))
            <div class="discount-applied mb-4">
                <div class="discount-info p-3 {{ session()->has('cart_point_voucher') ? 'bg-warning-subtle' : 'bg-success-subtle' }} rounded-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if (session()->has('cart_discount'))
                                <h6 class="fw-bold mb-1">{{ session('cart_discount')['name'] }}</h6>
                                <span class="badge bg-success">{{ session('cart_discount')['code'] }}</span>
                                <div class="text-success mt-1 fw-bold">
                                    - Rp {{ number_format(session('cart_discount')['amount'], 0, ',', '.') }}
                                </div>
                            @else
                                <h6 class="fw-bold mb-1">{{ session('cart_point_voucher')['name'] }}</h6>
                                <span class="badge bg-warning text-dark">{{ session('cart_point_voucher')['code'] }}</span>
                                <span class="badge bg-warning text-dark ms-1">
                                    <i class="fas fa-coins me-1"></i> Voucher Poin
                                </span>
                                <div class="text-warning text-dark mt-1 fw-bold">
                                    - Rp {{ number_format(session('cart_point_voucher')['amount'], 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('user.cart.remove.discount') }}" class="btn-remove-discount"
                            title="Hapus diskon/voucher">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Form Kupon Diskon Reguler -->
            <div class="discount-form mb-3">
                <h6 class="fw-semibold mb-2">Kode Promo</h6>
                <form action="{{ route('user.cart.apply.discount') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="discount_code" class="form-control"
                            placeholder="Masukkan kode promo" required>
                        <button type="submit" class="btn-apply-discount">Terapkan</button>
                    </div>
                    @error('discount_code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </form>
                <button type="button" class="btn-view-promos mt-2" data-bs-toggle="modal"
                    data-bs-target="#promosModal">
                    <i class="fas fa-tags me-2"></i>Lihat Promo
                </button>
            </div>

            <!-- Form untuk Penukaran Poin -->
            <div class="point-voucher-section mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-semibold mb-0">Voucher Poin</h6>
                    <span class="badge bg-secondary">
                        <i class="fas fa-coins me-1"></i> {{ Auth::user()->points }} Poin
                    </span>
                </div>

                <div class="point-voucher-options">
                    <!-- Opsi 1: Gunakan voucher yang sudah ada -->
                    <div class="point-option mb-2">
                        <form action="{{ route('user.cart.apply.point.voucher') }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="voucher_code" class="form-control"
                                    placeholder="Kode voucher poin" required>
                                <button type="submit" class="btn-apply-point-voucher">
                                    <i class="fas fa-check me-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Opsi 2: Tukar poin sekarang -->
                    <a href="{{ route('user.points.index') }}" class="btn-points w-100">
                        <i class="fas fa-coins me-2" style="color: #FFD700;"></i>
                        Tukar Poin
                    </a>
                </div>
            </div>
        @endif

        <!-- Ringkasan Harga -->
        <div class="summary-items mb-4">
            <div class="summary-item d-flex justify-content-between mb-3">
                <span class="label">Subtotal</span>
                <span class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>

            @if (session()->has('cart_discount'))
                <div class="summary-item d-flex justify-content-between mb-3 text-success">
                    <span class="label">Diskon</span>
                    <span class="value">- Rp {{ number_format(session('cart_discount')['amount'], 0, ',', '.') }}</span>
                </div>
            @elseif (session()->has('cart_point_voucher'))
                <div class="summary-item d-flex justify-content-between mb-3 text-warning">
                    <span class="label">Voucher Poin</span>
                    <span class="value">- Rp {{ number_format(session('cart_point_voucher')['amount'], 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="summary-item d-flex justify-content-between mb-3">
                <span class="label">Jumlah Item</span>
                <span class="value">{{ count($cartItems) }} item</span>
            </div>
        </div>

        <!-- Total -->
        <div class="summary-total d-flex justify-content-between align-items-center p-3 rounded-3">
            <span class="fw-bold">Total</span>
            <span class="total-price">
                Rp {{ number_format(
                    session('cart_discount')
                        ? $subtotal - session('cart_discount')['amount']
                        : (session('cart_point_voucher')
                            ? $subtotal - session('cart_point_voucher')['amount']
                            : $subtotal),
                    0, ',', '.')
                }}
            </span>
        </div>

        <!-- Tombol Checkout -->
        <div class="mt-4">
            <form action="{{ route('user.cart.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-checkout">
                    <span>Checkout</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- Link Lanjutkan Shopping -->
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
        // Menangani klik tombol "Gunakan Promo"
        document.addEventListener('DOMContentLoaded', function() {
            const usePromoButtons = document.querySelectorAll('.btn-use-promo');
            const discountInput = document.querySelector('input[name="discount_code"]');

            usePromoButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const promoCode = this.getAttribute('data-code');
                    if (discountInput) {
                        discountInput.value = promoCode;
                    }
                });
            });

            // Fungsi untuk menyalin kode promo
            const copyButtons = document.querySelectorAll('.btn-copy-code');
            copyButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const code = this.getAttribute('data-code');
                    navigator.clipboard.writeText(code).then(() => {
                        // Feedback visual bahwa kode telah disalin
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 1500);
                    });
                });
            });
        });
    </script>

    <!-- Modal Daftar Promo -->
    <div class="modal fade" id="promosModal" tabindex="-1" aria-labelledby="promosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promosModalLabel">Promo Tersedia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (isset($activeDiscounts) && count($activeDiscounts) > 0)
                        <div class="row">
                            @foreach ($activeDiscounts as $discount)
                                <div class="col-md-6 mb-3">
                                    <div class="promo-card">
                                        <div class="promo-card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="promo-title">{{ $discount->name }}</h5>
                                                <span class="promo-badge">
                                                    @if ($discount->type == 'percentage')
                                                        {{ number_format($discount->value, 0) }}%
                                                    @else
                                                        Rp {{ number_format($discount->value, 0, ',', '.') }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="promo-code">
                                                <span class="code-label">Kode:</span>
                                                <span class="code-value">{{ $discount->code }}</span>
                                                <button class="btn-copy-code" data-code="{{ $discount->code }}"
                                                    title="Salin kode">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="promo-card-body">
                                            <p class="promo-description">{{ $discount->description }}</p>

                                            <div class="promo-details">
                                                <div class="promo-detail-item">
                                                    <i class="fas fa-tag text-danger"></i>
                                                    <span>
                                                        @if ($discount->applicable_to == 'all')
                                                            Berlaku untuk semua item
                                                        @elseif($discount->applicable_to == 'field_booking')
                                                            Hanya untuk booking lapangan
                                                        @elseif($discount->applicable_to == 'rental_item')
                                                            Hanya untuk penyewaan peralatan
                                                        @elseif($discount->applicable_to == 'membership')
                                                            Hanya untuk membership
                                                        @elseif($discount->applicable_to == 'photographer')
                                                            Hanya untuk jasa fotografer
                                                        @endif
                                                    </span>
                                                </div>

                                                @if ($discount->min_order > 0)
                                                    <div class="promo-detail-item">
                                                        <i class="fas fa-money-bill-wave text-danger"></i>
                                                        <span>Min. pembelian Rp
                                                            {{ number_format($discount->min_order, 0, ',', '.') }}</span>
                                                    </div>
                                                @endif

                                                @if ($discount->end_date)
                                                    <div class="promo-detail-item">
                                                        <i class="far fa-calendar-alt text-danger"></i>
                                                        <span>Berlaku sampai
                                                            {{ \Carbon\Carbon::parse($discount->end_date)->format('d M Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="promo-card-footer">
                                            <button class="btn-use-promo" data-code="{{ $discount->code }}"
                                                data-bs-dismiss="modal">
                                                Gunakan Promo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-ticket-alt fa-3x text-muted"></i>
                            </div>
                            <h5>Tidak ada promo tersedia saat ini</h5>
                            <p class="text-muted">Silakan periksa kembali nanti untuk promo terbaru.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <style>
        /* Modern Cart Styling */

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

        /* Bootstrap Alert Styling */
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

        .btn-close {
            font-size: 0.8rem;
        }

        /* Discount Form Styling */
        .discount-form {
            border: 1px dashed #dee2e6;
            border-radius: 12px;
            padding: 16px;
            background-color: #f8f9fa;
        }

        .btn-apply-discount {
            background-color: #9e0620;
            color: white;
            border: none;
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            padding: 0 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-apply-discount:hover {
            background-color: #7d0318;
        }

        .btn-view-promos {
            display: block;
            width: 100%;
            padding: 8px;
            background-color: transparent;
            color: #9e0620;
            border: 1px dashed #9e0620;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .btn-view-promos:hover {
            background-color: rgba(158, 6, 32, 0.05);
            transform: translateY(-2px);
        }

        .discount-info {
            border-left: 4px solid #28a745;
        }

        .btn-remove-discount {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            color: #dc3545;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-remove-discount:hover {
            background-color: #dc3545;
            color: white;
            transform: rotate(90deg);
        }

        /* Promo Modal Styling */
        .btn-secondary {
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Promo Card Styling */
        .promo-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .promo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .promo-card-header {
            padding: 16px;
            background-color: #fff8f8;
            border-bottom: 1px solid #f1f1f1;
        }

        .promo-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #2D3748;
        }

        .promo-badge {
            background-color: #9e0620;
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .promo-code {
            margin-top: 8px;
            display: flex;
            align-items: center;
        }

        .code-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-right: 5px;
        }

        .code-value {
            font-family: monospace;
            background-color: rgba(158, 6, 32, 0.1);
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #9e0620;
            margin-right: 8px;
        }

        .btn-copy-code {
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-copy-code:hover {
            color: #9e0620;
        }

        .promo-card-body {
            padding: 16px;
            flex-grow: 1;
        }

        .promo-description {
            font-size: 0.9rem;
            color: #4A5568;
            margin-bottom: 16px;
        }

        .promo-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .promo-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #4A5568;
        }

        .promo-card-footer {
            padding: 12px 16px;
            border-top: 1px solid #f1f1f1;
            background-color: #fafafa;
        }

        .btn-use-promo {
            width: 100%;
            padding: 8px 0;
            background-color: #9e0620;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-use-promo:hover {
            background-color: #7d0318;
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

            .promo-card {
                margin-bottom: 15px;
            }

            .modal-dialog {
                margin: 10px;
            }
        }
        .btn-points {
    display: block;
    padding: 8px 12px;
    background-color: #9E0620;
    color: white;
    border: none;
    border-radius: 4px;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-points:hover {
    background-color: #7d0519;
    color: white;
    text-decoration: none;
}
/* Styling untuk Form Voucher Poin */
.point-voucher-section {
    border: 1px dashed #ffc107;
    border-radius: 12px;
    padding: 16px;
    background-color: #fff8e1;
}

.btn-apply-point-voucher {
    background-color: #ffc107;
    color: #212529;
    border: none;
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
    padding: 0 16px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-apply-point-voucher:hover {
    background-color: #e0a800;
}

.point-option {
    padding: 5px 0;
}

.btn-points {
    display: block;
    padding: 10px 12px;
    background-color: #212529;
    color: white;
    border: none;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-points:hover {
    background-color: #343a40;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

/* Badge styling */
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

/* Hover effect untuk badge */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: translateY(-2px);
}

/* Warna untuk voucher point */
.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.15) !important;
}

.text-warning {
    color: #ffc107 !important;
}
    </style>
@endsection
