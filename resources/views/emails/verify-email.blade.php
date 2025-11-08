@component('mail::message')
{{-- Logo --}}


{{-- Greeting --}}
<h1 class="greeting">Verifikasi Email Anda</h1>

{{-- Content --}}
<p style="text-align: center; margin-bottom: 30px;">
    Terima kasih telah mendaftar di Alena Soccer. Untuk mengaktifkan akun Anda dan mengakses semua fitur, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah.
</p>

{{-- Stats --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
    <tr>
        <td width="48%" style="padding-right: 10px;">
            <div class="stats-item">
                <span class="stats-number">500+</span>
                <span class="stats-label">Pemain Aktif</span>
            </div>
        </td>
        <td width="48%" style="padding-left: 10px;">
            <div class="stats-item">
                <span class="stats-number">4.9</span>
                <span class="stats-label">Rating Pengguna</span>
            </div>
        </td>
    </tr>
</table>

{{-- Action Button --}}
@component('mail::button', ['url' => $url, 'color' => 'primary'])
Verifikasi Email Sekarang
@endcomponent

<p style="text-align: center; font-size: 14px; color: #64748b; margin-top: 30px;">
    Jika Anda tidak membuat akun ini, tidak perlu melakukan tindakan lebih lanjut.
</p>

<div style="text-align: center; margin-top: 40px; color: #666; font-weight: 500;">
    <p>Salam Olahraga,<br>Tim Alena Soccer</p>
</div>

@endcomponent
