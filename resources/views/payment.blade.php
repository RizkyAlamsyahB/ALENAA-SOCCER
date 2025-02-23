@extends('layouts.app')
@section('content')
<style>
/* Modern Checkout Styling */
:root {
    --primary-color: #9e0620;
    --primary-light: #fff8f8;
    --gray-light: #f8f9fa;
    --gray-medium: #6c757d;
    --border-color: #eee;
}

/* Card Base Styles */
.checkout-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    position: relative;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    margin-bottom: 1.5rem;
}

.checkout-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

/* Progress Steps */
.progress-track {
    position: relative;
    padding: 2rem 0;
    margin-bottom: 3rem;
}

.progress-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background: var(--border-color);
    transform: translateY(-50%);
}

.progress-line .filled {
    position: absolute;
    height: 100%;
    width: 50%;
    background: var(--primary-color);
    transition: width 0.3s ease;
}

.progress-steps {
    position: relative;
    display: flex;
    justify-content: space-between;
    z-index: 1;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.step-indicator {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    transition: all 0.3s ease;
    background: white;
    border: 2px solid var(--border-color);
}

.step-item.completed .step-indicator {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.step-item.active .step-indicator {
    background: var(--primary-light);
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.step-label {
    font-size: 0.875rem;
    color: var(--gray-medium);
    font-weight: 500;
}

/* Form Styling */
.form-control {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(158, 6, 32, 0.1);
}

.form-control[readonly] {
    background: var(--gray-light);
}

/* Payment Method Cards */
.payment-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method-card .card {
    border-radius: 16px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.payment-method-card:hover .card {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
}

.payment-method-card .form-check-input:checked + .form-check-label .card {
    border-color: var(--primary-color);
    background: var(--primary-light);
}

.payment-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.payment-icon.bank {
    background: var(--primary-light);
    color: var(--primary-color);
}

.payment-icon.wallet {
    background: #e8f5e9;
    color: #2e7d32;
}

/* Order Summary */
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    color: var(--gray-medium);
}

.summary-total {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary-color);
}

/* Buttons */
.btn-primary {
    background: var(--primary-color);
    border: none;
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #7d051a;
    transform: translateX(5px);
}

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: white;
}

/* Security Badges */
.security-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    color: var(--gray-medium);
}

.security-badge i {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-light);
    color: var(--primary-color);
    font-size: 0.875rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .checkout-card {
        padding: 1rem;
    }

    .step-indicator {
        width: 35px;
        height: 35px;
        border-radius: 10px;
    }

    .step-label {
        font-size: 0.75rem;
    }
}
</style>

<div class="container py-5">
    <!-- Progress Steps -->
    <div class="progress-track">
        <div class="progress-line">
            <div class="filled"></div>
        </div>
        <div class="progress-steps">
            <div class="step-item completed">
                <div class="step-indicator">
                    <i class="fas fa-check"></i>
                </div>
                <span class="step-label">Select Plan</span>
            </div>
            <div class="step-item active">
                <div class="step-indicator">2</div>
                <span class="step-label">Fill Details</span>
            </div>
            <div class="step-item">
                <div class="step-indicator">3</div>
                <span class="step-label">Payment</span>
            </div>
            <div class="step-item">
                <div class="step-indicator">4</div>
                <span class="step-label">Confirmation</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Personal Info -->
            <div class="checkout-card">
                <h5 class="mb-4">Informasi Pribadi</h5>
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

            <!-- Payment Methods -->
            <div class="checkout-card">
                <h5 class="mb-4">Metode Pembayaran</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="payment-method-card">
                            <input type="radio" class="form-check-input" name="payment" id="bank" checked>
                            <label class="form-check-label w-100" for="bank">
                                <div class="card border p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="payment-icon bank">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Transfer Bank</h6>
                                            <small class="text-muted">BCA, Mandiri, BNI</small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="payment-method-card">
                            <input type="radio" class="form-check-input" name="payment" id="ewallet">
                            <label class="form-check-label w-100" for="ewallet">
                                <div class="card border p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="payment-icon wallet">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">E-Wallet</h6>
                                            <small class="text-muted">GoPay, OVO, DANA</small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="checkout-card">
                <h5 class="mb-4">Ringkasan Pesanan</h5>

                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>Rp 150.000</span>
                </div>
                <div class="summary-item">
                    <span>Diskon Member</span>
                    <span class="text-success">- Rp 30.000</span>
                </div>
                <div class="summary-item">
                    <span>Biaya Admin</span>
                    <span>Rp 5.000</span>
                </div>

                <hr class="my-4">

                <div class="summary-item">
                    <span class="fw-bold">Total</span>
                    <span class="summary-total">Rp 125.000</span>
                </div>

                <div class="mt-4">
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" placeholder="Kode Promo">
                        <button class="btn btn-outline-primary">Apply</button>
                    </div>

                    <button class="btn btn-primary w-100 mb-4">
                        Lanjut ke Pembayaran
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>

                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Pembayaran Aman & Terenkripsi</span>
                    </div>
                    <div class="security-badge">
                        <i class="fas fa-undo"></i>
                        <span>Kebijakan Pembatalan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
