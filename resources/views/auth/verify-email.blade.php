<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Alena Soccer</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #9E0620;
            --secondary-color: #2A2A2A;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f6f8fd 0%, #f1f4f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .verify-container {
            max-width: 900px;
            margin: auto;
        }

        .verify-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .verify-sidebar {
            background: linear-gradient(45deg, var(--primary-color), #c51b32);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .btn-danger {
            background: var(--primary-color);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: #7d051a;
            transform: translateY(-2px);
        }

        .floating-shape {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }

        .text-danger {
            color: var(--primary-color) !important;
        }

        .verify-icon {
            background-color: rgba(158, 6, 32, 0.1);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .alert {
            border-radius: 10px;
            padding: 16px;
        }

        @media (max-width: 768px) {
            .verify-sidebar {
                display: none;
            }
        }

        html,
        body {
            overflow-x: hidden;
        }
    </style>
</head>

<body>
    <!-- Background Shapes -->
    <div class="floating-shape" style="top: 10%; left: 5%;">
        <svg width="100" height="100" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="var(--primary-color)"/>
        </svg>
    </div>
    <div class="floating-shape" style="bottom: 10%; right: 5%;">
        <svg width="120" height="120" viewBox="0 0 100 100">
            <rect width="80" height="80" fill="var(--primary-color)"/>
        </svg>
    </div>

    <div class="container">
        <div class="verify-container">
            <!-- Logo -->
            <div class="text-center mb-4">
                <a href="/" class="text-decoration-none">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="fw-bold fs-3" style="color: black;">
                            Alena<span class="text-dark">
                                S<img src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                                width="30" height="30" class="" alt="Logo Alena Soccer">ccer
                            </span>
                        </span>
                    </div>
                </a>
            </div>

            <div class="card verify-card shadow-lg">
                <div class="row g-0">
                    <!-- Verify Sidebar -->
                    <div class="col-lg-5 verify-sidebar">
                        <h2 class="h3 fw-bold mb-4">Hampir Selesai!</h2>
                        <p class="mb-4 opacity-75">Verifikasi alamat email Anda untuk menyelesaikan pendaftaran dan akses semua fitur olahraga premium kami.</p>
                        <div class="d-flex gap-3 mb-4">
                            <div>
                                <h4 class="h2 fw-bold mb-0">500+</h4>
                                <p class="mb-0 opacity-75">Pemain Aktif</p>
                            </div>
                            <div class="border-start border-white border-opacity-25 ps-3">
                                <h4 class="h2 fw-bold mb-0">4.9</h4>
                                <p class="mb-0 opacity-75">Rating Pengguna</p>
                            </div>
                        </div>
                    </div>

                    <!-- Verify Content -->
                    <div class="col-lg-7">
                        <div class="card-body p-4 p-lg-5">
                            <div class="verify-icon">
                                <i class="fas fa-envelope-open-text text-danger fa-2x"></i>
                            </div>
                            <h1 class="h3 fw-bold mb-3 text-center">Verifikasi Alamat Email Anda</h1>
                            <p class="text-center text-muted mb-4">
                                Sebelum melanjutkan, silakan periksa email Anda untuk tautan verifikasi.
                                Jika Anda tidak menerima email, kami akan dengan senang hati mengirimkan yang baru.
                            </p>

                            @if (session('resent'))
                                <div class="alert alert-success mb-4" role="alert">
                                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('verification.send') }}" class="d-flex flex-column align-items-center">
                                @csrf
                                <button type="submit" class="btn btn-danger mb-4">
                                    Kirim Ulang Email Verifikasi
                                    <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </form>

                            <div class="text-center mt-3">
                                <p class="mb-0">
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                       class="text-muted text-decoration-none">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Kembali ke halaman login
                                    </a>
                                </p>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
