<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Perizinan;
use App\Models\Absensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AbsensiPerizinanHmacTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $instansi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instansi = Instansi::create([
            'nama_instansi'    => 'Kantor Pengujian Skripsi',
            'jenis_instansi'   => 'kantor',
            'alamat'           => 'Jl. Pengujian No. 1 Jakarta',
            'latitude'         => -6.2000000,
            'longitude'        => 106.8166667,
            'radius'           => 100, // 100 meter
            'jam_masuk_mulai'  => '07:00:00',
            'jam_masuk_batas'  => '09:00:00',
            'jam_pulang_mulai' => '16:00:00',
            'jam_pulang_batas' => '18:00:00',
        ]);

        $this->user = User::factory()->create([
            'role'        => 'peserta',
            'instansi_id' => $this->instansi->id,
        ]);
    }

    /**
     * Skenario 1: Presensi Normal Tepat Waktu (< 08:00) -> Hadir + HMAC Valid
     */
    public function test_presensi_normal_sebelum_jam_8_dicatat_hadir_dan_memiliki_hmac()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 30, 0));

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 15.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('success');

        $absensi = Absensi::where('user_id', $this->user->id)->first();
        $this->assertNotNull($absensi);
        $this->assertEquals('hadir', $absensi->status);
        $this->assertNull($absensi->id_perizinan);
        $this->assertNotEmpty($absensi->hmac_signature);
        $this->assertTrue($absensi->verifySignature());
    }

    /**
     * Skenario 2: Presensi Normal Terlambat (08:00 - 09:00 Tanpa Izin) -> Telat + HMAC Valid
     */
    public function test_presensi_normal_antara_jam_8_dan_9_dicatat_telat_tanpa_id_perizinan()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 8, 25, 0));

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 20.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));

        $absensi = Absensi::where('user_id', $this->user->id)->first();
        $this->assertNotNull($absensi);
        $this->assertEquals('telat', $absensi->status);
        $this->assertNull($absensi->id_perizinan);
        $this->assertTrue($absensi->verifySignature());
    }

    /**
     * Skenario 3: Presensi Normal Lewat Batas (> 09:00 Tanpa Izin) -> Ditolak Berdasarkan Aturan Dinamis
     */
    public function test_presensi_normal_lewat_jam_9_ditolak()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 9, 15, 0));

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseCount('absensis', 0);
    }

    /**
     * Skenario 4: Izin Terlambat Disetujui (08:30 - 09:30), Tapi Presensi Sebelum Jam 08:30 -> Ditolak
     */
    public function test_izin_terlambat_sebelum_jam_mulai_izin_ditolak()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 8, 10, 0));

        $perizinan = Perizinan::create([
            'user_id'          => $this->user->id,
            'instansi_id'      => $this->instansi->id,
            'jenis_izin'       => 'terlambat',
            'tanggal'          => '2026-08-08',
            'jam_mulai_izin'   => '08:30:00',
            'jam_selesai_izin' => '09:30:00',
            'alasan'           => 'Ban bocor di jalan',
            'status'           => 'disetujui',
        ]);

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('absensis', 0);
    }

    /**
     * Skenario 5: Izin Terlambat Disetujui (08:30 - 09:30), Presensi Jam 08:45 -> Diterima (Status: Telat, id_perizinan terisi)
     */
    public function test_izin_terlambat_dalam_rentang_jam_berhasil_sebagai_telat_berizin()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 8, 45, 0));

        $perizinan = Perizinan::create([
            'user_id'          => $this->user->id,
            'instansi_id'      => $this->instansi->id,
            'jenis_izin'       => 'terlambat',
            'tanggal'          => '2026-08-08',
            'jam_mulai_izin'   => '08:30:00',
            'jam_selesai_izin' => '09:30:00',
            'alasan'           => 'Urusan kampus pagi',
            'status'           => 'disetujui',
        ]);

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 12.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('success');

        $absensi = Absensi::where('user_id', $this->user->id)->first();
        $this->assertNotNull($absensi);
        $this->assertEquals('telat', $absensi->status);
        $this->assertEquals($perizinan->id_perizinan, $absensi->id_perizinan);
        $this->assertTrue($absensi->verifySignature());
    }

    /**
     * Skenario 6: Izin Terlambat Disetujui (08:30 - 09:30), Presensi Jam 09:40 -> Ditolak
     */
    public function test_izin_terlambat_lewat_jam_selesai_izin_ditolak()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 9, 40, 0));

        $perizinan = Perizinan::create([
            'user_id'          => $this->user->id,
            'instansi_id'      => $this->instansi->id,
            'jenis_izin'       => 'terlambat',
            'tanggal'          => '2026-08-08',
            'jam_mulai_izin'   => '08:30:00',
            'jam_selesai_izin' => '09:30:00',
            'alasan'           => 'Macet parah',
            'status'           => 'disetujui',
        ]);

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 15.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('absensis', 0);
    }

    /**
     * Skenario 7: Verifikasi Keamanan HMAC Mendeteksi Modifikasi Ilegal di Database
     */
    public function test_hmac_mendeteksi_manipulasi_data_di_database()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 45, 0));

        $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $absensi = Absensi::where('user_id', $this->user->id)->first();
        $this->assertTrue($absensi->verifySignature());

        // Simulasi penyerang mengubah jam di database dari 07:45:00 menjadi 07:00:00
        $absensi->jam = '07:00:00';
        $this->assertFalse($absensi->verifySignature());
    }

    /**
     * Skenario 8 (Poin 4): Anti-GPS Spoofing - Deteksi Koordinat 0.0 (Null Island)
     */
    public function test_anti_spoofing_menolak_koordinat_nol()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 45, 0));

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => 0.0,
            'longitude'    => 0.0,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('absensis', 0);
    }

    /**
     * Skenario 9 (Poin 4): Anti-GPS Spoofing - Deteksi Akurasi Terlalu Lemah (> 150m)
     */
    public function test_anti_spoofing_menolak_akurasi_gps_lemah()
    {
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 45, 0));

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 650.0, // 650 meter (terlalu tidak akurat)
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('absensis', 0);
    }

    /**
     * Skenario 10 (Poin 4): Anti-GPS Spoofing - Deteksi Anomali Kecepatan Ekstrim (Teleportasi)
     */
    public function test_anti_spoofing_menolak_teleportasi_kecepatan_ekstrim()
    {
        // 1. Presensi pertama di Jakarta pada jam 07:30
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 30, 0));
        $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        // 2. User mencoba absen pulang 1 menit kemudian (07:31) di koordinat Surabaya (-7.25, 112.75) yang jaraknya ~600 km
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 31, 0));
        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -7.2500000,
            'longitude'    => 112.7500000,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'pulang',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('error');
        // Hanya presensi pertama yang tersimpan
        $this->assertDatabaseCount('absensis', 1);
    }

    /**
     * Skenario 11 (Poin 5): Aturan Jam Dinamis Instansi B (Masuk Mulai 08:30 - Batas 09:30)
     */
    public function test_aturan_jam_dinamis_berbeda_pada_instansi_b()
    {
        // Instansi B dengan jam mulai 08:30 dan batas 09:30
        $instansiB = Instansi::create([
            'nama_instansi'    => 'Instansi B Shift Khusus',
            'jenis_instansi'   => 'kantor',
            'alamat'           => 'Jl. Industri B',
            'latitude'         => -6.3000000,
            'longitude'        => 106.8500000,
            'radius'           => 100,
            'jam_masuk_mulai'  => '08:30:00',
            'jam_masuk_batas'  => '09:30:00',
            'jam_pulang_mulai' => '17:00:00',
            'jam_pulang_batas' => '19:00:00',
        ]);

        $userB = User::factory()->create([
            'role'        => 'peserta',
            'instansi_id' => $instansiB->id,
        ]);

        // Coba absen pada 08:15 (sebelum 08:30) -> Belum buka
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 8, 15, 0));
        $response1 = $this->actingAs($userB)->post(route('absensi.store'), [
            'latitude'     => -6.3000000,
            'longitude'    => 106.8500000,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
        $response1->assertSessionHas('error');

        // Coba absen pada 08:45 (antara 08:30 dan 09:30) -> Berhasil
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 8, 45, 0));
        $response2 = $this->actingAs($userB)->post(route('absensi.store'), [
            'latitude'     => -6.3000000,
            'longitude'    => 106.8500000,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
        $response2->assertSessionHas('success');
    }

    /**
     * Skenario 12 (Poin 18): Izin Tidak Hadir (Sakit/Izin Seharian)
     */
    public function test_izin_tidak_hadir_menolak_percobaan_presensi()
    {
        // Buat izin tidak hadir yang disetujui
        Perizinan::create([
            'user_id'          => $this->user->id,
            'instansi_id'      => $this->instansi->id,
            'jenis_izin'       => 'tidak_hadir',
            'tanggal'          => '2026-08-08',
            'alasan'           => 'Demam tinggi dan istirahat dokter',
            'status'           => 'disetujui',
        ]);

        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 45, 0));

        $response = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $response->assertRedirect(route('absensi.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('absensis', 0);
    }

    /**
     * Skenario 13 (Poin 19): Izin Pulang Awal (Windowing Pulang)
     */
    public function test_izin_pulang_awal_memvalidasi_rentang_jam_pulang()
    {
        // Buat izin pulang awal 14:00 - 15:00 yang disetujui
        $izin = Perizinan::create([
            'user_id'          => $this->user->id,
            'instansi_id'      => $this->instansi->id,
            'jenis_izin'       => 'pulang_awal',
            'tanggal'          => '2026-08-08',
            'jam_mulai_izin'   => '14:00:00',
            'jam_selesai_izin' => '15:00:00',
            'alasan'           => 'Urusan akademik kampus',
            'status'           => 'disetujui',
        ]);

        // 1. Coba pulang pada 13:30 (sebelum jam mulai izin) -> DITOLAK
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 13, 30, 0));
        $res1 = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'pulang',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
        $res1->assertSessionHas('error');

        // 2. Coba pulang pada 14:15 (dalam rentang jam izin) -> BERHASIL
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 14, 15, 0));
        $res2 = $this->actingAs($this->user)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'pulang',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
        $res2->assertSessionHas('success');

        $this->assertDatabaseHas('absensis', [
            'user_id'      => $this->user->id,
            'tipe_absensi' => 'pulang',
            'id_perizinan' => $izin->id_perizinan,
        ]);
    }

    /**
     * Skenario 14: Penempatan Lapangan - Masuk Wajib Pilih Teknisi & Dalam Radius
     */
    public function test_peserta_lapangan_masuk_wajib_pilih_teknisi_dan_dalam_radius()
    {
        $instansiLapangan = Instansi::create([
            'nama_instansi'    => 'Instansi Telekomunikasi Lapangan',
            'jenis_instansi'   => 'lapangan',
            'alamat'           => 'Jl. Lapangan No. 5',
            'latitude'         => -6.2000000,
            'longitude'        => 106.8166667,
            'radius'           => 100,
            'jam_masuk_mulai'  => '07:00:00',
            'jam_masuk_batas'  => '09:00:00',
            'jam_pulang_mulai' => '16:00:00',
            'jam_pulang_batas' => '18:00:00',
        ]);

        $teknisi = \App\Models\Teknisi::create([
            'instansi_id'  => $instansiLapangan->id,
            'nama'         => 'Budi Santoso',
            'email'        => 'budi.teknisi@monita.id',
            'nik'          => '3201001010100001',
            'alamat_kerja' => 'Area Proyek Fiber Optic',
            'no_hp'        => '08123456789',
        ]);

        $pesertaLapangan = User::factory()->create([
            'role'            => 'peserta',
            'tipe_penempatan' => 'lapangan',
            'instansi_id'     => $instansiLapangan->id,
        ]);

        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 45, 0));

        // 1. Coba presensi masuk tanpa teknisi -> GAGAL
        $resWithoutTeknisi = $this->actingAs($pesertaLapangan)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
        $resWithoutTeknisi->assertSessionHasErrors('teknisi_id');

        // 2. Coba presensi masuk dengan teknisi di luar radius kantor -> GAGAL
        $resOutsideRadius = $this->actingAs($pesertaLapangan)->post(route('absensi.store'), [
            'latitude'     => -6.2100000, // ~1.1km di luar radius
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'teknisi_id'   => $teknisi->id,
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
        $resOutsideRadius->assertSessionHas('error');

        // 3. Presensi masuk dengan teknisi dan dalam radius -> BERHASIL
        $resSuccess = $this->actingAs($pesertaLapangan)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'teknisi_id'   => $teknisi->id,
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
        $resSuccess->assertSessionHas('success');

        $this->assertDatabaseHas('absensis', [
            'user_id'      => $pesertaLapangan->id,
            'tipe_absensi' => 'masuk',
            'teknisi_id'   => $teknisi->id,
        ]);
    }

    /**
     * Skenario 15: Penempatan Lapangan - Pulang Bebas di Luar Radius & Teknisi Otomatis Terkunci
     */
    public function test_peserta_lapangan_pulang_bebas_di_luar_radius_dan_teknisi_terkunci()
    {
        $instansiLapangan = Instansi::create([
            'nama_instansi'    => 'Instansi Telekomunikasi Lapangan B',
            'jenis_instansi'   => 'lapangan',
            'alamat'           => 'Jl. Lapangan No. 10',
            'latitude'         => -6.2000000,
            'longitude'        => 106.8166667,
            'radius'           => 100,
            'jam_masuk_mulai'  => '07:00:00',
            'jam_masuk_batas'  => '09:00:00',
            'jam_pulang_mulai' => '16:00:00',
            'jam_pulang_batas' => '18:00:00',
        ]);

        $teknisi = \App\Models\Teknisi::create([
            'instansi_id'  => $instansiLapangan->id,
            'nama'         => 'Agus Pratama',
            'email'        => 'agus.teknisi@monita.id',
            'nik'          => '3201001010100002',
            'alamat_kerja' => 'Area Tower BTS',
            'no_hp'        => '08129876543',
        ]);

        $pesertaLapangan = User::factory()->create([
            'role'            => 'peserta',
            'tipe_penempatan' => 'lapangan',
            'instansi_id'     => $instansiLapangan->id,
        ]);

        // Presensi Masuk Pagi Hari
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 7, 30, 0));
        $this->actingAs($pesertaLapangan)->post(route('absensi.store'), [
            'latitude'     => -6.2000000,
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'masuk',
            'teknisi_id'   => $teknisi->id,
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        // Presensi Pulang Sore Hari di Lokasi Proyek Lapangan (~3 km di luar radius kantor)
        Carbon::setTestNow(Carbon::create(2026, 8, 8, 16, 30, 0));
        $resPulang = $this->actingAs($pesertaLapangan)->post(route('absensi.store'), [
            'latitude'     => -6.2270000, // ~3 km di luar radius kantor
            'longitude'    => 106.8166667,
            'accuracy'     => 10.0,
            'tipe_absensi' => 'pulang',
            'foto'         => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $resPulang->assertSessionHas('success');

        // Pastikan presensi pulang tercatat dengan teknisi_id yang sama persis
        $this->assertDatabaseHas('absensis', [
            'user_id'      => $pesertaLapangan->id,
            'tipe_absensi' => 'pulang',
            'teknisi_id'   => $teknisi->id,
        ]);
    }
}
