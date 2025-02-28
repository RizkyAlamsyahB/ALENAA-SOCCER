@extends('layouts.admin')

@section('title', 'Detail Item Sewa')
@section('breadcrumb', 'Detail Item Sewa')
@section('header-title', 'Detail Item Sewa')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Informasi Item Sewa</h4>

                <div class="row">
                    <div class="col-md-4">
                        @if($rentalItem->image)
                            <img src="{{ asset('storage/' . $rentalItem->image) }}" alt="{{ $rentalItem->name }}" class="img-fluid rounded">
                        @else
                            <div class="alert alert-info">Tidak ada gambar item</div>
                        @endif

                        <div class="mt-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Stok Ketersediaan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h3>{{ $rentalItem->stock_available }} / {{ $rentalItem->stock_total }}</h3>
                                        <p>Item tersedia dari total</p>
                                    </div>

                                    @php
                                        $percentage = ($rentalItem->stock_total > 0) ? ($rentalItem->stock_available / $rentalItem->stock_total) * 100 : 0;

                                        if ($percentage <= 20) {
                                            $progressClass = 'bg-danger';
                                            $statusText = 'Hampir Habis';
                                        } elseif ($percentage <= 50) {
                                            $progressClass = 'bg-warning';
                                            $statusText = 'Terbatas';
                                        } else {
                                            $progressClass = 'bg-success';
                                            $statusText = 'Tersedia';
                                        }
                                    @endphp

                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                             style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}"
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($percentage, 0) }}%
                                        </div>
                                    </div>
                                    <p class="text-center">
                                        <span class="badge {{ $progressClass }}">{{ $statusText }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">ID</th>
                                        <td>{{ $rentalItem->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Item</th>
                                        <td>{{ $rentalItem->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>
                                            @php
                                                $badges = [
                                                    'ball' => 'bg-primary',
                                                    'jersey' => 'bg-info',
                                                    'shoes' => 'bg-warning',
                                                    'other' => 'bg-secondary'
                                                ];

                                                $badge = isset($badges[$rentalItem->category]) ? $badges[$rentalItem->category] : 'bg-secondary';
                                                $categoryLabels = [
                                                    'ball' => 'Bola',
                                                    'jersey' => 'Jersey',
                                                    'shoes' => 'Sepatu',
                                                    'other' => 'Lainnya'
                                                ];
                                                $categoryLabel = isset($categoryLabels[$rentalItem->category]) ? $categoryLabels[$rentalItem->category] : ucfirst($rentalItem->category);
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $categoryLabel }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Harga Sewa</th>
                                        <td>Rp {{ number_format($rentalItem->rental_price, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi</th>
                                        <td>{{ $rentalItem->condition ?? 'Tidak ada informasi kondisi' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{{ $rentalItem->description ?? 'Tidak ada deskripsi' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($rentalItem->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Pada</th>
                                        <td>{{ $rentalItem->created_at->format('d F Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Terakhir Diperbarui</th>
                                        <td>{{ $rentalItem->updated_at->format('d F Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.rental-items.edit', $rentalItem->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.rental-items.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-danger" id="deleteBtn" data-id="{{ $rentalItem->id }}">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
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

        // Konfirmasi hapus
        $('#deleteBtn').on('click', function() {
            var itemId = $(this).data('id');

            toastr.warning(
                `<div>
                    <p>Apakah Anda yakin ingin menghapus item sewa ini?</p>
                    <p class="text-danger">Perhatian: Item yang sedang disewa tidak dapat dihapus.</p>
                    <button class="btn btn-danger btn-sm" id="confirmDelete" style="margin-right:10px;">Ya, Hapus!</button>
                    <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                </div>`,
                'Konfirmasi Hapus', {
                    closeButton: true,
                    onShown: function() {
                        $('#confirmDelete').on('click', function() {
                            hapusItem(itemId);
                        });

                        $('#cancelDelete').on('click', function() {
                            toastr.clear();
                        });
                    }
                }
            );
        });

        function hapusItem(itemId) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.rental-items.destroy', '') }}/' + itemId;
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
