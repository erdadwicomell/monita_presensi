<?php

namespace App\Http\Controllers\PembimbingInstansi;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class PembimbingLaporanController extends Controller
{
    /**
     * Daftar laporan kegiatan dari peserta bimbingan
     */
    public function index(Request $request)
    {
        $pembimbing = auth()->user();
        $pesertaIds = $pembimbing->pesertaBimbingan()->pluck('users.id')->toArray();

        $query = Laporan::with(['user.divisi', 'user.teknisi'])
            ->whereIn('user_id', $pesertaIds);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter peserta
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $laporans = $query->latest('tanggal')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $pesertas = $pembimbing->pesertaBimbingan;

        $totalMenunggu = Laporan::whereIn('user_id', $pesertaIds)->where('status', 'menunggu')->count();
        $totalDisetujui = Laporan::whereIn('user_id', $pesertaIds)->where('status', 'disetujui')->count();
        $totalRevisi = Laporan::whereIn('user_id', $pesertaIds)->where('status', 'revisi')->count();

        return view('pembimbing_instansi.laporan.index', compact(
            'laporans',
            'pesertas',
            'totalMenunggu',
            'totalDisetujui',
            'totalRevisi'
        ));
    }

    /**
     * Detail laporan kegiatan
     */
    public function show($id)
    {
        $pembimbing = auth()->user();
        $pesertaIds = $pembimbing->pesertaBimbingan()->pluck('users.id')->toArray();

        $laporan = Laporan::with(['user', 'verifikator'])
            ->whereIn('user_id', $pesertaIds)
            ->findOrFail($id);

        return view('pembimbing_instansi.laporan.show', compact('laporan'));
    }

    /**
     * Pembimbing menyetujui laporan kegiatan
     */
    public function setujui($id)
    {
        $pembimbing = auth()->user();
        $pesertaIds = $pembimbing->pesertaBimbingan()->pluck('users.id')->toArray();

        $laporan = Laporan::whereIn('user_id', $pesertaIds)->findOrFail($id);

        $laporan->update([
            'status'            => 'disetujui',
            'catatan_revisi'    => null,
            'diverifikasi_oleh' => $pembimbing->id,
            'diverifikasi_pada' => now(),
        ]);

        $tgl = \Carbon\Carbon::parse($laporan->tanggal)->format('Y-m-d');

        // Notifikasi ke peserta
        Notifikasi::kirim(
            $laporan->user_id,
            'Laporan Kegiatan Disetujui',
            "Laporan kegiatan Anda tanggal {$tgl} berstatus: Disetujui.",
            route('laporan.show', $laporan->id),
            'success'
        );

        // Audit Log
        AuditLog::catat(
            'laporan',
            'info',
            'Persetujuan Laporan Bimbingan',
            'Pembimbing ' . $pembimbing->name . ' menyetujui laporan kegiatan milik ' . ($laporan->user->name ?? 'Peserta'),
            $pembimbing
        );

        return redirect()->route('pembimbing.laporan.index')
            ->with('success', 'Laporan kegiatan peserta ' . ($laporan->user->name ?? '') . ' berhasil disetujui.');
    }

    /**
     * Pembimbing meminta revisi laporan kegiatan (+ catatan perbaikan)
     */
    public function revisi(Request $request, $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string|max:1000',
        ]);

        $pembimbing = auth()->user();
        $pesertaIds = $pembimbing->pesertaBimbingan()->pluck('users.id')->toArray();

        $laporan = Laporan::whereIn('user_id', $pesertaIds)->findOrFail($id);

        $laporan->update([
            'status'            => 'revisi',
            'catatan_revisi'    => $request->catatan_revisi,
            'diverifikasi_oleh' => $pembimbing->id,
            'diverifikasi_pada' => now(),
        ]);

        $tgl = \Carbon\Carbon::parse($laporan->tanggal)->format('Y-m-d');

        // Notifikasi ke peserta
        Notifikasi::kirim(
            $laporan->user_id,
            'Laporan Perlu Revisi',
            "Laporan kegiatan Anda tanggal {$tgl} berstatus: Perlu Revisi.",
            route('laporan.edit', $laporan->id),
            'warning'
        );

        // Audit Log
        AuditLog::catat(
            'laporan',
            'warning',
            'Permintaan Revisi Laporan Bimbingan',
            'Pembimbing ' . $pembimbing->name . ' meminta perbaikan laporan milik ' . ($laporan->user->name ?? 'Peserta') . ' dengan catatan: ' . $request->catatan_revisi,
            $pembimbing
        );

        return redirect()->route('pembimbing.laporan.index')
            ->with('success', 'Status laporan diubah menjadi Revisi dengan catatan perbaikan untuk peserta.');
    }
}
