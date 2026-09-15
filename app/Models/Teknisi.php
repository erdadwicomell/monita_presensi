<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teknisi extends Model
{
    protected $fillable = [

        'nama',
        'email',
        'no_hp',
        'nik',
        'alamat_kerja',
        'instansi_id'

    ];

    public function getNamaTeknisiAttribute()
    {
        return $this->nama;
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
}