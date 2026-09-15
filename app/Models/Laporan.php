<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'user_id',
        'kegiatan',
        'foto',
        'tanggal',
        'jam',
        'status',
        'catatan_revisi',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected $casts = [
        'tanggal'           => 'date',
        'diverifikasi_pada' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI USER (PESERTA PEMILIK LAPORAN)
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI VERIFIKATOR (PEMBIMBING / ADMIN YANG MEMVERIFIKASI)
    |--------------------------------------------------------------------------
    */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}