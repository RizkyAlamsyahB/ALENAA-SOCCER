<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SportVue</title>

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
            <circle cx="50" cy="50" r="40" fill="var(--primary-color)"/>
        </svg>
    </div>
    <div class="floating-shape" style="bottom: 10%; right: 5%;">
        <svg width="120" height="120" viewBox="0 0 100 100">
            <rect width="80" height="80" fill="var(--primary-color)"/>
        </svg>
    </div>

    <div class="container">
        <div class="register-container">
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

            <div class="card register-card shadow-lg">
                <div class="row g-0">
                    <!-- Register Form -->
                    <div class="col-lg-7">
                        <div class="card-body p-4 p-lg-5">
                            <h1 class="h3 fw-bold mb-4 text-center">Create Your Account</h1>

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- Full Name -->
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autocomplete="name" autofocus>
                                    </div>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" placeholder="Enter your email" required autocomplete="email">
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                            name="password" placeholder="Create a password" required autocomplete="new-password">
                                    </div>
                                    <div class="password-requirements mt-2">
                                        <div><i class="fas fa-check-circle text-success me-1"></i> Minimum 8 characters</div>
                                        <div><i class="fas fa-check-circle text-success me-1"></i> Include numbers & symbols</div>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-4">
                                    <label for="password-confirm" class="form-label fw-semibold">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" placeholder="Confirm your password" required autocomplete="new-password">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-danger w-100 mb-4">
                                    Create Account
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>

                                <!-- Login Link -->
                                <p class="text-center mb-0">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="text-danger text-decoration-none fw-semibold">Sign In</a>
                                </p>
                            </form>
                        </div>
                    </div>

                    <!-- Register Sidebar -->
                    <div class="col-lg-5 register-sidebar">
                        <div>
                            <h2 class="h3 fw-bold mb-4">Join Our Community!</h2>
                            <p class="mb-4 opacity-75">Get access to premium features and join our growing community of sports enthusiasts.</p>

                            <ul class="benefits-list mb-4">
                                <li>
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Easy booking system</span>
                                </li>
                                <li>
                                    <i class="fas fa-percentage"></i>
                                    <span>Exclusive member discounts</span>
                                </li>
                                <li>
                                    <i class="fas fa-users"></i>
                                    <span>Join sports communities</span>
                                </li>
                                <li>
                                    <i class="fas fa-trophy"></i>
                                    <span>Participate in tournaments</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto">
                            <div class="border-top border-white border-opacity-25 pt-4">
                                <div class="d-flex gap-3">
                                    <div>
                                        <h4 class="h2 fw-bold mb-0">500+</h4>
                                        <p class="mb-0 opacity-75">Active Members</p>
                                    </div>
                                    <div class="border-start border-white border-opacity-25 ps-3">
                                        <h4 class="h2 fw-bold mb-0">4.9</h4>
                                        <p class="mb-0 opacity-75">User Rating</p>
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
