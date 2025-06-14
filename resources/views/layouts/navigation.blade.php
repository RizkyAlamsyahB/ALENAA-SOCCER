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
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('user.fields.index') }}">
                        <span>Sewa Lapangan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('user.mabar.index') }}">
                        <span>Main Bareng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('user.rental_items.index') }}">
                        <span>Perlengkapan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('user.photographer.index') }}">
                        <span>Fotografer</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('user.membership.index') }}">
                        <span>Membership</span>
                    </a>
                </li>
            </ul>

            <!-- Right Side -->
            <div class="d-flex align-items-center gap-3">
                @auth
                    <!-- Cart Button -->
                    @php
                        $cartCount = \App\Models\CartItem::where('cart_id', function ($query) {
                            $query->select('id')->from('carts')->where('user_id', Auth::id())->limit(1);
                        })->count();
                    @endphp
                    <a href="{{ route('user.cart.view') }}" class="btn btn-light rounded-circle position-relative p-2"
                        type="button" id="cartButton">
                        <i class="fas fa-shopping-cart"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary cart-count">
                            {{ $cartCount }}
                        </span>
                    </a>




                    <!-- User Dropdown (Dark Style) -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill px-4 py-2 d-flex align-items-center gap-2" type="button"
                            id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end border-0 rounded-4 shadow-lg  text-dark"
                            style="min-width: 240px;">
                            <li>
                                <div class="dropdown-header px-4 py-3 border-bottom border-secondary">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <div class="text-light-50 small">{{ Auth::user()->email }}</div>

                                </div>
                            </li>
                            <li><a class="dropdown-item px-4 py-3 text-dark" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i>Profil Saya
                                </a></li>
                            <li><a class="dropdown-item px-4 py-3 text-dark"
                                    href="{{ route('user.membership.my-memberships') }}">
                                    <i class="fas fa-user-tag me-2"></i>Membership Saya
                                </a></li>
                            <li><a class="dropdown-item px-4 py-3 text-dark" href="{{ route('user.payment.history') }}">
                                    <i class="fas fa-receipt me-2"></i>Riwayat Pembayaran
                                </a></li>
                            <li>
                                <hr class="dropdown-divider border-secondary m-0">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item px-4 py-3 text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <!-- CSS untuk dropdown dark mode -->
                    <style>
                        .dropdown-menu. {
                            animation: dropdown-fade-in 0.2s ease forwards;
                        }

                        @keyframes dropdown-fade-in {
                            from {
                                opacity: 0;
                                transform: translateY(-10px);
                            }

                            to {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }
                    </style>
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

{{-- Navbar Scripts --}}
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
    });

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
