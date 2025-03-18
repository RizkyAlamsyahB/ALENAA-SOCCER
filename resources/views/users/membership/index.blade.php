@extends('layouts.app')
@section('content')
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Membership Program</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i>
                                    Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Membership</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Intro Section -->
    <div class="intro-section">
        <div class="container">
            <div class="intro-content">
                <h2>Join Our Membership Program</h2>
                <p>Nikmati berbagai keuntungan dengan menjadi member Alena Soccer. Jadwal bermain teratur 3x seminggu dengan
                    harga lebih hemat dan fasilitas lengkap.</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container content-wrapper">
        <!-- Field Selection Section -->
        <section class="fields-section">
            <div class="section-header">
                <h2>Pilih Lapangan</h2>
                <p>Pilih lapangan dengan fasilitas yang sesuai dengan kebutuhan Anda</p>
            </div>

            <div class="fields-grid">
                @forelse($fields as $field)
                    <div class="field-card">
                        <div class="field-image">
                            @if ($field->image)
                                <img src="{{ Storage::url($field->image) }}" alt="{{ $field->name }}">
                            @else
                                <img src="{{ asset('images/default-field.jpg') }}" alt="{{ $field->name }}">
                            @endif
                            <div class="field-badges">
                                <div class="badge-item">
                                    <i class="fas fa-star"></i>
                                    <span>{{ $field->rating ?? '4.5' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="field-content">
                            <h3>{{ $field->name }}</h3>
                            <div class="field-meta">
                                <div class="field-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Sidoarjo, Indonesia</span>
                                </div>
                                <div class="field-price">
                                    <span class="price">Rp {{ number_format($field->price, 0, ',', '.') }}</span>
                                    <span class="period">/jam</span>
                                </div>
                            </div>
                            <a href="#membership-plans-{{ $field->id }}" class="btn-secondary">
                                <span>Lihat Paket Membership</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Belum Ada Lapangan</h3>
                        <p>Saat ini belum ada lapangan tersedia untuk program membership.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Membership Plans For Each Field -->
        @foreach ($fields as $field)
            <section class="membership-plans-section" id="membership-plans-{{ $field->id }}">
                <div class="section-header">
                    <h2>Paket Membership {{ $field->name }}</h2>
                    <p>Pilih paket membership yang sesuai dengan kebutuhan Anda</p>
                </div>

                <div class="plans-grid">
                    <!-- Bronze Plan -->
                    <div class="plan-card bronze">
                        <div class="plan-header">
                            <div class="plan-type">Bronze</div>
                            <div class="plan-price">
                                <div class="price">Rp {{ number_format($field->price * 3 * 4 * 0.9, 0, ',', '.') }}</div>
                                <div class="period">3x main/minggu</div>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li><i class="fas fa-check-circle"></i> <span>Main 1 jam tiap sesi</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Sewa bola gratis</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Galon air mineral</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Fotografer 1 jam</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Loker gratis</span></li>
                            </ul>
                        </div>
                        <div class="plan-action">
                            @php
                                $bronzeMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'bronze')
                                    ->first();
                            @endphp
                            @if ($bronzeMembership)
                                <a href="{{ route('user.membership.show', $bronzeMembership->id) }}" class="btn-primary">
                                    <span>Pilih Paket</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            @else
                                <button class="btn-disabled">
                                    <span>Tidak Tersedia</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Silver Plan -->
                    <div class="plan-card silver">
                        <div class="popular-badge">Populer</div>
                        <div class="plan-header">
                            <div class="plan-type">Silver</div>
                            <div class="plan-price">
                                <div class="price">Rp {{ number_format($field->price * 3 * 4 * 2 * 0.85, 0, ',', '.') }}
                                </div>
                                <div class="period">3x main/minggu</div>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li><i class="fas fa-check-circle"></i> <span>Main 2 jam tiap sesi</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Sewa bola gratis</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Galon air mineral</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Fotografer 2 jam</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Loker gratis</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Jersey latihan</span></li>
                            </ul>
                        </div>
                        <div class="plan-action">
                            @php
                                $silverMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'silver')
                                    ->first();
                            @endphp
                            @if ($silverMembership)
                                <a href="{{ route('user.membership.show', $silverMembership->id) }}" class="btn-primary">
                                    <span>Pilih Paket</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            @else
                                <button class="btn-disabled">
                                    <span>Tidak Tersedia</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Gold Plan -->
                    <div class="plan-card gold">
                        <div class="plan-header">
                            <div class="plan-type">Gold</div>
                            <div class="plan-price">
                                <div class="price">Rp {{ number_format($field->price * 3 * 4 * 3 * 0.8, 0, ',', '.') }}
                                </div>
                                <div class="period">3x main/minggu</div>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li><i class="fas fa-check-circle"></i> <span>Main 3 jam tiap sesi</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Sewa bola gratis</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Galon air mineral</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Fotografer 3 jam</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Loker gratis</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Jersey latihan premium</span></li>
                                <li><i class="fas fa-check-circle"></i> <span>Pelatih pribadi 1x/bulan</span></li>
                            </ul>
                        </div>
                        <div class="plan-action">
                            @php
                                $goldMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'gold')
                                    ->first();
                            @endphp
                            @if ($goldMembership)
                                <a href="{{ route('user.membership.show', $goldMembership->id) }}" class="btn-primary">
                                    <span>Pilih Paket</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            @else
                                <button class="btn-disabled">
                                    <span>Tidak Tersedia</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endforeach

        <!-- Benefits Section -->
        <section class="benefits-section">
            <div class="section-header">
                <h2>Keuntungan Menjadi Member</h2>
                <p>Nikmati berbagai keuntungan dengan bergabung program membership</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Hemat Biaya</h3>
                        <p>Nikmati potongan harga hingga 20% dibandingkan booking reguler.</p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Jadwal Terjamin</h3>
                        <p>Dapatkan slot permainan tetap 3x seminggu tanpa perlu rebutan jadwal.</p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Fasilitas Ekslusif</h3>
                        <p>Akses ke fasilitas tambahan seperti fotografer, bola, dan air mineral gratis.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="section-header">
                <h2>Pertanyaan Umum</h2>
                <p>Temukan jawaban dari pertanyaan yang sering diajukan</p>
            </div>

            <div class="faq-accordion" id="membershipFAQ">
                <div class="faq-item">
                    <button class="faq-question collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false">
                        <span>Bagaimana cara mendaftar membership?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="collapseOne" class="faq-answer collapse" data-bs-parent="#membershipFAQ">
                        <div class="faq-content">
                            Pilih lapangan dan paket membership yang diinginkan, lalu pilih 3 jadwal permainan tetap dalam
                            satu minggu. Setelah itu, lakukan pembayaran dan nikmati fasilitas member.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false">
                        <span>Bagaimana sistem pembayaran membership?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="collapseTwo" class="faq-answer collapse" data-bs-parent="#membershipFAQ">
                        <div class="faq-content">
                            Pembayaran membership dilakukan setiap minggu. Invoice akan dikirimkan pada jadwal main kedua
                            dan harus dibayar sebelum jadwal main ketiga.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false">
                        <span>Apakah jadwal bisa diubah setelah terdaftar?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="collapseThree" class="faq-answer collapse" data-bs-parent="#membershipFAQ">
                        <div class="faq-content">
                            Jadwal yang sudah dipilih tidak dapat diubah selama periode membership berjalan. Pastikan untuk
                            memilih jadwal yang sesuai dengan ketersediaan Anda.
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFour" aria-expanded="false">
                        <span>Apa yang terjadi jika saya tidak hadir pada jadwal yang ditentukan?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="collapseFour" class="faq-answer collapse" data-bs-parent="#membershipFAQ">
                        <div class="faq-content">
                            Jadwal yang terlewat tidak dapat diganti atau dikompensasi. Pembayaran tetap harus dilakukan
                            sesuai jadwal yang telah disepakati.
                        </div>
                    </div>
                </div>
            </div>
        </section>
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

        /* Intro Section */
        .intro-section {
            background: white;
            padding: 3rem 0;
            text-align: center;
        }

        .intro-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .intro-content h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 1rem;
        }

        .intro-content p {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.6;
        }

        /* Content Wrapper */
        .content-wrapper {
            padding: 3rem 0;
        }

        /* Section Styling */
        section {
            margin-bottom: 4rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .section-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.75rem;
        }

        .section-header p {
            font-size: 1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Field Cards */
        .fields-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .field-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .field-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .field-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .field-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .field-card:hover .field-image img {
            transform: scale(1.05);
        }

        .field-badges {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .badge-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border-radius: 50px;
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
        }

        .field-content {
            padding: 1.5rem;
        }

        .field-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #212529;
        }

        .field-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .field-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .field-location i {
            color: #d00f25;
        }

        .field-price {
            text-align: right;
        }

        .field-price .price {
            font-weight: 700;
            color: #d00f25;
            font-size: 1.1rem;
        }

        .field-price .period {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .empty-icon {
            font-size: 3rem;
            color: #d00f25;
            opacity: 0.5;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #212529;
        }

        .empty-state p {
            color: #6c757d;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Membership Plans */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .plan-card {
            position: relative;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .plan-card.bronze .plan-header {
            background: linear-gradient(135deg, #A77044, #CD7F32);
        }

        .plan-card.silver .plan-header {
            background: linear-gradient(135deg, #7B8A8B, #C0C0C0);
        }

        .plan-card.gold .plan-header {
            background: linear-gradient(135deg, #B5903C, #FFD700);
        }

        .popular-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #d00f25;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            z-index: 1;
        }

        .plan-header {
            padding: 2rem;
            color: white;
            text-align: center;
        }

        .plan-type {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .plan-price .price {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .plan-price .period {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .plan-features {
            padding: 2rem;
        }

        .plan-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .plan-features li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }

        .plan-features li:last-child {
            border-bottom: none;
        }

        .plan-features i {
            color: #2e7d32;
            font-size: 1.1rem;
            margin-top: 0.1rem;
        }

        .plan-features span {
            font-size: 0.95rem;
            color: #495057;
        }

        .plan-action {
            padding: 0 2rem 2rem;
            text-align: center;
        }

        /* Benefits Section */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .benefit-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .benefit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .benefit-icon {
            width: 80px;
            height: 80px;
            background: rgba(208, 15, 37, 0.1);
            color: #d00f25;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .benefit-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.75rem;
        }

        .benefit-content p {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* FAQ Section */
        .faq-item {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
        }

        .faq-question {
            width: 100%;
            text-align: left;
            padding: 1.5rem;
            background: white;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1rem;
            font-weight: 600;
            color: #212529;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background: #f8f9fa;
        }

        .faq-question:not(.collapsed) {
            background: #f8f9fa;
            color: #d00f25;
        }

        .faq-question i {
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .faq-question:not(.collapsed) i {
            transform: rotate(180deg);
            color: #d00f25;
        }

        .faq-answer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .faq-content {
            padding: 1.5rem;
            color: #6c757d;
            font-size: 0.95rem;
        }

        /* Buttons */
        .btn-primary,
        .btn-secondary,
        .btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.95rem;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary {
            background: #d00f25;
            color: white;
        }

        .btn-primary:hover {
            background: #b00d1f;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(208, 15, 37, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #212529;
            border: 1px solid #dee2e6;
        }

        .btn-secondary:hover {
            background: #dee2e6;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {

            .plans-grid,
            .benefits-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 180px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .intro-content h2 {
                font-size: 1.8rem;
            }

            .intro-content p {
                font-size: 1rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .fields-grid,
            .plans-grid,
            .benefits-grid {
                grid-template-columns: 1fr;
            }

            .plan-header,
            .plan-features,
            .plan-action {
                padding: 1.5rem;
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

            .field-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .field-price {
                text-align: left;
            }

            .benefit-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
    @include('layouts.footer')
    <link rel="stylesheet" href="{{ asset('css/users/welcome.css') }}">

@endsection
