@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/modern-payment.css') }}">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-wrapper" style="margin-top: 50px;">
        <div class="container py-2">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Pembayaran</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 fw-bold">Riwayat Pembayaran</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                    <div class="card-header bg-white py-3 border-0 px-4">
                        <h5 class="mb-0 fw-bold">Daftar Pembayaran</h5>
                    </div>
                    <div class="card-body p-0">
                        @if(count($payments) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-3">Order ID</th>
                                            <th class="py-3">Tanggal</th>
                                            <th class="py-3">Total</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Metode</th>
                                            <th class="py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                            <tr>
                                                <td class="py-3">{{ $payment->order_id }}</td>
                                                <td class="py-3">{{ $payment->created_at->format('d M Y H:i') }}</td>
                                                <td class="py-3">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                <td class="py-3">
                                                    @if($payment->transaction_status == 'success')
                                                        <span class="badge bg-success rounded-pill">Sukses</span>
                                                    @elseif($payment->transaction_status == 'pending')
                                                        <span class="badge bg-warning rounded-pill">Menunggu</span>
                                                    @elseif($payment->transaction_status == 'failed')
                                                        <span class="badge bg-danger rounded-pill">Gagal</span>
                                                    @elseif($payment->transaction_status == 'challenge')
                                                        <span class="badge bg-info rounded-pill">Challenge</span>
                                                    @else
                                                        <span class="badge bg-secondary rounded-pill">{{ $payment->transaction_status }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-3">{{ $payment->payment_type ?? '-' }}</td>
                                                <td class="py-3">
                                                    <a href="{{ route('user.payment.detail', $payment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-4">
                                {{ $payments->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state mb-4">
                                    <i class="fas fa-file-invoice-dollar fa-4x text-muted"></i>
                                </div>
                                <h4 class="mb-3 fw-bold">Belum Ada Pembayaran</h4>
                                <p class="text-muted mb-4">Anda belum memiliki riwayat pembayaran.</p>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('user.fields.index') }}" class="btn-explore">
                                        <i class="fas fa-futbol me-2"></i>
                                        <span>Cari Lapangan</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Additional styles for payment history page */
        .badge {
            padding: 0.5em 0.8em;
            font-weight: 500;
        }

        .table th {
            font-weight: 600;
            border-top: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table td {
            vertical-align: middle;
            border-top: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .empty-state {
            opacity: 0.2;
        }

        .btn-explore {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: #9e0620;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-explore:hover {
            background-color: #7d0318;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(158, 6, 32, 0.2);
            color: white;
        }
    </style>
@endsection
