@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="mb-4">Pembayaran Berhasil</h2>
                    <p class="lead mb-4">Terima kasih! Pembayaran Anda telah berhasil diproses dan booking Anda telah dikonfirmasi.</p>

                    @if(isset($payment))
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-6 text-sm-right">Order ID:</div>
                                <div class="col-sm-6 text-sm-left">{{ $payment->order_id }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-6 text-sm-right">Total Pembayaran:</div>
                                <div class="col-sm-6 text-sm-left">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-6 text-sm-right">Metode Pembayaran:</div>
                                <div class="col-sm-6 text-sm-left">{{ $payment->payment_type ?? 'Midtrans' }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('user.payment.history') }}" class="btn btn-outline-primary mx-2">Lihat Riwayat Pembayaran</a>
                        <a href="{{ route('users.dashboard') }}" class="btn btn-primary mx-2">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
