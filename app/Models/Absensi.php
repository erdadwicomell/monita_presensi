<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Perizinan;

class Absensi extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */
    protected $table = 'absensis';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'id_perizinan',
        'teknisi_id',
        'latitude',
        'longitude',
        'jarak',
        'status',
        'tipe_absensi',
        'tanggal',
        'jam',
        'foto',
        'hmac_signature',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI USER
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI TEKNISI PENDAMPING
    |--------------------------------------------------------------------------
    */
    public function teknisi()
    {
        return $this->belongsTo(Teknisi::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI PERIZINAN
    |--------------------------------------------------------------------------
    */
    public function perizinan()
    {
        return $this->belongsTo(Perizinan::class, 'id_perizinan', 'id_perizinan');
    }

    /*
    |--------------------------------------------------------------------------
    | HMAC SHA-256 SIGNATURE GENERATOR & VERIFICATION
    |--------------------------------------------------------------------------
    */

    /**
     * Generate canonical HMAC SHA-256 signature for attendance record
     *
     * @param array $data
     * @param string|null $secretKey
     * @return string
     */
    public static function generateSignature(array $data, ?string $secretKey = null): string
    {
        $secret = $secretKey ?? config('app.hmac_secret', config('app.key', 'MonitaPresensiSecretKey2026'));

        // Canonical payload: user_id|tanggal|jam|tipe_absensi|latitude|longitude|status
        $payload = implode('|', [
            $data['user_id'] ?? '',
            $data['tanggal'] ?? '',
            $data['jam'] ?? '',
            $data['tipe_absensi'] ?? '',
            $data['latitude'] ?? '',
            $data['longitude'] ?? '',
            $data['status'] ?? '',
        ]);

        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Verifikasi keabsahan data presensi dengan membandingkan signature
     *
     * @param string|null $secretKey
     * @return bool
     */
    public function verifySignature(?string $secretKey = null): bool
    {
        if (empty($this->hmac_signature)) {
            return false;
        }

        $tanggalStr = $this->tanggal instanceof \Carbon\Carbon
            ? $this->tanggal->toDateString()
            : (string) $this->tanggal;

        $expected = self::generateSignature([
            'user_id'      => $this->user_id,
            'tanggal'      => $tanggalStr,
            'jam'          => $this->jam,
            'tipe_absensi' => $this->tipe_absensi,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'status'       => $this->status,
        ], $secretKey);

        return hash_equals($expected, $this->hmac_signature);
    }
}