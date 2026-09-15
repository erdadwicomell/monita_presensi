<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'instansi_id',
        'kategori',
        'tingkat_risiko',
        'judul',
        'deskripsi',
        'latitude',
        'longitude',
        'ip_address',
        'user_agent',
        'payload_extra',
    ];

    protected $casts = [
        'payload_extra' => 'array',
        'latitude'      => 'float',
        'longitude'     => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    /**
     * Helper static untuk mencatat audit log
     */
    public static function catat(
        string $kategori,
        string $tingkatRisiko,
        string $judul,
        string $deskripsi,
        ?User $user = null,
        ?float $lat = null,
        ?float $lon = null,
        array $extra = []
    ): self {
        $u = $user ?? auth()->user();
        return self::create([
            'user_id'        => $u?->id,
            'instansi_id'    => $u?->instansi_id,
            'kategori'       => $kategori,
            'tingkat_risiko' => $tingkatRisiko,
            'judul'          => $judul,
            'deskripsi'      => $deskripsi,
            'latitude'       => $lat,
            'longitude'      => $lon,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'payload_extra'  => $extra,
        ]);
    }
}
