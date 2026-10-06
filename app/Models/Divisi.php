<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = [
        'instansi_id',
        'nama_divisi',
        'kode_divisi',
        'kepala_divisi_id',
        'lokasi_ruangan',
        'kuota_maksimal',
        'deskripsi',
        'kepala_divisi',
        'nik_kepala',
        'no_hp_kepala',
    ];

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
    | RELASI KEPALA DIVISI (PEMBIMBING)
    |--------------------------------------------------------------------------
    */
    public function kepalaDivisi()
    {
        return $this->belongsTo(User::class, 'kepala_divisi_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI ANGGOTA PESERTA
    |--------------------------------------------------------------------------
    */
    public function pesertas()
    {
        return $this->hasMany(User::class, 'divisi_id');
    }
}