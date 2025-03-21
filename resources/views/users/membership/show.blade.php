@extends('layouts.app')
@section('content')
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">{{ $membership->name }}</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i>
                                    Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user.membership.index') }}">Membership</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $membership->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container content-wrapper">
        <div class="row g-4">
            <!-- Left Column: Membership Details -->
            <div class="col-lg-8">
                <!-- Top Card: Membership Overview -->
                <div class="card membership-overview-card">
                    <div class="card-body">
                        <div class="membership-header">
                            <div class="membership-info">
                                <div class="membership-badges">
                                    <div class="badge-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $field->name }}</span>
                                    </div>
                                    <div class="badge-item">
                                        <i
                                            class="fas fa-trophy {{ $membership->type == 'bronze' ? 'text-bronze' : ($membership->type == 'silver' ? 'text-silver' : 'text-gold') }}"></i>
                                        <span>{{ ucfirst($membership->type) }} Package</span>
                                    </div>
                                </div>
                            </div>
                            <div class="membership-price">
                                <div class="price">Rp {{ number_format($membership->price, 0, ',', '.') }}</div>
                                <div class="price-period">per minggu</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Field Image Gallery -->
                <div class="card gallery-card">
                    <div class="field-gallery">
                        @if ($field->image)
                            <img src="{{ Storage::url($field->image) }}" alt="{{ $field->name }}" class="field-image">
                        @else
                            <img src="{{ asset('images/default-field.jpg') }}" alt="{{ $field->name }}"
                                class="field-image">
                        @endif
                        <div class="image-overlay">
                            <div class="field-type-badge">
                                <i class="fas fa-volleyball-ball"></i>
                                <span>{{ $field->type }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership Description -->
                <div class="card content-card">
                    <div class="card-header">
                        <h3>Deskripsi Membership</h3>
                    </div>
                    <div class="card-body">
                        <p class="description-text">
                            {{ $membership->description ?? 'Nikmati keuntungan menjadi member Alena Soccer dengan jadwal main tetap 3x seminggu. Paket ini memberikan Anda akses ke lapangan premium dengan berbagai fasilitas menarik.' }}
                        </p>

                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Durasi Sesi</h4>
                                    <p>{{ $membership->session_duration }} jam per sesi</p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Permainan per Minggu</h4>
                                    <p>{{ $membership->sessions_per_week }} sesi</p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon {{ $membership->includes_ball ? 'active' : 'inactive' }}">
                                    <i class="fas fa-futbol"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Sewa Bola Gratis</h4>
                                    <p>{{ $membership->includes_ball ? 'Tersedia' : 'Tidak tersedia' }}</p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon {{ $membership->includes_water ? 'active' : 'inactive' }}">
                                    <i class="fas fa-tint"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Air Mineral Galon</h4>
                                    <p>{{ $membership->includes_water ? 'Tersedia' : 'Tidak tersedia' }}</p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon {{ $membership->includes_photographer ? 'active' : 'inactive' }}">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Jasa Fotografer</h4>
                                    <p>{{ $membership->includes_photographer ? $membership->photographer_duration . ' jam' : 'Tidak tersedia' }}
                                    </p>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon active">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Loker Pribadi</h4>
                                    <p>Tersedia</p>
                                </div>
                            </div>

                            @if ($membership->type == 'silver' || $membership->type == 'gold')
                                <div class="feature-card">
                                    <div class="feature-icon active">
                                        <i class="fas fa-tshirt"></i>
                                    </div>
                                    <div class="feature-content">
                                        <h4>Jersey Latihan</h4>
                                        <p>{{ $membership->type == 'gold' ? 'Premium' : 'Standar' }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($membership->type == 'gold')
                                <div class="feature-card">
                                    <div class="feature-icon active">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="feature-content">
                                        <h4>Pelatih Pribadi</h4>
                                        <p>1x per bulan</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Field Information -->
                <div class="card content-card">
                    <div class="card-header">
                        <h3>Informasi Lapangan</h3>
                    </div>
                    <div class="card-body">
                        <div class="field-info-grid">
                            <div class="field-info-card">
                                <div class="info-icon">
                                    <i class="fas fa-ruler"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Ukuran</h4>
                                    <p>25 x 15m</p>
                                </div>
                            </div>

                            <div class="field-info-card">
                                <div class="info-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Kapasitas</h4>
                                    <p>5v5 Players</p>
                                </div>
                            </div>

                            <div class="field-info-card">
                                <div class="info-icon">
                                    <i class="fas fa-volleyball-ball"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Tipe</h4>
                                    <p>{{ $field->type }}</p>
                                </div>
                            </div>

                            <div class="field-info-card">
                                <div class="info-icon">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Lokasi</h4>
                                    <p>Sidoarjo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="card content-card">
                    <div class="card-header">
                        <h3>Syarat dan Ketentuan</h3>
                    </div>
                    <div class="card-body">
                        <ul class="terms-list">
                            <li>
                                <div class="terms-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="terms-content">Jadwal permainan adalah tetap dan tidak dapat diubah selama
                                    periode membership</div>
                            </li>
                            <li>
                                <div class="terms-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="terms-content">Invoice akan dikirim pada jadwal main kedua dengan tenggat
                                    sampai jadwal main ketiga</div>
                            </li>
                            <li>
                                <div class="terms-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="terms-content">Jadwal permainan yang terlewat tidak dapat diganti atau
                                    dikompensasi</div>
                            </li>
                            <li>
                                <div class="terms-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="terms-content">Membership dapat dibatalkan dengan pemberitahuan minimal 1
                                    minggu sebelumnya</div>
                            </li>
                            <li>
                                <div class="terms-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="terms-content">Alena Soccer berhak mengubah syarat dan ketentuan dengan
                                    pemberitahuan terlebih dahulu</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Action Card -->
            <div class="col-lg-4">
                <div class="card action-card">
                    <div class="card-header">
                        <h3>Pilih Jadwal Membership</h3>
                    </div>
                    <div class="card-body">
                        <div class="price-details">
                            <div class="price-row">
                                <div class="price-label">Harga per Minggu</div>
                                <div class="price-value">Rp {{ number_format($membership->price, 0, ',', '.') }}</div>
                            </div>
                            <div class="price-row">
                                <div class="price-label">Durasi Sesi</div>
                                <div class="price-value">{{ $membership->session_duration }} jam</div>
                            </div>
                            <div class="price-row">
                                <div class="price-label">Jumlah Sesi per Minggu</div>
                                <div class="price-value">{{ $membership->sessions_per_week }} sesi</div>
                            </div>
                            <div class="price-divider"></div>
                            <div class="price-row total">
                                <div class="price-label">Total Harga</div>
                                <div class="price-value">Rp {{ number_format($membership->price, 0, ',', '.') }}</div>
                            </div>
                            <div class="price-period-note">per minggu</div>
                        </div>

                        <div class="info-alert">
                            <div class="info-alert-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="info-alert-content">
                                <h4>Informasi Penting</h4>
                                <p>Pilih 3 jadwal permainan dalam satu minggu yang sama. Jadwal ini akan menjadi jadwal
                                    tetap selama masa membership berlangsung.</p>
                            </div>
                        </div>

                        <a href="{{ route('user.membership.select.schedule', $membership->id) }}" class="btn-primary">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Pilih Jadwal</span>
                        </a>

                        <div class="support-contact">
                            <div class="support-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="support-text">
                                <p>Butuh bantuan? Hubungi kami di</p>
                                <a href="tel:+6285123456789">085123456789</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #d00f25 0%, #9e0620 100%);
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

        /* Content Wrapper */
        .content-wrapper {
            margin-top: -50px;
            position: relative;
            z-index: 1;
            padding-bottom: 3rem;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: white;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Membership Overview Card */
        .membership-overview-card {
            background: white;
            border-radius: 16px;
        }

        .membership-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .membership-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .badge-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            background: #f8f9fa;
            font-size: 0.9rem;
            color: #495057;
            transition: all 0.3s ease;
        }

        .badge-item:hover {
            background: #f0f0f0;
        }

        .badge-item i {
            color: #d00f25;
        }

        .badge-item i.text-bronze {
            color: #CD7F32;
        }

        .badge-item i.text-silver {
            color: #C0C0C0;
        }

        .badge-item i.text-gold {
            color: #FFD700;
        }

        .membership-price {
            text-align: right;
        }

        .membership-price .price {
            font-size: 1.75rem;
            font-weight: 700;
            color: #d00f25;
            line-height: 1.2;
        }

        .membership-price .price-period {
            font-size: 0.85rem;
            color: #6c757d;
        }

        /* Gallery Card */
        .gallery-card {
            padding: 0;
            overflow: hidden;
        }

        .field-gallery {
            position: relative;
            overflow: hidden;
        }

        .field-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-card:hover .field-image {
            transform: scale(1.05);
        }

        .image-overlay {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
        }

        .field-type-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border-radius: 50px;
            font-size: 0.9rem;
        }

        /* Content Cards */
        .content-card {
            border-radius: 16px;
        }

        .description-text {
            font-size: 1rem;
            line-height: 1.7;
            color: #4a5568;
            margin-bottom: 2rem;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.25rem;
        }

        .feature-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-3px);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: #f0f0f0;
            color: #6c757d;
            flex-shrink: 0;
        }

        .feature-icon.active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .feature-icon.inactive {
            background: #f5f5f5;
            color: #9e9e9e;
        }

        .feature-content h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #212529;
        }

        .feature-content p {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Field Info Grid */
        .field-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
        }

        .field-info-card {
            text-align: center;
            padding: 1.5rem 1rem;
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .field-info-card:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-3px);
        }

        .info-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: rgba(208, 15, 37, 0.1);
            color: #d00f25;
        }

        .info-content h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            color: #212529;
        }

        .info-content p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Terms List */
        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .terms-list li {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .terms-list li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .terms-icon {
            color: #d00f25;
            flex-shrink: 0;
            padding-top: 0.25rem;
        }

        .terms-content {
            color: #4a5568;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Action Card */
        .action-card {
            position: sticky;
            top: 2rem;
        }

        .price-details {
            margin-bottom: 1.5rem;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .price-label {
            color: #6c757d;
        }

        .price-value {
            font-weight: 500;
            color: #212529;
        }

        .price-row.total {
            font-weight: 700;
            font-size: 1.1rem;
            color: #212529;
        }

        .price-row.total .price-value {
            color: #d00f25;
        }

        .price-divider {
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
            margin: 1rem 0;
        }

        .price-period-note {
            text-align: right;
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: -0.5rem;
        }

        .info-alert {
            display: flex;
            padding: 1.25rem;
            background: #e8f4ff;
            border-radius: 12px;
            margin: 1.5rem 0;
            border-left: 4px solid #2196f3;
        }

        .info-alert-icon {
            font-size: 1.5rem;
            color: #2196f3;
            margin-right: 1rem;
        }

        .info-alert-content h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #0d47a1;
        }

        .info-alert-content p {
            font-size: 0.9rem;
            margin-bottom: 0;
            color: #0d47a1;
            opacity: 0.8;
        }

        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: #d00f25;
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background: #b00d1f;
            box-shadow: 0 8px 15px rgba(208, 15, 37, 0.3);
            transform: translateY(-2px);
        }

        .support-contact {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .support-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: #e9ecef;
            color: #d00f25;
            flex-shrink: 0;
        }

        .support-text {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .support-text p {
            margin-bottom: 0.25rem;
        }

        .support-text a {
            color: #d00f25;
            font-weight: 500;
            text-decoration: none;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .action-card {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 180px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .content-wrapper {
                margin-top: -30px;
            }

            .membership-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .membership-price {
                text-align: left;
                width: 100%;
            }

            .features-grid,
            .field-info-grid {
                grid-template-columns: 1fr;
            }

            .field-image {
                height: 250px;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }

            .card-header {
                padding: 1.25rem;
            }

            .card-body {
                padding: 1.25rem;
            }

            .feature-card,
            .field-info-card {
                padding: 1rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
@endsection
