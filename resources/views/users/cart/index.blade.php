@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Keranjang</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/"><i class="fas fa-home"></i> Home</a>
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
        <!-- Alert Messages -->
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
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                        <div class="card-header bg-white py-3 border-0 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">Item Keranjang ({{ count($cartItems) }})</h5>
                                <button class="btn-clear" data-action="clear-cart"
                                    data-url="{{ route('user.cart.clear') }}">
                                    <i class="fas fa-trash-alt me-2"></i>
                                    <span>Kosongkan</span>
                                </button>
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
                                                        @if ($item->type == 'field_booking' || $item->type == 'photographer')
                                                            <div class="info-badge">
                                                                <i class="far fa-calendar-alt"></i>
                                                                <span>{{ $item->formatted_date ?? \Carbon\Carbon::parse($item->start_time)->format('d M Y') }}</span>
                                                            </div>
                                                            <div class="info-badge">
                                                                <i class="far fa-clock"></i>
                                                                <span>
                                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
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
                                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        @elseif($item->type == 'membership')
                                                            <div class="info-badge">
                                                                <i class="fas fa-id-card"></i>
                                                                <span>{{ $item->details ?? 'Membership' }}</span>
                                                            </div>
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
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3 d-flex justify-content-between align-items-center">
                                                <div class="cart-item-price text-end">
                                                    <div class="price" id="price-{{ $item->id }}">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="cart-item-actions d-flex gap-2">
                                                    @if ($item->type == 'rental_item')
                                                        <button type="button" class="btn-edit" data-action="edit-item"
                                                            data-item-id="{{ $item->id }}" title="Edit Quantity">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn-remove" data-action="remove-item"
                                                        data-url="{{ route('user.cart.remove', $item->id) }}"
                                                        data-item-name="{{ $item->name ?? 'Item' }}" title="Hapus">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow summary-card">
                        <div class="card-header bg-white py-3 border-0 px-4">
                            <h5 class="mb-0 fw-bold">Ringkasan Booking</h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Applied Discount/Voucher -->
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
                                            <button type="button" class="btn-remove-discount"
                                                data-action="remove-discount"
                                                data-url="{{ route('user.cart.remove.discount') }}"
                                                title="Hapus diskon/voucher">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Promo Code Form -->
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

                                <!-- Point Voucher Section -->
                                <div class="point-voucher-section mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-semibold mb-0">Voucher Poin</h6>
                                        <span class="badge bg-secondary" id="userPointsBadge">
                                            <i class="fas fa-coins me-1"></i> {{ Auth::user()->points }} Poin
                                        </span>
                                    </div>
                                    <div class="point-voucher-options">
                                        <!-- Use existing voucher -->
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
                                        <!-- Redeem points button -->
                                        <button type="button" class="btn-points w-100" data-bs-toggle="modal"
                                            data-bs-target="#pointRedemptionModal">
                                            <i class="fas fa-coins me-2" style="color: #FFD700;"></i>
                                            Tukar Poin
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Price Summary -->
                            <div class="summary-items mb-4">
                                <div class="summary-item d-flex justify-content-between mb-3">
                                    <span class="label">Subtotal</span>
                                    <span class="value" id="cart-subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
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
                                <span class="total-price" id="cart-total">
                                    Rp {{ number_format(
                                        session('cart_discount') ? $subtotal - session('cart_discount')['amount'] :
                                        (session('cart_point_voucher') ? $subtotal - session('cart_point_voucher')['amount'] : $subtotal),
                                        0, ',', '.'
                                    ) }}
                                </span>
                            </div>

                            <!-- Checkout Button -->
                            <div class="mt-4">
                                <form action="{{ route('user.cart.checkout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-checkout">
                                        <span>Checkout</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Continue Shopping -->
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
            <!-- Empty Cart -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4 text-center py-5 px-4">
                        <div class="empty-cart mb-4">
                            <i class="fas fa-shopping-cart fa-4x"></i>
                        </div>
                        <h4 class="mb-3 fw-bold">Keranjang Anda Kosong</h4>
                        <p class="text-muted mb-4">Anda belum memiliki item di keranjang. Mulai booking lapangan sekarang.</p>
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

    <!-- Point Redemption Modal -->
    <div class="modal fade" id="pointRedemptionModal" tabindex="-1" aria-labelledby="pointRedemptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pointRedemptionModalLabel">
                        <i class="fas fa-coins text-warning me-2"></i>
                        Tukar Poin dengan Voucher
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- User Points Info -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Poin Anda Saat Ini</strong>
                                <div class="fs-4 fw-bold text-primary" id="modalUserPoints">
                                    {{ number_format(Auth::user()->points) }}
                                </div>
                            </div>
                            <div class="points-icon">
                                <i class="fas fa-coins fa-2x" style="color: #FFD700;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="vouchersLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2">Memuat voucher tersedia...</div>
                    </div>

                    <!-- Vouchers List -->
                    <div id="vouchersList" style="display: none;">
                        <h6 class="fw-semibold mb-3">Voucher Tersedia</h6>
                        <div class="vouchers-container" style="max-height: 400px; overflow-y: auto;">
                            <!-- Vouchers will be loaded here via JavaScript -->
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="noVouchersState" style="display: none;" class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-ticket-alt fa-3x text-muted"></i>
                        </div>
                        <h5>Tidak ada voucher yang bisa ditukar</h5>
                        <p class="text-muted">Kumpulkan lebih banyak poin untuk mendapatkan voucher menarik.</p>
                    </div>

                    <!-- Error State -->
                    <div id="vouchersError" style="display: none;" class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorMessage">Gagal memuat voucher. Silakan coba lagi.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                    <a href="{{ route('user.points.index') }}" class="btn btn-primary px-4">
                        <i class="fas fa-external-link-alt me-2"></i>Lihat Semua Voucher
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="confirmDeleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-3">
                        <div class="delete-icon mb-3">
                            <i class="fas fa-trash-alt fa-3x text-danger"></i>
                        </div>
                        <p class="mb-0 fs-6" id="deleteMessage">
                            Apakah Anda yakin ingin menghapus item ini?
                        </p>
                    </div>
                    <div class="alert alert-light border-start border-warning border-4 mb-0">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Tindakan ini tidak dapat dibatalkan.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4" id="confirmDeleteBtn">
                            <i class="fas fa-trash-alt me-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Quantity Modal -->
    <div class="modal fade" id="editQuantityModal" tabindex="-1" aria-labelledby="editQuantityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editQuantityModalLabel">
                        <i class="fas fa-edit text-danger me-2"></i>
                        Edit Quantity
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <h6 id="editItemName" class="fw-bold mb-2"></h6>
                        <div class="text-muted small mb-3">
                            <i class="far fa-calendar-alt me-1"></i>
                            <span id="editItemTime"></span>
                        </div>
                    </div>

                    <div class="quantity-control mb-3">
                        <label for="editQuantityInput" class="form-label fw-semibold">Quantity</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="decreaseBtn">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="editQuantityInput" min="1" value="1">
                            <button type="button" class="btn btn-outline-secondary" id="increaseBtn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">Maksimal: <span id="maxQuantity">1</span></small>
                            <small class="text-success">Tersedia: <span id="availableStock">0</span></small>
                        </div>
                    </div>

                    <div class="price-info p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Harga per unit:</span>
                            <span id="pricePerUnit">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold">Total harga:</span>
                            <span id="totalPrice" class="fw-bold text-danger">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-primary px-4" id="saveQuantityBtn">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Promos Modal -->
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
                                                <button class="btn-copy-code" data-code="{{ $discount->code }}" title="Salin kode">
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
                                                        <span>Min. pembelian Rp {{ number_format($discount->min_order, 0, ',', '.') }}</span>
                                                    </div>
                                                @endif
                                                @if ($discount->end_date)
                                                    <div class="promo-detail-item">
                                                        <i class="far fa-calendar-alt text-danger"></i>
                                                        <span>Berlaku sampai {{ \Carbon\Carbon::parse($discount->end_date)->format('d M Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="promo-card-footer">
                                            <button class="btn-use-promo" data-code="{{ $discount->code }}" data-bs-dismiss="modal">
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF Token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!csrfToken) {
                console.error('CSRF token not found. Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your head.');
            }

            // Base URL
            const cartBaseUrl = "{{ route('user.cart.view') }}";

            // ===== PROMO FUNCTIONALITY =====
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

            // Copy promo code
            const copyButtons = document.querySelectorAll('.btn-copy-code');
            copyButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const code = this.getAttribute('data-code');
                    navigator.clipboard.writeText(code).then(() => {
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 1500);
                    });
                });
            });

            // ===== DELETE CONFIRMATION =====
            const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            const deleteForm = document.getElementById('deleteForm');
            const deleteMessage = document.getElementById('deleteMessage');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            document.addEventListener('click', function(e) {
                const target = e.target.closest('[data-action]');
                if (!target) return;

                e.preventDefault();
                const action = target.getAttribute('data-action');
                const url = target.getAttribute('data-url');

                let message = '';
                let buttonText = '';
                let buttonIcon = '';

                switch (action) {
                    case 'remove-item':
                        const itemName = target.getAttribute('data-item-name') || 'item ini';
                        message = `Apakah Anda yakin ingin menghapus <strong>"${itemName}"</strong> dari keranjang?`;
                        buttonText = 'Hapus Item';
                        buttonIcon = 'fas fa-times';
                        break;
                    case 'clear-cart':
                        message = 'Apakah Anda yakin ingin <strong>mengosongkan seluruh keranjang</strong>? Semua item akan dihapus.';
                        buttonText = 'Kosongkan Keranjang';
                        buttonIcon = 'fas fa-trash-alt';
                        break;
                    case 'remove-discount':
                        message = 'Apakah Anda yakin ingin <strong>menghapus diskon/voucher</strong> yang sedang diterapkan?';
                        buttonText = 'Hapus Diskon';
                        buttonIcon = 'fas fa-times';
                        break;
                    case 'edit-item':
                        handleEditItem(target);
                        return;
                    default:
                        message = 'Apakah Anda yakin ingin melakukan tindakan ini?';
                        buttonText = 'Hapus';
                        buttonIcon = 'fas fa-trash-alt';
                }

                deleteMessage.innerHTML = message;
                confirmDeleteBtn.innerHTML = `<i class="${buttonIcon} me-2"></i>${buttonText}`;

                if (action === 'clear-cart' || action === 'remove-discount') {
                    deleteForm.setAttribute('action', url);
                    deleteForm.querySelector('input[name="_method"]').value = 'GET';
                } else {
                    deleteForm.setAttribute('action', url);
                    deleteForm.querySelector('input[name="_method"]').value = 'DELETE';
                }

                confirmDeleteModal.show();
            });

            deleteForm.addEventListener('submit', function(e) {
                confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menghapus...';
                confirmDeleteBtn.disabled = true;
            });

            document.getElementById('confirmDeleteModal').addEventListener('hidden.bs.modal', function() {
                confirmDeleteBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Hapus';
                confirmDeleteBtn.disabled = false;
            });

            // ===== EDIT QUANTITY MODAL =====
            const editQuantityModal = new bootstrap.Modal(document.getElementById('editQuantityModal'));
            const editQuantityInput = document.getElementById('editQuantityInput');
            const decreaseBtn = document.getElementById('decreaseBtn');
            const increaseBtn = document.getElementById('increaseBtn');
            const saveQuantityBtn = document.getElementById('saveQuantityBtn');
            const editItemName = document.getElementById('editItemName');
            const editItemTime = document.getElementById('editItemTime');
            const maxQuantitySpan = document.getElementById('maxQuantity');
            const availableStockSpan = document.getElementById('availableStock');
            const pricePerUnitSpan = document.getElementById('pricePerUnit');
            const totalPriceSpan = document.getElementById('totalPrice');

            let currentEditItemId = null;
            let currentPricePerUnit = 0;
            let currentMaxQuantity = 1;

            function handleEditItem(button) {
                const itemId = button.getAttribute('data-item-id');
                currentEditItemId = itemId;

                fetch(`${cartBaseUrl}/item/${itemId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const item = data.item;
                            editItemName.textContent = item.name;
                            editItemTime.textContent = `${item.start_time} - ${item.end_time}`;
                            editQuantityInput.value = item.current_quantity;
                            editQuantityInput.max = item.max_quantity;
                            maxQuantitySpan.textContent = item.max_quantity;
                            availableStockSpan.textContent = item.available_stock || item.max_quantity;
                            pricePerUnitSpan.textContent = `Rp ${item.price_per_unit.toLocaleString('id-ID')}`;

                            currentPricePerUnit = item.price_per_unit;
                            currentMaxQuantity = item.max_quantity;

                            updateTotalPrice();
                            editQuantityModal.show();
                        } else {
                            showAlert('Gagal mengambil detail item: ' + data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Terjadi kesalahan saat mengambil detail item', 'danger');
                    });
            }

            function updateTotalPrice() {
                const quantity = parseInt(editQuantityInput.value) || 1;
                const total = quantity * currentPricePerUnit;
                totalPriceSpan.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            }

            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(editQuantityInput.value);
                if (currentValue > 1) {
                    editQuantityInput.value = currentValue - 1;
                    updateTotalPrice();
                }
            });

            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(editQuantityInput.value);
                if (currentValue < currentMaxQuantity) {
                    editQuantityInput.value = currentValue + 1;
                    updateTotalPrice();
                }
            });

            editQuantityInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (value < 1) {
                    this.value = 1;
                } else if (value > currentMaxQuantity) {
                    this.value = currentMaxQuantity;
                }
                updateTotalPrice();
            });

            saveQuantityBtn.addEventListener('click', function() {
                const newQuantity = parseInt(editQuantityInput.value);

                if (!currentEditItemId || newQuantity < 1 || newQuantity > currentMaxQuantity) {
                    showAlert('Quantity tidak valid', 'danger');
                    return;
                }

                saveQuantityBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                saveQuantityBtn.disabled = true;

                const requestData = { quantity: newQuantity };
                const requestUrl = `/cart/update-quantity/${currentEditItemId}`;

                fetch(requestUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        const priceElement = document.getElementById(`price-${currentEditItemId}`);
                        if (priceElement && data.formatted_price) {
                            priceElement.textContent = data.formatted_price;
                        }

                        const subtotalElement = document.getElementById('cart-subtotal');
                        if (subtotalElement && data.formatted_subtotal) {
                            subtotalElement.textContent = data.formatted_subtotal;
                        }

                        const totalElement = document.getElementById('cart-total');
                        if (totalElement && !document.querySelector('.discount-applied') && data.formatted_subtotal) {
                            totalElement.textContent = data.formatted_subtotal;
                        }

                        // Update quantity display
                        const cartItem = document.querySelector(`[data-item-id="${currentEditItemId}"]`)?.closest('.cart-item');
                        if (cartItem) {
                            const quantityBadge = cartItem.querySelector('.info-badge span');
                            if (quantityBadge && quantityBadge.textContent.includes('Jumlah:')) {
                                quantityBadge.textContent = `Jumlah: ${newQuantity}`;
                            }
                        }

                        editQuantityModal.hide();
                        showAlert(data.message || 'Quantity berhasil diupdate', 'success');
                    } else {
                        showAlert('Gagal mengupdate quantity: ' + (data.message || 'Unknown error'), 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Terjadi kesalahan saat mengupdate quantity', 'danger');
                })
                .finally(() => {
                    saveQuantityBtn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan';
                    saveQuantityBtn.disabled = false;
                });
            });

            document.getElementById('editQuantityModal').addEventListener('hidden.bs.modal', function() {
                currentEditItemId = null;
                currentPricePerUnit = 0;
                currentMaxQuantity = 1;
                saveQuantityBtn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan';
                saveQuantityBtn.disabled = false;
            });

// REPLACE JavaScript untuk Point Redemption Modal di cart view
// Pastikan ini di dalam document.addEventListener('DOMContentLoaded', function() {

// ===== POINT REDEMPTION MODAL FUNCTIONALITY - FIXED VERSION =====
const pointRedemptionModal = document.getElementById('pointRedemptionModal');
const vouchersLoading = document.getElementById('vouchersLoading');
const vouchersList = document.getElementById('vouchersList');
const noVouchersState = document.getElementById('noVouchersState');
const vouchersError = document.getElementById('vouchersError');
const vouchersContainer = document.querySelector('.vouchers-container');
const modalUserPoints = document.getElementById('modalUserPoints');

let modalInstance = null;

if (pointRedemptionModal) {
    modalInstance = new bootstrap.Modal(pointRedemptionModal, {
        backdrop: true,
        keyboard: true
    });

    pointRedemptionModal.addEventListener('show.bs.modal', function() {
        loadAvailableVouchers();
    });
}

function loadAvailableVouchers() {
    // Reset states
    vouchersLoading.style.display = 'block';
    vouchersList.style.display = 'none';
    noVouchersState.style.display = 'none';
    vouchersError.style.display = 'none';

    fetch('/points/available-for-cart')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            vouchersLoading.style.display = 'none';

            if (data.success) {
                // Update user points display
                modalUserPoints.textContent = data.formatted_user_points;

                if (data.vouchers && data.vouchers.length > 0) {
                    renderVouchers(data.vouchers, data.owned_count, data.available_count);
                    vouchersList.style.display = 'block';
                } else {
                    noVouchersState.style.display = 'block';
                }
            } else {
                throw new Error(data.message || 'Gagal memuat voucher');
            }
        })
        .catch(error => {
            console.error('Error loading vouchers:', error);
            vouchersLoading.style.display = 'none';
            vouchersError.style.display = 'block';
            document.getElementById('errorMessage').textContent = error.message;
        });
}

function renderVouchers(vouchers, ownedCount, availableCount) {
    vouchersContainer.innerHTML = '';

    // Add section headers
    let hasOwnedVouchers = false;
    let hasAvailableVouchers = false;

    vouchers.forEach(voucher => {
        if (voucher.is_owned) {
            hasOwnedVouchers = true;
        } else {
            hasAvailableVouchers = true;
        }
    });

    // Render owned vouchers first
    if (hasOwnedVouchers) {
        const ownedHeader = document.createElement('div');
        ownedHeader.className = 'voucher-section-header mb-3';
        ownedHeader.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-check-circle text-success me-2"></i>
                <h6 class="mb-0 fw-bold">Voucher Yang Dimiliki (${ownedCount})</h6>
            </div>
            <p class="text-muted small mb-0">Voucher yang sudah Anda tukar dan siap digunakan</p>
        `;
        vouchersContainer.appendChild(ownedHeader);

        vouchers.filter(v => v.is_owned).forEach(voucher => {
            const voucherCard = createVoucherCard(voucher);
            vouchersContainer.appendChild(voucherCard);
        });
    }

    // Add separator if both sections exist
    if (hasOwnedVouchers && hasAvailableVouchers) {
        const separator = document.createElement('div');
        separator.className = 'my-4';
        separator.innerHTML = '<hr class="text-muted">';
        vouchersContainer.appendChild(separator);
    }

    // Render available vouchers
    if (hasAvailableVouchers) {
        const availableHeader = document.createElement('div');
        availableHeader.className = 'voucher-section-header mb-3';
        availableHeader.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-shopping-cart text-primary me-2"></i>
                <h6 class="mb-0 fw-bold">Voucher Tersedia (${availableCount})</h6>
            </div>
            <p class="text-muted small mb-0">Voucher yang bisa Anda tukar dengan poin</p>
        `;
        vouchersContainer.appendChild(availableHeader);

        vouchers.filter(v => !v.is_owned).forEach(voucher => {
            const voucherCard = createVoucherCard(voucher);
            vouchersContainer.appendChild(voucherCard);
        });
    }
}

function createVoucherCard(voucher) {
    const cardDiv = document.createElement('div');
    cardDiv.className = `card mb-3 ${voucher.is_owned ? 'border-success' : ''}`;

    const detailsHtml = [];

    if (voucher.formatted_min_order) {
        detailsHtml.push(`
            <div class="d-flex align-items-center">
                <i class="fas fa-money-bill-wave text-primary me-2"></i>
                <span>${voucher.formatted_min_order}</span>
            </div>
        `);
    }

    if (voucher.formatted_max_discount) {
        detailsHtml.push(`
            <div class="d-flex align-items-center">
                <i class="fas fa-percentage text-primary me-2"></i>
                <span>${voucher.formatted_max_discount}</span>
            </div>
        `);
    }

    if (voucher.formatted_end_date) {
        detailsHtml.push(`
            <div class="d-flex align-items-center">
                <i class="far fa-calendar-alt text-primary me-2"></i>
                <span>Berlaku sampai ${voucher.formatted_end_date}</span>
            </div>
        `);
    }

    // Different content based on voucher status
    let headerContent, buttonContent, cardClass;

    if (voucher.is_owned) {
        // Owned voucher
        headerContent = `
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-success">${voucher.name}</h6>
                <span class="badge bg-success">${voucher.formatted_discount}</span>
            </div>
            <div class="mt-2">
                <span class="badge bg-success-subtle text-success">
                    <i class="fas fa-check-circle me-1"></i>Dimiliki
                </span>
                <span class="badge bg-light text-dark ms-1">
                    <code class="small">${voucher.discount_code}</code>
                </span>
            </div>
        `;

        buttonContent = `
            <button class="btn btn-success btn-sm w-100 btn-apply-owned-voucher"
                    data-redemption-id="${voucher.redemption_id}">
                <i class="fas fa-check me-2"></i>Gunakan Voucher
            </button>
        `;

        cardClass = 'bg-success-subtle';
    } else {
        // Available voucher
        headerContent = `
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">${voucher.name}</h6>
                <span class="badge bg-danger">${voucher.formatted_discount}</span>
            </div>
            <div class="mt-2">
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-coins me-1"></i>${voucher.formatted_points} Poin
                </span>
            </div>
        `;

        if (voucher.status === 'can_buy') {
            buttonContent = `
                <button class="btn btn-primary btn-sm w-100 btn-redeem-voucher"
                        data-voucher-id="${voucher.id}">
                    <i class="fas fa-exchange-alt me-2"></i>Tukar Sekarang
                </button>
            `;
        } else {
            buttonContent = `
                <button class="btn btn-secondary btn-sm w-100" disabled>
                    <i class="fas fa-lock me-2"></i>Poin Tidak Cukup
                </button>
            `;
        }

        cardClass = '';
    }

    cardDiv.innerHTML = `
        <div class="card-header ${cardClass}">
            ${headerContent}
        </div>
        <div class="card-body">
            <p class="card-text small">${voucher.description}</p>
            ${detailsHtml.length > 0 ? `<div class="small text-muted mb-3">${detailsHtml.join('<br>')}</div>` : ''}
            ${buttonContent}
        </div>
    `;

    // Add click event listeners
    const redeemBtn = cardDiv.querySelector('.btn-redeem-voucher');
    const applyBtn = cardDiv.querySelector('.btn-apply-owned-voucher');

    if (redeemBtn) {
        redeemBtn.addEventListener('click', function() {
            redeemVoucherFromModal(voucher.id, this);
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            applyOwnedVoucherFromModal(voucher.redemption_id, this);
        });
    }

    return cardDiv;
}

function redeemVoucherFromModal(voucherId, buttonElement) {
    const originalHTML = buttonElement.innerHTML;
    buttonElement.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menukar...';
    buttonElement.disabled = true;

    fetch(`/points/redeem-from-cart/${voucherId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || `HTTP ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            buttonElement.innerHTML = '<i class="fas fa-check me-2"></i>Berhasil Ditukar!';
            buttonElement.className = 'btn btn-success btn-sm w-100';

            // Update user points
            updateUserPointsDisplay(data.formatted_user_points);

            // Apply voucher to cart
            applyVoucherToCart(data.voucher);

            showAlert(data.message, 'success');

            // Close modal properly after delay
            setTimeout(() => {
                closeModalProperly();
            }, 1500);
        } else {
            throw new Error(data.message || 'Gagal menukar voucher');
        }
    })
    .catch(error => {
        console.error('Error redeeming voucher:', error);
        buttonElement.innerHTML = originalHTML;
        buttonElement.disabled = false;
        showAlert('Gagal menukar voucher: ' + error.message, 'danger');
    });
}

function applyOwnedVoucherFromModal(redemptionId, buttonElement) {
    const originalHTML = buttonElement.innerHTML;
    buttonElement.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menerapkan...';
    buttonElement.disabled = true;

    fetch(`/points/apply-owned-voucher/${redemptionId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || `HTTP ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            buttonElement.innerHTML = '<i class="fas fa-check me-2"></i>Voucher Diterapkan!';
            buttonElement.className = 'btn btn-success btn-sm w-100';

            // Apply voucher to cart
            applyVoucherToCart(data.voucher);

            showAlert(data.message, 'success');

            // Close modal properly after delay
            setTimeout(() => {
                closeModalProperly();
            }, 1500);
        } else {
            throw new Error(data.message || 'Gagal menerapkan voucher');
        }
    })
    .catch(error => {
        console.error('Error applying voucher:', error);
        buttonElement.innerHTML = originalHTML;
        buttonElement.disabled = false;
        showAlert('Gagal menerapkan voucher: ' + error.message, 'danger');
    });
}

function updateUserPointsDisplay(formattedPoints) {
    modalUserPoints.textContent = formattedPoints;
    const pointsBadge = document.getElementById('userPointsBadge');
    if (pointsBadge) {
        pointsBadge.innerHTML = `<i class="fas fa-coins me-1"></i> ${formattedPoints} Poin`;
    }
}

function closeModalProperly() {
    if (modalInstance) {
        modalInstance.hide();
    }

    // Ensure backdrop is removed
    setTimeout(() => {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());

        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }, 300);
}

function applyVoucherToCart(voucherData) {
    // Remove existing discount forms
    const discountForm = document.querySelector('.discount-form');
    const pointVoucherSection = document.querySelector('.point-voucher-section');
    if (discountForm) discountForm.style.display = 'none';
    if (pointVoucherSection) pointVoucherSection.style.display = 'none';

    // Create discount applied section
    const summarySection = document.querySelector('.summary-card .card-body');
    const summaryItems = summarySection.querySelector('.summary-items');

    // Remove existing discount applied section if any
    const existingDiscount = document.querySelector('.discount-applied');
    if (existingDiscount) {
        existingDiscount.remove();
    }

    const discountAppliedHtml = `
        <div class="discount-applied mb-4">
            <div class="discount-info p-3 bg-warning-subtle rounded-3 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1">${voucherData.name}</h6>
                        <span class="badge bg-warning text-dark">${voucherData.code}</span>
                        <span class="badge bg-warning text-dark ms-1">
                            <i class="fas fa-coins me-1"></i> Voucher Poin
                        </span>
                        <div class="text-dark mt-1 fw-bold">
                            - ${voucherData.formatted_discount_amount}
                        </div>
                    </div>
                    <button type="button" class="btn-remove-discount"
                            data-action="remove-discount"
                            data-url="/cart/remove-discount"
                            title="Hapus voucher">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    if (summaryItems) {
        summaryItems.insertAdjacentHTML('beforebegin', discountAppliedHtml);
    }

    // Update total
    const totalPriceElement = document.querySelector('#cart-total');
    if (totalPriceElement && voucherData.formatted_new_total) {
        totalPriceElement.textContent = voucherData.formatted_new_total;
    }

    // Add discount line to summary
    const subtotalItem = summaryItems.querySelector('.summary-item');
    if (subtotalItem) {
        // Remove existing discount line
        const existingDiscountLine = summaryItems.querySelector('.summary-item.text-warning');
        if (existingDiscountLine) {
            existingDiscountLine.remove();
        }

        const discountLineHtml = `
            <div class="summary-item d-flex justify-content-between mb-3 text-warning">
                <span class="label">Voucher Poin</span>
                <span class="value">- ${voucherData.formatted_discount_amount}</span>
            </div>
        `;
        subtotalItem.insertAdjacentHTML('afterend', discountLineHtml);
    }
}

// Reset modal when closed
if (pointRedemptionModal) {
    pointRedemptionModal.addEventListener('hidden.bs.modal', function() {
        // Reset all states
        vouchersLoading.style.display = 'none';
        vouchersList.style.display = 'none';
        noVouchersState.style.display = 'none';
        vouchersError.style.display = 'none';
        vouchersContainer.innerHTML = '';

        // Ensure backdrop is completely removed
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());

        // Reset body state
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Show discount/voucher forms again if no voucher applied
        if (!document.querySelector('.discount-applied')) {
            const discountForm = document.querySelector('.discount-form');
            const pointVoucherSection = document.querySelector('.point-voucher-section');
            if (discountForm) discountForm.style.display = 'block';
            if (pointVoucherSection) pointVoucherSection.style.display = 'block';
        }
    });
}
            // ===== UTILITY FUNCTIONS =====
            function showAlert(message, type = 'success') {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                    <i class="${iconClass} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                const mainContainer = document.querySelector('.container.mt-4');
                if (mainContainer) {
                    mainContainer.insertBefore(alertDiv, mainContainer.firstElementChild);
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.remove();
                        }
                    }, 5000);
                }
            }
        });
    </script>

    <!-- Styles -->
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

        .btn-remove,
        .btn-edit {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f8f9fa;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-remove,
        .btn-edit {
            color: #9e0620;
        }

        .btn-remove:hover {
            background-color: #9e0620;
            color: white;
            transform: rotate(90deg);
        }

        .btn-edit:hover {
            background-color: #9e0620;
            color: white;
            transform: scale(1.1);
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

        /* Point Voucher Section */
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

        /* Promo Cards */
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

        /* Warning subtle */
        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.15) !important;
        }

        .bg-success-subtle {
            background-color: rgba(40, 167, 69, 0.15) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 10px;
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
@endsection
