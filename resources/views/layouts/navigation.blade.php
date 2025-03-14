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
                    <a class="nav-link d-flex align-items-center px-3" href="{{ route('user.rental_items.index') }}">
                        <i class="fas fa-basketball-ball me-2"></i> <span>Rental</span>
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

<!-- User Dropdown -->
<div class="dropdown">
    <button class="btn btn-light rounded-pill px-4 py-2 d-flex align-items-center gap-2" type="button"
        id="userMenu" data-bs-toggle="dropdown" aria-expanded="false"
        style="min-width: 150px; max-width: 220px;">

        @if(Auth::user()->profile_picture)
            <!-- Display profile picture if available -->
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px; overflow: hidden;">
                <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="{{ Auth::user()->name }}"
                    class="w-100 h-100" style="object-fit: cover;">
            </div>
        @else
            <!-- Fallback to initial if no profile picture -->
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px; font-size: 18px;">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        @endif

        <span class="d-none d-md-inline" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
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
        <li><a class="dropdown-item px-4 py-2" href="{{ route('user.payment.history') }}">
                <i class="fas fa-receipt me-2 text-muted"></i>Payment History
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
