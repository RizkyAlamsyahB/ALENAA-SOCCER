@extends('layouts.admin')

@section('title', 'Detail Produk')
@section('breadcrumb', 'Detail Produk')
@section('header-title', 'Detail Produk')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Informasi Produk</h4>

                <div class="row">
                    <div class="col-md-4">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                        @else
                            <div class="alert alert-info">Tidak ada gambar produk</div>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">ID</th>
                                        <td>{{ $product->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <td>{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>
                                            @php
                                                $badges = [
                                                    'food' => 'bg-primary',
                                                    'beverage' => 'bg-info',
                                                    'equipment' => 'bg-warning',
                                                    'other' => 'bg-secondary'
                                                ];

                                                $badge = isset($badges[$product->category]) ? $badges[$product->category] : 'bg-secondary';
                                                $categoryLabels = [
                                                    'food' => 'Makanan',
                                                    'beverage' => 'Minuman',
                                                    'equipment' => 'Peralatan',
                                                    'other' => 'Lainnya'
                                                ];
                                                $categoryLabel = isset($categoryLabels[$product->category]) ? $categoryLabels[$product->category] : ucfirst($product->category);
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $categoryLabel }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Harga</th>
                                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Stok</th>
                                        <td>{{ $product->stock }}</td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{{ $product->description ?? 'Tidak ada deskripsi' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Pada</th>
                                        <td>{{ $product->created_at->format('d F Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Terakhir Diperbarui</th>
                                        <td>{{ $product->updated_at->format('d F Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-danger" id="deleteBtn" data-id="{{ $product->id }}">
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
            var productId = $(this).data('id');

            toastr.warning(
                `<div>
                    <p>Apakah Anda yakin ingin menghapus produk ini?</p>
                    <button class="btn btn-danger btn-sm" id="confirmDelete" style="margin-right:10px;">Ya, Hapus!</button>
                    <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                </div>`,
                'Konfirmasi Hapus', {
                    closeButton: true,
                    onShown: function() {
                        $('#confirmDelete').on('click', function() {
                            hapusProduk(productId);
                        });

                        $('#cancelDelete').on('click', function() {
                            toastr.clear();
                        });
                    }
                }
            );
        });

        function hapusProduk(productId) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.products.destroy', '') }}/' + productId;
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
