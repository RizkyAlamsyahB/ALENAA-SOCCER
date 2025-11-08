@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Jadwal Membership</h3>
                <p class="text-subtitle text-muted">Jadwal sesi {{ $subscription->membership ? $subscription->membership->name : 'Membership' }} untuk {{ $subscription->user->name }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schedule.membership') }}">Jadwal Membership</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Membership</h5>
                        <a href="{{ route('admin.schedule.membership') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="200">ID Membership</th>
                                        <td>#{{ $subscription->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Member</th>
                                        <td>{{ $subscription->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $subscription->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Paket</th>
                                        <td>
                                            @if($subscription->membership)
                                                <span class="badge bg-{{ $subscription->membership->type === 'bronze' ? 'secondary' : ($subscription->membership->type === 'silver' ? 'light text-dark' : 'warning') }}">
                                                    {{ $subscription->membership->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Tidak tersedia</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Lapangan</th>
                                        <td>
                                            @if($subscription->membership && $subscription->membership->field)
                                                {{ $subscription->membership->field->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="200">Status</th>
                                        <td>
                                            @if($subscription->status === 'active')
                                                <span class="badge bg-success">Aktif</span>
                                                @if($subscription->renewal_status === 'renewal_pending')
                                                    <span class="badge bg-info">Menunggu Perpanjangan</span>
                                                @elseif($subscription->renewal_status === 'renewed')
                                                    <span class="badge bg-primary">Diperpanjang</span>
                                                @endif
                                            @elseif($subscription->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($subscription->status === 'expired')
                                                <span class="badge bg-danger">Kadaluarsa</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($subscription->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Mulai</th>
                                        <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Berakhir</th>
                                        <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Sesi</th>
                                        <td>{{ $subscription->sessions->count() }} sesi</td>
                                    </tr>
                                    <tr>
                                        <th>Pembayaran Terakhir</th>
                                        <td>
                                            @if($subscription->last_payment_date)
                                                {{ \Carbon\Carbon::parse($subscription->last_payment_date)->format('d M Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Jadwal Sesi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sesi</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Fotografer</th>
                                        <th>Item Rental</th>
                                        {{-- <th>Aksi</th> --}}
                                    </tr>
                                </thead>
<!-- Bagian tabel sesi di view membership-detail.blade.php -->
<tbody>
    @forelse($subscription->sessions as $index => $session)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>Sesi {{ $session->session_number }}</td>
            <td>{{ \Carbon\Carbon::parse($session->start_time)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</td>
            <td>
                @if($session->status === 'scheduled')
                    <span class="badge bg-primary">Terjadwal</span>
                @elseif($session->status === 'ongoing')
                    <span class="badge bg-info">Berlangsung</span>
                @elseif($session->status === 'completed')
                    <span class="badge bg-success">Selesai</span>
                @elseif($session->status === 'cancelled')
                    <span class="badge bg-danger">Dibatalkan</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($session->status) }}</span>
                @endif
            </td>
            <td>
                @if($session->fieldBooking && $session->fieldBooking->photographerBookings->isNotEmpty())
                    @foreach($session->fieldBooking->photographerBookings as $photographerBooking)
                        <span class="badge bg-info">{{ $photographerBooking->photographer->name ?? 'Fotografer' }}</span>
                    @endforeach
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>
                @if($session->fieldBooking && $session->fieldBooking->rentalBookings->isNotEmpty())
                    @foreach($session->fieldBooking->rentalBookings as $rentalBooking)
                        <span class="badge bg-secondary">{{ $rentalBooking->rentalItem->name ?? 'Item' }} ({{ $rentalBooking->quantity }})</span>
                    @endforeach
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            {{-- <td>
                @if($session->fieldBooking)
                    <a href="{{ route('admin.schedule.booking.edit', $session->fieldBooking->id) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td> --}}
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">Belum ada jadwal sesi tersedia</td>
        </tr>
    @endforelse
</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
