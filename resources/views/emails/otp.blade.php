<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Aktivasi Akun - MONITA</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #0f172a; color: #334155;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #0f172a; padding: 40px 10px;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 540px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 12px 30px rgba(0,0,0,0.35);">
                    
                    <!-- HEADER BRANDING -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); padding: 35px 20px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: 1px;">MONITA</h1>
                            <p style="color: #bfdbfe; margin: 6px 0 0 0; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; font-weight: 600;">Monitoring Internship Attendance</p>
                        </td>
                    </tr>

                    <!-- MAIN BODY -->
                    <tr>
                        <td style="padding: 40px 30px; text-align: center;">
                            <h2 style="color: #0f172a; font-size: 22px; font-weight: 800; margin: 0 0 12px 0;">
                                @if($type === 'registrasi_admin')
                                    Verifikasi Registrasi Admin Instansi
                                @elseif($type === 'aktivasi_peserta')
                                    Aktivasi Akun Peserta Magang
                                @elseif($type === 'aktivasi_pembimbing')
                                    Aktivasi Akun Pembimbing Instansi
                                @else
                                    Kode Verifikasi OTP Akun
                                @endif
                            </h2>

                            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 25px 0;">
                                Halo! Akun Anda telah didaftarkan di platform <strong>MONITA</strong>. Silakan gunakan 6-digit kode verifikasi di bawah ini untuk mengaktifkan akun dan mengatur password Anda:
                            </p>

                            <!-- OTP HIGHLIGHT BOX -->
                            <div style="background-color: #f8fafc; border: 2px dashed #3b82f6; border-radius: 18px; padding: 22px; margin: 0 auto 30px auto; max-width: 320px;">
                                <span style="font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: 800; color: #1d4ed8; letter-spacing: 8px; display: block;">
                                    {{ $otpCode }}
                                </span>
                            </div>

                            <!-- DIRECT CTA BUTTON -->
                            @php
                                $verifyUrl = url('/verify-otp?email=' . urlencode($email) . '&type=' . urlencode($type));
                            @endphp

                            <div style="margin-bottom: 30px;">
                                <a href="{{ $verifyUrl }}" target="_blank" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 12px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);">
                                    🚀 Klik Di Sini Untuk Verifikasi & Atur Password
                                </a>
                            </div>

                            <!-- HOW IT WORKS INFO -->
                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 16px; text-align: left; margin-bottom: 25px;">
                                <p style="color: #166534; font-size: 12px; font-weight: 700; margin: 0 0 6px 0;">
                                    📌 Langkah Aktivasi:
                                </p>
                                <ol style="color: #15803d; font-size: 12px; line-height: 1.6; margin: 0; padding-left: 18px;">
                                    <li>Klik tombol biru di atas atau buka halaman verifikasi di web.</li>
                                    <li>Masukkan kode OTP <strong style="font-family: monospace; font-size: 13px;">{{ $otpCode }}</strong>.</li>
                                    <li>Buat password rahasia baru untuk login akun Anda.</li>
                                    <li>Selesai! Anda dapat langsung masuk ke dashboard sistem.</li>
                                </ol>
                            </div>

                            <p style="color: #e11d48; font-size: 12px; font-weight: 600; margin: 0 0 10px 0;">
                                ⚠️ Kode OTP ini bersifat rahasia dan berlaku selama 15 menit.
                            </p>

                            <p style="color: #94a3b8; font-size: 11px; margin: 0; line-height: 1.5;">
                                Jika tombol di atas tidak dapat diklik, salin dan buka tautan berikut di browser Anda:<br>
                                <a href="{{ $verifyUrl }}" style="color: #3b82f6; word-break: break-all;">{{ $verifyUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px; text-align: center;">
                            <p style="color: #94a3b8; font-size: 11px; margin: 0;">
                                &copy; {{ date('Y') }} MONITA System. Hak Cipta Dilindungi.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
