<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <style>
                    .logo a {
                        font-size: 16px;
                        font-weight: bold;
                        color: #435ebe;
                        /* Warna sesuai dengan logo sebelumnya */
                        text-decoration: none;
                        /* Menghapus garis bawah */
                        font-family: Arial, sans-serif;
                        /* Menyesuaikan font */
                    }
                </style>

                <div class="logo">
                    <a href="index.html">ALENA SOCCER</a>
                </div>

                <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                        role="img" class="iconify iconify--system-uicons" width="20" height="20"
                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                        <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                opacity=".3"></path>
                            <g transform="translate(-210 -1)">
                                <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                <circle cx="220.5" cy="11.5" r="4"></circle>
                                <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                </path>
                            </g>
                        </g>
                    </svg>
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                        <label class="form-check-label"></label>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20"
                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                        </path>
                    </svg>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
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
                            <li
                                class="submenu-item {{ request()->routeIs('admin.schedule.all-bookings') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.all-bookings') }}" class="submenu-link">Semua
                                    Booking</a>
                            </li>
                            <li
                                class="submenu-item {{ request()->routeIs('admin.schedule.membership') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.membership') }}" class="submenu-link">Jadwal
                                    Membership</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Lapangan (Field Management) --}}
                    <li
                        class="sidebar-item has-sub {{ request()->routeIs('admin.fields.*') || request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-geo-alt"></i>
                            <span>Lapangan</span>
                        </a>
                        <ul
                            class="submenu {{ request()->routeIs('admin.fields.*') || request()->routeIs('admin.schedule.field*') ? 'active' : '' }}">
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
                    <li
                        class="sidebar-item has-sub {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.rental-items.*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-cart"></i>
                            <span>Produk</span>
                        </a>
                        <ul
                            class="submenu {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.rental-items.*') ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.products.index') }}" class="submenu-link">Produk Jualan</a>
                            </li>
                            <li class="submenu-item {{ request()->routeIs('admin.rental-items.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.rental-items.index') }}" class="submenu-link">Produk Sewa</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Membership Management --}}
                    <li
                        class="sidebar-item has-sub {{ request()->routeIs('admin.memberships.*') || request()->routeIs('admin.schedule.membership*') ? 'active' : '' }}">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-person-badge"></i>
                            <span>Membership</span>
                        </a>
                        <ul
                            class="submenu {{ request()->routeIs('admin.memberships.*') || request()->routeIs('admin.schedule.membership*') ? 'active' : '' }}">
                            <li
                                class="submenu-item {{ request()->routeIs('admin.memberships.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.memberships.index') }}" class="submenu-link">Member</a>
                            </li>
                            <li
                                class="submenu-item {{ request()->routeIs('admin.schedule.membership') ? 'active' : '' }}">
                                <a href="{{ route('admin.schedule.membership') }}" class="submenu-link">Jadwal
                                    Member</a>
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
                                <a href="{{ route('owner.reports.index') }}" class="submenu-link">Dashboard
                                    Laporan</a>
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
    <a href="{{ route('logout') }}"
       class="sidebar-link bg-danger text-white rounded-3"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
       style="display: flex; align-items: center; padding: 0.5rem 1rem;">
        <i class="bi bi-box-arrow-right me-2 text-white"></i>
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
