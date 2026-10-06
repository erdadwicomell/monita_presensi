<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class OtpVerificationController extends Controller
{
    /**
     * Tampilkan halaman verifikasi OTP
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->email ?? session('otp_email', '');
        $type = $request->type ?? session('otp_type', 'aktivasi_peserta');

        return view('auth.verify_otp', compact('email', 'type'));
    }

    /**
     * Verifikasi kode OTP
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'otp_code' => ['required', 'string', 'size:6'],
            'type'     => ['nullable', 'string'],
        ]);

        $otp = Otp::verifyOtp($request->email, $request->otp_code, $request->type);

        if (!$otp) {
            return back()->withErrors(['otp_code' => 'Kode OTP salah atau telah kadaluwarsa (berlaku 15 menit).'])
                ->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->update([
                'otp_verified_at' => now(),
            ]);
        }

        $activeType = $otp->tipe ?? $request->type;

        // Alur Berdasarkan Tipe
        if ($activeType === 'registrasi_admin') {
            // Login langsung Admin Instansi
            if ($user) {
                Auth::login($user);

                // Cek apakah sudah mengisi data instansi
                if (!$user->instansi_id) {
                    return redirect()->route('admin.onboarding.create')
                        ->with('success', 'Verifikasi OTP berhasil! Silakan lengkapi pendataan instansi Anda.');
                }
            }

            return redirect()->route('dashboard')
                ->with('success', 'Akun berhasil diverifikasi!');
        }

        if (in_array($activeType, ['aktivasi_peserta', 'aktivasi_pembimbing'])) {
            // Arahkan ke form buat password mandiri
            session([
                'set_password_email' => $request->email,
                'set_password_user_id' => $user ? $user->id : null,
            ]);

            return redirect()->route('otp.set-password.form')
                ->with('success', 'Verifikasi OTP berhasil! Silakan atur password akun Anda.');
        }

        return redirect()->route('login')
            ->with('success', 'Verifikasi berhasil! Silakan masuk ke akun Anda.');
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'type'  => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        $otp = Otp::generate($request->email, $request->type, $user ? $user->id : null);

        session([
            'otp_email' => $request->email,
            'otp_type'  => $request->type,
        ]);

        if (isset($otp->mail_sent) && !$otp->mail_sent) {
            return back()->with('info', "Koneksi email SMTP diblokir oleh firewall jaringan WiFi ini. Kode OTP baru Anda: [ {$otp->otp_code} ].");
        }

        return back()->with('info', "Kode OTP baru telah dikirimkan ke alamat email {$request->email}. Silakan periksa Kotak Masuk atau folder Spam Anda.");
    }

    /**
     * Halaman atur password mandiri (Peserta / Pembimbing)
     */
    public function showSetPasswordForm()
    {
        $email = session('set_password_email');

        if (!$email) {
            return redirect()->route('login')
                ->with('error', 'Sesi aktivasi telah berakhir.');
        }

        return view('auth.set_password', compact('email'));
    }

    /**
     * Simpan password mandiri
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $user->update([
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Bersihkan session
        session()->forget(['set_password_email', 'set_password_user_id', 'otp_email']);

        // Login dan arahkan ke dashboard
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Password akun berhasil dibuat! Selamat datang di sistem MONITA.');
    }
}
