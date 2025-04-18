@extends('layouts.app')
@section('content')
    <!-- Breadcrumb -->
    <nav class="breadcrumb-wrapper" style="margin-top: 50px;">
        <div class="container py-2">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item active text-white">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Rental Equipment</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-4">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h2 class="section-title fw-bold mb-3">Rental Equipment</h2>
                <p class="section-desc mx-auto" style="max-width: 700px;">
                    Lupa bawa perlengkapan olahraga? Jangan khawatir! Alena Soccer menyediakan layanan penyewaan
                    perlengkapan olahraga berkualitas untuk kebutuhan bermain Anda.
                </p>
            </div>

            <!-- Filter & Sort Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('user.rental_items.index') }}" method="GET" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="search-input">
                                    <i
                                        class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                    <input type="text" name="search" class="form-control ps-5"
                                        placeholder="Cari equipment..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select" onchange="this.form.submit()">
                                    <option value="all"
                                        {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>Semua
                                        Kategori</option>
                                    <option value="jersey" {{ request('category') == 'jersey' ? 'selected' : '' }}>Jersey
                                        ({{ $categoryCounts['jersey'] }})</option>
                                    <option value="shoes" {{ request('category') == 'shoes' ? 'selected' : '' }}>Sepatu
                                        ({{ $categoryCounts['shoes'] }})</option>
                                    <option value="ball" {{ request('category') == 'ball' ? 'selected' : '' }}>Bola
                                        ({{ $categoryCounts['ball'] }})</option>
                                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Aksesoris
                                        ({{ $categoryCounts['other'] }})</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="latest"
                                        {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru
                                    </option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga
                                        Terendah</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                        Harga Tertinggi</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-danger rounded-pill w-100">
                                    <i class="fas fa-filter me-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="result-badge">
                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                        {{ $rentalItems->total() }} item ditemukan
                    </span>
                </div>
                <div class="view-toggle">
                    <button class="btn btn-light rounded-pill active me-2">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="btn btn-light rounded-pill">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Category Navigation -->
            <div class="category-nav mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.rental_items.index') }}"
                        class="category-pill {{ !request('category') || request('category') == 'all' ? 'active' : '' }}">
                        <i class="fas fa-futbol me-2"></i>
                        Semua
                    </a>
                    <a href="{{ route('user.rental_items.index', ['category' => 'jersey']) }}"
                        class="category-pill {{ request('category') == 'jersey' ? 'active' : '' }}">
                        <i class="fas fa-tshirt me-2"></i>
                        Jersey
                    </a>
                    <a href="{{ route('user.rental_items.index', ['category' => 'shoes']) }}"
                        class="category-pill {{ request('category') == 'shoes' ? 'active' : '' }}">
                        <i class="fas fa-shoe-prints me-2"></i>
                        Sepatu
                    </a>
                    <a href="{{ route('user.rental_items.index', ['category' => 'ball']) }}"
                        class="category-pill {{ request('category') == 'ball' ? 'active' : '' }}">
                        <i class="fas fa-futbol me-2"></i>
                        Bola
                    </a>
                    <a href="{{ route('user.rental_items.index', ['category' => 'other']) }}"
                        class="category-pill {{ request('category') == 'other' ? 'active' : '' }}">
                        <i class="fas fa-mitten me-2"></i>
                        Aksesoris
                    </a>
                </div>
            </div>

            <!-- Equipment Grid -->
            <div class="row g-4">
                @forelse($rentalItems as $item)
                    <!-- Equipment Card -->
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-img-wrapper position-relative">
                                @if ($item->stock_available <= 3 && $item->stock_available > 0)
                                    <span class="badge bg-warning position-absolute top-0 end-0 m-2">Stok Terbatas</span>
                                @elseif($item->created_at->diffInDays(now()) < 7)
                                    <span class="badge bg-secondary position-absolute top-0 end-0 m-2">Baru</span>
                                @endif

                                @if ($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top rounded-top-4"
                                        alt="{{ $item->name }}">
                                @else
                                    <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4"
                                        alt="{{ $item->name }}">
                                @endif
                            </div>
                            <div class="card-body p-3">
                                <!-- Header -->
                                <div class="mb-2">
                                    <div class="category-badge mb-2">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill">
                                            @if ($item->category == 'ball')
                                                <i class="fas fa-futbol me-1"></i> Bola
                                            @elseif($item->category == 'jersey')
                                                <i class="fas fa-tshirt me-1"></i> Jersey
                                            @elseif($item->category == 'shoes')
                                                <i class="fas fa-shoe-prints me-1"></i> Sepatu
                                            @else
                                                <i class="fas fa-mitten me-1"></i> Aksesoris
                                            @endif
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-1">{{ $item->name }}</h5>
                                </div>

                                <!-- Availability -->
                                <div class="availability mb-3">
                                    <div
                                        class="stock-info {{ $item->stock_available > 0 ? 'text-success' : 'text-danger' }}">
                                        @if ($item->stock_available > 0)
                                            <i class="fas fa-check-circle me-1"></i>
                                            Tersedia ({{ $item->stock_available }} stok)
                                        @else
                                            <i class="fas fa-times-circle me-1"></i>
                                            Stok Habis
                                        @endif
                                    </div>
                                    @if ($item->condition)
                                        <div class="condition-info text-muted mt-1">
                                            <small><i class="fas fa-info-circle me-1"></i> Kondisi:
                                                {{ $item->condition }}</small>
                                        </div>
                                    @endif
                                </div>

                                <hr class="border-1 border-dashed opacity-50 my-2">

                                <!-- Footer -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-info">
                                        <span
                                            class="h5 mb-0 text-danger">Rp{{ number_format($item->rental_price, 0, ',', '.') }}</span>
                                        <small class="text-muted">/hari</small>
                                    </div>
                                    <a href="{{ route('user.rental_items.show', $item->id) }}"
                                        class="btn btn-sm btn-outline-danger rounded-pill">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>Tidak Ada Item Ditemukan</h4>
                            <p class="text-muted">Maaf, tidak ada item yang sesuai dengan kriteria pencarian Anda.</p>
                            <a href="{{ route('user.rental_items.index') }}" class="btn btn-outline-danger rounded-pill mt-3">
                                <i class="fas fa-sync-alt me-2"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $rentalItems->withQueryString()->links() }}
            </div>

            <!-- Info Section -->
            <div class="info-section mt-5 p-4 bg-light rounded-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6">
                        <h3 class="mb-3">Cara Menyewa Equipment</h3>
                        <ol class="rental-steps">
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">1</div>
                                    <div>
                                        <h5 class="mb-1">Pilih Equipment</h5>
                                        <p class="text-muted mb-0">Pilih equipment yang ingin Anda sewa sesuai dengan
                                            ukuran dan kebutuhan.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">2</div>
                                    <div>
                                        <h5 class="mb-1">Tentukan Durasi</h5>
                                        <p class="text-muted mb-0">Pilih tanggal mulai dan berakhirnya masa sewa.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">3</div>
                                    <div>
                                        <h5 class="mb-1">Konfirmasi Pembayaran</h5>
                                        <p class="text-muted mb-0">Lakukan pembayaran melalui metode yang tersedia.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="step-number me-3">4</div>
                                    <div>
                                        <h5 class="mb-1">Ambil Equipment</h5>
                                        <p class="text-muted mb-0">Ambil equipment di lokasi lapangan Alena Soccer.</p>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="{{ asset('assets/rental.jpg') }}" alt="Rental Guide"
                            class="img-fluid rounded-4 shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Breadcrumb Wrapper */
        .breadcrumb-wrapper {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            position: relative;
            overflow: hidden;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-breadcrumb {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
            align-items: center;
            justify-content: center;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 800;
            font-size: 1.3rem;
        }

        .breadcrumb-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 800;
            font-size: 1.3rem;
        }

        .breadcrumb-item.active {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.15);
            font-weight: 800;
            font-size: 1.3rem;
        }

        /* Section Styles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
        }

        .section-desc {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.6;
        }

        /* Category Pills */
        .category-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            background: #fff;
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border: 1.5px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .category-pill:hover {
            background: #f8f9fa;
            color: #9e0620;
            transform: translateY(-2px);
        }

        .category-pill.active {
            background: #9e0620;
            color: white;
            border-color: #9e0620;
        }

        /* Equipment Card Styles */
        .equipment-card .card-img-wrapper {
            height: 200px;
            overflow: hidden;
        }

        .equipment-card .card-img-top {
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .equipment-card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Stock Info */
        .stock-info {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Rental Steps */
        .rental-steps {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #9e0620;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Card Hover Effects */
        .card {
            background: #ffffff;
            transition: all 0.3s ease;
            border: none !important;
        }

        .card.border-0.shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        /* Form Controls */
        .search-input {
            position: relative;
        }

        .form-control,
        .form-select {
            border: 1.5px solid #dee2e6;
            padding: 0.75
        }
    </style>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
