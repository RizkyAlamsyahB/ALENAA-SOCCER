<div class="left side-menu">
    <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
        <i class="ion-close"></i>
    </button>

    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            <a href="index.html" class="logo">
                <img src="{{ asset('assets/template/assets/images/logo-lg.png') }}" alt="" class="logo-large">
            </a>
        </div>
    </div>

    <div class="sidebar-inner niceScrollleft">
        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>

                <li>
                    <a href="dashboard" class="waves-effect">
                        <i class="mdi mdi-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- MANAJEMEN LAPANGAN -->
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-soccer"></i>
                        <span>Lapangan</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="lapangan">Data Lapangan</a></li>
                        <li><a href="jadwal">Jadwal Lapangan</a></li>
                        <li><a href="booking">Pemesanan Lapangan</a></li>
                    </ul>
                </li>

                <!-- MANAJEMEN MEMBERSHIP -->
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-account-card-details"></i>
                        <span>Membership</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="member">Data Member</a></li>
                        <li><a href="pembayaran-member">Pembayaran Member</a></li>
                        <li><a href="jadwal-member">Jadwal Member</a></li>
                    </ul>
                </li>

                <!-- MANAJEMEN PRODUK & F&B -->
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-shopping"></i>
                        <span>Produk</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="produk-jual">Produk Jualan</a></li>
                        <li><a href="produk-sewa">Produk Sewa</a></li>
                        <li><a href="stok">Manajemen Stok</a></li>
                    </ul>
                </li>

                <!-- MANAJEMEN FOTOGRAFER -->
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-camera"></i>
                        <span>Fotografer</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="fotografer">Data Fotografer</a></li>
                        <li><a href="jadwal-fotografer">Jadwal Fotografer</a></li>
                        <li><a href="booking-fotografer">Pemesanan Fotografer</a></li>
                    </ul>
                </li>

                <!-- MANAJEMEN TRANSAKSI -->
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-cart"></i>
                        <span>Transaksi</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="pos">Point of Sale (POS)</a></li>
                        <li><a href="pesanan">Data Pesanan</a></li>
                        <li><a href="pembayaran">Pembayaran</a></li>
                        <li><a href="keranjang">Keranjang Belanja</a></li>
                    </ul>
                </li>

                <!-- OPEN MABAR -->
                <li>
                    <a href="open-mabar" class="waves-effect">
                        <i class="mdi mdi-account-multiple"></i>
                        <span>Open Mabar</span>
                    </a>
                </li>

                <!-- CUSTOMER & REWARD -->
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-account-star"></i>
                        <span>Customer & Reward</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="customer">Data Customer</a></li>
                        <li><a href="poin-reward">Sistem Poin Reward</a></li>
                        <li><a href="review">Review & Rating</a></li>
                    </ul>
                </li>

                <!-- KHUSUS OWNER -->
                <li class="menu-title">Manajemen (Owner)</li>

                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-chart-bar"></i>
                        <span>Laporan</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="laporan-booking">Laporan Booking</a></li>
                        <li><a href="laporan-penjualan">Laporan Penjualan</a></li>
                        <li><a href="laporan-keuangan">Laporan Keuangan</a></li>
                        <li><a href="statistik">Statistik Penggunaan</a></li>
                    </ul>
                </li>

                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect">
                        <i class="mdi mdi-settings"></i>
                        <span>Pengaturan</span>
                        <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                    </a>
                    <ul class="list-unstyled">
                        <li><a href="diskon">Kelola Diskon</a></li>
                        <li><a href="pengguna">Manajemen Pengguna</a></li>
                        <li><a href="pengaturan-sistem">Pengaturan Sistem</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div> <!-- end sidebarinner -->
</div>
<!-- End left Sidebar -->
