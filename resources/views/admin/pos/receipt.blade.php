@extends('layouts.admin')

@section('title', 'Struk Pembayaran')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Struk Pembayaran</h3>
                <p class="text-subtitle text-muted">Detail transaksi POS</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.pos.index') }}">POS</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Struk Pembayaran</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">Struk Pembayaran #{{ $payment->order_id }}</h4>
                        <div>
                            <a href="{{ route('admin.pos.receipt.download', ['id' => $payment->id]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <a href="{{ route('admin.pos.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Header Struk -->
                        <div class="text-center mb-4">
                            <h4>ALENA SOCCER CENTER</h4>
                            <p class="mb-0">Jl. Raya Contoh No. 123, Jakarta</p>
                            <p class="mb-0">Telp: 021-12345678</p>
                        </div>

                        <!-- Info Transaksi -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="mb-1"><strong>No. Transaksi:</strong> {{ $payment->order_id }}</p>
                                <p class="mb-1"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y H:i') }}</p>
                                <p class="mb-1"><strong>Kasir:</strong> {{ auth()->user()->name }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><strong>Pelanggan:</strong> {{ $payment->user->name ?? 'Umum' }}</p>
                                <p class="mb-1"><strong>No. Telp:</strong> {{ $payment->user->phone_number ?? '-' }}</p>
                                <p class="mb-1"><strong>Metode Pembayaran:</strong>
                                    @php
                                        $paymentMethods = [
                                            'cash' => 'Tunai',
                                            'transfer' => 'Transfer Bank',
                                            'points' => 'Poin',
                                            'other' => 'Lainnya'
                                        ];
                                    @endphp
                                    {{ $paymentMethods[$payment->payment_type] ?? $payment->payment_type }}
                                </p>
                            </div>
                        </div>

                        <hr>

                        <!-- Detail Item -->
                        <div class="mb-3">
                            <h6>Detail Pembelian</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-end">Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Field Bookings -->
                                        @foreach($payment->fieldBookings as $booking)
                                        <tr>
                                            <td>
                                                <strong>Lapangan: {{ $booking->field->name }}</strong><br>
                                                <small>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach

                                        <!-- Rental Bookings -->
                                        @foreach($payment->rentalBookings as $booking)
                                        <tr>
                                            <td>
                                                <strong>Sewa: {{ $booking->rentalItem->name }}</strong><br>
                                                <small>Jumlah: {{ $booking->quantity }} | {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach

                                        <!-- Photographer Bookings -->
                                        @foreach($payment->photographerBookings as $booking)
                                        <tr>
                                            <td>
                                                <strong>Fotografer: {{ $booking->photographer->name }}</strong><br>
                                                <small>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($booking->price, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach

                                        <!-- Product Sales -->
                                        @if(isset($productSale) && $productSale)
                                            @foreach($productSale->productSaleItems as $item)
                                            <tr>
                                                <td>
                                                    <strong>Produk: {{ $item->product->name }}</strong><br>
                                                    <small>Jumlah: {{ $item->quantity }} &times; Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                                </td>
                                                <td class="text-end">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th>Subtotal</th>
                                            <th class="text-end">Rp {{ number_format($payment->original_amount, 0, ',', '.') }}</th>
                                        </tr>
                                        @if($payment->discount_amount > 0)
                                        <tr>
                                            <th>Diskon</th>
                                            <th class="text-end">- Rp {{ number_format($payment->discount_amount, 0, ',', '.') }}</th>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end">Rp {{ number_format($payment->amount, 0, ',', '.') }}</th>
                                        </tr>
                                        @if(isset($cashAmount) && $cashAmount > 0)
                                        <tr>
                                            <th>Tunai</th>
                                            <th class="text-end">Rp {{ number_format($cashAmount, 0, ',', '.') }}</th>
                                        </tr>
                                        <tr>
                                            <th>Kembalian</th>
                                            <th class="text-end">Rp {{ number_format($change, 0, ',', '.') }}</th>
                                        </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($payment->payment_details)
                            @php
                                $paymentDetails = json_decode($payment->payment_details, true);
                                $notes = $paymentDetails['notes'] ?? null;
                            @endphp
                            @if($notes)
                            <div class="mb-3">
                                <h6>Catatan:</h6>
                                <p>{{ $notes }}</p>
                            </div>
                            @endif
                        @endif

                        <hr>

                        <!-- Footer -->
                        <div class="text-center mt-4">
                            <p class="mb-0">Poin diperoleh: {{ floor($payment->original_amount / 10000) }}</p>
                            <p class="mb-1">Terima kasih atas kunjungan Anda</p>
                            <p class="mb-0">Semoga harimu menyenangkan!</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Transaksi Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
