<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Absensi;
use Illuminate\Support\Facades\Hash;

class RekapPresensiExportTest extends TestCase
{
    use RefreshDatabase;

    protected $instansi;
    protected $adminInstansi;
    protected $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instansi = Instansi::create([
            'nama_instansi'     => 'PT Inovasi Digital',
            'jenis_instansi'    => 'kantor',
            'alamat'            => 'Jl. Jenderal Sudirman No 1',
            'latitude'          => -6.2000000,
            'longitude'         => 106.8166667,
            'radius'            => 100,
            'jam_masuk_mulai'   => '07:00:00',
            'jam_masuk_batas'   => '09:00:00',
            'jam_pulang_mulai'  => '16:00:00',
            'jam_pulang_batas'  => '18:00:00',
        ]);

        $this->adminInstansi = User::create([
            'name'              => 'Admin Rekap',
            'email'             => 'admin.rekap@inovasi.test',
            'password'          => Hash::make('password'),
            'role'              => 'admin_instansi',
            'instansi_id'       => $this->instansi->id,
        ]);

        $this->peserta = User::create([
            'name'              => 'Peserta Magang B',
            'email'             => 'peserta.b@inovasi.test',
            'password'          => Hash::make('password'),
            'role'              => 'peserta',
            'instansi_id'       => $this->instansi->id,
        ]);

        // Sample attendance logs
        Absensi::create([
            'user_id'      => $this->peserta->id,
            'tanggal'      => now()->toDateString(),
            'jam'          => '07:45:00',
            'tipe_absensi' => 'masuk',
            'status'       => 'hadir',
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'jarak'        => 15.5,
            'foto'         => 'uploads/absensi_sample.png',
        ]);
    }

    public function test_admin_can_view_rekap_presensi_with_filter(): void
    {
        $response = $this->actingAs($this->adminInstansi)
            ->get(route('rekap.absensi', [
                'status'       => 'hadir',
                'tipe_absensi' => 'masuk',
            ]));

        $response->assertOk();
        $response->assertSee('Peserta Magang B');
        $response->assertSee('Hadir');
    }

    public function test_admin_can_export_rekap_pdf(): void
    {
        $response = $this->actingAs($this->adminInstansi)
            ->get(route('rekap.absensi.pdf', [
                'user_id' => $this->peserta->id,
            ]));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_admin_can_export_rekap_csv(): void
    {
        $response = $this->actingAs($this->adminInstansi)
            ->get(route('rekap.absensi.csv'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }
}
