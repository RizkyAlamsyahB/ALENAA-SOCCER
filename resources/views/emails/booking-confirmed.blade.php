<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Dikonfirmasi</title>
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
            background-color: #28a745;
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>✅ Booking Dikonfirmasi!</h1>
        </div>

        <p>Halo <strong>{{ $user->name }}</strong>,</p>

        <p>Kabar baik! Booking fotografer Anda telah dikonfirmasi oleh fotografer. Berikut detail booking Anda:</p>

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
                <span class="label">Tanggal:</span>
                <span class="value">{{ $formattedDate }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Waktu:</span>
                <span class="value">{{ $formattedTimeStart }} - {{ $formattedTimeEnd }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Harga:</span>
                <span class="value">Rp {{ number_format($booking->price, 0, ',', '.') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value">Dikonfirmasi</span>
            </div>
            @if($booking->notes)
            <div class="detail-row">
                <span class="label">Catatan:</span>
                <span class="value">{{ $booking->notes }}</span>
            </div>
            @endif
        </div>

        <h3>🎯 Langkah Selanjutnya:</h3>
        <ul>
            <li>Pastikan Anda hadir tepat waktu pada jadwal yang telah ditentukan</li>
            <li>Fotografer akan menghubungi Anda jika ada informasi tambahan</li>
            <li>Setelah sesi foto selesai, Anda akan menerima link galeri foto melalui email</li>
        </ul>

        <h3>📞 Kontak Fotografer:</h3>
        <p>Jika Anda memiliki pertanyaan, silakan hubungi fotografer Anda:</p>
        <ul>
            <li><strong>Nama:</strong> {{ $photographer->user->name }}</li>
            <li><strong>Email:</strong> {{ $photographer->user->email }}</li>
            @if($photographer->phone)
            <li><strong>Telepon:</strong> {{ $photographer->phone }}</li>
            @endif
        </ul>

        <div class="footer">
            <p>Terima kasih telah menggunakan layanan kami!</p>
            <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>
