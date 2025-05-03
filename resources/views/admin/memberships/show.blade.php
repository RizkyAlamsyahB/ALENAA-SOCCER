@extends('layouts.admin')

@section('title', 'Detail Paket Membership')
@section('breadcrumb', 'Detail Paket Membership')
@section('header-title', 'Detail Paket Membership')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Detail Paket Membership "{{ $membership->name }}"</h4>
                        <div>
                            <a href="{{ route('admin.memberships.edit', $membership->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('admin.memberships.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $membership->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Paket</th>
                                    <td>{{ $membership->name }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe</th>
                                    <td>
                                        @php
                                            $badgeClass = '';
                                            switch ($membership->type) {
                                                case 'bronze':
                                                    $badgeClass = 'bg-secondary';
                                                    break;
                                                case 'silver':
                                                    $badgeClass = 'bg-light text-dark';
                                                    break;
                                                case 'gold':
                                                    $badgeClass = 'bg-warning';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($membership->type) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lapangan</th>
                                    <td>
                                        @if ($membership->field)
                                            {{ $membership->field->name }} ({{ $membership->field->type }})
                                        @else
                                            <span class="text-muted">Tidak ada lapangan</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>Rp {{ number_format($membership->price, 0, ',', '.') }} / minggu</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ $membership->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $membership->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $membership->description }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <td>{{ $membership->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diperbarui</th>
                                    <td>{{ $membership->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Detail Sesi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box bg-light">
                                                <div class="info-box-content text-center p-3">
                                                    <span class="info-box-text">Sesi per Minggu</span>
                                                    <span class="info-box-number display-6">{{ $membership->sessions_per_week }}</span>
                                                    <span class="info-box-text">sesi</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box bg-light">
                                                <div class="info-box-content text-center p-3">
                                                    <span class="info-box-text">Durasi per Sesi</span>
                                                    <span class="info-box-number display-6">{{ $membership->session_duration }}</span>
                                                    <span class="info-box-text">jam</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6>Total Jam per Minggu:</h6>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ min(100, ($membership->sessions_per_week * $membership->session_duration / 10) * 100) }}%;"
                                                aria-valuenow="{{ $membership->sessions_per_week * $membership->session_duration }}"
                                                aria-valuemin="0" aria-valuemax="10">
                                                {{ $membership->sessions_per_week * $membership->session_duration }} jam
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($membership->image)
                                <div class="text-center mb-4">
                                    <img src="{{ asset('storage/' . $membership->image) }}" alt="{{ $membership->name }}"
                                        class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            @endif

                            @if($membership->includes_photographer && $membership->photographer)
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Layanan Fotografer</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                @if($membership->photographer->image)
                                                    <img src="{{ asset('storage/' . $membership->photographer->image) }}"
                                                        class="rounded-circle" width="60">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                        style="width: 60px; height: 60px;">
                                                        <i class="bi bi-camera fs-4"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">{{ $membership->photographer->name }}</h5>
                                                <span class="badge bg-info">{{ ucfirst($membership->photographer->package_type) }}</span>
                                                <p class="text-muted mb-0">Rp {{ number_format($membership->photographer->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Total {{ $membership->photographer_duration }} jam</strong> layanan fotografer
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($membership->includes_rental_item && $rentalItem)
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">Barang Sewaan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">{{ $rentalItem->name }}</h5>
                                                <p class="text-muted mb-0">Rp {{ number_format($rentalItem->rental_price, 0, ',', '.') }} / item</p>
                                            </div>
                                            <div class="ms-auto">
                                                <span class="badge bg-dark">{{ $membership->rental_item_quantity }} item</span>
                                            </div>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Total Nilai: Rp {{ number_format($rentalItem->rental_price * $membership->rental_item_quantity, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
