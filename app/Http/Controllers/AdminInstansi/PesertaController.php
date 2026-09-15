<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Teknisi;
use App\Models\Absensi;

use Illuminate\Support\Facades\Hash;

use Barryvdh\DomPDF\Facade\Pdf;

class PesertaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $pesertas = User::where('role', 'peserta')
            ->where(
                'instansi_id',
                auth()->user()->instansi_id
            )
            ->latest()
            ->get();

        return view(
            'admin_instansi.peserta.index',
            compact('pesertas')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $instansiId = auth()->user()->instansi_id;

        $divisis = Divisi::where(
            'instansi_id',
            $instansiId
        )->get();

        $teknisis = Teknisi::where(
            'instansi_id',
            $instansiId
        )->get();

        $pembimbings = User::where('instansi_id', $instansiId)
            ->where('role', 'pembimbing_instansi')
            ->orderBy('name')
            ->get();

        return view(
            'admin_instansi.peserta.create',
            compact(
                'divisis',
                'teknisis',
                'pembimbings'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $instansi = auth()->user()->instansi;

        $rules = [
            'name'            => 'required|string|max:255',
            'asal_sekolah_pt' => 'required|string|max:255',
            'nim_nisn'        => 'required|string|max:50',
            'email'           => 'required|email|unique:users,email',
            'nomor_telepon'   => 'required|string|max:25',
            'alamat'          => 'required|string',
            'pembimbing_id'   => 'required|exists:users,id',
        ];

        if ($instansi && in_array($instansi->jenis_instansi, ['kantor', 'pemerintahan'])) {
            $rules['divisi_id'] = 'required|exists:divisis,id';
        }

        if ($instansi && $instansi->jenis_instansi === 'lapangan') {
            $rules['teknisi_id'] = 'required|exists:teknisis,id';
        }

        $request->validate($rules);

        // Buat Akun Peserta dengan Password Sementara (Belum Aktif sebelum verifikasi OTP & set password mandiri)
        $tempPassword = \Illuminate\Support\Str::random(32);

        // Otomatisasi tipe penempatan sesuai jenis instansi
        $tipePenempatan = ($instansi && $instansi->jenis_instansi === 'lapangan') ? 'lapangan' : 'kantor';

        $peserta = User::create([
            'name'            => $request->name,
            'asal_sekolah_pt' => $request->asal_sekolah_pt,
            'nim_nisn'        => $request->nim_nisn,
            'email'           => $request->email,
            'password'        => Hash::make($tempPassword),
            'role'            => 'peserta',
            'tipe_penempatan' => $tipePenempatan,
            'instansi_id'     => auth()->user()->instansi_id,
            'divisi_id'       => $request->divisi_id,
            'teknisi_id'      => $request->teknisi_id,
            'pembimbing_id'   => $request->pembimbing_id,
            'nomor_telepon'   => $request->nomor_telepon,
            'alamat'          => $request->alamat,
            'is_active'       => false,
        ]);

        // Sinkronisasi ke pivot pembimbing_peserta untuk kompatibilitas modul laporan & dashboard pembimbing
        if ($request->pembimbing_id) {
            \Illuminate\Support\Facades\DB::table('pembimbing_peserta')->updateOrInsert(
                [
                    'pembimbing_id' => $request->pembimbing_id,
                    'peserta_id'    => $peserta->id,
                ],
                [
                    'instansi_id'   => auth()->user()->instansi_id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }

        // Generate OTP Aktivasi untuk Peserta
        \App\Models\Otp::generate($peserta->email, 'aktivasi_peserta', $peserta->id);

        return redirect()
            ->route('peserta.index')
            ->with(
                'success',
                "Akun Peserta Magang {$peserta->name} berhasil didaftarkan! Kode OTP aktivasi telah dikirimkan secara otomatis ke email peserta yang terdaftar."
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $peserta = User::where(
                'instansi_id',
                auth()->user()->instansi_id
            )
            ->where('id', $id)
            ->firstOrFail();

        $divisis = Divisi::where(
            'instansi_id',
            auth()->user()->instansi_id
        )->get();

        $teknisis = Teknisi::where(
            'instansi_id',
            auth()->user()->instansi_id
        )->get();

        $pembimbings = User::where('instansi_id', auth()->user()->instansi_id)
            ->where('role', 'pembimbing_instansi')
            ->orderBy('name')
            ->get();

        return view(
            'admin_instansi.peserta.edit',
            compact(
                'peserta',
                'divisis',
                'teknisis',
                'pembimbings'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $peserta = User::where(
                'instansi_id',
                auth()->user()->instansi_id
            )
            ->where('id', $id)
            ->firstOrFail();

        $peserta->update([

            'name'          => $request->name,

            'email'         => $request->email,

            'divisi_id'     => $request->divisi_id,

            'teknisi_id'    => $request->teknisi_id,

            'pembimbing_id' => $request->pembimbing_id,

            'nim'           => $request->nim,

            'no_hp'         => $request->no_hp,

            'alamat'        => $request->alamat,

        ]);

        if ($request->pembimbing_id) {
            \Illuminate\Support\Facades\DB::table('pembimbing_peserta')->updateOrInsert(
                [
                    'peserta_id' => $peserta->id,
                ],
                [
                    'pembimbing_id' => $request->pembimbing_id,
                    'instansi_id'   => auth()->user()->instansi_id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE PASSWORD JIKA DIISI
        |--------------------------------------------------------------------------
        */

        if ($request->password) {

            $peserta->update([

                'password' => Hash::make($request->password)

            ]);
        }

        return redirect()
            ->route('peserta.index')
            ->with(
                'success',
                'Peserta berhasil diupdate'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $peserta = User::where(
                'instansi_id',
                auth()->user()->instansi_id
            )
            ->where('id', $id)
            ->firstOrFail();

        $peserta->delete();

        return redirect()
            ->route('peserta.index')
            ->with(
                'success',
                'Peserta berhasil dihapus'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER QUERY FILTER REKAP ABSENSI
    |--------------------------------------------------------------------------
    */
    private function buildRekapQuery(Request $request, $instansiId)
    {
        $pesertaIds = User::where('role', 'peserta')
            ->where('instansi_id', $instansiId)
            ->pluck('id')
            ->toArray();

        $query = Absensi::with(['user.divisi', 'perizinan'])
            ->whereIn('user_id', $pesertaIds);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipe_absensi')) {
            $query->where('tipe_absensi', $request->tipe_absensi);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | REKAP ABSENSI PESERTA (MULTI-FILTER & STATISTIK)
    |--------------------------------------------------------------------------
    */
    public function rekapAbsensi(Request $request)
    {
        $instansiId = auth()->user()->instansi_id;

        $pesertas = User::where('role', 'peserta')
            ->where('instansi_id', $instansiId)
            ->orderBy('name')
            ->get();

        $query = $this->buildRekapQuery($request, $instansiId);

        // Hitung statistik berdasarkan filter aktif
        $totalPresensi = (clone $query)->count();
        $totalHadir    = (clone $query)->where('status', 'hadir')->count();
        $totalTelat    = (clone $query)->where('status', 'telat')->count();
        $totalMasuk    = (clone $query)->where('tipe_absensi', 'masuk')->count();
        $totalPulang   = (clone $query)->where('tipe_absensi', 'pulang')->count();

        $rekap = $query->latest('tanggal')
            ->latest('jam')
            ->paginate(20)
            ->withQueryString();

        return view('admin_instansi.rekap_absensi', compact(
            'rekap',
            'pesertas',
            'totalPresensi',
            'totalHadir',
            'totalTelat',
            'totalMasuk',
            'totalPulang'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF REKAP ABSENSI (FORMAT BERSIH TANPA KOP SURAT)
    |--------------------------------------------------------------------------
    */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $instansi = $user->instansi;
        $instansiId = $user->instansi_id;

        $query = $this->buildRekapQuery($request, $instansiId);
        $rekap = $query->oldest('tanggal')->oldest('jam')->get();

        $totalHadir = $rekap->where('status', 'hadir')->count();
        $totalTelat = $rekap->where('status', 'telat')->count();
        $totalData  = $rekap->count();

        $selectedPeserta = $request->filled('user_id')
            ? User::find($request->user_id)
            : null;

        $periodeText = 'Semua Periode';
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $periodeText = \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y');
        } elseif ($request->filled('tanggal_mulai')) {
            $periodeText = 'Mulai ' . \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y');
        } elseif ($request->filled('tanggal_selesai')) {
            $periodeText = 'Hingga ' . \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y');
        }

        $pdf = Pdf::loadView('admin_instansi.rekap_pdf', compact(
            'rekap',
            'instansi',
            'user',
            'selectedPeserta',
            'periodeText',
            'totalHadir',
            'totalTelat',
            'totalData'
        ))->setPaper('a4', 'portrait');

        $fileName = 'Rekap_Presensi_' . ($selectedPeserta ? str_replace(' ', '_', $selectedPeserta->name) : 'Instansi') . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT CSV / EXCEL REKAP ABSENSI
    |--------------------------------------------------------------------------
    */
    public function exportCsv(Request $request)
    {
        $instansiId = auth()->user()->instansi_id;
        $query = $this->buildRekapQuery($request, $instansiId);
        $rekap = $query->oldest('tanggal')->oldest('jam')->get();

        $fileName = 'rekap_presensi_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($rekap) {
            $file = fopen('php://output', 'w');
            // Menulis UTF-8 BOM untuk kompatibilitas Excel bahasa Indonesia
            fputs($file, "\xEF\xBB\xBF");

            // Header Kolom CSV
            fputcsv($file, [
                'No',
                'Nama Peserta',
                'NIM / ID',
                'Divisi / Unit',
                'Tanggal',
                'Jam Presensi',
                'Tipe Presensi',
                'Status Kehadiran',
                'Jarak Radius (Meter)',
                'Keterangan Izin / Keterlambatan',
                'Integritas HMAC',
            ]);

            foreach ($rekap as $index => $item) {
                $izinInfo = '-';
                if ($item->perizinan) {
                    $izinInfo = ucfirst($item->perizinan->jenis_izin) . ' (' . $item->perizinan->alasan . ')';
                }

                fputcsv($file, [
                    $index + 1,
                    $item->user->name ?? '-',
                    $item->user->nim ?? '-',
                    $item->user->divisi->nama_divisi ?? '-',
                    $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-',
                    $item->jam ?? '-',
                    ucfirst($item->tipe_absensi ?? '-'),
                    ucfirst($item->status ?? '-'),
                    $item->jarak ? $item->jarak . ' m' : '-',
                    $izinInfo,
                    $item->hmac_signature ? 'Valid (HMAC Terverifikasi)' : 'Standar',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

