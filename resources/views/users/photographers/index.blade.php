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

            <!-- Filter & Sort Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('user.photographer.index') }}" method="GET" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="search-input">
                                    <i
                                        class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                    <input type="text" name="search" class="form-control ps-5"
                                        placeholder="Cari jasa fotografer..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="latest"
                                        {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru
                                    </option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga
                                        Terendah</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga
                                        Tertinggi</option>
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
                        {{ $photographers->count() }} paket ditemukan
                    </span>
                </div>
            </div>

            <!-- Package Grid -->
            <div class="row g-4">
                @if ($photographers->isEmpty())
                    <div class="row">
                        <div class="col-12 text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                <h4>Tidak Ada Paket Fotografer Ditemukan</h4>
                                <p class="text-muted">Maaf, tidak ada paket fotografer yang sesuai dengan kriteria pencarian
                                    Anda.</p>
                                <a href="{{ route('user.photographer.index') }}"
                                    class="btn btn-outline-danger rounded-pill mt-3">
                                    <i class="fas fa-sync-alt me-2"></i> Reset Filter
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Loop berdasarkan jenis paket -->
                    @foreach (['exclusive', 'favorite', 'plus'] as $packageType)
                        <div class="mt-5 mb-4">
                            <h3 class="section-title">Paket {{ ucfirst($packageType) }}</h3>
                        </div>

                        <div class="row g-4">
                            @forelse($photographersByType->get($packageType, collect()) as $photographer)
                                <!-- Photographer Card -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="card photographer-card border-0 shadow-sm rounded-4 h-100">
                                        <!-- Card content -->
                                        <div class="card-body p-3">
                                            <h5 class="card-title mb-2">{{ $photographer->name }}</h5>
                                            <p class="card-text mb-2">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                <span>Lapangan: {{ $photographer->assigned_field }}</span>
                                            </p>
                                            <!-- Harga dan Detail -->
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div class="price-info">
                                                    <span
                                                        class="h5 mb-0 text-danger">Rp{{ number_format($photographer->price, 0, ',', '.') }}</span>
                                                </div>
                                                <a href="{{ route('user.photographer.show', $photographer->id) }}"
                                                    class="btn btn-outline-danger rounded-pill">Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info">Tidak ada paket {{ ucfirst($packageType) }} tersedia.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Pagination -->


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
                                        <h5 class="mb-1">Pilih Paket</h5>
                                        <p class="text-muted mb-0">Pilih paket fotografer yang sesuai dengan kebutuhan
                                            Anda.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">2</div>
                                    <div>
                                        <h5 class="mb-1">Tentukan Jadwal</h5>
                                        <p class="text-muted mb-0">Pilih tanggal dan jam yang Anda inginkan.</p>
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
                                        <h5 class="mb-1">Dapatkan Foto</h5>
                                        <p class="text-muted mb-0">Fotografer akan hadir sesuai jadwal dan hasil foto akan
                                            dikirim melalui Google Drive.</p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
