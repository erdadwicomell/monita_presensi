<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = [

        'nama_divisi',
        'kepala_divisi',
        'nik_kepala',
        'no_hp_kepala',
        'instansi_id'

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
}