@extends('layouts.admin')

@section('title', 'Riwayat Transaksi POS')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Riwayat Transaksi POS</h3>
                <p class="text-subtitle text-muted">Daftar seluruh transaksi dari Point of Sale</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.pos.index') }}">POS</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Riwayat Transaksi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Filter Panel -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">Filter Transaksi</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pos.history') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="payment_type">Metode Pembayaran</label>
                                <select class="form-select" id="payment_type" name="payment_type">
                                    <option value="">Semua</option>
                                    <option value="cash" {{ $paymentType == 'cash' ? 'selected' : '' }}>Tunai</option>
                                    <option value="transfer" {{ $paymentType == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="points" {{ $paymentType == 'points' ? 'selected' : '' }}>Poin</option>
                                    <option value="other" {{ $paymentType == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Cari</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Order ID / Nama Customer" value="{{ $search }}">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mb-3">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Pendapatan</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-receipt"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Jumlah Transaksi</h6>
                                <h6 class="font-extrabold mb-0">{{ $transactions->total() }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-currency-exchange"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Rata-rata Transaksi</h6>
                                <h6 class="font-extrabold mb-0">
                                    Rp {{ $transactions->total() > 0 ? number_format($totalRevenue / $transactions->total(), 0, ',', '.') : 0 }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">Grafik Transaksi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <canvas id="transaction-chart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">Pembayaran berdasarkan Metode</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($revenueByPaymentType as $revenue)
                        <div class="col-md-3 col-sm-6">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar avatar-lg bg-light-{{ getPaymentTypeColor($revenue->payment_type) }} me-3">
                                            <span class="avatar-content">
                                                <i class="{{ getPaymentTypeIcon($revenue->payment_type) }}"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ getPaymentTypeName($revenue->payment_type) }}</h6>
                                            <small class="text-muted">{{ number_format(($revenue->total / $totalRevenue) * 100, 1) }}%</small>
                                        </div>
                                    </div>
                                    <h4 class="font-weight-bold mb-0">Rp {{ number_format($revenue->total, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Transaksi</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Item</th>
                                <th>Metode Pembayaran</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->order_id }}</td>
                                    <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                    <td>{{ $transaction->user->name ?? 'Guest' }}
                                        @if($transaction->user && $transaction->user->phone_number)
                                            <br><small>{{ $transaction->user->phone_number }}</small>
                                        @endif
                                    </td>
                                   
                                    <td>
                                        <span class="badge bg-light-{{ getPaymentTypeColor($transaction->payment_type) }}">
                                            {{ getPaymentTypeName($transaction->payment_type) }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $transaction->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $transaction->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.pos.receipt', $transaction->id) }}">
                                                        <i class="bi bi-eye"></i> Lihat Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.pos.receipt.download', $transaction->id) }}">
                                                        <i class="bi bi-download"></i> Download Struk
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="bi bi-receipt-cutoff"></i>
                                            </div>
                                            <h5 class="mt-4">Tidak ada data transaksi</h5>
                                            <p class="text-muted">Tidak ada transaksi yang ditemukan dengan filter yang diterapkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
    <!-- JS Dependencies -->
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/static/js/pages/datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert@2"></script>
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk grafik
        const chartData = @json($chartData);

        // Buat grafik transaksi
        const ctx = document.getElementById('transaction-chart').getContext('2d');
        const transactionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.dates,
                datasets: [
                    {
                        label: 'Jumlah Transaksi',
                        data: chartData.counts,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y-axis-1',
                    },
                    {
                        label: 'Total Pendapatan (Rp)',
                        data: chartData.totals,
                        type: 'line',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        yAxisID: 'y-axis-2',
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        id: 'y-axis-1',
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        id: 'y-axis-2',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    });
</script>

@php
// Helper functions - sebaiknya dipindahkan ke Helper atau BladeServiceProvider
function getPaymentTypeName($type) {
    $types = [
        'cash' => 'Tunai',
        'transfer' => 'Transfer',
        'points' => 'Poin',
        'other' => 'Lainnya'
    ];
    return $types[$type] ?? $type;
}

function getPaymentTypeIcon($type) {
    $icons = [
        'cash' => 'bi bi-cash',
        'transfer' => 'bi bi-bank',
        'points' => 'bi bi-star',
        'other' => 'bi bi-credit-card'
    ];
    return $icons[$type] ?? 'bi bi-question-circle';
}

function getPaymentTypeColor($type) {
    $colors = [
        'cash' => 'success',
        'transfer' => 'primary',
        'points' => 'warning',
        'other' => 'info'
    ];
    return $colors[$type] ?? 'secondary';
}
@endphp
@endsection
