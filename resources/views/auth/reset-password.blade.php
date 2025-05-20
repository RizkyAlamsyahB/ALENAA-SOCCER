<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Alena Soccer</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .reset-sidebar {
            background: linear-gradient(45deg, var(--primary-color), #c51b32);
            color: white;
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

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            padding: 5px;
        }

        .btn-submit {
            background: var(--primary-color);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #7d051a;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(158, 6, 32, 0.3);
        }

        .password-requirements {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .password-requirements li i {
            margin-right: 0.5rem;
            font-size: 0.75rem;
        }

        .requirement-met {
            color: #198754 !important;
        }

        @media (max-width: 768px) {
            .reset-sidebar {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Logo -->
        <div class="text-center mb-4">
            <a href="/" class="text-decoration-none">
                <div class="d-flex align-items-center justify-content-center">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                        width="50" height="50" class="me-2" alt="SportVue Logo">
                    <span class="fw-bold fs-3" style="color: var(--primary-color);">Sport<span class="text-dark">Vue</span></span>
                </div>
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card reset-card shadow-lg">
                    <div class="row g-0">
                        <!-- Form Section -->
                        <div class="col-md-7">
                            <div class="card-body p-4 p-lg-5">
                                <div class="text-center mb-4">
                                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                        <i class="fas fa-key text-danger fa-2x"></i>
                                    </div>
                                    <h2 class="h3 fw-bold">Reset Kata Sandi</h2>
                                    <p class="text-muted">Buat kata sandi baru yang kuat untuk akun Anda</p>
                                </div>

                                <form method="POST" action="{{ route('password.store') }}" class="needs-validation" novalidate>
                                    @csrf
                                    <!-- Hidden Token -->
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                    <!-- Email -->
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold">Alamat Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-0 bg-light">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                            <input type="email"
                                                id="email"
                                                name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $request->email) }}"
                                                required
                                                readonly
                                            >
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-semibold">Kata Sandi Baru</label>
                                        <div class="position-relative">
                                            <div class="input-group">
                                                <span class="input-group-text border-0 bg-light">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                                <input type="password"
                                                    id="password"
                                                    name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    required
                                                >
                                                <span class="password-toggle" onclick="togglePassword('password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Kata Sandi</label>
                                        <div class="position-relative">
                                            <div class="input-group">
                                                <span class="input-group-text border-0 bg-light">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                                <input type="password"
                                                    id="password_confirmation"
                                                    name="password_confirmation"
                                                    class="form-control"
                                                    required
                                                >
                                                <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-submit text-white w-100 mb-4">
                                        Reset Kata Sandi
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Info Sidebar -->
                        <div class="col-md-5 reset-sidebar p-4 p-lg-5 d-flex flex-column justify-content-center">
                            <h3 class="h4 text-white mb-4">Persyaratan Kata Sandi</h3>
                            <ul class="password-requirements mb-4" id="passwordRequirements">
                                <li><i class="fas fa-circle"></i>Minimal 8 karakter</li>
                                <li><i class="fas fa-circle"></i>Mengandung huruf kapital</li>
                                <li><i class="fas fa-circle"></i>Mengandung huruf kecil</li>
                                <li><i class="fas fa-circle"></i>Mengandung angka</li>
                                <li><i class="fas fa-circle"></i>Mengandung karakter khusus</li>
                            </ul>

                            <div class="mt-auto">
                                <div class="bg-white bg-opacity-10 rounded-3 p-3">
                                    <h4 class="h6 mb-2">Butuh Bantuan?</h4>
                                    <p class="mb-0 small">Hubungi tim dukungan kami di
                                        <a href="mailto:support@sportvue.com" class="text-white">support@sportvue.com</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Password requirements checker
        const password = document.getElementById('password');
        const requirements = document.getElementById('passwordRequirements').getElementsByTagName('li');

        password.addEventListener('input', function() {
            const value = this.value;

            // Length check
            if (value.length >= 8) {
                requirements[0].classList.add('requirement-met');
                requirements[0].querySelector('i').classList.replace('fa-circle', 'fa-check-circle');
            } else {
                requirements[0].classList.remove('requirement-met');
                requirements[0].querySelector('i').classList.replace('fa-check-circle', 'fa-circle');
            }

            // Uppercase check
            if (/[A-Z]/.test(value)) {
                requirements[1].classList.add('requirement-met');
                requirements[1].querySelector('i').classList.replace('fa-circle', 'fa-check-circle');
            } else {
                requirements[1].classList.remove('requirement-met');
                requirements[1].querySelector('i').classList.replace('fa-check-circle', 'fa-circle');
            }

            // Lowercase check
            if (/[a-z]/.test(value)) {
                requirements[2].classList.add('requirement-met');
                requirements[2].querySelector('i').classList.replace('fa-circle', 'fa-check-circle');
            } else {
                requirements[2].classList.remove('requirement-met');
                requirements[2].querySelector('i').classList.replace('fa-check-circle', 'fa-circle');
            }

            // Number check
            if (/\d/.test(value)) {
                requirements[3].classList.add('requirement-met');
                requirements[3].querySelector('i').classList.replace('fa-circle', 'fa-check-circle');
            } else {
                requirements[3].classList.remove('requirement-met');
                requirements[3].querySelector('i').classList.replace('fa-check-circle', 'fa-circle');
            }

            // Special character check
            if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
                requirements[4].classList.add('requirement-met');
                requirements[4].querySelector('i').classList.replace('fa-circle', 'fa-check-circle');
            } else {
                requirements[4].classList.remove('requirement-met');
                requirements[4].querySelector('i').classList.replace('fa-check-circle', 'fa-circle');
            }
        });

        // Form submission handling
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Memproses Reset Kata Sandi...
            `;
        });
    </script>
</body>
</html>
