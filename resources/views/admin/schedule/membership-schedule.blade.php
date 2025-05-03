@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Jadwal Membership</h3>
                <p class="text-subtitle text-muted">Kelola jadwal sesi membership yang aktif</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Jadwal Membership</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.schedule.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar3"></i> Kalender Jadwal
                    </a>
                    <a href="{{ route('admin.schedule.all-bookings') }}" class="btn btn-outline-primary">
                        <i class="bi bi-table"></i> Tabel Booking
                    </a>
                    <a href="{{ route('admin.schedule.membership') }}" class="btn btn-primary active">
                        <i class="bi bi-card-list"></i> Jadwal Membership
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Membership Aktif</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Member</th>
                                <th>Paket</th>
                                <th>Lapangan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Berakhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberships as $subscription)
                                <tr>
                                    <td>{{ $subscription->id }}</td>
                                    <td>{{ $subscription->user->name }}</td>
                                    <td>
                                        @if($subscription->membership)
                                            <span class="badge bg-{{ $subscription->membership->type === 'bronze' ? 'secondary' : ($subscription->membership->type === 'silver' ? 'light text-dark' : 'warning') }}">
                                                {{ $subscription->membership->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Tidak tersedia</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->membership && $subscription->membership->field)
                                            {{ $subscription->membership->field->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</td>
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
                                    <td>
                                        <a href="{{ route('admin.schedule.membership.detail', $subscription->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-calendar3-week"></i> Jadwal
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($memberships->isEmpty())
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada membership aktif saat ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
