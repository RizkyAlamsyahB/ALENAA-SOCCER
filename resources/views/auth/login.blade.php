<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Alena Soccer</title>

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

        .login-container {
            max-width: 900px;
            margin: auto;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .login-sidebar {
            background: linear-gradient(45deg, var(--primary-color), #c51b32);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-control {
            border: 1.5px solid #e5e9f2;
            padding: 12px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(158, 6, 32, 0.1);
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

        .social-btn {
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #6c757d;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e9f2;
        }

        .divider span {
            padding: 0 1rem;
        }

        .floating-shape {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }

        .text-danger {
            color: var(--primary-color) !important;
        }

        @media (max-width: 768px) {
            .login-sidebar {
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
            <circle cx="50" cy="50" r="40" fill="var(--primary-color)" />
        </svg>
    </div>
    <div class="floating-shape" style="bottom: 10%; right: 5%;">
        <svg width="120" height="120" viewBox="0 0 100 100">
            <rect width="80" height="80" fill="var(--primary-color)" />
        </svg>
    </div>

    <div class="container">
        <div class="login-container">


            <div class="card login-card shadow-lg">
                <div class="row g-0">
                    <!-- Login Sidebar -->
                    <div class="col-lg-5 login-sidebar">
                        <h2 class="h3 fw-bold mb-4">Selamat Datang Kembali!</h2>
                        <p class="mb-4 opacity-75">Masuk untuk mengakses akun Anda dan nikmati fasilitas olahraga
                            premium kami.</p>
                        <div class="d-flex gap-3 mb-4">
                            <div>
                                <h4 class="h2 fw-bold mb-0">500+</h4>
                                <p class="mb-0 opacity-75">Pemain Bahagia</p>
                            </div>
                            <div class="border-start border-white border-opacity-25 ps-3">
                                <h4 class="h2 fw-bold mb-0">4.9</h4>
                                <p class="mb-0 opacity-75">Rating Pengguna</p>
                            </div>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <div class="col-lg-7">
                        <div class="card-body p-4 p-lg-5">
                            <h1 class="h3 fw-bold mb-4 text-center">Masuk ke Akun Anda</h1>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <!-- Email -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Alamat Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Masukkan email Anda" value="{{ old('email') }}" required
                                            autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Kata Sandi</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Masukkan kata sandi Anda" required
                                            autocomplete="current-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">Ingat saya</label>
                                    </div>
                                    <a href="/forgot-password" class="text-danger text-decoration-none">Lupa Kata
                                        Sandi?</a>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-danger w-100 mb-4">
                                    Masuk
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </form>

                            <!-- Divider -->
                            <div class="divider mb-4">
                                <span>atau lanjutkan dengan</span>
                            </div>

                            <!-- Social Login -->
                            <div class="d-flex gap-3 mb-4">
                                <button class="social-btn btn btn-outline-light flex-grow-1 bg-white">
                                    <img src="assets/icons8-google-48.png" width="24" alt="Google">
                                    <span>Google</span>
                                </button>
                                <button class="social-btn btn btn-outline-light flex-grow-1 bg-white">
                                    <img src="assets/icons8-facebook-48.png" width="24" alt="Facebook">
                                    <span>Facebook</span>
                                </button>
                            </div>

                            <!-- Register Link -->
                            <p class="text-center mb-0">
                                Belum punya akun?
                                <a href="/register" class="text-danger text-decoration-none fw-semibold">Buat Akun</a>
                            </p>
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
