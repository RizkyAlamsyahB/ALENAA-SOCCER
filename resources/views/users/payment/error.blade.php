@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="mb-4">Pembayaran Gagal</h2>
                    <p class="lead mb-4">Maaf, pembayaran Anda tidak dapat diproses. Terjadi kesalahan selama proses pembayaran.</p>

                    @if(isset($errorMessage))
                    <div class="alert alert-danger mb-4">
                        <p class="mb-0">{{ $errorMessage }}</p>
                    </div>
                    @endif

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Kemungkinan penyebab kegagalan:</h5>
                            <ul class="text-left mb-0">
                                <li>Kartu kredit atau rekening bank Anda tidak memiliki saldo yang cukup</li>
                                <li>Metode pembayaran yang Anda pilih sedang mengalami gangguan</li>
                                <li>Waktu pembayaran telah habis (timeout)</li>
                                <li>Masalah koneksi internet selama proses pembayaran</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('user.cart.view') }}" class="btn btn-outline-primary mx-2">Kembali ke Keranjang</a>
                        <a href="{{ route('user.cart.checkout') }}" class="btn btn-primary mx-2">Coba Bayar Lagi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
