<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Laporan;
use Illuminate\Support\Facades\Hash;

class LaporanKegiatanWorkflowTest extends TestCase
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
            'alamat'            => 'Jl. Sudirman No 1, Jakarta',
            'latitude'          => -6.2000000,
            'longitude'         => 106.8166667,
            'radius'            => 100,
            'jam_masuk_mulai'   => '07:00:00',
            'jam_masuk_batas'   => '09:00:00',
            'jam_pulang_mulai'  => '16:00:00',
            'jam_pulang_batas'  => '18:00:00',
        ]);

        $this->adminInstansi = User::create([
            'name'              => 'Admin Pembimbing',
            'email'             => 'admin@inovasi.test',
            'password'          => Hash::make('password'),
            'role'              => 'admin_instansi',
            'instansi_id'       => $this->instansi->id,
        ]);

        $this->peserta = User::create([
            'name'              => 'Peserta Magang A',
            'email'             => 'peserta@inovasi.test',
            'password'          => Hash::make('password'),
            'role'              => 'peserta',
            'instansi_id'       => $this->instansi->id,
        ]);
    }

    public function test_peserta_can_create_laporan_with_initial_status_menunggu(): void
    {
        $response = $this->actingAs($this->peserta)->post(route('laporan.store'), [
            'judul'     => 'Instalasi Server Database',
            'deskripsi' => 'Melakukan konfigurasi database MySQL dan optimasi query.',
            'foto'      => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('laporan.index'));

        $this->assertDatabaseHas('laporans', [
            'user_id' => $this->peserta->id,
            'status'  => 'menunggu',
        ]);

        $laporan = Laporan::where('user_id', $this->peserta->id)->first();
        $this->assertStringContainsString('Instalasi Server Database', $laporan->kegiatan);
        $this->assertStringContainsString('konfigurasi database MySQL', $laporan->kegiatan);
    }

    public function test_admin_can_approve_laporan(): void
    {
        $laporan = Laporan::create([
            'user_id'   => $this->peserta->id,
            'kegiatan'  => 'Melakukan backup data server mingguan',
            'foto'      => 'uploads/dummy.png',
            'tanggal'   => now()->toDateString(),
            'jam'       => '10:00:00',
            'status'    => 'menunggu',
        ]);

        $response = $this->actingAs($this->adminInstansi)
            ->put(route('admin.laporan.setujui', $laporan->id));

        $response->assertRedirect(route('admin.laporan.index'));

        $laporan->refresh();
        $this->assertEquals('disetujui', $laporan->status);
        $this->assertEquals($this->adminInstansi->id, $laporan->diverifikasi_oleh);
        $this->assertNotNull($laporan->diverifikasi_pada);
    }

    public function test_admin_can_request_revision_with_feedback_note(): void
    {
        $laporan = Laporan::create([
            'user_id'   => $this->peserta->id,
            'kegiatan'  => 'Laporan singkat',
            'foto'      => 'uploads/dummy.png',
            'tanggal'   => now()->toDateString(),
            'jam'       => '11:00:00',
            'status'    => 'menunggu',
        ]);

        $response = $this->actingAs($this->adminInstansi)
            ->put(route('admin.laporan.revisi', $laporan->id), [
                'catatan_revisi' => 'Tolong jelaskan konfigurasi IP address yang digunakan secara lengkap.',
            ]);

        $response->assertRedirect(route('admin.laporan.index'));

        $laporan->refresh();
        $this->assertEquals('revisi', $laporan->status);
        $this->assertEquals('Tolong jelaskan konfigurasi IP address yang digunakan secara lengkap.', $laporan->catatan_revisi);
        $this->assertEquals($this->adminInstansi->id, $laporan->diverifikasi_oleh);
    }

    public function test_peserta_can_resubmit_revised_laporan_and_status_resets_to_menunggu(): void
    {
        $laporan = Laporan::create([
            'user_id'           => $this->peserta->id,
            'kegiatan'          => 'Laporan singkat',
            'foto'              => 'uploads/dummy.png',
            'tanggal'           => now()->toDateString(),
            'jam'               => '11:00:00',
            'status'            => 'revisi',
            'catatan_revisi'    => 'Perlu detail IP address',
            'diverifikasi_oleh' => $this->adminInstansi->id,
            'diverifikasi_pada' => now(),
        ]);

        $response = $this->actingAs($this->peserta)
            ->put(route('laporan.update', $laporan->id), [
                'judul'     => 'Konfigurasi Jaringan & IP Address',
                'deskripsi' => 'Menambahkan setting subnet 192.168.1.0/24 pada router utama.',
            ]);

        $response->assertRedirect(route('laporan.index'));

        $laporan->refresh();
        $this->assertEquals('menunggu', $laporan->status);
        $this->assertNull($laporan->diverifikasi_oleh);
        $this->assertNull($laporan->diverifikasi_pada);
        $this->assertStringContainsString('192.168.1.0/24', $laporan->kegiatan);
    }
}
