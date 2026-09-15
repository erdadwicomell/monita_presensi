<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Perizinan;

class Instansi extends Model
{
    protected $table = 'instansis';

    protected $fillable = [
        'nama_instansi',
        'jenis_instansi',
        'no_telp',
        'alamat',
        'latitude',
        'longitude',
        'radius',
        'jam_masuk_mulai',
        'jam_masuk_batas',
        'jam_pulang_mulai',
        'jam_pulang_batas',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI USER
    |--------------------------------------------------------------------------
    */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI PERIZINAN
    |--------------------------------------------------------------------------
    */
    public function perizinans()
    {
        return $this->hasMany(Perizinan::class, 'instansi_id', 'id');
    }
}

