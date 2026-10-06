<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminRegisterController extends Controller
{
    /**
     * Tampilkan formulir registrasi Admin Instansi
     */
    public function showRegistrationForm()
    {
        return view('auth.register_admin');
    }

    /**
     * Proses registrasi akun Admin Instansi & Generate OTP
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'nomor_telepon'  => ['required', 'string', 'max:25'],
            'alamat'         => ['required', 'string'],
            'jabatan'        => ['required', 'string', 'max:100'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Buat user Admin Instansi
        $user = User::create([
            'name'          => $request->name,
            'nomor_telepon' => $request->nomor_telepon,
            'alamat'        => $request->alamat,
            'jabatan'       => $request->jabatan,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => 'admin_instansi',
            'is_active'     => true,
        ]);

        // Generate OTP Registrasi
        $otp = Otp::generate($user->email, 'registrasi_admin', $user->id);

        // Simpan email di session untuk halaman OTP
        session([
            'otp_email' => $user->email,
            'otp_type'  => 'registrasi_admin',
        ]);

        if (isset($otp->mail_sent) && !$otp->mail_sent) {
            return redirect()->route('otp.verify.form')
                ->with('info', "Koneksi email SMTP diblokir oleh firewall jaringan WiFi ini. Untuk keperluan demo/sidang, gunakan Kode OTP darurat: [ {$otp->otp_code} ].");
        }

        return redirect()->route('otp.verify.form')
            ->with('info', "Kode OTP 6 digit telah dikirimkan ke alamat email {$user->email}. Silakan periksa Kotak Masuk atau folder Spam Anda.");
    }
}
