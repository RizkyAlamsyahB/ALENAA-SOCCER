@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Fotografer</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-camera"></i> Fotografer
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-4">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <p class="section-desc mx-auto" style="max-width: 700px;">
                    Abadikan momen bermain sepak bola Anda dengan jasa fotografer profesional kami. Dapatkan foto
                    berkualitas tinggi untuk kenangan tak terlupakan.
                </p>
            </div>

            <!-- Cart Filter Info - TAMBAHAN BARU -->
            @if($filterInfo['has_filter'])
            <div class="alert alert-info border-0 rounded-4 mb-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle fa-lg text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="alert-heading mb-1">
                            <i class="fas fa-filter me-2"></i>Filter Berdasarkan Keranjang Anda
                        </h6>
                        <p class="mb-2">{{ $filterInfo['message'] }}</p>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($filterInfo['cart_fields'] as $field)
                                <span class="badge bg-primary rounded-pill">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $field->name }}
                                </span>
                            @endforeach
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('user.cart.view') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart me-1"></i>Lihat Keranjang
                            </a>
                            <a href="{{ route('user.fields.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="fas fa-plus me-1"></i>Tambah Lapangan Lain
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-light border-0 rounded-4 mb-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lightbulb fa-lg text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="alert-heading mb-1">💡 Tips</h6>
                        <p class="mb-2">Booking lapangan terlebih dahulu untuk melihat fotografer yang tersedia untuk lapangan tersebut.</p>
                        <a href="{{ route('user.fields.index') }}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fas fa-futbol me-1"></i>Pilih Lapangan Dulu
                        </a>
                    </div>
                </div>
            </div>
            @endif
            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="result-badge">
                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                        {{ $photographers->count() }} paket ditemukan
                        @if($filterInfo['has_filter'])
                            <span class="ms-1">(difilter berdasarkan {{ count($filterInfo['cart_fields']) }} lapangan di keranjang)</span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Package Grid -->
            <div class="row g-4">
                @if($photographers->isEmpty())
                    <div class="row">
                        <div class="col-12 text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                @if($filterInfo['has_filter'])
                                    <h4>Tidak Ada Fotografer untuk Lapangan Terpilih</h4>
                                    <p class="text-muted">Maaf, tidak ada fotografer yang tersedia untuk lapangan di keranjang Anda saat ini.</p>
                                    <div class="d-flex gap-2 justify-content-center flex-wrap mt-3">
                                        <a href="{{ route('user.fields.index') }}" class="btn btn-primary rounded-pill">
                                            <i class="fas fa-plus me-2"></i> Pilih Lapangan Lain
                                        </a>
                                        <a href="{{ route('user.cart.view') }}" class="btn btn-outline-secondary rounded-pill">
                                            <i class="fas fa-shopping-cart me-2"></i> Lihat Keranjang
                                        </a>
                                    </div>
                                @else
                                    <h4>Tidak Ada Paket Fotografer Ditemukan</h4>
                                    <p class="text-muted">Maaf, tidak ada paket fotografer yang sesuai dengan kriteria pencarian Anda.</p>
                                    <a href="{{ route('user.photographer.index') }}" class="btn btn-outline-danger rounded-pill mt-3">
                                        <i class="fas fa-sync-alt me-2"></i> Reset Filter
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Loop berdasarkan jenis paket -->
                    @foreach(['exclusive', 'favorite', 'plus'] as $packageType)
                        @if($photographersByType->has($packageType) && $photographersByType->get($packageType)->isNotEmpty())
                            <div class="col-12 mt-5 mb-4">
                                <h3 class="section-title">Paket {{ ucfirst($packageType) }}</h3>
                            </div>

                            <div class="row g-4">
                                @foreach($photographersByType->get($packageType) as $photographer)
                                    <!-- Photographer Card -->
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card photographer-card border-0 shadow-sm rounded-4 h-100
                                            {{ $photographer->field_restriction ? 'field-restricted' : 'field-flexible' }}">
                                            <!-- Card content -->
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0">{{ $photographer->name }}</h5>

                                                </div>

                                                <p class="card-text mb-2">
                                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                                    <span>{{ $photographer->assigned_field }}</span>
                                                </p>

                                                <!-- Rating jika ada -->
                                                @if($photographer->rating > 0)
                                                    <div class="rating mb-2">
                                                        <div class="d-flex align-items-center">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $photographer->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                            @endfor
                                                            <span class="ms-2 small text-muted">({{ $photographer->reviews_count }} review)</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Harga dan Detail -->
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div class="price-info">
                                                        <span class="h5 mb-0 text-danger">Rp{{ number_format($photographer->price, 0, ',', '.') }}</span>
                                                    </div>
                                                    <a href="{{ route('user.photographer.show', $photographer->id) }}"
                                                       class="btn btn-outline-danger rounded-pill">
                                                        Detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- Info Section -->
            <div class="info-section mt-5 p-4 bg-light rounded-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6">
                        <h3 class="mb-3">Cara Booking Fotografer</h3>
                        <ol class="photographer-steps">
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">1</div>
                                    <div>
                                        <h5 class="mb-1">Pilih Lapangan</h5>
                                        <p class="text-muted mb-0">
                                            <strong>Pilih lapangan terlebih dahulu</strong> untuk melihat fotografer yang tersedia.
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">2</div>
                                    <div>
                                        <h5 class="mb-1">Pilih Fotografer</h5>
                                        <p class="text-muted mb-0">Pilih paket fotografer yang sesuai dengan kebutuhan dan lapangan Anda.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">3</div>
                                    <div>
                                        <h5 class="mb-1">Tentukan Jadwal</h5>
                                        <p class="text-muted mb-0">Pilih tanggal dan jam yang Anda inginkan.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">4</div>
                                    <div>
                                        <h5 class="mb-1">Konfirmasi Pembayaran</h5>
                                        <p class="text-muted mb-0">Lakukan pembayaran melalui metode yang tersedia.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="step-number me-3">5</div>
                                    <div>
                                        <h5 class="mb-1">Dapatkan Foto</h5>
                                        <p class="text-muted mb-0">Fotografer akan hadir sesuai jadwal dan hasil foto akan dikirim melalui Google Drive.</p>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="{{ asset('assets/fotografer.png') }}" alt="Photographer Service"
                            class="img-fluid rounded-4 shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Existing styles... */

        /* TAMBAHAN BARU: Styles untuk cart-based filtering */
        .field-restricted {
            border-left: 4px solid #ffc107 !important;
        }

        .field-flexible {
            border-left: 4px solid #198754 !important;
        }

        .restriction-info {
            background: rgba(255, 193, 7, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .rating .fa-star {
            font-size: 0.85rem;
        }

        /* Alert dengan border yang lebih smooth */
        .alert {
            border: 1px solid rgba(0,0,0,0.08);
        }

        /* Badge improvements */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Empty state improvements */
        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-state i {
            opacity: 0.5;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            height: 220px;
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-content {
            color: white;
            text-align: center;
            width: 100%;
        }

        .hero-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .breadcrumb-wrapper {
            display: flex;
            justify-content: center;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            display: inline-flex;
            margin-bottom: 0;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .breadcrumb-item.active {
            color: white;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.6);
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

        /* Photographer Card Styles */
        .photographer-card .card-img-wrapper {
            height: 200px;
            overflow: hidden;
        }

        .photographer-card .card-img-top {
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .photographer-card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Features List */
        .features-list li {
            font-size: 0.9rem;
        }

        /* Photographer Steps */
        .photographer-steps {
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
            padding: 0.75rem 1rem;
            border-radius: 50px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
