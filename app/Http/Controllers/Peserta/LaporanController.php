<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN UTAMA LAPORAN KEGIATAN PESERTA
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $laporans = Laporan::with('verifikator')
            ->where('user_id', auth()->id())
            ->latest('tanggal')
            ->latest('jam')
            ->get();

        return view('peserta.laporan', compact('laporans'));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN LAPORAN KEGIATAN BARU
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kegiatan'  => 'nullable|string',
            'foto'      => 'required',
        ]);

        $kegiatanText = $request->kegiatan;
        if (empty($kegiatanText)) {
            if ($request->filled('judul') && $request->filled('deskripsi')) {
                $kegiatanText = "【" . $request->judul . "】\n" . $request->deskripsi;
            } elseif ($request->filled('deskripsi')) {
                $kegiatanText = $request->deskripsi;
            } elseif ($request->filled('judul')) {
                $kegiatanText = $request->judul;
            }
        }

        if (empty($kegiatanText)) {
            return back()
                ->withErrors(['deskripsi' => 'Kolom judul atau deskripsi kegiatan wajib diisi.'])
                ->withInput();
        }

        $uploadDir = public_path('uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (str_contains($request->foto, ';base64,')) {
            $imageParts = explode(';base64,', $request->foto);
            $imageBase64 = base64_decode($imageParts[1]);
        } else {
            $imageBase64 = base64_decode($request->foto);
        }

        $fileName = 'laporan_' . auth()->id() . '_' . time() . '.png';
        file_put_contents($uploadDir . '/' . $fileName, $imageBase64);

        $laporan = Laporan::create([
            'user_id'           => auth()->id(),
            'kegiatan'          => $kegiatanText,
            'foto'              => 'uploads/' . $fileName,
            'tanggal'           => now()->toDateString(),
            'jam'               => now()->toTimeString(),
            'status'            => 'menunggu',
            'catatan_revisi'    => null,
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ]);

        // Notifikasi ke Pembimbing Instansi
        $user = auth()->user();
        $pembimbingIds = collect([$user->pembimbing_id])
            ->merge(DB::table('pembimbing_peserta')->where('peserta_id', $user->id)->pluck('pembimbing_id'))
            ->filter()
            ->unique();

        $pesanNotif = "{$user->name} telah mengunggah laporan kegiatan baru untuk tanggal " . \Carbon\Carbon::parse($laporan->tanggal)->format('Y-m-d') . ".";
        foreach ($pembimbingIds as $pembimbingId) {
            Notifikasi::kirim(
                $pembimbingId,
                'Laporan Kegiatan Baru',
                $pesanNotif,
                route('pembimbing.laporan.index'),
                'info'
            );
        }

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan kegiatan harian berhasil dikirim dan menunggu verifikasi pembimbing.');
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL LAPORAN KEGIATAN
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $laporan = Laporan::with('verifikator')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('peserta.laporan.show', compact('laporan'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM EDIT / PERBAIKAN LAPORAN
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $laporan = Laporan::with('verifikator')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($laporan->status === 'disetujui') {
            return redirect()->route('laporan.index')
                ->with('error', 'Laporan yang telah disetujui pembimbing tidak dapat diubah.');
        }

        // Pisahkan judul dan deskripsi jika menggunakan format 【Judul】\nDeskripsi
        $judul = '';
        $deskripsi = $laporan->kegiatan;
        if (preg_match('/^【(.*?)】\n?(.*)$/s', $laporan->kegiatan, $matches)) {
            $judul = $matches[1];
            $deskripsi = $matches[2];
        }

        return view('peserta.laporan.edit', compact('laporan', 'judul', 'deskripsi'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE LAPORAN (KIRIM ULANG HASIL REVISI)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $laporan = Laporan::where('user_id', auth()->id())
            ->findOrFail($id);

        if ($laporan->status === 'disetujui') {
            return redirect()->route('laporan.index')
                ->with('error', 'Laporan yang telah disetujui tidak dapat diubah.');
        }

        $request->validate([
            'judul'     => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'kegiatan'  => 'nullable|string',
            'foto'      => 'nullable|string',
        ]);

        $kegiatanText = $request->kegiatan;
        if (empty($kegiatanText)) {
            if ($request->filled('judul') && $request->filled('deskripsi')) {
                $kegiatanText = "【" . $request->judul . "】\n" . $request->deskripsi;
            } elseif ($request->filled('deskripsi')) {
                $kegiatanText = $request->deskripsi;
            } elseif ($request->filled('judul')) {
                $kegiatanText = $request->judul;
            }
        }

        if (empty($kegiatanText)) {
            return back()
                ->withErrors(['deskripsi' => 'Kolom kegiatan wajib diisi.'])
                ->withInput();
        }

        $fotoPath = $laporan->foto;
        if ($request->filled('foto') && str_contains($request->foto, ';base64,')) {
            $imageParts = explode(';base64,', $request->foto);
            $imageBase64 = base64_decode($imageParts[1]);
            $fileName = 'laporan_' . auth()->id() . '_' . time() . '.png';
            file_put_contents(public_path('uploads/' . $fileName), $imageBase64);
            $fotoPath = 'uploads/' . $fileName;
        }

        $laporan->update([
            'kegiatan'          => $kegiatanText,
            'foto'              => $fotoPath,
            'status'            => 'menunggu', // Reset ke menunggu agar dievaluasi ulang
            'catatan_revisi'    => $laporan->catatan_revisi, // Simpan histori catatan sebelumnya
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ]);

        // Notifikasi ke Pembimbing Instansi
        $user = auth()->user();
        $pembimbingIds = collect([$user->pembimbing_id])
            ->merge(DB::table('pembimbing_peserta')->where('peserta_id', $user->id)->pluck('pembimbing_id'))
            ->filter()
            ->unique();

        $pesanNotif = "{$user->name} telah mengunggah laporan kegiatan baru untuk tanggal " . \Carbon\Carbon::parse($laporan->tanggal)->format('Y-m-d') . ".";
        foreach ($pembimbingIds as $pembimbingId) {
            Notifikasi::kirim(
                $pembimbingId,
                'Laporan Kegiatan Baru',
                $pesanNotif,
                route('pembimbing.laporan.index'),
                'info'
            );
        }

        return redirect()->route('laporan.index')
            ->with('success', 'Perbaikan laporan kegiatan berhasil dikirimkan kembali untuk diverifikasi.');
    }
}