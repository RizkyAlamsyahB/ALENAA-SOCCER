@extends('layouts.app')
@section('content')
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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


 <!-- Main Content -->
 <div class="container">
    <!-- Intro Section -->
    <section class="intro-section">
        <div class="intro-content">
            <span class="section-tag">Benefits</span>
            <h2>Keuntungan Menjadi Member</h2>
            <p>Nikmati berbagai keuntungan eksklusif dengan bergabung sebagai member kami</p>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Jadwal Tetap</h3>
                        <p>Dapatkan slot jadwal tetap setiap minggu sesuai dengan pilihan Anda</p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Harga Spesial</h3>
                        <p>Nikmati potongan harga khusus dibandingkan dengan booking regular</p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Dokumentasi</h3>
                        <p>Layanan fotografer profesional untuk mengabadikan momen bermain Anda</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Membership Plans For Each Field -->
    @foreach ($fields as $field)
        <section class="membership-plans-section" id="membership-plans-{{ $field->id }}">
            <div class="section-header">
                <span class="section-tag">Packages</span>
                <h2>Paket Membership {{ $field->name }}</h2>
                <p>Pilih paket membership yang sesuai dengan kebutuhan Anda</p>
            </div>

            <div class="plans-grid">
                <!-- Bronze Plan -->
                <div class="plan-card">
                    <div class="plan-bg" style="background-image: url('/images/bg-card-bronze.jpg')">
                        <div class="plan-overlay bronze-overlay"></div>
                    </div>
                    <div class="plan-content">
                        <div class="plan-header">
                            <div class="plan-badge bronze">Bronze</div>
                                            @php
                                $bronzeMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'bronze')
                                    ->first();
                            @endphp
                            <div class="plan-price">
                                <div class="price">
                                    @if ($bronzeMembership)
                                        Rp {{ number_format($bronzeMembership->price, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($field->price * 3 * 4 * 0.9, 0, ',', '.') }}
                                    @endif
                                </div>
                                <div class="period">
                                    @if ($bronzeMembership)
                                        {{ $bronzeMembership->sessions_per_week }}x main/minggu
                                    @else
                                        3x main/minggu
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="plan-features">
                            @php
                                $bronzeMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'bronze')
                                    ->first();
                            @endphp
                            @if ($bronzeMembership)
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Jadwal tetap {{ $bronzeMembership->sessions_per_week }}x seminggu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Durasi {{ $bronzeMembership->session_duration }} jam per sesi</span>
                                </div>
                                @if($bronzeMembership->description)
                                    @foreach(explode("\n", $bronzeMembership->description) as $benefit)
                                        @if(trim($benefit))
                                            <div class="feature-item">
                                                <i class="fas fa-check"></i>
                                                <span>{{ trim($benefit) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Jadwal tetap 3x seminggu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Diskon 10% dari harga regular</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Durasi 1 jam per sesi</span>
                                </div>
                            @endif
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
                </div>

                <!-- Silver Plan -->
                <div class="plan-card featured">
                    <div class="popular-badge">Populer</div>
                    <div class="plan-bg" style="background-image: url('/images/bg-card-silver.jpg')">
                        <div class="plan-overlay silver-overlay"></div>
                    </div>
                    <div class="plan-content">
                        <div class="plan-header">
                            <div class="plan-badge silver">Silver</div>
                            <div class="plan-price">
                                <div class="price">Rp {{ number_format($field->price * 3 * 4 * 2 * 0.85, 0, ',', '.') }}
                                </div>
                                <div class="period">3x main/minggu</div>
                            </div>
                        </div>

                        <div class="plan-features">
                            @php
                                $silverMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'silver')
                                    ->first();
                            @endphp
                            @if ($silverMembership)
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Jadwal tetap {{ $silverMembership->sessions_per_week }}x seminggu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Durasi {{ $silverMembership->session_duration }} jam per sesi</span>
                                </div>
                                @if($silverMembership->photographer_duration > 0)
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Fotografer {{ $silverMembership->photographer_duration }} jam per sesi</span>
                                </div>
                                @endif
                                @if($silverMembership->description)
                                    @foreach(explode("\n", $silverMembership->description) as $benefit)
                                        @if(trim($benefit))
                                            <div class="feature-item">
                                                <i class="fas fa-check"></i>
                                                <span>{{ trim($benefit) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Jadwal tetap 3x seminggu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Diskon 15% dari harga regular</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Durasi 1 jam per sesi</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Fotografer 1x dalam sebulan</span>
                                </div>
                            @endif
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
                </div>

                <!-- Gold Plan -->
                <div class="plan-card">
                    <div class="plan-bg" style="background-image: url('/images/bg-card-gold.jpg')">
                        <div class="plan-overlay gold-overlay"></div>
                    </div>
                    <div class="plan-content">
                        <div class="plan-header">
                            <div class="plan-badge gold">Gold</div>
                                            @php
                                $goldMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'gold')
                                    ->first();
                            @endphp
                            <div class="plan-price">
                                <div class="price">
                                    @if ($goldMembership)
                                        Rp {{ number_format($goldMembership->price, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($field->price * 3 * 4 * 3 * 0.8, 0, ',', '.') }}
                                    @endif
                                </div>
                                <div class="period">
                                    @if ($goldMembership)
                                        {{ $goldMembership->sessions_per_week }}x main/minggu
                                    @else
                                        3x main/minggu
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="plan-features">
                            @php
                                $goldMembership = $memberships
                                    ->where('field_id', $field->id)
                                    ->where('type', 'gold')
                                    ->first();
                            @endphp
                            @if ($goldMembership)
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Jadwal tetap {{ $goldMembership->sessions_per_week }}x seminggu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Durasi {{ $goldMembership->session_duration }} jam per sesi</span>
                                </div>
                                @if($goldMembership->photographer_duration > 0)
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Fotografer {{ $goldMembership->photographer_duration }} jam per sesi</span>
                                </div>
                                @endif
                                @if($goldMembership->description)
                                    @foreach(explode("\n", $goldMembership->description) as $benefit)
                                        @if(trim($benefit))
                                            <div class="feature-item">
                                                <i class="fas fa-check"></i>
                                                <span>{{ trim($benefit) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Jadwal tetap 3x seminggu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Diskon 20% dari harga regular</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Durasi 1 jam per sesi</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Fotografer 2x dalam sebulan</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check"></i>
                                    <span>Minuman gratis setiap sesi</span>
                                </div>
                            @endif
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
            </div>
        </section>
    @endforeach

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="section-header">
            <span class="section-tag">Information</span>
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

            <div class="faq-item">
                <button class="faq-question collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseFive" aria-expanded="false">
                    <span>Berapa lama durasi membership yang harus diambil?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="collapseFive" class="faq-answer collapse" data-bs-parent="#membershipFAQ">
                    <div class="faq-content">
                        Durasi minimum membership adalah 1 bulan (4 minggu). Setelah periode tersebut, Anda dapat memperpanjang atau mengakhiri membership.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Siap untuk bergabung?</h2>
            <p>Pilih paket membership sekarang dan nikmati berbagai keuntungannya</p>
            <a href="#" class="btn-primary large">
                <span>Mulai Sekarang</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
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

        /* Content Wrapper */
        .content-wrapper {
            padding: 4rem 0;
        }

        /* Section Styling */
        section {
            margin-bottom: 5rem;
        }

        .section-tag {
            display: inline-block;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #d00f25;
            background: rgba(208, 15, 37, 0.1);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            margin-bottom: 1rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .section-header p {
            font-size: 1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Intro Section */
        .intro-section {
            margin-bottom: 5rem;
        }

        .intro-content {
            text-align: center;
            margin-bottom: 3rem;
        }

        .intro-content h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .intro-content p {
            font-size: 1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Benefits Grid */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .benefit-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
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
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        .benefit-content h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .benefit-content p {
            color: #6c757d;
            font-size: 0.95rem;
        }

        /* Membership Plans */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
        }

        .plan-card {
            position: relative;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .plan-card.featured {
            transform: scale(1.05);
            z-index: 2;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .plan-card.featured:hover {
            transform: translateY(-10px) scale(1.05);
        }

        .plan-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-position: center;
            background-size: cover;
            z-index: 1;
        }

        .plan-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .bronze-overlay {
            background: linear-gradient(135deg, rgba(167, 112, 68, 0.9), rgba(205, 127, 50, 0.8));
        }

        .silver-overlay {
            background: linear-gradient(135deg, rgba(123, 138, 139, 0.9), rgba(192, 192, 192, 0.8));
        }

        .gold-overlay {
            background: linear-gradient(135deg, rgba(181, 144, 60, 0.9), rgba(255, 215, 0, 0.8));
        }

        .plan-content {
            position: relative;
            z-index: 3;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .popular-badge {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: #d00f25;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            z-index: 5;
            box-shadow: 0 4px 15px rgba(208, 15, 37, 0.5);
        }

        .plan-header {
            margin-bottom: 2rem;
        }

        .plan-badge {
            display: inline-block;
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .plan-badge.bronze {
            background: rgba(205, 127, 50, 0.2);
            color: white;
        }

        .plan-badge.silver {
            background: rgba(192, 192, 192, 0.2);
            color: white;
        }

        .plan-badge.gold {
            background: rgba(255, 215, 0, 0.2);
            color: white;
        }

        .plan-price .price {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: white;
            line-height: 1.2;
        }

        .plan-price .period {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .plan-features {
            margin-bottom: 2rem;
            flex-grow: 1;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: white;
        }

        .feature-item i {
            color: white;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .feature-item span {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .plan-action {
            margin-top: auto;
        }

        /* FAQ Section */
        .faq-item {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }

        .faq-question {
            width: 100%;
            text-align: left;
            padding: 1.75rem;
            background: white;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.1rem;
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
            background: #f0f0f0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .faq-question:not(.collapsed) i {
            transform: rotate(180deg);
            color: white;
            background: #d00f25;
        }

        .faq-answer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .faq-content {
            padding: 1.5rem 1.75rem;
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.7;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #9e0620, #bb2d3b);
            padding: 4rem;
            border-radius: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: 4rem;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/images/pattern.svg');
            opacity: 0.1;
            z-index: 1;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-section h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Buttons */
        .btn-primary,
        .btn-secondary,
        .btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.85rem 1.75rem;
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
            box-shadow: 0 8px 15px rgba(208, 15, 37, 0.3);
        }

        .btn-primary:hover {
            background: #b00d1f;
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(208, 15, 37, 0.4);
        }

        .btn-primary.large {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            max-width: 250px;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
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
        @media (max-width: 1200px) {
            .plans-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }

            .plan-card.featured {
                transform: none;
            }

            .plan-card.featured:hover {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-header h2,
            .intro-content h2,
            .cta-section h2 {
                font-size: 2rem;
            }

            .benefits-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }

            .plan-content {
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 280px;
            }

            .hero-title {
                font-size: 2rem;
            }

            .section-header h2,
            .intro-content h2,
            .cta-section h2 {
                font-size: 1.8rem;
            }

            .plan-price .price {
                font-size: 1.8rem;
            }

            .cta-section {
                padding: 3rem 2rem;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                height: 250px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }

            .section-header h2,
            .intro-content h2,
            .cta-section h2 {
                font-size: 1.6rem;
            }

            .faq-question {
                font-size: 1rem;
                padding: 1.5rem;
            }

            .benefit-icon {
                width: 70px;
                height: 70px;
                font-size: 1.75rem;
                border-radius: 16px;
            }

            .plan-content {
                padding: 1.75rem;
            }

            .cta-section {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
