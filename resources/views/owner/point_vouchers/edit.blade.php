@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Voucher Poin</h3>
                <p class="text-subtitle text-muted">Ubah informasi voucher poin yang sudah ada.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.point_vouchers.index') }}">Data Voucher Poin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Voucher Poin</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Edit Voucher Poin</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.point_vouchers.update', $pointVoucher->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="code" class="form-label">Kode Voucher <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $pointVoucher->code) }}" required placeholder="Contoh: POINT500OFF">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kode voucher harus unik dan akan digunakan pelanggan saat penukaran poin.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Voucher <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pointVoucher->name) }}" required placeholder="Contoh: 500.000 POINT VOUCHER">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="discount_type" class="form-label">Tipe Diskon <span class="text-danger">*</span></label>
                                <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" required>
                                    <option value="" disabled>Pilih Tipe Diskon</option>
                                    <option value="percentage" {{ old('discount_type', $pointVoucher->discount_type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                    <option value="fixed" {{ old('discount_type', $pointVoucher->discount_type) == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                                </select>
                                @error('discount_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="discount_value" class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="valuePrefix">Rp</span>
                                    <input type="number" class="form-control @error('discount_value') is-invalid @enderror" id="discount_value" name="discount_value" value="{{ old('discount_value', $pointVoucher->discount_value) }}" required min="0" step="0.01">
                                    <span class="input-group-text" id="valueSuffix" style="display: none;">%</span>
                                </div>
                                @error('discount_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="valueHelp">Masukkan nilai numerik tanpa titik atau koma.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="points_required" class="form-label">Poin Dibutuhkan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('points_required') is-invalid @enderror" id="points_required" name="points_required" value="{{ old('points_required', $pointVoucher->points_required) }}" required min="1">
                                @error('points_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Jumlah poin yang dibutuhkan untuk menukarkan voucher ini.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="min_order" class="form-label">Minimum Order <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('min_order') is-invalid @enderror" id="min_order" name="min_order" value="{{ old('min_order', $pointVoucher->min_order) }}" required min="0">
                                </div>
                                @error('min_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nilai minimum order untuk menggunakan voucher ini.</small>
                            </div>

                            <div class="form-group mb-3" id="maxDiscountGroup">
                                <label for="max_discount" class="form-label">Maksimum Diskon</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('max_discount') is-invalid @enderror" id="max_discount" name="max_discount" value="{{ old('max_discount', $pointVoucher->max_discount) }}" min="0">
                                </div>
                                @error('max_discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maksimum jumlah diskon yang dapat diberikan (Opsional, hanya untuk diskon persentase).</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="applicable_to" class="form-label">Berlaku Untuk <span class="text-danger">*</span></label>
                                <select class="form-select @error('applicable_to') is-invalid @enderror" id="applicable_to" name="applicable_to" required>
                                    <option value="all" {{ old('applicable_to', $pointVoucher->applicable_to) == 'all' ? 'selected' : '' }}>Semua Layanan</option>
                                    <option value="field_booking" {{ old('applicable_to', $pointVoucher->applicable_to) == 'field_booking' ? 'selected' : '' }}>Booking Lapangan</option>
                                    <option value="rental_item" {{ old('applicable_to', $pointVoucher->applicable_to) == 'rental_item' ? 'selected' : '' }}>Rental Peralatan</option>
                                    <option value="membership" {{ old('applicable_to', $pointVoucher->applicable_to) == 'membership' ? 'selected' : '' }}>Keanggotaan</option>
                                    <option value="photographer" {{ old('applicable_to', $pointVoucher->applicable_to) == 'photographer' ? 'selected' : '' }}>Fotografer</option>
                                </select>
                                @error('applicable_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="usage_limit" class="form-label">Batas Penggunaan Keseluruhan</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $pointVoucher->usage_limit) }}" min="1" placeholder="Tidak terbatas">
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Berapa kali voucher ini dapat ditukarkan secara keseluruhan? Kosongkan jika tidak terbatas.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $pointVoucher->start_date ? date('Y-m-d\TH:i', strtotime($pointVoucher->start_date)) : '') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="end_date" class="form-label">Tanggal Berakhir</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $pointVoucher->end_date ? date('Y-m-d\TH:i', strtotime($pointVoucher->end_date)) : '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $pointVoucher->is_active) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                                <small class="text-muted">Voucher poin hanya akan berlaku jika aktif.</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $pointVoucher->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('owner.point_vouchers.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk memperbarui tampilan input nilai berdasarkan tipe diskon
            function updateValueInput() {
                var type = $('#discount_type').val();

                if (type === 'percentage') {
                    $('#valuePrefix').hide();
                    $('#valueSuffix').show();
                    $('#valueHelp').text('Masukkan nilai persentase (0-100).');
                    $('#maxDiscountGroup').show();
                } else if (type === 'fixed') {
                    $('#valuePrefix').show();
                    $('#valueSuffix').hide();
                    $('#valueHelp').text('Masukkan nilai numerik tanpa titik atau koma.');
                    $('#maxDiscountGroup').hide();
                } else {
                    $('#valuePrefix').show();
                    $('#valueSuffix').hide();
                    $('#maxDiscountGroup').hide();
                }
            }

            // Panggil fungsi saat halaman dimuat dan saat tipe diskon berubah
            updateValueInput();
            $('#discount_type').change(updateValueInput);
        });
    </script>
@endsection
