<!-- File: resources/views/layouts/navigation.blade.php -->

<link rel="stylesheet" href="{{ asset('css/users/navigation.css') }}">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top ">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="/">
            <span class="fw-bold">
                <span class="brand-primary">ALENA</span>
                <span class="text-dark">SOCCER</span>
            </span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 shadow-none" type="button" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="/">
                        <i class="fas fa-home me-2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('user.fields.index') }}">
                        <i class="fas fa-calendar-alt me-2"></i> <span>Sewa Lapangan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('mabar.index') }}">
                        <i class="fas fa-users me-2"></i> <span>Main Bareng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('rental.index') }}">
                        <i class="fas fa-basketball-ball me-2"></i> <span>Rental</span>
                    </a>
                </li>
            </ul>

            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                @auth
                    <!-- Cart Button -->
                    @php
                        $cartItems = session()->get('booking_cart', []);
                        $cartCount = count($cartItems);
                    @endphp
                    <button class="btn btn-light rounded-circle position-relative p-2" type="button" id="cartButton"
                        onclick="toggleCart()">
                        <i class="fas fa-shopping-cart"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary cart-count">
                            {{ $cartCount }}
                        </span>
                    </button>

                    <!-- Include Cart Sidebar Component -->
                    @include('components.cart-sidebar')

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill px-4 py-2 d-flex align-items-center gap-2" type="button"
                            id="userMenu" data-bs-toggle="dropdown" aria-expanded="false"
                            style="min-width: 150px; max-width: 220px;">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px; font-size: 18px;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="d-none d-md-inline text-truncate" style="max-width: 120px;">
                                {{ Auth::user()->name }}
                            </span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                            <li>
                                <div class="dropdown-header px-4 py-3 border-bottom">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <div class="text-muted small">{{ Auth::user()->email }}</div>
                                </div>
                            </li>
                            <li><a class="dropdown-item px-4 py-2" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2 text-muted"></i>My Profile
                                </a></li>
                            <li><a class="dropdown-item px-4 py-2" href="#">
                                    <i class="fas fa-cog me-2 text-muted"></i>Settings
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item px-4 py-2 text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="/login" class="btn btn-outline-danger rounded-pill px-4 py-2 hover-scale">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </a>
                    <a href="/register" class="btn btn-danger rounded-pill px-4 py-2 hover-scale">
                        <i class="fas fa-user-plus me-2"></i>Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Navbar and Cart Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Navbar toggle script
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        function closeNavbar() {
            let bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            bsCollapse.hide();
        }

        // Toggle navbar secara manual
        navbarToggler.addEventListener('click', function() {
            if (navbarCollapse.classList.contains('show')) {
                closeNavbar();
            } else {
                let bsCollapse = new bootstrap.Collapse(navbarCollapse);
            }
        });

        // Menutup navbar saat klik di luar
        document.addEventListener('click', function(event) {
            if (!navbarToggler.contains(event.target) && !navbarCollapse.contains(event.target)) {
                closeNavbar();
            }
        });

        // Menutup navbar saat klik link di dalam navbar
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => closeNavbar());
        });

        // Cart integration for AJAX removal
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const url = form.getAttribute('action');
                const itemElement = this.closest('.cart-item');
                const itemId = itemElement.dataset.itemId;

                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove item from DOM
                            itemElement.remove();

                            // Update cart count
                            updateCartCount(data.cart_count);

                            // Update totals
                            updateCartTotals();

                            // Show empty message if needed
                            checkEmptyCart();
                        }
                    })
                    .catch(error => {
                        console.error('Error removing item from cart:', error);
                    });
            });
        });
    });

    // Toggle cart sidebar
    function toggleCart() {
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.querySelector('.cart-overlay');

        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

        // Toggle body scroll
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    // Close cart when pressing ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const sidebar = document.getElementById('cartSidebar');
            if (sidebar.classList.contains('active')) {
                toggleCart();
            }
        }
    });

    // Update cart count badge
    function updateCartCount(count) {
        const badge = document.querySelector('.cart-count');
        if (badge) {
            badge.textContent = count;
        }
    }

    // Update cart totals
    function updateCartTotals() {
        const cartItems = document.querySelectorAll('.cart-item');
        let subtotal = 0;

        cartItems.forEach(item => {
            const priceText = item.querySelector('.text-danger').textContent;
            const price = parseInt(priceText.replace(/[^0-9]/g, ''));
            subtotal += price;
        });

        // Update subtotal
        const subtotalElement = document.querySelector('.cart-summary .fw-semibold');
        if (subtotalElement) {
            subtotalElement.textContent = `Rp ${formatNumber(subtotal)}`;
        }

        // Update total (add any discounts here if needed)
        const totalElement = document.querySelector('.cart-summary .fw-bold.text-danger');
        if (totalElement) {
            totalElement.textContent = `Rp ${formatNumber(subtotal)}`;
        }
    }

    // Check if cart is empty and show message if needed
    function checkEmptyCart() {
        const cartItems = document.querySelectorAll('.cart-item');
        const cartItemsContainer = document.querySelector('.cart-items');
        const cartSummary = document.querySelector('.cart-summary');

        if (cartItems.length === 0 && cartItemsContainer) {
            cartItemsContainer.innerHTML = `
                <div class="cart-empty p-4 text-center">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">Keranjang Anda kosong</p>
                </div>
            `;

            // Hide summary if cart is empty
            if (cartSummary) {
                cartSummary.style.display = 'none';
            }
        }
    }

    // Format number with thousand separator
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // JavaScript untuk mendeteksi scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');

        if (window.scrollY > 0) {
            navbar.classList.add('navbar-scroll');
        } else {
            navbar.classList.remove('navbar-scroll');
        }
    });
</script>
