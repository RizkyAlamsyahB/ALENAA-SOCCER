@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tabel Booking Lapangan</h3>
                <p class="text-subtitle text-muted">Daftar semua booking lapangan dan membership</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tabel Booking</li>
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
                    <a href="{{ route('admin.schedule.all-bookings') }}" class="btn btn-primary active">
                        <i class="bi bi-table"></i> Tabel Booking
                    </a>
                    <a href="{{ route('admin.schedule.membership') }}" class="btn btn-outline-primary">
                        <i class="bi bi-card-list"></i> Jadwal Membership
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Semua Booking Lapangan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bookingsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Tipe</th>
                                <th>Membership</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
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
                    {{-- <a href="#" class="btn btn-warning" id="edit-booking-btn">Edit Booking</a> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#bookingsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.schedule.all-bookings') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'field_name', name: 'field_name' },
                    { data: 'start_time', name: 'start_time' },
                    { data: 'end_time', name: 'end_time' },
                    { data: 'booking_type', name: 'booking_type' },
                    { data: 'membership_info', name: 'membership_info' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']] // Sort by start_time by default
            });

            // Handle view booking details
            $('#bookingsTable').on('click', '.view-details', function() {
                const bookingId = $(this).data('id');
                showBookingDetails(bookingId);
            });

            // Function to show booking details
// Update pada fungsi JavaScript untuk menampilkan detail booking

function showBookingDetails(bookingId) {
    fetch(`{{ url('admin/schedule/booking') }}/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            const booking = data.booking;

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
                if (booking.membership_session.subscription &&
                    booking.membership_session.subscription.membership) {
                    document.getElementById('booking-membership').textContent =
                        booking.membership_session.subscription.membership.name;
                    document.getElementById('booking-session').textContent =
                        'Sesi ' + booking.membership_session.session_number;
                }
            } else {
                membershipInfoElements.forEach(el => el.style.display = 'none');
            }

            // Show/hide photographer info
            const photographerInfo = document.querySelector('.photographer-info');
            if (booking.photographer_bookings && booking.photographer_bookings.length > 0) {
                photographerInfo.style.display = 'block';
                const photographerName = booking.photographer_bookings[0].photographer ?
                    booking.photographer_bookings[0].photographer.name : 'Nama tidak tersedia';
                document.getElementById('photographer-name').textContent = photographerName;
            } else {
                photographerInfo.style.display = 'none';
            }

            // Show/hide rental info
            const rentalInfo = document.querySelector('.rental-info');
            const rentalItemsList = document.getElementById('rental-items-list');

            // Improved code to check rental bookings
            console.log('Rental bookings:', booking.rental_bookings); // Debug log

            if (booking.rental_bookings && booking.rental_bookings.length > 0) {
                rentalInfo.style.display = 'block';
                rentalItemsList.innerHTML = '';

                booking.rental_bookings.forEach(rentalBooking => {
                    if (rentalBooking.rental_item) {
                        const itemHtml = `
                            <div class="d-flex justify-content-between mb-2">
                                <div>${rentalBooking.rental_item.name}</div>
                                <div><span class="badge bg-dark">${rentalBooking.quantity} item</span></div>
                            </div>
                        `;
                        rentalItemsList.innerHTML += itemHtml;
                    } else {
                        console.log('Missing rental item data for booking:', rentalBooking.id);
                    }
                });
            } else {
                rentalInfo.style.display = 'none';
                console.log('No rental bookings found for this field booking');
            }

            // Set edit button URL
            // document.getElementById('edit-booking-btn').href = `{{ url('admin/schedule/booking') }}/${booking.id}/edit`;

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
