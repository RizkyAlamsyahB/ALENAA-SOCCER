<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top ">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="/">

            <span class="fw-bold" style="font-size: 24px;">
                ALENA
                <span class="text-dark">
                    S
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                        width="30" height="30" alt="SportVue Logo" class="align-text-bottom">
                    CCER
                </span>
            </span>

        </a>


        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Navigation -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="#dashboard">
                        <i class="fas fa-home me-2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="#booking">
                        <i class="fas fa-calendar-alt me-2"></i> <span>Booking</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="#membership">
                        <i class="fas fa-star me-2"></i> <span>Membership</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="#shop">
                        <i class="fas fa-shopping-bag me-2"></i> <span>Shop</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="#community">
                        <i class="fas fa-users me-2"></i> <span>Community</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="#contact">
                        <i class="fas fa-envelope me-2"></i> <span>Contact</span>
                    </a>
                </li>
            </ul>


            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                @auth
                    <!-- Cart Button -->
                    <button class="btn btn-light rounded-circle position-relative p-2" type="button" id="cartButton"
                        onclick="toggleCart()">
                        <i class="fas fa-shopping-cart"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
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
                                <!-- Item template -->
                                <div class="cart-item p-2 border-bottom">
                                    <div class="d-flex gap-3">
                                        <div class="cart-item-image">
                                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                                class="rounded" alt="Field" width="80" height="80"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="cart-item-details flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">Lapangan A</h6>
                                            <p class="mb-1 text-muted small">Indoor Field • 5v5</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-danger fw-semibold">Rp 30.000</span>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary">-</button>
                                                    <button type="button" class="btn btn-outline-secondary px-3">1</button>
                                                    <button type="button" class="btn btn-outline-secondary">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn-close ms-auto align-self-start"></button>
                                    </div>
                                </div>
                                <!-- Repeat items... -->
                                <!-- Item template -->
                                <div class="cart-item p-2 border-bottom">
                                    <div class="d-flex gap-3">
                                        <div class="cart-item-image">
                                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                                class="rounded" alt="Field" width="80" height="80"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="cart-item-details flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">Lapangan A</h6>
                                            <p class="mb-1 text-muted small">Indoor Field • 5v5</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-danger fw-semibold">Rp 30.000</span>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-secondary">-</button>
                                                    <button type="button"
                                                        class="btn btn-outline-secondary px-3">1</button>
                                                    <button type="button" class="btn btn-outline-secondary">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn-close ms-auto align-self-start"></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Cart Summary -->
                            <div class="cart-summary p-3 border-top bg-light mt-auto">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-semibold">Rp 90.000</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Diskon</span>
                                    <span class="text-success">- Rp 10.000</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold text-danger">Rp 80.000</span>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-danger" onclick="window.location.href='/payment'">
                                        <i class="fas fa-shopping-cart me-2"></i>Checkout
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Cart Overlay -->
                    <div class="cart-overlay" onclick="toggleCart()"></div>


                    <script>
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

                        // Initialize quantity buttons
                        document.querySelectorAll('.btn-group').forEach(group => {
                            const minusBtn = group.querySelector('button:first-child');
                            const plusBtn = group.querySelector('button:last-child');
                            const quantityBtn = group.querySelector('button:nth-child(2)');

                            minusBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                let quantity = parseInt(quantityBtn.textContent);
                                if (quantity > 1) {
                                    quantityBtn.textContent = quantity - 1;
                                    updateCartTotal();
                                }
                            });

                            plusBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                let quantity = parseInt(quantityBtn.textContent);
                                quantityBtn.textContent = quantity + 1;
                                updateCartTotal();
                            });
                        });

                        function updateCartTotal() {
                            const cartItems = document.querySelectorAll('.cart-item');
                            let subtotal = 0;

                            cartItems.forEach(item => {
                                const price = parseInt(item.querySelector('.text-danger').textContent.replace(/[^0-9]/g, ''));
                                const quantity = parseInt(item.querySelector('.btn-group button:nth-child(2)').textContent);
                                subtotal += price * quantity;
                            });

                            document.querySelector('.cart-summary .fw-semibold').textContent = `Rp ${subtotal.toLocaleString()}`;

                            const discount = 10000;
                            const total = subtotal - discount;
                            document.querySelector('.cart-summary .fw-bold.text-danger').textContent = `Rp ${total.toLocaleString()}`;
                        }

                        // Remove cart items
                        document.querySelectorAll('.cart-item .btn-close').forEach(button => {
                            button.addEventListener('click', (e) => {
                                e.stopPropagation();
                                const item = e.target.closest('.cart-item');
                                item.remove();
                                updateCartTotal();
                                updateCartBadge();
                            });
                        });

                        function updateCartBadge() {
                            const itemCount = document.querySelectorAll('.cart-item').length;
                            const badge = document.querySelector('#cartButton .badge');

                            if (badge) {
                                badge.textContent = itemCount;
                            }

                            // Show empty cart message if no items
                            const cartItems = document.querySelector('.cart-items');
                            if (itemCount === 0 && cartItems) {
                                cartItems.innerHTML = `
                                <div class="cart-empty p-4 text-center">
                                 <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="mb-0 text-muted">Keranjang Anda kosong</p>
                                </div>
                                `;
                            }
                        }
                    </script>
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill px-4 py-2 d-flex align-items-center gap-2"
                        type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false"
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
<script>
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

<style>
    :root {
        --primary-color: #9E0620;
        --secondary-color: #2A2A2A;
        --danger-color: #9E0620;
        /* Custom danger color */
    }

    /* Cart Sidebar Styles */
    .cart-sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100vh;
        background: white;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 1060;
        transition: right 0.3s ease-out;
        display: flex;
        flex-direction: column;
    }

    .cart-sidebar.active {
        right: 0;
    }

    .cart-body {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .cart-items {
        flex: 1;
    }

    .cart-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        display: none;
    }

    .cart-overlay.active {
        display: block;
    }

    /* Cart Item Styles */
    .cart-item {
        transition: background-color 0.2s;
    }

    .cart-item:hover {
        background-color: #f8f9fa;
    }

    .cart-item-image img {
        transition: transform 0.2s;
    }

    .cart-item:hover .cart-item-image img {
        transform: scale(1.05);
    }

    /* Responsive Styles */
    @media (max-width: 576px) {
        .cart-sidebar {
            width: 100%;
            right: -100%;
        }
    }

    .navbar-scroll {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .navbar {
        padding: 0.5rem 1rem;
        font-size: 18px;

    }

    .navbar-nav {
        gap: 1rem;
        /* Space between nav items */
    }

    .nav-link {
        padding: 0.5rem 1rem;
        white-space: nowrap;
        /* Prevent text wrapping */
        display: flex;
        align-items: center;
    }

    .nav-link i {
        font-size: 1.1rem;
        width: 1.5rem;
        /* Fixed width for icons */
        text-align: center;
    }

    @media (min-width: 1920px) {
        .container {
            max-width: 1800px;
            /* Wider container for large screens */
        }

        .navbar-nav {
            gap: 2rem;
            /* More space between items on large screens */
        }
    }

    /* Hover effect untuk button outline danger */
    .btn-outline-danger {
        border-color: var(--primary-color);
        color: var(--primary-color);
        transition: all 0.3s ease;
    }

    .btn-outline-danger:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(158, 6, 32, 0.3);
    }

    /* Animasi hover */
    .hover-scale {
        transition: all 0.3s ease;
    }

    .hover-scale:hover {
        transform: translateY(-3px);
    }
</style>
