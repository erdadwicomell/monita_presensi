<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;

class AdminLaporanController extends Controller
{
    /**
     * Menampilkan daftar seluruh laporan kegiatan peserta
     * bimbingan instansi dengan filter & pencarian.
     */
    public function index(Request $request)
    {
        $instansiId = auth()->user()->instansi_id;

        $query = Laporan::with(['user', 'verifikator'])
            ->whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            });

        // 1. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. Filter Rentang Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // 3. Filter Pencarian Nama Peserta
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $laporans = $query->latest('tanggal')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Statistik Laporan Instansi
        $totalMenunggu = Laporan::whereHas('user', fn($q) => $q->where('instansi_id', $instansiId))->where('status', 'menunggu')->count();
        $totalDisetujui = Laporan::whereHas('user', fn($q) => $q->where('instansi_id', $instansiId))->where('status', 'disetujui')->count();
        $totalRevisi = Laporan::whereHas('user', fn($q) => $q->where('instansi_id', $instansiId))->where('status', 'revisi')->count();

        return view('admin_instansi.laporan.index', compact(
            'laporans',
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
        $instansiId = auth()->user()->instansi_id;

        $laporan = Laporan::with(['user', 'verifikator'])
            ->whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            })
            ->findOrFail($id);

        return view('admin_instansi.laporan.show', compact('laporan'));
    }

    /**
     * Setujui Laporan Kegiatan
     */
    public function setujui($id)
    {
        $instansiId = auth()->user()->instansi_id;

        $laporan = Laporan::whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            })
            ->findOrFail($id);

        $laporan->update([
            'status'            => 'disetujui',
            'catatan_revisi'    => null,
            'diverifikasi_oleh' => auth()->id(),
            'diverifikasi_pada' => now(),
        ]);

        // Notifikasi ke peserta
        \App\Models\Notifikasi::kirim(
            $laporan->user_id,
            'Laporan Kegiatan Disetujui',
            'Laporan kegiatan Anda untuk tanggal ' . $laporan->tanggal . ' telah disetujui oleh Pembimbing Instansi.',
            route('laporan.show', $laporan->id),
            'success'
        );

        // Audit Log
        \App\Models\AuditLog::catat(
            'laporan',
            'info',
            'Persetujuan Laporan',
            'Admin menyetujui laporan kegiatan milik ' . ($laporan->user->name ?? 'Peserta'),
            auth()->user()
        );

        return redirect()->route('admin.laporan.index')
            ->with('success', 'Laporan kegiatan peserta ' . ($laporan->user->name ?? '') . ' berhasil disetujui.');
    }

    /**
     * Minta Revisi Laporan Kegiatan (+ Catatan Revisi)
     */
    public function revisi(Request $request, $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string|max:1000',
        ]);

        $instansiId = auth()->user()->instansi_id;

        $laporan = Laporan::whereHas('user', function ($q) use ($instansiId) {
                $q->where('instansi_id', $instansiId);
            })
            ->findOrFail($id);

        $laporan->update([
            'status'            => 'revisi',
            'catatan_revisi'    => $request->catatan_revisi,
            'diverifikasi_oleh' => auth()->id(),
            'diverifikasi_pada' => now(),
        ]);

        // Notifikasi ke peserta
        \App\Models\Notifikasi::kirim(
            $laporan->user_id,
            'Laporan Perlu Revisi',
            'Laporan kegiatan tanggal ' . $laporan->tanggal . ' memerlukan perbaikan: ' . $request->catatan_revisi,
            route('laporan.edit', $laporan->id),
            'warning'
        );

        // Audit Log
        \App\Models\AuditLog::catat(
            'laporan',
            'warning',
            'Permintaan Revisi Laporan',
            'Admin meminta revisi laporan kegiatan milik ' . ($laporan->user->name ?? 'Peserta') . ' dengan catatan: ' . $request->catatan_revisi,
            auth()->user()
        );

        return redirect()->route('admin.laporan.index')
            ->with('success', 'Status laporan diubah menjadi Revisi dengan catatan perbaikan.');
    }
}
