<?php

namespace App\Models;

use App\Models\Instansi;
use App\Models\Laporan;
use App\Models\Divisi;
use App\Models\Teknisi;
use App\Models\Perizinan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'foto_profil',
        'password',
        'role',
        'tipe_penempatan',
        'instansi_id',
        'divisi_id',
        'teknisi_id',
        'pembimbing_id',
        'nim',
        'no_hp',
        'nomor_telepon',
        'alamat',
        'jabatan',
        'nip',
        'asal_sekolah_pt',
        'nim_nisn',
        'is_active',
        'otp_verified_at',
    ];

    public function isLapangan(): bool
    {
        return $this->tipe_penempatan === 'lapangan' || ($this->instansi && $this->instansi->jenis_instansi === 'lapangan');
    }

    public function isKantor(): bool
    {
        return !$this->isLapangan();
    }

    public function getFotoProfilUrlAttribute(): ?string
    {
        if ($this->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto_profil)) {
            return asset('storage/' . $this->foto_profil);
        }
        return null;
    }

    public function getNimAttribute($value)
    {
        return $value ?: ($this->attributes['nim_nisn'] ?? null);
    }

    public function getNimNisnAttribute($value)
    {
        return $value ?: ($this->attributes['nim'] ?? null);
    }

    public function getNoHpAttribute($value)
    {
        return $value ?: ($this->attributes['nomor_telepon'] ?? null);
    }

    public function getNomorTeleponAttribute($value)
    {
        return $value ?: ($this->attributes['no_hp'] ?? null);
    }

    /*
    |--------------------------------------------------------------------------
    | HIDDEN
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_verified_at'   => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI INSTANSI
    |--------------------------------------------------------------------------
    */

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI DIVISI
    |--------------------------------------------------------------------------
    */

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI TEKNISI
    |--------------------------------------------------------------------------
    */

    public function teknisi()
    {
        return $this->belongsTo(Teknisi::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI LAPORAN
    |--------------------------------------------------------------------------
    */

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI PERIZINAN
    |--------------------------------------------------------------------------
    */

    public function perizinans()
    {
        return $this->hasMany(Perizinan::class, 'user_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI ADMIN YANG MENYETUJUI IZIN
    |--------------------------------------------------------------------------
    */

    public function izinDisetujui()
    {
        return $this->hasMany(Perizinan::class, 'disetujui_oleh', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI PEMBIMBING & PESERTA MAGANG
    |--------------------------------------------------------------------------
    */

    // Peserta belongsTo Pembimbing Instansi (Langsung via FK pembimbing_id)
    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    // Pembimbing hasMany Peserta Bimbingan (Langsung via FK pembimbing_id)
    public function bimbingan()
    {
        return $this->hasMany(User::class, 'pembimbing_id');
    }

    // Relasi pivot table pembimbing_peserta untuk kompatibilitas modul laporan & dashboard
    public function pesertaBimbingan()
    {
        return $this->belongsToMany(User::class, 'pembimbing_peserta', 'pembimbing_id', 'peserta_id')
            ->withTimestamps();
    }
}