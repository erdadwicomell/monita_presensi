<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instansi;
use App\Models\Absensi;
use App\Models\Laporan;
use App\Models\Perizinan;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'super_admin') {
            $today = now()->toDateString();

            // 1. Ringkasan Global
            $totalInstansi      = Instansi::count();
            $totalKantor        = Instansi::where('jenis_instansi', 'kantor')->count();
            $totalPemerintahan  = Instansi::where('jenis_instansi', 'pemerintahan')->count();
            $totalLapangan      = Instansi::where('jenis_instansi', 'lapangan')->count();

            $totalAdminInstansi = User::where('role', 'admin_instansi')->count();
            $totalPembimbing    = User::where('role', 'pembimbing_instansi')->count();
            $totalPeserta       = User::where('role', 'peserta')->count();
            $totalUserGlobal    = User::count();

            // 2. Presensi Hari Ini Lintas Seluruh Instansi
            $hadirHariIni = Absensi::whereDate('tanggal', $today)
                ->where('tipe_absensi', 'masuk')
                ->where('status', 'hadir')
                ->count();

            $telatHariIni = Absensi::whereDate('tanggal', $today)
                ->where('tipe_absensi', 'masuk')
                ->where('status', 'telat')
                ->count();

            $totalPresensiHariIni = $hadirHariIni + $telatHariIni;

            // 3. Laporan & Keamanan
            $totalLaporan         = Laporan::count();
            $laporanPending       = Laporan::where('status', 'menunggu')->count();
            $totalAnomaliKeamanan = AuditLog::count();
            $totalSpoofingGlobal  = AuditLog::where('kategori', 'gps_spoofing')->count();
            $anomaliHariIni       = AuditLog::whereDate('created_at', $today)->count();

            // 4. Data Tren 7 Hari Terakhir Global
            $dates     = [];
            $hadirData = [];
            $telatData = [];

            for ($i = 6; $i >= 0; $i--) {
                $d = now()->subDays($i)->toDateString();
                $dates[] = Carbon::parse($d)->format('d M');

                $h = Absensi::whereDate('tanggal', $d)
                    ->where('tipe_absensi', 'masuk')
                    ->where('status', 'hadir')
                    ->count();

                $t = Absensi::whereDate('tanggal', $d)
                    ->where('tipe_absensi', 'masuk')
                    ->where('status', 'telat')
                    ->count();

                $hadirData[] = $h;
                $telatData[] = $t;
            }

            // 5. Instansi Overview dengan jumlah peserta dan admin
            $instansiOverview = Instansi::withCount(['users as peserta_count' => function ($q) {
                    $q->where('role', 'peserta');
                }, 'users as pembimbing_count' => function ($q) {
                    $q->where('role', 'pembimbing_instansi');
                }])
                ->with(['users' => function ($q) {
                    $q->where('role', 'admin_instansi');
                }])
                ->latest()
                ->take(5)
                ->get();

            // 6. Security Audit Log Terbaru Global
            $recentAuditLogs = AuditLog::with(['user', 'instansi'])
                ->latest()
                ->take(6)
                ->get();

            // 7. Feed Presensi Terkini Global
            $recentPresensi = Absensi::with(['user.instansi'])
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard.super_admin', compact(
                'totalInstansi',
                'totalKantor',
                'totalPemerintahan',
                'totalLapangan',
                'totalAdminInstansi',
                'totalPembimbing',
                'totalPeserta',
                'totalUserGlobal',
                'hadirHariIni',
                'telatHariIni',
                'totalPresensiHariIni',
                'totalLaporan',
                'laporanPending',
                'totalAnomaliKeamanan',
                'totalSpoofingGlobal',
                'anomaliHariIni',
                'dates',
                'hadirData',
                'telatData',
                'instansiOverview',
                'recentAuditLogs',
                'recentPresensi'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN INSTANSI
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'admin_instansi') {
            $instansiId = $user->instansi_id;
            $today = now()->toDateString();

            $totalPeserta = User::where('role', 'peserta')
                ->where('instansi_id', $instansiId)
                ->count();

            // Kehadiran Hari Ini
            $hadirHariIni = Absensi::whereHas('user', function ($q) use ($instansiId) {
                    $q->where('instansi_id', $instansiId);
                })
                ->whereDate('tanggal', $today)
                ->where('tipe_absensi', 'masuk')
                ->where('status', 'hadir')
                ->count();

            $telatHariIni = Absensi::whereHas('user', function ($q) use ($instansiId) {
                    $q->where('instansi_id', $instansiId);
                })
                ->whereDate('tanggal', $today)
                ->where('tipe_absensi', 'masuk')
                ->where('status', 'telat')
                ->count();

            // Pending Tasks
            $perizinanPending = Perizinan::where('instansi_id', $instansiId)
                ->where('status', 'menunggu')
                ->count();

            $laporanPending = Laporan::whereHas('user', function ($q) use ($instansiId) {
                    $q->where('instansi_id', $instansiId);
                })
                ->where('status', 'menunggu')
                ->count();

            // Anomali Spoofing
            $totalSpoofing = AuditLog::where('instansi_id', $instansiId)
                ->where('kategori', 'gps_spoofing')
                ->count();

            // Data Tren 7 Hari Terakhir
            $dates = [];
            $hadirData = [];
            $telatData = [];

            for ($i = 6; $i >= 0; $i--) {
                $d = now()->subDays($i)->toDateString();
                $dates[] = Carbon::parse($d)->format('d M');

                $h = Absensi::whereHas('user', function ($q) use ($instansiId) {
                        $q->where('instansi_id', $instansiId);
                    })
                    ->whereDate('tanggal', $d)
                    ->where('tipe_absensi', 'masuk')
                    ->where('status', 'hadir')
                    ->count();

                $t = Absensi::whereHas('user', function ($q) use ($instansiId) {
                        $q->where('instansi_id', $instansiId);
                    })
                    ->whereDate('tanggal', $d)
                    ->where('tipe_absensi', 'masuk')
                    ->where('status', 'telat')
                    ->count();

                $hadirData[] = $h;
                $telatData[] = $t;
            }

            // Log Presensi Terbaru Hari Ini
            $recentAbsensi = Absensi::with('user')
                ->whereHas('user', function ($q) use ($instansiId) {
                    $q->where('instansi_id', $instansiId);
                })
                ->whereDate('tanggal', $today)
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard.admin_instansi', compact(
                'totalPeserta',
                'hadirHariIni',
                'telatHariIni',
                'perizinanPending',
                'laporanPending',
                'totalSpoofing',
                'dates',
                'hadirData',
                'telatData',
                'recentAbsensi'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | PESERTA
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'peserta') {
            $today = now()->toDateString();
            $currentMonth = now()->month;
            $currentYear = now()->year;

            // Status Hari Ini
            $absenMasuk = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->where('tipe_absensi', 'masuk')
                ->first();

            $absenPulang = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->where('tipe_absensi', 'pulang')
                ->first();

            $perizinanHariIni = Perizinan::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->where('status', 'disetujui')
                ->first();

            // Statistik Bulanan
            $totalHadirBulan = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('tipe_absensi', 'masuk')
                ->where('status', 'hadir')
                ->count();

            $totalTelatBulan = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('tipe_absensi', 'masuk')
                ->where('status', 'telat')
                ->count();

            $totalIzinBulan = Perizinan::where('user_id', $user->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'disetujui')
                ->count();

            // Status Laporan
            $laporanDisetujui = Laporan::where('user_id', $user->id)->where('status', 'disetujui')->count();
            $laporanRevisi = Laporan::where('user_id', $user->id)->where('status', 'revisi')->count();
            $laporanMenunggu = Laporan::where('user_id', $user->id)->where('status', 'menunggu')->count();

            // Laporan Terbaru
            $recentLaporan = Laporan::where('user_id', $user->id)->latest()->take(4)->get();

            return view('dashboard.peserta', compact(
                'absenMasuk',
                'absenPulang',
                'perizinanHariIni',
                'totalHadirBulan',
                'totalTelatBulan',
                'totalIzinBulan',
                'laporanDisetujui',
                'laporanRevisi',
                'laporanMenunggu',
                'recentLaporan'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | PEMBIMBING INSTANSI
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'pembimbing_instansi') {
            return redirect()->route('pembimbing.dashboard');
        }
    }
}
