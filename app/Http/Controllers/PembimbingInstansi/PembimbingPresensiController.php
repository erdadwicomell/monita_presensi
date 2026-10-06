<?php

namespace App\Http\Controllers\PembimbingInstansi;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;

class PembimbingPresensiController extends Controller
{
    /**
     * Halaman Rekap Presensi khusus Peserta Bimbingan dari Pembimbing yang sedang login
     */
    public function index(Request $request)
    {
        $pembimbing = auth()->user();

        // Mengambil seluruh ID peserta yang dibimbing secara aman (pivot dan foreign key)
        $pivotIds = $pembimbing->pesertaBimbingan()->pluck('users.id')->toArray();
        $fkIds = User::where('pembimbing_id', $pembimbing->id)->pluck('id')->toArray();
        $pesertaIds = array_values(array_unique(array_merge($pivotIds, $fkIds)));

        $pesertas = User::whereIn('id', $pesertaIds)
            ->orderBy('name')
            ->get();

        $query = Absensi::with(['user.divisi', 'user.teknisi', 'perizinan'])
            ->whereIn('user_id', $pesertaIds);

        // Filter Peserta
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Rentang Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // Filter Status Kehadiran
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Tipe Presensi (masuk/pulang)
        if ($request->filled('tipe_absensi')) {
            $query->where('tipe_absensi', $request->tipe_absensi);
        }

        // Statistik Ringkasan
        $totalPresensi = (clone $query)->count();
        $totalHadir    = (clone $query)->where('status', 'hadir')->count();
        $totalTelat    = (clone $query)->where('status', 'telat')->count();
        $totalMasuk    = (clone $query)->where('tipe_absensi', 'masuk')->count();
        $totalPulang   = (clone $query)->where('tipe_absensi', 'pulang')->count();

        $rekap = $query->latest('tanggal')
            ->latest('jam')
            ->paginate(15)
            ->withQueryString();

        return view('pembimbing_instansi.presensi.index', compact(
            'rekap',
            'pesertas',
            'totalPresensi',
            'totalHadir',
            'totalTelat',
            'totalMasuk',
            'totalPulang'
        ));
    }
}
