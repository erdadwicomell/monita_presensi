<?php

namespace App\Http\Controllers\PembimbingInstansi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Laporan;

class PembimbingDashboardController extends Controller
{
    public function index()
    {
        $pembimbing = auth()->user();
        $instansiId = $pembimbing->instansi_id;
        $today = now()->toDateString();

        // Ambil ID peserta yang dibimbing
        $pesertaIds = $pembimbing->pesertaBimbingan()->pluck('users.id')->toArray();

        $totalPesertaBimbingan = count($pesertaIds);

        // Presensi hari ini dari peserta bimbingan
        $hadirHariIni = Absensi::whereIn('user_id', $pesertaIds)
            ->whereDate('tanggal', $today)
            ->where('tipe_absensi', 'masuk')
            ->where('status', 'hadir')
            ->count();

        $telatHariIni = Absensi::whereIn('user_id', $pesertaIds)
            ->whereDate('tanggal', $today)
            ->where('tipe_absensi', 'masuk')
            ->where('status', 'telat')
            ->count();

        // Laporan kegiatan menunggu verifikasi
        $laporanPending = Laporan::whereIn('user_id', $pesertaIds)
            ->where('status', 'menunggu')
            ->count();

        $laporanDisetujui = Laporan::whereIn('user_id', $pesertaIds)
            ->where('status', 'disetujui')
            ->count();

        // Daftar Peserta Bimbingan beserta status absen hari ini
        $pesertaBimbingans = User::with(['divisi', 'teknisi'])
            ->whereIn('id', $pesertaIds)
            ->get();

        // Laporan Terbaru yang Menunggu Bimbingan
        $recentLaporan = Laporan::with('user')
            ->whereIn('user_id', $pesertaIds)
            ->latest()
            ->take(5)
            ->get();

        return view('pembimbing_instansi.dashboard', compact(
            'totalPesertaBimbingan',
            'hadirHariIni',
            'telatHariIni',
            'laporanPending',
            'laporanDisetujui',
            'pesertaBimbingans',
            'recentLaporan'
        ));
    }
}
