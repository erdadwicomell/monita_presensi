<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Instansi;

class Perizinan extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE & PRIMARY KEY
    |--------------------------------------------------------------------------
    */
    protected $table = 'perizinans';

    protected $primaryKey = 'id_perizinan';

    public $incrementing = true;

    protected $keyType = 'int';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'instansi_id',
        'jenis_izin',
        'tanggal',
        'jam_mulai_izin',
        'jam_selesai_izin',
        'alasan',
        'bukti',
        'status',
        'disetujui_oleh',
        'disetujui_pada',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'tanggal'        => 'date',
        'disetujui_pada' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI USER (PESERTA MAGANG)
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
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
    | RELASI ADMIN YANG MENYETUJUI
    |--------------------------------------------------------------------------
    */
    public function admin()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}