@extends('layouts.admin')

@section('title', 'Detail Paket Foto')
@section('breadcrumb', 'Detail Paket Foto')
@section('header-title', 'Detail Paket Foto')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Informasi Paket Foto</h4>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 class="card-title">{{ $photoPackage->name }}</h3>
                                <div class="pricing-header">
                                    <h1 class="display-4 text-primary">Rp {{ number_format($photoPackage->price, 0, ',', '.') }}</h1>
                                </div>
                                <hr>
                                <div class="package-features">
                                    <p><i class="fa fa-clock text-primary"></i> Durasi: {{ $photoPackage->formatted_duration }}</p>
                                    <p><i class="fa fa-camera text-primary"></i> {{ $photoPackage->number_of_photos }} Foto</p>
                                    <p>
                                        <i class="fa fa-{{ $photoPackage->includes_editing ? 'check text-success' : 'times text-danger' }}"></i>
                                        {{ $photoPackage->includes_editing ? 'Termasuk Editing' : 'Tanpa Editing' }}
                                    </p>
                                  
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Deskripsi Paket</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $photoPackage->description }}</p>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Informasi Tambahan</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%">ID</th>
                                                <td>{{ $photoPackage->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Dibuat Pada</th>
                                                <td>{{ $photoPackage->created_at->format('d F Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Terakhir Diperbarui</th>
                                                <td>{{ $photoPackage->updated_at->format('d F Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.photo-packages.edit', $photoPackage->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.photo-packages.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-danger" id="deleteBtn" data-id="{{ $photoPackage->id }}">
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
            var packageId = $(this).data('id');

            toastr.warning(
                `<div>
                    <p>Apakah Anda yakin ingin menghapus paket foto ini?</p>
                    <button class="btn btn-danger btn-sm" id="confirmDelete" style="margin-right:10px;">Ya, Hapus!</button>
                    <button class="btn btn-secondary btn-sm" id="cancelDelete">Batal</button>
                </div>`,
                'Konfirmasi Hapus', {
                    closeButton: true,
                    onShown: function() {
                        $('#confirmDelete').on('click', function() {
                            hapusPackage(packageId);
                        });

                        $('#cancelDelete').on('click', function() {
                            toastr.clear();
                        });
                    }
                }
            );
        });

        function hapusPackage(packageId) {
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
