<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto Sudah Siap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #17a2b8;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
        }
        .booking-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .label {
            font-weight: bold;
            color: #495057;
        }
        .value {
            color: #212529;
        }
        .gallery-link {
            background-color: #e7f3ff;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .photographer-notes {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📸 Galeri Foto Sudah Siap!</h1>
        </div>

        <p>Halo <strong>{{ $user->name }}</strong>,</p>

        <p>Kabar gembira! Fotografer telah menyelesaikan editing foto dari sesi pemotretan Anda. Galeri foto Anda sudah siap untuk dilihat dan diunduh!</p>

        <div class="booking-details">
            <div class="detail-row">
                <span class="label">ID Booking:</span>
                <span class="value">#{{ $booking->id }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Fotografer:</span>
                <span class="value">{{ $photographer->user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Tanggal Sesi:</span>
                <span class="value">{{ $formattedDate }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value">Selesai - Foto Sudah Dikirim</span>
            </div>
        </div>

        <div class="gallery-link">
            <h3>🎨 Akses Galeri Foto Anda</h3>
            <p>Klik tombol di bawah ini untuk melihat dan mengunduh foto-foto Anda:</p>
            <a href="{{ $galleryLink }}" class="btn" target="_blank">
                🔗 Buka Galeri Foto
            </a>
            <p style="font-size: 12px; color: #6c757d; margin-top: 10px;">
                Link: <a href="{{ $galleryLink }}" target="_blank">{{ $galleryLink }}</a>
            </p>
        </div>

        @if($photographerNotes)
        <div class="photographer-notes">
            <h3>💬 Pesan dari Fotografer:</h3>
            <p>{{ $photographerNotes }}</p>
        </div>
        @endif

        <h3>📝 Informasi Penting:</h3>
        <ul>
            <li><strong>Kualitas Tinggi:</strong> Semua foto tersedia dalam resolusi tinggi</li>
            <li><strong>Masa Akses:</strong> Link galeri akan aktif selama 30 hari dari tanggal pengiriman</li>
            <li><strong>Unduh Semua:</strong> Anda dapat mengunduh semua foto sekaligus atau satu per satu</li>
            <li><strong>Berbagi:</strong> Link dapat dibagikan kepada keluarga dan teman</li>
        </ul>

        <h3>🆘 Butuh Bantuan?</h3>
        <p>Jika Anda mengalami kesulitan mengakses galeri atau memiliki pertanyaan lain, silakan hubungi fotografer Anda:</p>
        <ul>
            <li><strong>Nama:</strong> {{ $photographer->user->name }}</li>
            <li><strong>Email:</strong> {{ $photographer->user->email }}</li>
            @if($photographer->phone)
            <li><strong>Telepon:</strong> {{ $photographer->phone }}</li>
            @endif
        </ul>

        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin: 20px 0; text-align: center;">
            <p style="margin: 0; color: #155724; font-weight: bold;">
                🌟 Terima kasih telah mempercayakan momen spesial Anda kepada kami!
            </p>
        </div>

        <div class="footer">
            <p>Kami harap Anda puas dengan hasil foto yang telah dibuat.</p>
            <p>Jangan lupa untuk memberikan review dan rating untuk fotografer Anda!</p>
            <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>
