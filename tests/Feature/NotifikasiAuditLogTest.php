<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Notifikasi;
use App\Models\AuditLog;
use App\Models\Perizinan;
use App\Models\Laporan;
use Illuminate\Support\Facades\Hash;

class NotifikasiAuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected $instansi;
    protected $admin;
    protected $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instansi = Instansi::create([
            'nama_instansi'     => 'PT Notif Digital',
            'jenis_instansi'    => 'kantor',
            'alamat'            => 'Jl. Digital No 1',
            'latitude'          => -6.2000000,
            'longitude'         => 106.8166667,
            'radius'            => 100,
        ]);

        $this->admin = User::create([
            'name'        => 'Admin Notif',
            'email'       => 'admin.notif@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'admin_instansi',
            'instansi_id' => $this->instansi->id,
        ]);

        $this->peserta = User::create([
            'name'        => 'Peserta Notif',
            'email'       => 'peserta.notif@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'peserta',
            'instansi_id' => $this->instansi->id,
        ]);
    }

    public function test_can_fetch_unread_notifications_json(): void
    {
        Notifikasi::kirim(
            $this->peserta->id,
            'Perizinan Disetujui',
            'Izin Anda telah disetujui',
            '/perizinan',
            'success'
        );

        $response = $this->actingAs($this->peserta)
            ->getJson(route('notifikasi.latest'));

        $response->assertOk();
        $response->assertJson([
            'unread_count' => 1,
        ]);
    }

    public function test_can_mark_notification_as_read(): void
    {
        $notif = Notifikasi::kirim(
            $this->peserta->id,
            'Laporan Disetujui',
            'Laporan kegiatan Anda disetujui'
        );

        $this->assertFalse($notif->is_read);

        $response = $this->actingAs($this->peserta)
            ->postJson(route('notifikasi.read', $notif->id));

        $response->assertOk();
        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_super_admin_can_view_audit_log_page(): void
    {
        $superAdmin = User::create([
            'name'     => 'Super Admin Global',
            'email'    => 'superadmin.audit@monita.com',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
        ]);

        AuditLog::catat(
            'gps_spoofing',
            'bahaya',
            'Spoofing Dicegah',
            'Anomali GPS',
            $this->peserta
        );

        $response = $this->actingAs($superAdmin)
            ->get(route('admin.audit.log'));

        $response->assertOk();
        $response->assertSee('Spoofing Dicegah');
        $response->assertSee('Peserta Notif');
    }

    public function test_admin_instansi_cannot_view_audit_log_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit.log'));

        $response->assertForbidden();
    }

    public function test_dashboard_renders_for_admin_and_peserta(): void
    {
        $superAdmin = User::create([
            'name'     => 'Super Admin Global',
            'email'    => 'superadmin@monita.com',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
        ]);

        $resSuper = $this->actingAs($superAdmin)->get(route('dashboard'));
        $resSuper->assertOk();
        $resSuper->assertSee('Dashboard Super Admin');

        $resAdmin = $this->actingAs($this->admin)->get(route('dashboard'));
        $resAdmin->assertOk();
        $resAdmin->assertSee('Dashboard Admin Instansi');

        $resPeserta = $this->actingAs($this->peserta)->get(route('dashboard'));
        $resPeserta->assertOk();
        $resPeserta->assertSee('Halo, Peserta Notif');
    }

    public function test_admin_receives_notification_when_peserta_submits_perizinan(): void
    {
        $response = $this->actingAs($this->peserta)->post(route('perizinan.store'), [
            'jenis_izin' => 'tidak_hadir',
            'tanggal'    => now()->toDateString(),
            'alasan'     => 'Sakit flu',
        ]);

        $response->assertRedirect(route('perizinan.index'));

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $this->admin->id,
            'pesan'   => "Pengajuan izin baru dari {$this->peserta->name} memerlukan evaluasi Anda.",
        ]);
    }

    public function test_peserta_receives_notification_when_admin_evaluates_perizinan(): void
    {
        $perizinan = Perizinan::create([
            'user_id'     => $this->peserta->id,
            'instansi_id' => $this->instansi->id,
            'jenis_izin'  => 'tidak_hadir',
            'tanggal'     => now()->toDateString(),
            'alasan'      => 'Sakit panas',
            'status'      => 'menunggu',
        ]);

        $this->actingAs($this->admin)->put(route('admin.perizinan.setujui', $perizinan->id_perizinan));

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $this->peserta->id,
            'pesan'   => 'Pengajuan izin Anda telah Disetujui oleh Admin',
        ]);

        $this->actingAs($this->admin)->put(route('admin.perizinan.tolak', $perizinan->id_perizinan));

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $this->peserta->id,
            'pesan'   => 'Pengajuan izin Anda telah Ditolak oleh Admin',
        ]);
    }

    public function test_pembimbing_receives_notification_when_mentored_peserta_submits_laporan(): void
    {
        $pembimbing = User::create([
            'name'        => 'Pembimbing Khusus',
            'email'       => 'pembimbing.khusus@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'pembimbing_instansi',
            'instansi_id' => $this->instansi->id,
        ]);

        $pembimbingLain = User::create([
            'name'        => 'Pembimbing Lain',
            'email'       => 'pembimbing.lain@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'pembimbing_instansi',
            'instansi_id' => $this->instansi->id,
        ]);

        $this->peserta->update(['pembimbing_id' => $pembimbing->id]);
        $pembimbing->pesertaBimbingan()->attach($this->peserta->id, ['instansi_id' => $this->instansi->id]);

        $response = $this->actingAs($this->peserta)->post(route('laporan.store'), [
            'judul'     => 'Kegiatan Riset',
            'deskripsi' => 'Mengerjakan analisis data.',
            'foto'      => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('laporan.index'));

        $today = now()->toDateString();
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pembimbing->id,
            'pesan'   => "{$this->peserta->name} telah mengunggah laporan kegiatan baru untuk tanggal {$today}.",
        ]);

        $this->assertDatabaseMissing('notifikasis', [
            'user_id' => $pembimbingLain->id,
        ]);
    }

    public function test_peserta_receives_notification_when_laporan_evaluated(): void
    {
        $pembimbing = User::create([
            'name'        => 'Pembimbing Evaluator',
            'email'       => 'pembimbing.evaluator@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'pembimbing_instansi',
            'instansi_id' => $this->instansi->id,
        ]);

        $pembimbing->pesertaBimbingan()->attach($this->peserta->id, ['instansi_id' => $this->instansi->id]);

        $today = now()->toDateString();
        $laporan = Laporan::create([
            'user_id'  => $this->peserta->id,
            'tanggal'  => $today,
            'jam'      => '14:00:00',
            'kegiatan' => 'Kegiatan testing notifikasi.',
            'status'   => 'menunggu',
        ]);

        $this->actingAs($pembimbing)->put(route('pembimbing.laporan.setujui', $laporan->id));

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $this->peserta->id,
            'pesan'   => "Laporan kegiatan Anda tanggal {$today} berstatus: Disetujui.",
        ]);

        $this->actingAs($pembimbing)->put(route('pembimbing.laporan.revisi', $laporan->id), [
            'catatan_revisi' => 'Perbaiki tata bahasa.',
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $this->peserta->id,
            'pesan'   => "Laporan kegiatan Anda tanggal {$today} berstatus: Perlu Revisi.",
        ]);
    }
}
