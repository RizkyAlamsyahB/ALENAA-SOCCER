<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foto Hasil Sesi Fotografi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .booking-info h3 {
            margin-top: 0;
            color: #495057;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .download-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .download-button:hover {
            transform: translateY(-2px);
            color: white;
        }
        .photographer-notes {
            background: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .camera-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="camera-icon">📸</div>
            <h1>Foto Anda Sudah Siap!</h1>
            <p>Hasil sesi fotografi telah selesai diedit</p>
        </div>

        <div class="content">
            <p>Halo <strong>{{ $userName }}</strong>,</p>

            <p>Kabar baik! Fotografer <strong>{{ $photographerName }}</strong> telah menyelesaikan editing foto dari sesi fotografi Anda.</p>

            <div class="booking-info">
                <h3>📅 Detail Booking</h3>
                <div class="info-row">
                    <span><strong>Tanggal:</strong></span>
                    <span>{{ $bookingDate }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Waktu:</strong></span>
                    <span>{{ $bookingTime }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Fotografer:</strong></span>
                    <span>{{ $photographerName }}</span>
                </div>
            </div>

            @if($photographerNotes)
            <div class="photographer-notes">
                <h4>💬 Pesan dari Fotografer:</h4>
                <p>{{ $photographerNotes }}</p>
            </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $galleryLink }}" class="download-button">
                    🖼️ Lihat & Download Foto
                </a>
            </div>

            <p><strong>Cara mengunduh:</strong></p>
            <ol>
                <li>Klik tombol "Lihat & Download Foto" di atas</li>
                <li>Anda akan diarahkan ke galeri online</li>
                <li>Pilih foto yang ingin diunduh</li>
                <li>Download dalam kualitas tinggi</li>
            </ol>

            <p style="color: #dc3545; font-weight: bold;">⚠️ Penting: Link ini akan aktif selama 30 hari. Pastikan untuk mengunduh semua foto sebelum link kedaluwarsa.</p>

            <p>Terima kasih telah menggunakan layanan fotografi kami. Semoga Anda puas dengan hasilnya!</p>
        </div>

        <div class="footer">
            <p>© 2025 ALENAA Soccer. Semua hak dilindungi.</p>
            <p>Email ini dikirim otomatis, mohon jangan membalas.</p>
        </div>
    </div>
</body>
</html>
