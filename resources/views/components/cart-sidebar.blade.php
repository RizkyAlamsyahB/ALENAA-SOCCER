<!-- Cart Button (to open sidebar) -->
<button class="cart-btn" onclick="toggleCart()">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count" id="cartCount">
        @php
            $cartItems = session()->get('booking_cart', []);
            echo count($cartItems);
        @endphp
    </span>
</button>

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

<style>
/* Cart Sidebar Styles */
.cart-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #9e0620;
    color: white;
    border: none;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    z-index: 999;
    transition: all 0.3s ease;
}

.cart-btn:hover {
    transform: scale(1.05);
    background-color: #bb2d3b;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #212529;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-sidebar {
    position: fixed;
    top: 0;
    right: -400px; /* Start off-screen */
    width: 350px;
    max-width: 90vw;
    height: 100vh;
    background-color: white;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}

.cart-sidebar.open {
    right: 0;
}

.cart-header {
    flex-shrink: 0;
}

.cart-body {
    flex-grow: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.cart-items {
    overflow-y: auto;
    flex-grow: 1;
}

.cart-summary {
    flex-shrink: 0;
}

.cart-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9998;
    display: none;
}

.cart-overlay.open {
    display: block;
}

.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.cart-item-image img {
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.remove-item-btn {
    font-size: 0.7rem;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.remove-item-btn:hover {
    opacity: 1;
    transform: scale(1.2);
}

.cart-empty {
    color: #6c757d;
}

.cart-empty i {
    opacity: 0.3;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .cart-btn {
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }

    .cart-count {
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
    }

    .cart-sidebar {
        width: 300px;
    }
}
</style>

<script>
function toggleCart() {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.querySelector('.cart-overlay');

    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');

    // Prevent scrolling when cart is open
    if (sidebar.classList.contains('open')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Handle cart item removal with AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Listen for form submissions from remove buttons
    document.querySelectorAll('.cart-item form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const itemElement = this.closest('.cart-item');
            const itemId = itemElement.dataset.itemId;
            const formAction = this.getAttribute('action');
            const formData = new FormData(this);

            // Add animation
            itemElement.style.opacity = '0.5';

            // Send AJAX request
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item with animation
                    itemElement.style.height = itemElement.offsetHeight + 'px';
                    itemElement.style.overflow = 'hidden';

                    setTimeout(() => {
                        itemElement.style.height = '0';
                        itemElement.style.padding = '0';
                        itemElement.style.margin = '0';

                        setTimeout(() => {
                            itemElement.remove();

                            // Update cart count
                            document.getElementById('cartCount').textContent = data.cartCount;

                            // Update subtotal and total
                            if (data.cartCount > 0) {
                                document.querySelector('.cart-summary .text-muted + .fw-semibold').textContent = 'Rp ' + data.formattedSubtotal;
                                document.querySelector('.cart-summary .fw-bold + .fw-bold').textContent = 'Rp ' + data.formattedTotal;
                            } else {
                                // If cart is empty, show empty cart message
                                const cartItems = document.querySelector('.cart-items');
                                cartItems.innerHTML = `
                                    <div class="cart-empty p-4 text-center">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">Keranjang Anda kosong</p>
                                    </div>
                                `;

                                // Remove summary
                                const cartSummary = document.querySelector('.cart-summary');
                                if (cartSummary) {
                                    cartSummary.remove();
                                }
                            }

                            // Show success message
                            showNotification('Item telah dihapus dari keranjang');
                        }, 300);
                    }, 10);
                } else {
                    // Restore opacity if there was an error
                    itemElement.style.opacity = '1';
                    showNotification('Gagal menghapus item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                itemElement.style.opacity = '1';
                showNotification('Terjadi kesalahan', 'error');
            });
        });
    });
});

// Function to show notifications
function showNotification(message, type = 'success') {
    // Check if toastr is available
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        // Create a simple notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');

            // Hide after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');

                // Remove from DOM after animation
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }, 10);
    }
}

// Add this to your CSS if not using toastr
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification.success {
        background-color: #28a745;
    }

    .notification.error {
        background-color: #dc3545;
    }
`;
document.head.appendChild(notificationStyles);
</script>
