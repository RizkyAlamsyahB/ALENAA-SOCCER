<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Alena Soccer</title>

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

        .register-container {
            max-width: 1000px;
            margin: auto;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .register-sidebar {
            background: linear-gradient(45deg, var(--primary-color), #c51b32);
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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

        .floating-shape {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }

        .text-danger {
            color: var(--primary-color) !important;
        }

        .benefits-list {
            list-style: none;
            padding: 0;
        }

        .benefits-list li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .benefits-list li i {
            margin-right: 10px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .register-sidebar {
                display: none;
            }
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .password-requirements i {
            font-size: 0.75rem;
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
        <div class="register-container">


            <div class="card register-card shadow-lg">
                <div class="row g-0">
                    <!-- Register Form -->
                    <div class="col-lg-7">
                        <div class="card-body p-4 p-lg-5">
                            <h1 class="h3 fw-bold mb-4 text-center">Buat Akun Anda</h1>

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- Full Name -->
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input id="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" name="name"
                                            value="{{ old('name') }}" placeholder="Masukkan nama lengkap Anda"
                                            required autocomplete="name" autofocus>
                                    </div>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-4">
                                    <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input id="phone" type="text"
                                            class="form-control @error('phone') is-invalid @enderror" name="phone"
                                            value="{{ old('phone') }}" placeholder="Masukkan nomor telepon Anda"
                                            required autocomplete="phone">
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>


                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Alamat Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" placeholder="Masukkan email Anda" required
                                            autocomplete="email">
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">Kata Sandi</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            placeholder="Buat kata sandi" required autocomplete="new-password">
                                    </div>
                                    <div class="password-requirements mt-2">
                                        <div><i id="min-char-check" class="fas fa-times-circle text-danger me-1"></i>
                                            Minimal 8 karakter</div>
                                        <div><i id="symbol-check" class="fas fa-times-circle text-danger me-1"></i>
                                            Sertakan angka & simbol</div>
                                    </div>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>


                                <!-- Confirm Password -->
                                <div class="mb-4">
                                    <label for="password-confirm" class="form-label fw-semibold">Konfirmasi Kata
                                        Sandi</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" placeholder="Konfirmasi kata sandi Anda"
                                            required autocomplete="new-password">
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const passwordInput = document.getElementById("password");
                                        const minCharCheck = document.getElementById("min-char-check");
                                        const symbolCheck = document.getElementById("symbol-check");

                                        passwordInput.addEventListener("input", function() {
                                            const value = passwordInput.value;

                                            // Cek panjang minimal 8 karakter
                                            if (value.length >= 8) {
                                                minCharCheck.classList.remove("text-danger", "fa-times-circle");
                                                minCharCheck.classList.add("text-success", "fa-check-circle");
                                            } else {
                                                minCharCheck.classList.remove("text-success", "fa-check-circle");
                                                minCharCheck.classList.add("text-danger", "fa-times-circle");
                                            }

                                            // Cek apakah ada angka dan simbol
                                            const hasNumber = /\d/.test(value);
                                            const hasSymbol = /[^A-Za-z0-9]/.test(value); // Ini akan cek semua karakter non-alfanumerik
                                            if (hasNumber && hasSymbol) {
                                                symbolCheck.classList.remove("text-danger", "fa-times-circle");
                                                symbolCheck.classList.add("text-success", "fa-check-circle");
                                            } else {
                                                symbolCheck.classList.remove("text-success", "fa-check-circle");
                                                symbolCheck.classList.add("text-danger", "fa-times-circle");
                                            }
                                        });
                                    });
                                </script>


                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-danger w-100 mb-4">
                                    Buat Akun
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>

                                <!-- Login Link -->
                                <p class="text-center mb-0">
                                    Sudah punya akun?
                                    <a href="{{ route('login') }}"
                                        class="text-danger text-decoration-none fw-semibold">Masuk</a>
                                </p>
                            </form>
                        </div>
                    </div>

                    <!-- Register Sidebar -->
                    <div class="col-lg-5 register-sidebar">
                        <div>
                            <h2 class="h3 fw-bold mb-4">Bergabunglah dengan Komunitas Kami!</h2>
                            <p class="mb-4 opacity-75">Dapatkan akses ke fitur premium dan bergabunglah dengan
                                komunitas penggemar olahraga kami yang terus berkembang.</p>

                            <ul class="benefits-list mb-4">
                                <li>
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Sistem pemesanan yang mudah</span>
                                </li>
                                <li>
                                    <i class="fas fa-percentage"></i>
                                    <span>Diskon eksklusif anggota</span>
                                </li>
                                <li>
                                    <i class="fas fa-users"></i>
                                    <span>Bergabung dengan komunitas olahraga</span>
                                </li>
                                <li>
                                    <i class="fas fa-trophy"></i>
                                    <span>Berpartisipasi dalam turnamen</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto">
                            <div class="border-top border-white border-opacity-25 pt-4">
                                <div class="d-flex gap-3">
                                    <div>
                                        <h4 class="h2 fw-bold mb-0">500+</h4>
                                        <p class="mb-0 opacity-75">Anggota Aktif</p>
                                    </div>
                                    <div class="border-start border-white border-opacity-25 ps-3">
                                        <h4 class="h2 fw-bold mb-0">4.9</h4>
                                        <p class="mb-0 opacity-75">Rating Pengguna</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
