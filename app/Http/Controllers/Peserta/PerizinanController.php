<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perizinan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerizinanController extends Controller
{
    /**
     * Menampilkan daftar perizinan peserta.
     */
    public function index()
    {
        $perizinans = Perizinan::where('user_id', Auth::id())
            ->latest('tanggal')
            ->latest('id_perizinan')
            ->get();

        return view('peserta.perizinan.index', compact('perizinans'));
    }

    /**
     * Menampilkan form pengajuan izin.
     */
    public function create()
    {
        return view('peserta.perizinan.create');
    }

    /**
     * Menyimpan pengajuan perizinan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_izin'       => 'required|in:terlambat,tidak_hadir,pulang_awal',
            'tanggal'          => 'required|date',
            'jam_mulai_izin'   => 'nullable|required_if:jenis_izin,terlambat',
            'jam_selesai_izin' => 'nullable|required_if:jenis_izin,terlambat',
            'alasan'           => 'required|string|max:1000',
            'bukti'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $bukti = null;
        if ($request->hasFile('bukti')) {
            $bukti = $request->file('bukti')->store('perizinan', 'public');
        }

        Perizinan::create([
            'user_id'          => Auth::id(),
            'instansi_id'      => Auth::user()->instansi_id,
            'jenis_izin'       => $request->jenis_izin,
            'tanggal'          => $request->tanggal,
            'jam_mulai_izin'   => $request->jenis_izin === 'tidak_hadir' ? null : $request->jam_mulai_izin,
            'jam_selesai_izin' => $request->jenis_izin === 'tidak_hadir' ? null : $request->jam_selesai_izin,
            'alasan'           => $request->alasan,
            'bukti'            => $bukti,
            'status'           => 'menunggu',
            'disetujui_oleh'   => null,
            'disetujui_pada'   => null,
        ]);

        return redirect()
            ->route('perizinan.index')
            ->with('success', 'Pengajuan perizinan berhasil dikirim dan menunggu persetujuan admin.');
    }

    /**
     * Menampilkan detail perizinan peserta.
     */
    public function show($id)
    {
        $perizinan = Perizinan::with(['admin', 'instansi'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('peserta.perizinan.show', compact('perizinan'));
    }

    /**
     * Form edit perizinan (Hanya jika status masih 'menunggu').
     */
    public function edit($id)
    {
        $perizinan = Perizinan::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($perizinan->status !== 'menunggu') {
            return redirect()
                ->route('perizinan.index')
                ->with('error', 'Perizinan yang sudah diproses (' . ucfirst($perizinan->status) . ') tidak dapat diedit.');
        }

        return view('peserta.perizinan.edit', compact('perizinan'));
    }

    /**
     * Update pengajuan perizinan (Hanya jika status masih 'menunggu').
     */
    public function update(Request $request, $id)
    {
        $perizinan = Perizinan::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($perizinan->status !== 'menunggu') {
            return redirect()
                ->route('perizinan.index')
                ->with('error', 'Perizinan yang sudah diproses tidak dapat diubah.');
        }

        $request->validate([
            'jenis_izin'       => 'required|in:terlambat,tidak_hadir,pulang_awal',
            'tanggal'          => 'required|date',
            'jam_mulai_izin'   => 'nullable|required_if:jenis_izin,terlambat',
            'jam_selesai_izin' => 'nullable|required_if:jenis_izin,terlambat',
            'alasan'           => 'required|string|max:1000',
            'bukti'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $bukti = $perizinan->bukti;
        if ($request->hasFile('bukti')) {
            if ($perizinan->bukti && Storage::disk('public')->exists($perizinan->bukti)) {
                Storage::disk('public')->delete($perizinan->bukti);
            }
            $bukti = $request->file('bukti')->store('perizinan', 'public');
        }

        $perizinan->update([
            'jenis_izin'       => $request->jenis_izin,
            'tanggal'          => $request->tanggal,
            'jam_mulai_izin'   => $request->jenis_izin === 'tidak_hadir' ? null : $request->jam_mulai_izin,
            'jam_selesai_izin' => $request->jenis_izin === 'tidak_hadir' ? null : $request->jam_selesai_izin,
            'alasan'           => $request->alasan,
            'bukti'            => $bukti,
        ]);

        return redirect()
            ->route('perizinan.index')
            ->with('success', 'Pengajuan perizinan berhasil diperbarui.');
    }

    /**
     * Batalkan / Hapus perizinan (Hanya jika status masih 'menunggu').
     */
    public function destroy($id)
    {
        $perizinan = Perizinan::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($perizinan->status !== 'menunggu') {
            return redirect()
                ->route('perizinan.index')
                ->with('error', 'Perizinan yang sudah diproses tidak dapat dibatalkan.');
        }

        if ($perizinan->bukti && Storage::disk('public')->exists($perizinan->bukti)) {
            Storage::disk('public')->delete($perizinan->bukti);
        }

        $perizinan->delete();

        return redirect()
            ->route('perizinan.index')
            ->with('success', 'Pengajuan perizinan berhasil dibatalkan.');
    }
}