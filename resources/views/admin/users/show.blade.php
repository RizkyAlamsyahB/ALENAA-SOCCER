@extends('layouts.admin')

@section('title', 'Detail Pengguna')
@section('breadcrumb', 'Detail Pengguna')
@section('header-title', 'Detail Pengguna')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Informasi Pengguna</h4>

                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}"
                                class="img-fluid rounded-circle border" style="max-width: 200px; height: auto;">
                        @else
                            <div class="bg-light p-5 rounded-circle d-inline-block">
                                <i class="bi bi-person text-secondary" style="font-size: 6rem;"></i>
                            </div>
                        @endif

                        <h4 class="mt-3">{{ $user->name }}</h4>
                        <span class="badge bg-{{ $user->role == 'owner' ? 'primary' : ($user->role == 'admin' ? 'success' : ($user->role == 'photographer' ? 'info' : 'secondary')) }} px-3 py-2 fs-6 mb-3">
                            {{ ucfirst($user->role) }}
                        </span>

                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button type="button" class="btn btn-danger delete-btn" data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Email Diverifikasi</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Terverifikasi pada {{ $user->email_verified_at->format('d M Y H:i') }}</span>
                                        @else
                                            <span class="badge bg-warning">Belum diverifikasi</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nomor Telepon</th>
                                    <td>{{ $user->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $user->address }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td>{{ \Carbon\Carbon::parse($user->birthdate)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Usia</th>
                                    <td>{{ \Carbon\Carbon::parse($user->birthdate)->age }} tahun</td>
                                </tr>
                                <tr>
                                    <th>Points</th>
                                    <td>
                                        <span class="badge bg-primary px-3 py-2">{{ number_format($user->points, 0, ',', '.') }} points</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Terdaftar Pada</th>
                                    <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diperbarui</th>
                                    <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        @if($user->role == 'photographer')
                        <div class="mt-4">
                            <h5>Data Fotografer</h5>
                            @if(isset($photographerData) && $photographerData->count() > 0)
                                @foreach($photographerData as $package)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6>{{ $package->name }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="30%">Nama Paket</th>
                                                        <td>{{ $package->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jenis Paket</th>
                                                        <td>
                                                            <span class="badge bg-{{ $package->package_type == 'favorite' ? 'warning' : ($package->package_type == 'plus' ? 'info' : ($package->package_type == 'exclusive' ? 'primary' : 'secondary')) }}">
                                                                {{ ucfirst($package->package_type) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Harga</th>
                                                        <td>Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Durasi</th>
                                                        <td>{{ $package->duration }} jam</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>
                                                            <span class="badge bg-{{ $package->status == 'active' ? 'success' : 'danger' }}">
                                                                {{ ucfirst($package->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Fitur</th>
                                                        <td>
                                                            @if(is_array(json_decode($package->features, true)))
                                                                <ul class="mb-0">
                                                                    @foreach(json_decode($package->features) as $feature)
                                                                        <li>{{ $feature }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                {{ $package->features }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    Tidak ada data paket fotografer.
                                    <a href="{{ route('admin.photo-packages.create') }}">Buat paket fotografer</a> untuk pengguna ini.
                                </div>
                            @endif
                        </div>
                    @endif
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // Configure Toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Konfirmasi hapus menggunakan Toastr
        $('.delete-btn').on('click', function() {
            var userId = $(this).data('id');
            var userName = $(this).data('name');

            toastr.warning(
                `<div>
                    <p>Apakah Anda yakin ingin menghapus pengguna "<b>${userName}</b>"?</p>
                    <button class="btn btn-danger btn-sm" id="confirmDelete" data-id="${userId}" style="margin-right:10px;">Ya, Hapus!</button>
                    <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                </div>`,
                'Konfirmasi Hapus', {
                    closeButton: true,
                    onShown: function() {
                        // Event listener untuk tombol hapus
                        $('#confirmDelete').on('click', function() {
                            var id = $(this).data('id');
                            hapusPengguna(id);
                        });

                        // Event listener untuk tombol batal
                        $('#cancelDelete').on('click', function() {
                            toastr.clear(); // Hilangkan toastr jika dibatalkan
                        });
                    }
                }
            );
        });

        // Fungsi untuk menghapus data
        function hapusPengguna(userId) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.users.destroy', '') }}/' + userId;
            form.style.display = 'none';

            var csrfToken = document.createElement('input');
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            var methodField = document.createElement('input');
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
@endsection
