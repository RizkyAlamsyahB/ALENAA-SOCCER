<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <!-- Header content remains unchanged -->
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="index.html"><img src="data:image/svg+xml..." alt="Logo" srcset=""></a>
                </div>
                <!-- Theme toggle and other header elements remain unchanged -->
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                @if (auth()->user()->hasRole('admin'))
                    <li class="sidebar-title">Main Menu</li>

                    {{-- Point of Sale (POS) --}}
                    <li class="sidebar-item {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pos.index') }}" class="sidebar-link">
                            <i class="bi bi-receipt"></i>
                            <span>Point of Sale (POS)</span>
                        </a>
                    </li>

                    {{-- Dashboard --}}
                    <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Schedule Management --}}
                    <li class="sidebar-item has-sub {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-calendar3"></i>
                            <span>Jadwal</span>
                        </a>
                        <ul class="submenu {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('admin.schedule.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.index') }}" class="submenu-link">Kalender Jadwal</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('admin.schedule.all-bookings') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.all-bookings') }}" class="submenu-link">Semua Booking</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('admin.schedule.membership') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.membership') }}" class="submenu-link">Jadwal Membership</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Lapangan (Field Management) --}}
                    <li class="sidebar-item has-sub {{ request()->routeIs('admin.fields.*') || request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-geo-alt"></i>
                            <span>Lapangan</span>
                        </a>
                        <ul class="submenu {{ request()->routeIs('admin.fields.*') || request()->routeIs('admin.schedule.field*') ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('admin.fields.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.fields.index') }}" class="submenu-link">Data Lapangan</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('admin.schedule.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.index') }}" class="submenu-link">Jadwal Lapangan</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Fotografer --}}
                    <li class="sidebar-item {{ request()->routeIs('admin.photo-packages.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.photo-packages.index') }}" class="sidebar-link">
                            <i class="bi bi-camera"></i>
                            <span>Data Fotografer</span>
                        </a>
                    </li>

                    {{-- Product Management --}}
                    <li class="sidebar-item has-sub {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.rental-items.*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-cart"></i>
                            <span>Produk</span>
                        </a>
                        <ul class="submenu {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.rental-items.*') ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.products.index') }}" class="submenu-link">Produk Jualan</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('admin.rental-items.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.rental-items.index') }}" class="submenu-link">Produk Sewa</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Membership Management --}}
                    <li class="sidebar-item has-sub {{ request()->routeIs('admin.memberships.*') || request()->routeIs('admin.schedule.membership*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-person-badge"></i>
                            <span>Membership</span>
                        </a>
                        <ul class="submenu {{ request()->routeIs('admin.memberships.*') || request()->routeIs('admin.schedule.membership*') ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('admin.memberships.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.memberships.index') }}" class="submenu-link">Member</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('admin.schedule.membership') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.membership') }}" class="submenu-link">Jadwal Member</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (auth()->user()->hasRole('owner'))
                    {{-- Owner Section --}}
                    <li class="sidebar-title">Manajemen (Owner)</li>

                    {{-- Reports & Statistics --}}
                    <li class="sidebar-item has-sub {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-bar-chart-fill"></i>
                            <span>Laporan & Statistik</span>
                        </a>
                        <ul class="submenu {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('owner.reports.index') ? 'active' : '' }}">
                                <a href="{{ route('owner.reports.index') }}">Dashboard Laporan</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('owner.reports.revenue') ? 'active' : '' }}">
                                <a href="{{ route('owner.reports.revenue') }}">Ringkasan Pendapatan</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('owner.reports.field-revenue') ? 'active' : '' }}">
                                <a href="{{ route('owner.reports.field-revenue') }}">Pendapatan Lapangan</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('owner.reports.rental-revenue') ? 'active' : '' }}">
                                <a href="{{ route('owner.reports.rental-revenue') }}">Pendapatan Rental</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('owner.reports.photographer-revenue') ? 'active' : '' }}">
                                <a href="{{ route('owner.reports.photographer-revenue') }}">Pendapatan Fotografer</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('owner.reviews.summary') ? 'active' : '' }}">
                                <a href="{{ route('owner.reviews.summary') }}">Ringkasan Review</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('owner.reports.transactions') ? 'active' : '' }}">
                                <a href="{{ route('owner.reports.transactions') }}">Riwayat Pendapatan POS</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Kelola Diskon --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.discounts.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.discounts.index') }}" class="sidebar-link">
                            <i class="bi bi-tag-fill"></i>
                            <span>Kelola Diskon</span>
                        </a>
                    </li>

                    {{-- Review & Rating --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.reviews.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.reviews.index') }}" class="sidebar-link">
                            <i class="bi bi-star-fill"></i>
                            <span>Review & Rating</span>
                        </a>
                    </li>

                    {{-- Poin Voucher --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.point_vouchers.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.point_vouchers.index') }}" class="sidebar-link">
                            <i class="bi bi-gift-fill"></i>
                            <span>Poin Voucher</span>
                        </a>
                    </li>

                    {{-- Data Pengguna - Direct link without submenu --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.users.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.users.index') }}" class="sidebar-link">
                            <i class="bi bi-people-fill"></i>
                            <span>Data Pengguna</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasRole('photographer'))
                    {{-- Photographer Section --}}
                    <li class="sidebar-title">Panel Fotografer</li>

                    {{-- Photographer Dashboard --}}
                    <li class="sidebar-item {{ request()->routeIs('photographers.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('photographers.dashboard') }}" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Photographer Schedule --}}
                    <li class="sidebar-item {{ request()->routeIs('photographers.schedule') ? 'active' : '' }}">
                        <a href="{{ route('photographers.schedule') }}" class="sidebar-link">
                            <i class="bi bi-calendar-week"></i>
                            <span>Jadwal Saya</span>
                        </a>
                    </li>
                @endif

                {{-- Logout - Direct link without submenu --}}
                <li class="sidebar-item">
                    <a href="{{ route('logout') }}" class="sidebar-link text-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
