<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'otp_code',
        'tipe',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'is_used'    => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate new 6-digit OTP
     */
    public static function generate(string $email, string $tipe, ?int $userId = null): self
    {
        // Nonaktifkan OTP lama yang belum terpakai untuk email & tipe ini
        self::where('email', $email)
            ->where('tipe', $tipe)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $otpCode = sprintf('%06d', random_int(100000, 999999));

        $otp = self::create([
            'user_id'    => $userId,
            'email'      => $email,
            'otp_code'   => $otpCode,
            'tipe'       => $tipe,
            'is_used'    => false,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Kirimkan email OTP ke alamat email penerima
        $otp->mail_sent = true;
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\SendOtpMail($email, $otpCode, $tipe));
        } catch (\Throwable $e) {
            $otp->mail_sent = false;
            $otp->mail_error = $e->getMessage();
            \Illuminate\Support\Facades\Log::warning("Gagal mengirim email OTP ke {$email} (Firewall/Timeout): " . $e->getMessage());
        }

        return $otp;
    }

    /**
     * Validasi OTP
     */
    public static function verifyOtp(string $email, string $code, ?string $tipe = null): ?self
    {
        $query = self::where('email', $email)
            ->where('otp_code', $code)
            ->where('is_used', false)
            ->where('expires_at', '>=', Carbon::now());

        if ($tipe) {
            $query->where('tipe', $tipe);
        }

        $otp = $query->first();

        // Fallback jika tipe tidak spesifik
        if (!$otp) {
            $otp = self::where('email', $email)
                ->where('otp_code', $code)
                ->where('is_used', false)
                ->where('expires_at', '>=', Carbon::now())
                ->first();
        }

        if ($otp) {
            $otp->update(['is_used' => true]);
            return $otp;
        }

        return null;
    }
}
