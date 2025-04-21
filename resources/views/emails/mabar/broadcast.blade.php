<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $messageSubject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #777;
            font-size: 13px;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            background-color: #d00f25;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            margin-top: 15px;
            font-weight: bold;
        }
        .event-info {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .event-info p {
            margin: 5px 0;
        }
        h2 {
            color: #d00f25;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Alena Soccer - Open Mabar</h2>
        </div>

        <div class="content">
            <p>Halo,</p>

            <p>Anda menerima pesan ini karena Anda terdaftar sebagai peserta Open Mabar <strong>{{ $openMabar->title }}</strong>.</p>

            <p><strong>Pesan dari {{ $sender->name }}:</strong></p>

            <div style="background: #f5f5f5; padding: 15px; border-radius: 6px; border-left: 4px solid #d00f25;">
                {!! nl2br(e($messageContent)) !!}
            </div>

            <div class="event-info">
                <h3>Detail Event:</h3>
                {!! nl2br(e($eventDetails)) !!}
            </div>

            <p>Jika Anda memiliki pertanyaan, silakan balas langsung ke email ini atau hubungi penyelenggara event di {{ $sender->email }} atau {{ $sender->phone_number ?? 'nomor kontak tidak tersedia' }}.</p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('user.mabar.show', $openMabar->id) }}" class="button">
                    Lihat Detail Event
                </a>
            </div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Alena Soccer. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, mohon jangan membalas langsung ke alamat ini.</p>
        </div>
    </div>
</body>
</html>
