@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Booking</h3>
                <p class="text-subtitle text-muted">Perbarui status booking lapangan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schedule.all-bookings') }}">Tabel Booking</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Booking</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Booking #{{ $booking->id }}</h5>
                        <div>
                            <a href="{{ route('admin.schedule.all-bookings') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Informasi Booking</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="150">Lapangan</th>
                                        <td>{{ $booking->field->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal & Waktu</th>
                                        <td>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}<br>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Pemesan</th>
                                        <td>{{ $booking->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tipe Booking</th>
                                        <td>
                                            @if($booking->is_membership)
                                                <span class="badge bg-primary">Membership</span>
                                                @if($booking->membershipSession && $booking->membershipSession->membershipSubscription && $booking->membershipSession->membershipSubscription->membership)
                                                    {{ $booking->membershipSession->membershipSubscription->membership->name }}
                                                    (Sesi {{ $booking->membershipSession->session_number }})
                                                @endif
                                            @else
                                                <span class="badge bg-info">Regular</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status Saat Ini</th>
                                        <td>
                                            @if($booking->status === 'confirmed')
                                                <span class="badge bg-success">Confirmed</span>
                                            @elseif($booking->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($booking->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted">Informasi Tambahan</h6>
                                <div class="row">
                                    @if($booking->is_membership)
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Perhatian:</strong> Ini adalah booking dari paket membership. Perubahan status akan memengaruhi jadwal membership terkait.
                                        </div>
                                    @endif

                                    @if($booking->photographerBookings && $booking->photographerBookings->count() > 0)
                                        <div class="col-md-12 mb-3">
                                            <div class="card border">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0">Fotografer</h6>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($booking->photographerBookings as $photographerBooking)
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>{{ $photographerBooking->photographer->name ?? 'Fotografer tidak tersedia' }}</div>
                                                            <span class="badge bg-{{ $photographerBooking->status === 'confirmed' ? 'success' : ($photographerBooking->status === 'pending' ? 'warning' : 'secondary') }}">
                                                                {{ ucfirst($photographerBooking->status) }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($booking->rentalBookings && $booking->rentalBookings->count() > 0)
                                        <div class="col-md-12">
                                            <div class="card border">
                                                <div class="card-header bg-warning text-dark">
                                                    <h6 class="mb-0">Item Rental</h6>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($booking->rentalBookings as $rentalBooking)
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>
                                                                {{ $rentalBooking->rentalItem->name ?? 'Item tidak tersedia' }}
                                                                <span class="badge bg-dark">{{ $rentalBooking->quantity }} item</span>
                                                            </div>
                                                            <span class="badge bg-{{ $rentalBooking->status === 'confirmed' ? 'success' : ($rentalBooking->status === 'pending' ? 'warning' : 'secondary') }}">
                                                                {{ ucfirst($rentalBooking->status) }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.schedule.booking.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="status" class="form-label">Status Booking</label>
                                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Perubahan status akan diterapkan ke semua booking terkait (fotografer & rental).</small>
                                    </div>

                                    <div class="form-group mb-3" id="cancellation-reason-group" style="{{ $booking->status !== 'cancelled' ? 'display: none' : '' }}">
                                        <label for="cancellation_reason" class="form-label">Alasan Pembatalan</label>
                                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control @error('cancellation_reason') is-invalid @enderror" rows="3">{{ old('cancellation_reason', $booking->cancellation_reason) }}</textarea>
                                        @error('cancellation_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="notes" class="form-label">Catatan Admin</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Perubahan
                                    </button>
                                    <a href="{{ route('admin.schedule.all-bookings') }}" class="btn btn-secondary">
                                        <i class="bi bi-x"></i> Batal
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const cancellationReasonGroup = document.getElementById('cancellation-reason-group');

            statusSelect.addEventListener('change', function() {
                if (this.value === 'cancelled') {
                    cancellationReasonGroup.style.display = 'block';
                } else {
                    cancellationReasonGroup.style.display = 'none';
                }
            });
        });
    </script>
@endsection
