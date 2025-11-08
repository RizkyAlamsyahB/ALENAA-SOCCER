@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Jadwal Lapangan {{ $field->name }}</h3>
                <p class="text-subtitle text-muted">Lihat semua booking untuk lapangan ini</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.schedule.index') }}">Jadwal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $field->name }}</li>
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
                    <a href="{{ route('admin.schedule.membership') }}" class="btn btn-outline-primary">
                        <i class="bi bi-card-list"></i> Jadwal Membership
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Jadwal {{ $field->name }}</h4>
                    <p class="text-muted mt-1 mb-0">{{ $field->type }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.schedule.index') }}?field_id={{ $field->id }}" class="btn btn-primary">
                        <i class="bi bi-calendar3"></i> Lihat di Kalender
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="fieldBookingsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Pemesan</th>
                                <th>Status</th>
                                <th>Tipe</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                                    <td>{{ $booking->user->name }}</td>
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
                                    <td>
                                        @if($booking->is_membership)
                                            <span class="badge bg-primary">Membership</span>
                                        @else
                                            <span class="badge bg-info">Regular</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-info view-booking" data-id="{{ $booking->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.schedule.booking.edit', $booking->id) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada booking untuk lapangan ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Booking -->
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingDetailModalLabel">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Lapangan</th>
                                    <td id="booking-field"></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td id="booking-date"></td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td id="booking-time"></td>
                                </tr>
                                <tr>
                                    <th>Pemesan</th>
                                    <td id="booking-user"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="booking-status"></td>
                                </tr>
                                <tr>
                                    <th>Tipe Booking</th>
                                    <td id="booking-type"></td>
                                </tr>
                                <tr class="membership-info" style="display: none;">
                                    <th>Paket Membership</th>
                                    <td id="booking-membership"></td>
                                </tr>
                                <tr class="membership-info" style="display: none;">
                                    <th>Sesi</th>
                                    <td id="booking-session"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3 photographer-info" style="display: none;">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Fotografer</h5>
                                </div>
                                <div class="card-body">
                                    <p id="photographer-name"></p>
                                </div>
                            </div>

                            <div class="card rental-info" style="display: none;">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Item Rental</h5>
                                </div>
                                <div class="card-body">
                                    <div id="rental-items-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" class="btn btn-warning" id="edit-booking-btn">Edit Booking</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#fieldBookingsTable').DataTable({
                order: [[1, 'desc'], [2, 'asc']] // Sort by date (desc) and then time (asc)
            });

            // Handle view booking details
            $('.view-booking').on('click', function() {
                const bookingId = $(this).data('id');
                showBookingDetails(bookingId);
            });

            // Function to show booking details
            function showBookingDetails(bookingId) {
                fetch(`{{ url('admin/schedule/booking') }}/${bookingId}`)
                    .then(response => response.json())
                    .then(data => {
                        const booking = data.booking;
                        const photographerBooking = data.photographer_booking;
                        const rentalBookings = data.rental_bookings;

                        // Fill modal with booking details
                        document.getElementById('booking-field').textContent = booking.field.name;
                        document.getElementById('booking-date').textContent = new Date(booking.start_time).toLocaleDateString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        document.getElementById('booking-time').textContent = new Date(booking.start_time).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        }) + ' - ' + new Date(booking.end_time).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        });
                        document.getElementById('booking-user').textContent = booking.user.name;

                        // Status with badge
                        let statusBadge = '';
                        switch(booking.status) {
                            case 'confirmed':
                                statusBadge = '<span class="badge bg-success">Confirmed</span>';
                                break;
                            case 'pending':
                                statusBadge = '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 'cancelled':
                                statusBadge = '<span class="badge bg-danger">Cancelled</span>';
                                break;
                            default:
                                statusBadge = '<span class="badge bg-secondary">' + booking.status + '</span>';
                        }
                        document.getElementById('booking-status').innerHTML = statusBadge;

                        // Type of booking
                        document.getElementById('booking-type').textContent = booking.is_membership ? 'Membership' : 'Regular';

                        // Show/hide membership info
                        const membershipInfoElements = document.querySelectorAll('.membership-info');
                        if (booking.is_membership && booking.membership_session) {
                            membershipInfoElements.forEach(el => el.style.display = 'table-row');

                            // Fill membership info
                            if (booking.membership_session.membership_subscription &&
                                booking.membership_session.membership_subscription.membership) {
                                document.getElementById('booking-membership').textContent =
                                    booking.membership_session.membership_subscription.membership.name;
                                document.getElementById('booking-session').textContent =
                                    'Sesi ' + booking.membership_session.session_number;
                            }
                        } else {
                            membershipInfoElements.forEach(el => el.style.display = 'none');
                        }

                        // Show/hide photographer info
                        const photographerInfo = document.querySelector('.photographer-info');
                        if (photographerBooking) {
                            photographerInfo.style.display = 'block';
                            document.getElementById('photographer-name').textContent =
                                photographerBooking.photographer ? photographerBooking.photographer.name : 'Nama tidak tersedia';
                        } else {
                            photographerInfo.style.display = 'none';
                        }

                        // Show/hide rental info
                        const rentalInfo = document.querySelector('.rental-info');
                        const rentalItemsList = document.getElementById('rental-items-list');

                        if (rentalBookings && rentalBookings.length > 0) {
                            rentalInfo.style.display = 'block';
                            rentalItemsList.innerHTML = '';

                            rentalBookings.forEach(rentalBooking => {
                                const rentalItem = rentalBooking.rental_item;
                                if (rentalItem) {
                                    const itemHtml = `
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>${rentalItem.name}</div>
                                            <div><span class="badge bg-dark">${rentalBooking.quantity} item</span></div>
                                        </div>
                                    `;
                                    rentalItemsList.innerHTML += itemHtml;
                                }
                            });
                        } else {
                            rentalInfo.style.display = 'none';
                        }

                        // Set edit button URL
                        document.getElementById('edit-booking-btn').href = `{{ url('admin/schedule/booking') }}/${booking.id}/edit`;

                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching booking details:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat mengambil detail booking',
                            icon: 'error'
                        });
                    });
            }
        });
    </script>
@endsection
