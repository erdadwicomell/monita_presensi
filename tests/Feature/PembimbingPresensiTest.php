<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Instansi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingPresensiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembimbing_can_view_rekap_presensi_filtered_to_mentored_peserta()
    {
        $instansi = Instansi::create([
            'nama_instansi'    => 'PT Monita Digital',
            'jenis_instansi'   => 'kantor',
            'alamat'           => 'Jl. Digital No. 1',
            'latitude'         => -6.200000,
            'longitude'        => 106.816666,
            'radius'           => 100,
            'jam_masuk_mulai'  => '07:00',
            'jam_masuk_batas'  => '08:30',
            'jam_pulang_mulai' => '16:00',
            'jam_pulang_batas' => '18:00',
        ]);

        $pembimbing = User::create([
            'name'          => 'Pembimbing Budi',
            'email'         => 'pembimbing@test.com',
            'password'      => Hash::make('password123'),
            'role'          => 'pembimbing_instansi',
            'instansi_id'   => $instansi->id,
            'is_active'     => true,
            'nomor_telepon' => '08123456789',
        ]);

        // Peserta A (Bimbingan)
        $pesertaA = User::create([
            'name'          => 'Peserta Magang A',
            'email'         => 'peserta_a@test.com',
            'password'      => Hash::make('password123'),
            'role'          => 'peserta',
            'instansi_id'   => $instansi->id,
            'pembimbing_id' => $pembimbing->id,
            'is_active'     => true,
        ]);

        // Peserta B (Bukan Bimbingan)
        $pesertaB = User::create([
            'name'          => 'Peserta Magang B',
            'email'         => 'peserta_b@test.com',
            'password'      => Hash::make('password123'),
            'role'          => 'peserta',
            'instansi_id'   => $instansi->id,
            'pembimbing_id' => null,
            'is_active'     => true,
        ]);

        // Absensi Peserta A
        Absensi::create([
            'user_id'        => $pesertaA->id,
            'tanggal'        => now()->toDateString(),
            'jam'            => '07:55:00',
            'tipe_absensi'   => 'masuk',
            'status'         => 'hadir',
            'latitude'       => -6.200000,
            'longitude'      => 106.816666,
            'jarak'          => 15.5,
            'hmac_signature' => Absensi::generateSignature([
                'user_id'      => $pesertaA->id,
                'tanggal'      => now()->toDateString(),
                'jam'          => '07:55:00',
                'tipe_absensi' => 'masuk',
                'latitude'     => -6.200000,
                'longitude'    => 106.816666,
                'status'       => 'hadir',
            ]),
        ]);

        // Absensi Peserta B
        Absensi::create([
            'user_id'        => $pesertaB->id,
            'tanggal'        => now()->toDateString(),
            'jam'            => '08:10:00',
            'tipe_absensi'   => 'masuk',
            'status'         => 'telat',
            'latitude'       => -6.200000,
            'longitude'      => 106.816666,
            'jarak'          => 20.0,
            'hmac_signature' => Absensi::generateSignature([
                'user_id'      => $pesertaB->id,
                'tanggal'      => now()->toDateString(),
                'jam'          => '08:10:00',
                'tipe_absensi' => 'masuk',
                'latitude'     => -6.200000,
                'longitude'    => 106.816666,
                'status'       => 'telat',
            ]),
        ]);

        // Login as Pembimbing
        $response = $this->actingAs($pembimbing)->get(route('pembimbing.presensi.index'));

        $response->assertStatus(200);
        $response->assertSee($pesertaA->name);
        $response->assertDontSee($pesertaB->name);
        $response->assertSee('Rekap Presensi Peserta Bimbingan');
    }
}
