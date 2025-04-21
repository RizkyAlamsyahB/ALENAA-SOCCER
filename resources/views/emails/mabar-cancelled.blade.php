<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Mabar Dibatalkan</title>
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
        .alert-box {
            background-color: #fff3f3;
            border-left: 4px solid #d00f25;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Alena Soccer - Pembatalan Open Mabar</h2>
        </div>

        <div class="content">
            <p>Halo {{ $participant->user->name ?? 'Peserta' }},</p>

            <div class="alert-box">
                <p><strong>Pemberitahuan Penting:</strong> Open Mabar berikut telah dibatalkan oleh penyelenggara.</p>
            </div>

            <div class="event-info">
                <h3>Detail Event yang Dibatalkan:</h3>
                <p><strong>Judul:</strong> {{ $openMabar->title ?? 'Open Mabar' }}</p>

                @if(isset($openMabar->start_time))
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($openMabar->start_time)->format('d M Y') }}</p>
                <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($openMabar->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($openMabar->end_time)->format('H:i') }}</p>
                @endif

                @if(isset($openMabar->fieldBooking) && isset($openMabar->fieldBooking->field))
                <p><strong>Lokasi:</strong> {{ $openMabar->fieldBooking->field->name }}</p>
                @endif

                @if(!empty($cancellationReason))
                <p><strong>Alasan Pembatalan:</strong> {{ $cancellationReason }}</p>
                @endif
            </div>

            @if(!empty($refundInfo))
            <div class="event-info">
                <h3>Informasi Pengembalian Dana:</h3>
                <p>{!! nl2br(e($refundInfo)) !!}</p>
            </div>
            @endif

            <p>Jika Anda memiliki pertanyaan lebih lanjut mengenai pembatalan ini, silakan hubungi penyelenggara event:</p>

            @if(isset($organizer))
            <p><strong>{{ $organizer->name }}</strong> ({{ $organizer->email ?? 'Email tidak tersedia' }})</p>
            @endif

            <p>Mohon maaf atas ketidaknyamanan ini. Terima kasih atas pengertian Anda.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Alena Soccer. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, mohon jangan membalas langsung ke alamat ini.</p>
        </div>
    </div>
</body>
</html>
