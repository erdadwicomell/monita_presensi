<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Absensi;
use App\Models\Perizinan;

class AbsensiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN UTAMA ABSENSI PESERTA
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = auth()->user();
        $instansi = $user->instansi;
        $today = now()->toDateString();

        // 1. Cek perizinan aktif hari ini yang disetujui
        $perizinan = Perizinan::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('status', 'disetujui')
            ->first();

        // 2. Cek riwayat absensi masuk & pulang hari ini
        $absenMasuk = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('tipe_absensi', 'masuk')
            ->first();

        $absenPulang = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('tipe_absensi', 'pulang')
            ->first();

        // 3. Statistik bulanan peserta
        $totalHadir = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', 'hadir')
            ->count();

        $totalTelat = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', 'telat')
            ->count();

        // 4. Deteksi Penempatan Lapangan vs Kantor & Daftar Teknisi
        $isLapangan = $user->isLapangan();
        $teknisis = $isLapangan && $instansi
            ? \App\Models\Teknisi::where('instansi_id', $instansi->id)->get()
            : collect();

        return view('peserta.absensi', compact(
            'instansi',
            'perizinan',
            'absenMasuk',
            'absenPulang',
            'totalHadir',
            'totalTelat',
            'isLapangan',
            'teknisis'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN ABSENSI (ANTI-SPOOFING, DYNAMIC HOURS, HAVERSINE, HMAC-SHA256)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // 1. Normalisasi Longitude: Wilayah Indonesia (Bujur Timur) selalu bernilai positif
        if ($request->has('longitude')) {
            $request->merge([
                'longitude' => abs((float) $request->longitude)
            ]);
        }

        $request->validate([
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'accuracy'     => 'nullable|numeric',
            'tipe_absensi' => 'required|in:masuk,pulang',
            'foto'         => 'required',
        ]);

        $user = auth()->user();
        $instansi = $user->instansi;
        $today = now()->toDateString();
        $jamSekarang = now()->format('H:i:s');
        $isLapangan = $user->isLapangan();

        // Validasi Teknisi untuk Penempatan Lapangan (Wajib saat presensi MASUK)
        if ($isLapangan && $request->tipe_absensi === 'masuk') {
            $request->validate([
                'teknisi_id' => 'required|exists:teknisis,id',
            ], [
                'teknisi_id.required' => 'Peserta magang penempatan lapangan wajib memilih Teknisi pendamping saat presensi masuk.',
                'teknisi_id.exists'   => 'Data teknisi yang dipilih tidak valid.',
            ]);
        }

        // =====================================================================
        // 1. LAYER ANTI-GPS SPOOFING & INTEGRITAS KOORDINAT (SERVER-SIDE)
        // =====================================================================
        $lat = (float) $request->latitude;
        $lon = abs((float) $request->longitude);
        $accuracy = $request->filled('accuracy') ? (float) $request->accuracy : null;

        // A. Deteksi Koordinat Nol / Null Island (0.0, 0.0)
        if ($lat == 0.0 && $lon == 0.0) {
            $this->logSpoofingAttempt($user, $lat, $lon, $accuracy, 'Koordinat 0.0 terdeteksi (Null Island)');
            return redirect()->route('absensi.index')
                ->with('error', 'Koordinat GPS tidak valid. Pastikan GPS perangkat Anda aktif.');
        }

        // B. Deteksi Akurasi GPS Lemah / Tidak Masuk Akal (> 500m atau <= 0m)
        if ($accuracy !== null && ($accuracy > 500 || $accuracy <= 0)) {
            $this->logSpoofingAttempt($user, $lat, $lon, $accuracy, 'Akurasi GPS tidak valid atau terlalu rendah (' . $accuracy . 'm)');
            return redirect()->route('absensi.index')
                ->with('error', 'Sinyal GPS terlalu lemah atau tidak akurat (Akurasi: ' . round($accuracy) . 'm). Silakan tunggu sinyal stabil.');
        }

        // C. Deteksi Anomali Kecepatan / Teleportasi (Speed Kinematic Anomaly)
        $lastAbsensi = Absensi::where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        if ($lastAbsensi && $lastAbsensi->created_at) {
            $secondsDiff = now()->diffInSeconds($lastAbsensi->created_at);
            // Cek jika presensi sebelumnya terjadi dalam rentang 15 menit (900 detik)
            if ($secondsDiff > 0 && $secondsDiff < 900) {
                $displacement = $this->calculateHaversineDistance(
                    $lat,
                    $lon,
                    (float) $lastAbsensi->latitude,
                    (float) $lastAbsensi->longitude
                );

                // Kecepatan dalam km/jam = (jarak_meter / detik) * 3.6
                $speedKmh = ($displacement / $secondsDiff) * 3.6;

                // Jika kecepatan perpindahan > 300 km/jam pada jarak > 2000 meter -> terindikasi teleportasi/spoofing
                if ($speedKmh > 300 && $displacement > 2000) {
                    $this->logSpoofingAttempt(
                        $user,
                        $lat,
                        $lon,
                        $accuracy,
                        "Anomali perpindahan lokasi ekstrim (Jarak: " . round($displacement) . "m dlm {$secondsDiff} detik, Kecepatan: " . round($speedKmh) . " km/jam)"
                    );
                    return redirect()->route('absensi.index')
                        ->with('error', 'Terdeteksi anomali perpindahan lokasi GPS tidak wajar. Percobaan presensi dicatat dalam audit log.');
                }
            }
        }

        // =====================================================================
        // 2. VALIDASI DOUBLE ABSENSI
        // =====================================================================
        $cek = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('tipe_absensi', $request->tipe_absensi)
            ->first();

        if ($cek) {
            return redirect()->route('absensi.index')
                ->with('error', 'Anda sudah melakukan absensi ' . $request->tipe_absensi . ' hari ini.');
        }

        // =====================================================================
        // 3. HITUNG JARAK (HAVERSINE) & VALIDASI GEOFENCING SKENARIO A vs B
        // =====================================================================
        $distance = $this->calculateHaversineDistance(
            $lat,
            $lon,
            (float) $instansi->latitude,
            (float) $instansi->longitude
        );

        if ($isLapangan && $request->tipe_absensi === 'pulang') {
            // Skenario A (Lapangan - Pulang): BEBAS DI MANA SAJA (BYPASS Radius Kantor)
            // Jarak tetap dicatat dalam database untuk transparansi
        } else {
            // Skenario A (Lapangan - Masuk) & Skenario B (Perkantoran - Masuk & Pulang): WAJIB DI DALAM RADIUS
            if ($distance > $instansi->radius) {
                return redirect()->route('absensi.index')
                    ->with('error', 'Anda berada di luar radius absensi kantor (' . round($distance) . ' meter dari batas radius ' . $instansi->radius . ' meter).');
            }
        }

        // =====================================================================
        // 4. ATURAN JAM DINAMIS PER INSTANSI & VALIDASI PERIZINAN
        // =====================================================================
        // A. Cek Izin Tidak Hadir (Jika disetujui, peserta dibebaskan dari presensi)
        $izinTidakHadir = Perizinan::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('status', 'disetujui')
            ->where('jenis_izin', 'tidak_hadir')
            ->first();

        if ($izinTidakHadir) {
            return redirect()->route('absensi.index')
                ->with('error', 'Anda memiliki perizinan Tidak Hadir yang telah disetujui untuk hari ini (' . $izinTidakHadir->alasan . '). Anda dibebaskan dari kewajiban presensi.');
        }

        $jamMasukMulai  = $instansi->jam_masuk_mulai ?? '07:00:00';
        $jamMasukBatas  = $instansi->jam_masuk_batas ?? '09:00:00';
        $jamPulangMulai = $instansi->jam_pulang_mulai ?? '16:00:00';
        $jamPulangBatas = $instansi->jam_pulang_batas ?? '18:00:00';

        $statusKehadiran = 'hadir';
        $idPerizinan = null;

        if ($request->tipe_absensi === 'masuk') {
            // Cek perizinan terlambat aktif disetujui hari ini
            $perizinan = Perizinan::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->where('status', 'disetujui')
                ->where('jenis_izin', 'terlambat')
                ->first();

            if ($perizinan) {
                // A. Jam sekarang < jam_mulai_izin -> BELUM BOLEH
                if ($jamSekarang < $perizinan->jam_mulai_izin) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Belum memasuki waktu presensi sesuai izin Anda (Jadwal izin: ' . $perizinan->jam_mulai_izin . ' - ' . $perizinan->jam_selesai_izin . ').');
                }

                // C. Jam sekarang > jam_selesai_izin -> DITOLAK
                if ($jamSekarang > $perizinan->jam_selesai_izin) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Waktu presensi sesuai perizinan terlambat telah berakhir pada ' . $perizinan->jam_selesai_izin . '.');
                }

                // B. Jam sekarang >= jam_mulai_izin DAN <= jam_selesai_izin -> BOLEH
                $statusKehadiran = 'telat';
                $idPerizinan = $perizinan->id_perizinan;

            } else {
                // Presensi Masuk Normal Menggunakan Konfigurasi Jam Dinamis Instansi
                if ($jamSekarang < $jamMasukMulai) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Presensi masuk belum dibuka untuk instansi ini (Jadwal masuk: ' . $jamMasukMulai . ' - ' . $jamMasukBatas . ').');
                }

                if ($jamSekarang > $jamMasukBatas) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Jam presensi masuk telah berakhir (Batas maksimal: ' . $jamMasukBatas . ').');
                }

                // Jika masuk melewati jam nominal masuk mulai
                $onTimeLimit = min($jamMasukBatas, '08:00:00');
                if ($jamSekarang > $onTimeLimit) {
                    $statusKehadiran = 'telat';
                } else {
                    $statusKehadiran = 'hadir';
                }
            }
        } elseif ($request->tipe_absensi === 'pulang') {
            // Cek perizinan pulang awal disetujui hari ini
            $izinPulangAwal = Perizinan::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->where('status', 'disetujui')
                ->where('jenis_izin', 'pulang_awal')
                ->first();

            if ($izinPulangAwal) {
                $jamMulaiIzin = $izinPulangAwal->jam_mulai_izin ?? '12:00:00';
                $jamSelesaiIzin = $izinPulangAwal->jam_selesai_izin ?? $jamPulangBatas;

                if ($jamSekarang < $jamMulaiIzin) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Belum memasuki waktu izin pulang awal Anda (Jadwal izin: ' . $jamMulaiIzin . ' - ' . $jamSelesaiIzin . ').');
                }

                if ($jamSekarang > $jamSelesaiIzin && $jamSekarang < $jamPulangMulai) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Waktu izin pulang awal Anda telah berakhir pada ' . $jamSelesaiIzin . '. Silakan tunggu jam pulang normal (' . $jamPulangMulai . ').');
                }

                if ($jamSekarang > $jamPulangBatas) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Waktu presensi pulang telah berakhir pada ' . $jamPulangBatas . '.');
                }

                $statusKehadiran = 'hadir';
                $idPerizinan = $izinPulangAwal->id_perizinan;
            } else {
                // Presensi Pulang Normal Berdasarkan Jam Dinamis Instansi
                if ($jamSekarang < $jamPulangMulai) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Presensi pulang belum dibuka (Jadwal pulang: ' . $jamPulangMulai . ' - ' . $jamPulangBatas . ').');
                }

                if ($jamSekarang > $jamPulangBatas) {
                    return redirect()->route('absensi.index')
                        ->with('error', 'Waktu presensi pulang telah berakhir pada ' . $jamPulangBatas . '.');
                }
            }
        }

        // =====================================================================
        // 5. PROSES SIMPAN FOTO SELFIE
        // =====================================================================
        $fotoPath = $this->storePhoto($request->foto, $user->id);

        // =====================================================================
        // 6. RESOLUSI DATA TEKNISI PENDAMPING (LAPANGAN)
        // =====================================================================
        $teknisiId = null;
        if ($isLapangan) {
            if ($request->tipe_absensi === 'masuk') {
                $teknisiId = $request->teknisi_id;
                // Sinkronisasi teknisi default ke profil user
                $user->update(['teknisi_id' => $teknisiId]);
            } else {
                // Presensi Pulang: otomatis mengunci dan menggunakan teknisi dari presensi masuk hari ini
                $absenMasukHariIni = Absensi::where('user_id', $user->id)
                    ->whereDate('tanggal', $today)
                    ->where('tipe_absensi', 'masuk')
                    ->first();
                $teknisiId = $absenMasukHariIni->teknisi_id ?? $user->teknisi_id;
            }
        }

        // =====================================================================
        // 7. GENERATE HMAC-SHA256 SIGNATURE (DATA INTEGRITY)
        // =====================================================================
        $signature = Absensi::generateSignature([
            'user_id'      => $user->id,
            'tanggal'      => $today,
            'jam'          => $jamSekarang,
            'tipe_absensi' => $request->tipe_absensi,
            'latitude'     => $lat,
            'longitude'    => $lon,
            'status'       => $statusKehadiran,
        ]);

        // =====================================================================
        // 8. SIMPAN DATA ABSENSI KE DATABASE
        // =====================================================================
        Absensi::create([
            'user_id'        => $user->id,
            'id_perizinan'   => $idPerizinan,
            'teknisi_id'     => $teknisiId,
            'latitude'       => $lat,
            'longitude'      => $lon,
            'jarak'          => round($distance, 2),
            'status'         => $statusKehadiran,
            'tipe_absensi'   => $request->tipe_absensi,
            'tanggal'        => $today,
            'jam'            => $jamSekarang,
            'foto'           => $fotoPath,
            'hmac_signature' => $signature,
        ]);

        $keterangan = $idPerizinan ? 'Telat (Dengan Izin Terlambat)' : ucfirst($statusKehadiran);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi ' . $request->tipe_absensi . ' berhasil disimpan. Status kehadiran: ' . $keterangan . '.');
    }

    /*
    |--------------------------------------------------------------------------
    | AUDIT LOGGING PERCOBAAN GPS SPOOFING
    |--------------------------------------------------------------------------
    */
    private function logSpoofingAttempt($user, $lat, $lon, $accuracy, string $reason): void
    {
        Log::warning('[GPS_SPOOFING_ATTEMPT] Terdeteksi percobaan manipulasi lokasi presensi', [
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'email'      => $user->email,
            'latitude'   => $lat,
            'longitude'  => $lon,
            'accuracy'   => $accuracy,
            'reason'     => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp'  => now()->toDateTimeString(),
        ]);

        \App\Models\AuditLog::catat(
            'gps_spoofing',
            'bahaya',
            'Percobaan GPS Spoofing Terdeteksi',
            $reason,
            $user,
            $lat,
            $lon,
            ['accuracy' => $accuracy]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG JARAK GPS (HAVERSINE FORMULA) - SATUAN METER
    |--------------------------------------------------------------------------
    */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad(abs($lon1));
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad(abs($lon2));

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) *
            pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN FOTO BASE64
    |--------------------------------------------------------------------------
    */
    private function storePhoto($base64String, $userId)
    {
        $uploadDir = public_path('uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (str_contains($base64String, ';base64,')) {
            $imageParts = explode(';base64,', $base64String);
            $imageBase64 = base64_decode($imageParts[1]);
        } else {
            $imageBase64 = base64_decode($base64String);
        }

        $fileName = 'absensi_' . $userId . '_' . time() . '.png';
        file_put_contents($uploadDir . '/' . $fileName, $imageBase64);

        return 'uploads/' . $fileName;
    }

    /*
    |--------------------------------------------------------------------------
    | RIWAYAT ABSENSI PESERTA
    |--------------------------------------------------------------------------
    */
    public function riwayat()
    {
        $riwayat = Absensi::with('perizinan')
            ->where('user_id', auth()->id())
            ->latest('tanggal')
            ->latest('jam')
            ->get();

        return view('peserta.riwayat', compact('riwayat'));
    }
}