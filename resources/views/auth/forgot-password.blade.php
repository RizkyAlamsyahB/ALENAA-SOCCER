<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - SportVue</title>

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

        .forgot-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .forgot-sidebar {
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

        .floating-shape {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }

        .steps-list {
            list-style: none;
            padding: 0;
            position: relative;
        }

        .steps-list li {
            padding-left: 35px;
            position: relative;
            margin-bottom: 1rem;
        }

        .steps-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 25px;
            height: 25px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .steps-list li:nth-child(1)::before { content: '1'; }
        .steps-list li:nth-child(2)::before { content: '2'; }
        .steps-list li:nth-child(3)::before { content: '3'; }

        @media (max-width: 768px) {
            .forgot-sidebar {
                display: none;
            }
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
                <div class="card forgot-card shadow-lg">
                    <div class="row g-0">
                        <!-- Form Section -->
                      <!-- Form Section -->
<div class="col-md-7">
    <div class="card-body p-4 p-lg-5">
        <div class="text-center mb-4">
            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                <i class="fas fa-lock text-danger fa-2x"></i>
            </div>
            <h2 class="h3 fw-bold">Forgot Password?</h2>
            <p class="text-muted">Don't worry! It happens. Please enter the email associated with your account.</p>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('status') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="mb-4">
            @csrf
            <div class="mb-4">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text border-0 bg-light">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Enter your email"
                        required
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="invalid-feedback d-block mt-2">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        {{ $message }}
                    </div>
                @enderror
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i>
                    We'll send a password reset link to this email
                </small>
            </div>

            <button type="submit" class="btn btn-submit text-white w-100">
                <span class="d-flex align-items-center justify-content-center">
                    Send Reset Link
                    <i class="fas fa-arrow-right ms-2"></i>
                </span>
            </button>
        </form>

        <div class="text-center">
            <p class="mb-0">Remember your password?
                <a href="/login" class="text-danger text-decoration-none fw-semibold">Sign In</a>
            </p>
        </div>
    </div>
</div>

<style>
.alert {
    border: none;
    border-radius: 12px;
}

.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
}

.btn-submit:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.invalid-feedback {
    color: var(--primary-color);
    font-size: 0.875rem;
}
</style>

<script>
// Disable submit button after form submission to prevent double submission
document.querySelector('form').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <span class="d-flex align-items-center justify-content-center">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Sending...
        </span>
    `;
});
</script>

                        <!-- Info Sidebar -->
                        <div class="col-md-5 forgot-sidebar p-4 p-lg-5 d-flex flex-column justify-content-center">
                            <h3 class="h4 text-white mb-4">Password Recovery Steps</h3>
                            <ul class="steps-list mb-4">
                                <li>Enter your email address</li>
                                <li>Check your inbox for the reset link</li>
                                <li>Create a new secure password</li>
                            </ul>

                            <div class="mt-auto">
                                <div class="bg-white bg-opacity-10 rounded-3 p-3">
                                    <h4 class="h6 mb-2">Need Help?</h4>
                                    <p class="mb-0 small">Contact our support team at
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
</body>
</html>
