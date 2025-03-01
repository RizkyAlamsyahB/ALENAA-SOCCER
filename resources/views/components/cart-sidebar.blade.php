<!-- File: resources/views/components/cart-sidebar.blade.php -->

<!-- Cart Sidebar -->
<div class="cart-sidebar" id="cartSidebar">
    <!-- Cart Header -->
    <div class="cart-header p-3 border-bottom bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Keranjang Saya</h6>
            <button class="btn-close" onclick="toggleCart()"></button>
        </div>
    </div>

    <!-- Cart Body -->
    <div class="cart-body">
        <!-- Cart Items -->
        <div class="cart-items p-2">
            @php
                $cartItems = session()->get('booking_cart', []);
                $subtotal = 0;
            @endphp

            @if(count($cartItems) > 0)
                @foreach($cartItems as $itemId => $item)
                    @php
                        $subtotal += $item['price'];
                    @endphp
                    <div class="cart-item p-2 border-bottom" data-item-id="{{ $itemId }}">
                        <div class="d-flex gap-3">
                            <div class="cart-item-image">
                                @if($item['field_image'])
                                    <img src="{{ Storage::url($item['field_image']) }}" class="rounded" alt="{{ $item['field_name'] }}" width="80" height="80" style="object-fit: cover;">
                                @else
                                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e" class="rounded" alt="{{ $item['field_name'] }}" width="80" height="80" style="object-fit: cover;">
                                @endif
                            </div>
                            <div class="cart-item-details flex-grow-1">
                                <h6 class="mb-1 fw-semibold">{{ $item['field_name'] }}</h6>
                                <p class="mb-1 text-muted small">{{ $item['field_type'] }} • {{ $item['formatted_date'] }}</p>
                                <p class="mb-1 text-muted small">{{ $item['time_slot'] }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-danger fw-semibold">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <form action="{{ route('user.fields.remove-from-cart', $itemId) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-close ms-auto align-self-start remove-item-btn"></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="cart-empty p-4 text-center">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">Keranjang Anda kosong</p>
                </div>
            @endif
        </div>

        @if(count($cartItems) > 0)
            <!-- Cart Summary -->
            <div class="cart-summary p-3 border-top bg-light mt-auto">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <!-- Jika ada diskon, bisa ditambahkan disini -->
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold text-danger">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('user.fields.checkout') }}" class="btn btn-danger">
                        <i class="fas fa-credit-card me-2"></i>Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Cart Overlay -->
<div class="cart-overlay" onclick="toggleCart()"></div>
