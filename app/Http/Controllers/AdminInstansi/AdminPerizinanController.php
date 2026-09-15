<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perizinan;

class AdminPerizinanController extends Controller
{
    /**
     * Menampilkan seluruh data perizinan milik peserta
     * pada instansi admin dengan filter & statistik.
     */
    public function index(Request $request)
    {
        $instansiId = auth()->user()->instansi_id;

        $query = Perizinan::with(['user', 'admin'])
            ->where('instansi_id', $instansiId);

        // 1. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. Filter Jenis Izin
        if ($request->filled('jenis_izin')) {
            $query->where('jenis_izin', $request->jenis_izin);
        }

        // 3. Filter Rentang Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // 4. Filter Pencarian Nama Peserta
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perizinans = $query->latest('tanggal')
            ->latest('id_perizinan')
            ->paginate(15)
            ->withQueryString();

        // Statistik Status Perizinan Instansi
        $totalMenunggu = Perizinan::where('instansi_id', $instansiId)->where('status', 'menunggu')->count();
        $totalDisetujui = Perizinan::where('instansi_id', $instansiId)->where('status', 'disetujui')->count();
        $totalDitolak = Perizinan::where('instansi_id', $instansiId)->where('status', 'ditolak')->count();

        return view('admin_instansi.perizinan.index', compact(
            'perizinans',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    /**
     * Detail perizinan
     */
    public function show($id)
    {
        $instansiId = auth()->user()->instansi_id;

        $perizinan = Perizinan::with(['user', 'admin', 'instansi'])
            ->where('instansi_id', $instansiId)
            ->findOrFail($id);

        return view('admin_instansi.perizinan.show', compact('perizinan'));
    }

    /**
     * Setujui perizinan
     */
    public function setujui($id)
    {
        $instansiId = auth()->user()->instansi_id;

        $perizinan = Perizinan::where('instansi_id', $instansiId)
            ->findOrFail($id);

        $perizinan->update([
            'status'         => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        // Notifikasi ke peserta
        \App\Models\Notifikasi::kirim(
            $perizinan->user_id,
            'Perizinan Disetujui',
            'Perizinan jenis ' . str_replace('_', ' ', $perizinan->jenis_izin) . ' Anda untuk tanggal ' . $perizinan->tanggal . ' telah disetujui.',
            route('perizinan.show', $perizinan->id_perizinan),
            'success'
        );

        // Audit Log
        \App\Models\AuditLog::catat(
            'perizinan',
            'info',
            'Persetujuan Perizinan',
            'Admin menyetujui perizinan ' . $perizinan->jenis_izin . ' milik ' . ($perizinan->user->name ?? 'Peserta'),
            auth()->user()
        );

        return redirect()
            ->route('admin.perizinan.index')
            ->with('success', 'Perizinan peserta ' . ($perizinan->user->name ?? '') . ' berhasil disetujui.');
    }

    /**
     * Tolak perizinan
     */
    public function tolak($id)
    {
        $instansiId = auth()->user()->instansi_id;

        $perizinan = Perizinan::where('instansi_id', $instansiId)
            ->findOrFail($id);

        $perizinan->update([
            'status'         => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        // Notifikasi ke peserta
        \App\Models\Notifikasi::kirim(
            $perizinan->user_id,
            'Perizinan Ditolak',
            'Perizinan jenis ' . str_replace('_', ' ', $perizinan->jenis_izin) . ' Anda untuk tanggal ' . $perizinan->tanggal . ' telah ditolak oleh Admin Instansi.',
            route('perizinan.show', $perizinan->id_perizinan),
            'danger'
        );

        // Audit Log
        \App\Models\AuditLog::catat(
            'perizinan',
            'warning',
            'Penolakan Perizinan',
            'Admin menolak perizinan ' . $perizinan->jenis_izin . ' milik ' . ($perizinan->user->name ?? 'Peserta'),
            auth()->user()
        );

        return redirect()
            ->route('admin.perizinan.index')
            ->with('success', 'Perizinan peserta ' . ($perizinan->user->name ?? '') . ' berhasil ditolak.');
    }
}