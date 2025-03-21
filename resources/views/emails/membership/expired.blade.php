<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Telah Kedaluwarsa - Alena Soccer</title>
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
        }
        .content {
            margin-bottom: 30px;
            text-align: center;
        }
        .expired-icon {
            font-size: 60px;
            color: #ef4444;
            margin-bottom: 20px;
        }
        .renew-button {
            display: inline-block;
            background-color: #9E0620;
            color: white !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            margin: 20px 0;
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
            color: #9E0620;
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
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Logo -->
            <div class="logo">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                alt="Alena Soccer Logo">
                <div class="brand-name">Alena<span>Soccer</span></div>
            </div>

            <!-- Content -->
            <div class="greeting">Membership Anda Telah Kedaluwarsa</div>

            <div class="content">
                <div class="expired-icon">⚠️</div>
                <p>Halo {{ $data['user']->name }},</p>
                <p>Kami ingin memberitahukan bahwa membership Anda telah kedaluwarsa karena belum melakukan pembayaran perpanjangan sebelum batas waktu.</p>

                <p>Rincian membership yang kedaluwarsa:</p>
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
                                <span class="stats-number">{{ \Carbon\Carbon::parse($data['subscription']->end_date)->format('d F Y') }}</span>
                                <span class="stats-label">Tanggal Kedaluwarsa</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <p>Jika Anda masih berminat untuk melanjutkan membership, silakan daftar kembali melalui tautan di bawah ini:</p>

                <center>
                    <a href="{{ route('user.membership.index') }}" class="renew-button">
                        Daftar Membership Baru
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
                <p>Jl. Kebon Agung No.1, Keputih, Kec. Sukolilo, Kota Surabaya, Jawa Timur 60111</p>
            </div>
        </div>
    </div>
</body>
</html>
