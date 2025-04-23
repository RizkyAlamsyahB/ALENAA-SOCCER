@extends('layouts.admin')

@section('title', 'Detail Paket Fotografer')
@section('breadcrumb', 'Detail Paket Fotografer')
@section('header-title', 'Detail Paket Fotografer')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Informasi Paket Fotografer</h4>

                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="card">
                            <div class="card-body p-0">
                                @if($photographer->image)
                                    <img src="{{ asset('storage/' . $photographer->image) }}" alt="{{ $photographer->name }}"
                                        class="img-fluid border-bottom" style="max-width: 100%; height: auto;">
                                @else
                                    <div class="bg-light p-5 text-center">
                                        <i class="bi bi-camera text-secondary" style="font-size: 6rem;"></i>
                                        <p class="mt-3">Tidak ada gambar</p>
                                    </div>
                                @endif

                                <div class="p-3">
                                    <h4>{{ $photographer->name }}</h4>
                                    <span class="badge bg-{{ $photographer->package_type == 'favorite' ? 'warning' : ($photographer->package_type == 'plus' ? 'info' : ($photographer->package_type == 'exclusive' ? 'primary' : 'secondary')) }} px-3 py-2 fs-6 mb-2">
                                        {{ ucfirst($photographer->package_type) }}
                                    </span>
                                    <p class="mb-0"><strong>Harga:</strong> Rp {{ number_format($photographer->price, 0, ',', '.') }}</p>
                                    <p class="mb-0"><strong>Durasi:</strong> {{ $photographer->duration }} jam</p>
                                    <p class="mb-0">
                                        <strong>Status:</strong>
                                        <span class="badge bg-{{ $photographer->status == 'active' ? 'success' : 'danger' }}">
                                            {{ $photographer->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <a href="{{ route('admin.photo-packages.edit', $photographer->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button type="button" class="btn btn-danger delete-btn" data-id="{{ $photographer->id }}" data-name="{{ $photographer->name }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Detail Paket</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6>Deskripsi</h6>
                                    <p>{{ $photographer->description }}</p>
                                </div>

                                <div class="mb-4">
                                    <h6>Fitur Paket</h6>
                                    @if(is_array($photographer->features) || is_object($photographer->features))
                                        <ul class="list-group">
                                            @foreach($photographer->features as $feature)
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>Tidak ada fitur</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Fotografer</h5>
                            </div>
                            <div class="card-body">
                                @if($user)
                                    <div class="d-flex align-items-center mb-3">
                                        @if($user->profile_picture)
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}"
                                                class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-circle me-3 d-flex justify-content-center align-items-center"
                                                style="width: 64px; height: 64px;">
                                                <i class="bi bi-person text-secondary" style="font-size: 2rem;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h5 class="mb-1">{{ $user->name }}</h5>
                                            <p class="mb-0 text-muted">{{ $user->email }}</p>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <p><strong>Telepon:</strong> {{ $user->phone_number }}</p>
                                            <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($user->birthdate)->format('d M Y') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Alamat:</strong> {{ $user->address }}</p>
                                            <p><strong>Bergabung Sejak:</strong> {{ $user->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Lihat Detail Pengguna
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Data fotografer tidak ditemukan atau telah dihapus.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.photo-packages.index') }}" class="btn btn-secondary">
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
            var packageId = $(this).data('id');
            var packageName = $(this).data('name');

            toastr.warning(
                `<div>
                    <p>Apakah Anda yakin ingin menghapus paket fotografer "<b>${packageName}</b>"?</p>
                    <button class="btn btn-danger btn-sm" id="confirmDelete" data-id="${packageId}" style="margin-right:10px;">Ya, Hapus!</button>
                    <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                </div>`,
                'Konfirmasi Hapus', {
                    closeButton: true,
                    onShown: function() {
                        // Event listener untuk tombol hapus
                        $('#confirmDelete').on('click', function() {
                            var id = $(this).data('id');
                            hapusPaket(id);
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
        function hapusPaket(packageId) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.photo-packages.destroy', '') }}/' + packageId;
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
