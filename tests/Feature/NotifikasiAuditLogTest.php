<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Notifikasi;
use App\Models\AuditLog;
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
}
