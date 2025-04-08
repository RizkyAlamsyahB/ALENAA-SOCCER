<!-- resources/views/emails/membership/renewal-success.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpanjangan Membership Berhasil - Alena Soccer</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #2A2A2A;
            margin: 0;
            padding: 0;
            background-color: #f6f8fd;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            height: 50px;
        }
        .brand-name {
            font-size: 24px;
            font-weight: bold;
            color: #9E0620;
        }
        .brand-name span {
            color: #2A2A2A;
        }
        .greeting {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #16a34a;
        }
        .content {
            margin-bottom: 30px;
            text-align: center;
        }
        .details-button {
            display: inline-block;
            background-color: #9E0620;
            color: white !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            margin: 20px 0;
        }
        .details-table {
            width: 100%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        .details-table th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            color: #64748b;
        }
        .details-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }
        .help-text {
            margin-top: 20px;
            font-size: 14px;
            color: #64748b;
            text-align: center;
        }
        .stats-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .stats-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stats-number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #16a34a;
        }
        .stats-label {
            display: block;
            font-size: 14px;
            color: #64748b;
        }
        .signature {
            text-align: center;
            margin-top: 40px;
            color: #666;
            font-weight: 500;
        }
        .success-box {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: left;
        }
        .success-title {
            color: #16a34a;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .session-list {
            list-style-type: none;
            padding: 0;
            margin: 20px 0;
        }
        .session-list li {
            background-color: #f8f9fa;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 6px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">


            <!-- Content -->
            <div class="greeting">Perpanjangan Membership Berhasil!</div>

            <div class="content">
                <p>Halo {{ $data['user']->name }},</p>
                <p>Selamat! Pembayaran perpanjangan membership Anda telah berhasil diproses. Membership Anda telah diperpanjang dan jadwal baru sudah disiapkan.</p>

                <!-- Stats -->
                <table class="stats-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="48%" style="padding-right: 10px;">
                            <div class="stats-item">
                                <span class="stats-number">{{ $data['subscription']->membership->name }}</span>
                                <span class="stats-label">Paket Membership</span>
                            </div>
                        </td>
                        <td width="48%" style="padding-left: 10px;">
                            <div class="stats-item">
                                <span class="stats-number">{{ \Carbon\Carbon::parse($data['subscription']->end_date)->format('d M Y') }}</span>
                                <span class="stats-label">Aktif Hingga</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="success-box">
                    <div class="success-title">Perpanjangan Berhasil!</div>
                    <p>Membership Anda telah diperpanjang dan akan tetap aktif hingga tanggal {{ \Carbon\Carbon::parse($data['subscription']->end_date)->format('d F Y') }}.</p>
                </div>

                <h3 style="margin-top: 30px;">Jadwal Membership Anda</h3>

                <h3 style="margin-top: 30px;">Jadwal Membership Anda</h3>

                <p>Berikut adalah jadwal untuk periode membership yang baru:</p>

                @if(isset($data['subscription']->sessions) && count($data['subscription']->sessions) > 0)
                <ul class="session-list">
                    @php
                        // Ambil tanggal renewal terakhir
                        $renewalDate = $data['subscription']->last_payment_date ?? \Carbon\Carbon::now();

                        // Filter hanya sesi yang dibuat setelah tanggal perpanjangan terakhir
                        $newSessions = $data['subscription']->sessions->filter(function($session) use ($renewalDate) {
                            return \Carbon\Carbon::parse($session->created_at)->gt($renewalDate->subMinutes(5));
                        })->sortBy('session_number');
                    @endphp

                    @foreach($newSessions as $session)
                    <li>
                        Sesi {{ $session->session_number }}:
                        {{ \Carbon\Carbon::parse($session->start_time)->format('l, d M Y') }},
                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                    </li>
                    @endforeach
                </ul>
                @else
                <p>Jadwal akan muncul di halaman detail membership Anda.</p>
                @endif

                <center>
                    <a href="{{ route('user.membership.subscription-detail', ['id' => $data['subscription']->id]) }}" class="details-button">
                        Lihat Detail Membership
                    </a>
                </center>

                <div class="help-text">
                    <p>Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi tim dukungan kami.</p>
                </div>
            </div>

            <!-- Signature -->
            <div class="signature">
                <p>Salam Olahraga,<br>Tim Alena Soccer</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>© {{ date('Y') }} Alena Soccer. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
