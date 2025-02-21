<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SportVue</title>

    <!-- CSS & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #9E0620;
            --secondary-color: #2A2A2A;
            --danger-color: #9E0620;
            /* Custom danger color */
        }


        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* Override Bootstrap danger color */
        .btn-danger {
            background-color: var(--danger-color) !important;
            border-color: var(--danger-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .bg-danger {
            background-color: var(--danger-color) !important;
        }

        .border-danger {
            border-color: var(--danger-color) !important;
        }

       
    </style>
</head>

<body>
    @include('partials.navbar')

    <div class="container py-5">
        <!-- Checkout Progress Bar -->
        <div class="checkout-header position-relative py-4 border-bottom border-3 border-danger mb-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="progress-track">
                            <ul class="list-unstyled d-flex justify-content-between position-relative">
                                <li class="progress-step completed">
                                    <div class="step-indicator">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="step-label">Select Plan</span>
                                </li>
                                <li class="progress-step active">
                                    <div class="step-indicator">2</div>
                                    <span class="step-label">Fill Details</span>
                                </li>
                                <li class="progress-step">
                                    <div class="step-indicator">3</div>
                                    <span class="step-label">Payment</span>
                                </li>
                                <li class="progress-step">
                                    <div class="step-indicator">4</div>
                                    <span class="step-label">Confirmation</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Checkout Header Styling */
            .checkout-header {
                background: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                margin-top: 72px;
                /* Adjusts spacing from navbar */
            }

            /* Progress Track */
            .progress-track {
                padding: 0 10%;
            }

            .progress-track ul {
                margin: 0;
                padding: 0;
            }

            /* Progress Line */
            .progress-track ul::before {
                content: '';
                position: absolute;
                top: 23px;
                left: 15%;
                width: 70%;
                height: 2px;
                background-color: #e9ecef;
                z-index: 1;
            }

            /* Progress Steps */
            .progress-step {
                text-align: center;
                position: relative;
                z-index: 2;
            }

            .step-indicator {
                width: 45px;
                height: 45px;
                background: #fff;
                border: 2px solid #e9ecef;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 10px;
                color: #6c757d;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .step-label {
                font-size: 0.875rem;
                color: #6c757d;
                margin-top: 0.5rem;
            }

            /* Active Step */
            .progress-step.active .step-indicator {
                border-color: var(--primary-color);
                background: var(--primary-color);
                color: #fff;
            }

            .progress-step.active .step-label {
                color: var(--primary-color);
                font-weight: 600;
            }

            /* Completed Step */
            .progress-step.completed .step-indicator {
                border-color: var(--primary-color);
                background: var(--primary-color);
                color: #fff;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .progress-track {
                    padding: 0;
                }

                .step-label {
                    font-size: 0.75rem;
                }

                .step-indicator {
                    width: 35px;
                    height: 35px;
                }

                .progress-track ul::before {
                    top: 18px;
                }
            }
        </style>

        <!-- Main Content Area -->
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Content goes here -->
                </div>
            </div>
        </div>
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Personal Info -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Informasi Pribadi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" value="John Doe" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="john@example.com" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Detail Pemesanan</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th class="text-end">Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/api/placeholder/60/60" class="rounded me-3" alt="Field">
                                                <div>
                                                    <h6 class="mb-0">Lapangan A</h6>
                                                    <small class="text-muted">Indoor Field • 5v5</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>12 Jan 2024</td>
                                        <td>15:00 - 17:00</td>
                                        <td class="text-end">Rp 150.000</td>
                                    </tr>
                                    <!-- More items... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Metode Pembayaran</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card border rounded-3 p-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="transfer"
                                        checked>
                                    <label class="form-check-label w-100" for="transfer">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-university fa-lg text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-0">Transfer Bank</h6>
                                                <small class="text-muted">BCA, Mandiri, BNI</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card border rounded-3 p-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="ewallet">
                                    <label class="form-check-label w-100" for="ewallet">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-wallet fa-lg text-success me-3"></i>
                                            <div>
                                                <h6 class="mb-0">E-Wallet</h6>
                                                <small class="text-muted">GoPay, OVO, DANA</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Ringkasan Pesanan</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp 150.000</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Diskon Member</span>
                            <span class="text-success">- Rp 30.000</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Biaya Admin</span>
                            <span>Rp 5.000</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-danger">Rp 125.000</span>
                        </div>

                        <!-- Promo Code -->
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Kode Promo">
                                <button class="btn btn-outline-danger">Aplikasikan</button>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <div class="d-grid">
                            <a href="/payment" class="btn btn-danger btn-lg">
                                Lanjut ke Pembayaran
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="fas fa-shield-alt me-2"></i>
                                Pembayaran Aman & Terenkripsi
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-undo me-2"></i>
                                Kebijakan Pembatalan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
