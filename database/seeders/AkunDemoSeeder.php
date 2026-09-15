<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Instansi;
use Illuminate\Support\Facades\Hash;

class AkunDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pembimbing / Admin Instansi untuk 'Perusahaan' (Instansi peserta 'cantik')
        $instansiPerusahaan = Instansi::where('nama_instansi', 'like', '%Perusahaan%')->first();
        if ($instansiPerusahaan) {
            User::updateOrCreate(
                ['email' => 'admin.perusahaan@monita.test'],
                [
                    'name'        => 'Pembimbing Instansi Perusahaan',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin_instansi',
                    'instansi_id' => $instansiPerusahaan->id,
                ]
            );
        }

        // 2. Akun Pembimbing / Admin Instansi untuk 'Telkom Cabang Bengkalis'
        $instansiTelkom = Instansi::where('nama_instansi', 'like', '%Telkom%')->first();
        if ($instansiTelkom) {
            User::updateOrCreate(
                ['email' => 'admin.telkom@monita.test'],
                [
                    'name'        => 'Pembimbing Telkom Bengkalis',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin_instansi',
                    'instansi_id' => $instansiTelkom->id,
                ]
            );
        }

        // 3. Reset password akun cantik & mput agar mudah login
        User::whereIn('email', ['cantik@gmail.com', 'mput@gmail.com', 'febripunyo@gmail.com'])
            ->update(['password' => Hash::make('password')]);
    }
}
