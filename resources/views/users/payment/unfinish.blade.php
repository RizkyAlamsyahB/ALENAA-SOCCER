@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-clock text-warning" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="mb-4">Pembayaran Tertunda</h2>
                    <p class="lead mb-4">Pembayaran Anda sedang diproses dan menunggu konfirmasi. Kami akan memberi tahu Anda jika ada perubahan status.</p>

                    @if(isset($orderId))
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <p class="mb-0"><strong>Order ID:</strong> {{ $orderId }}</p>
                        </div>
                    </div>
                    <p>Mohon simpan Order ID ini untuk referensi Anda.</p>
                    @endif

                    <div class="alert alert-info">
                        <p class="mb-0">Mohon selesaikan pembayaran Anda sesuai dengan instruksi yang diberikan. Jika Anda telah melakukan pembayaran, mohon tunggu hingga sistem memperbarui status pembayaran Anda.</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('user.payment.history') }}" class="btn btn-outline-primary mx-2">Cek Status Pembayaran</a>
                        <a href="{{ route('users.dashboard') }}" class="btn btn-primary mx-2">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
