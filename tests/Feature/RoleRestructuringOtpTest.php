<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Divisi;
use App\Models\Otp;
use App\Models\Laporan;
use Illuminate\Support\Facades\Hash;

class RoleRestructuringOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_instansi_can_register_and_receive_otp(): void
    {
        $response = $this->post(route('register.admin.submit'), [
            'name'                  => 'Admin Baru',
            'nomor_telepon'         => '081234567890',
            'alamat'                => 'Jl. Kantor Baru No. 10',
            'jabatan'               => 'Staff HRD',
            'email'                 => 'admin.baru@instansi.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('otp.verify.form'));
        $this->assertDatabaseHas('users', [
            'email' => 'admin.baru@instansi.com',
            'role'  => 'admin_instansi',
        ]);

        $otp = Otp::where('email', 'admin.baru@instansi.com')->first();
        $this->assertNotNull($otp);
        $this->assertEquals(6, strlen($otp->otp_code));
    }

    public function test_admin_instansi_can_verify_otp_and_complete_onboarding(): void
    {
        $user = User::create([
            'name'          => 'Admin Onboard',
            'email'         => 'admin.onboard@test.com',
            'password'      => Hash::make('password123'),
            'role'          => 'admin_instansi',
            'nomor_telepon' => '0811111111',
            'alamat'        => 'Jl. Test',
            'jabatan'       => 'HR Manager',
        ]);

        $otp = Otp::generate($user->email, 'registrasi_admin', $user->id);

        // 1. Verify OTP
        $verifyRes = $this->post(route('otp.verify.submit'), [
            'email'    => $user->email,
            'otp_code' => $otp->otp_code,
            'type'     => 'registrasi_admin',
        ]);

        $verifyRes->assertRedirect(route('admin.onboarding.create'));
        $this->assertAuthenticatedAs($user);

        // 2. Onboarding Instansi
        $onboardingRes = $this->actingAs($user)->post(route('admin.onboarding.store'), [
            'nama_instansi'    => 'PT Inovasi Mandiri',
            'jenis_instansi'   => 'kantor',
            'alamat'           => 'Jl. Merdeka No 45',
            'latitude'         => -6.2088000,
            'longitude'        => 106.8456000,
            'radius'           => 150,
            'jam_masuk_mulai'  => '07:00',
            'jam_masuk_batas'  => '08:30',
            'jam_pulang_mulai' => '16:00',
            'jam_pulang_batas' => '18:00',
        ]);

        $onboardingRes->assertRedirect(route('dashboard'));
        $this->assertNotNull($user->fresh()->instansi_id);
    }

    public function test_admin_can_register_peserta_and_peserta_can_set_password(): void
    {
        $instansi = Instansi::create([
            'nama_instansi'  => 'PT Solusi Cerdas',
            'jenis_instansi' => 'kantor',
            'alamat'         => 'Jl. Sudirman',
            'latitude'       => -6.2000000,
            'longitude'      => 106.8166667,
            'radius'         => 100,
        ]);

        $divisi = Divisi::create([
            'instansi_id' => $instansi->id,
            'nama_divisi' => 'Software Engineering',
        ]);

        $admin = User::create([
            'name'        => 'Admin Solusi',
            'email'       => 'admin.solusi@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'admin_instansi',
            'instansi_id' => $instansi->id,
        ]);

        $pembimbing = User::create([
            'name'        => 'Pembimbing Utama',
            'email'       => 'pembimbing.utama@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'pembimbing_instansi',
            'instansi_id' => $instansi->id,
        ]);

        // 1. Admin daftarkan peserta
        $res = $this->actingAs($admin)->post(route('peserta.store'), [
            'name'            => 'Peserta Baru',
            'asal_sekolah_pt' => 'Universitas Indonesia',
            'nim_nisn'        => '2026001',
            'divisi_id'       => $divisi->id,
            'pembimbing_id'   => $pembimbing->id,
            'email'           => 'peserta.baru@test.com',
            'nomor_telepon'   => '081299998888',
            'alamat'          => 'Jl. Kos No 5',
        ]);

        $res->assertRedirect(route('peserta.index'));
        $peserta = User::where('email', 'peserta.baru@test.com')->first();
        $this->assertNotNull($peserta);
        $this->assertEquals($pembimbing->id, $peserta->pembimbing_id);
        $this->assertEquals($pembimbing->id, $peserta->pembimbing->id);
        $this->assertTrue($pembimbing->bimbingan->contains($peserta));

        // 2. Peserta verifikasi OTP
        $otp = Otp::where('email', $peserta->email)->first();
        $this->assertNotNull($otp);

        $verifyRes = $this->post(route('otp.verify.submit'), [
            'email'    => $peserta->email,
            'otp_code' => $otp->otp_code,
            'type'     => 'aktivasi_peserta',
        ]);

        $verifyRes->assertRedirect(route('otp.set-password.form'));

        // 3. Peserta set password mandiri
        $setPassRes = $this->withSession(['set_password_email' => $peserta->email])
            ->post(route('otp.set-password.submit'), [
                'email'                 => $peserta->email,
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $setPassRes->assertRedirect(route('dashboard'));
        $this->assertTrue(Hash::check('newpassword123', $peserta->fresh()->password));
        $this->assertTrue($peserta->fresh()->is_active);
    }

    public function test_pembimbing_instansi_can_review_and_approve_mentored_report(): void
    {
        $instansi = Instansi::create([
            'nama_instansi'  => 'PT Bimbingan Mandiri',
            'jenis_instansi' => 'kantor',
            'alamat'         => 'Jl. Gatot Subroto',
            'latitude'       => -6.2000000,
            'longitude'      => 106.8166667,
            'radius'         => 100,
        ]);

        $pembimbing = User::create([
            'name'        => 'Pembimbing Utama',
            'email'       => 'pembimbing@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'pembimbing_instansi',
            'instansi_id' => $instansi->id,
            'nip'         => '198801012015011001',
            'jabatan'     => 'Senior Analyst',
        ]);

        $peserta = User::create([
            'name'        => 'Peserta Bimbingan',
            'email'       => 'peserta.bimbingan@test.com',
            'password'    => Hash::make('password'),
            'role'        => 'peserta',
            'instansi_id' => $instansi->id,
        ]);

        // Assign peserta ke pembimbing
        $pembimbing->pesertaBimbingan()->attach($peserta->id, ['instansi_id' => $instansi->id]);

        // Peserta buat laporan
        $laporan = Laporan::create([
            'user_id'  => $peserta->id,
            'tanggal'  => now()->toDateString(),
            'jam'      => '14:30:00',
            'kegiatan' => 'Implementasi modul autentikasi dan pengujian sistem.',
            'status'   => 'menunggu',
        ]);

        // Pembimbing menyetujui laporan
        $approveRes = $this->actingAs($pembimbing)
            ->put(route('pembimbing.laporan.setujui', $laporan->id));

        $approveRes->assertRedirect(route('pembimbing.laporan.index'));
        $this->assertEquals('disetujui', $laporan->fresh()->status);
        $this->assertEquals($pembimbing->id, $laporan->fresh()->diverifikasi_oleh);

        // Pembimbing minta revisi
        $revisiRes = $this->actingAs($pembimbing)
            ->put(route('pembimbing.laporan.revisi', $laporan->id), [
                'catatan_revisi' => 'Tolong lampirkan diagram alur arsitektur.',
            ]);

        $revisiRes->assertRedirect(route('pembimbing.laporan.index'));
        $this->assertEquals('revisi', $laporan->fresh()->status);
        $this->assertEquals('Tolong lampirkan diagram alur arsitektur.', $laporan->fresh()->catatan_revisi);
    }
}
