<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminPembimbingController extends Controller
{
    /**
     * Tampilkan daftar Pembimbing Instansi
     */
    public function index()
    {
        $instansiId = auth()->user()->instansi_id;

        $pembimbings = User::with('pesertaBimbingan')
            ->where('role', 'pembimbing_instansi')
            ->where('instansi_id', $instansiId)
            ->latest()
            ->paginate(15);

        return view('admin_instansi.pembimbing.index', compact('pembimbings'));
    }

    /**
     * Tampilkan form registrasi Pembimbing Instansi baru
     */
    public function create()
    {
        $instansiId = auth()->user()->instansi_id;

        // Ambil peserta magang pada instansi ini untuk di-assign
        $pesertas = User::where('role', 'peserta')
            ->where('instansi_id', $instansiId)
            ->orderBy('name')
            ->get();

        return view('admin_instansi.pembimbing.create', compact('pesertas'));
    }

    /**
     * Simpan data Pembimbing Instansi & kirim OTP aktivasi
     */
    public function store(Request $request)
    {
        $instansiId = auth()->user()->instansi_id;

        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'nip'            => ['required', 'string', 'max:50'],
            'alamat'         => ['required', 'string'],
            'jabatan'        => ['required', 'string', 'max:100'],
            'nomor_telepon'  => ['required', 'string', 'max:25'],
            'email'          => ['required', 'email', 'unique:users,email'],
            'peserta_ids'    => ['nullable', 'array'],
            'peserta_ids.*'  => ['exists:users,id'],
        ]);

        $tempPassword = Str::random(32);

        $pembimbing = User::create([
            'name'          => $request->name,
            'nip'           => $request->nip,
            'alamat'        => $request->alamat,
            'jabatan'       => $request->jabatan,
            'nomor_telepon' => $request->nomor_telepon,
            'email'         => $request->email,
            'password'      => Hash::make($tempPassword),
            'role'          => 'pembimbing_instansi',
            'instansi_id'   => $instansiId,
            'is_active'     => false,
        ]);

        // Hubungkan peserta yang dibimbing
        if ($request->filled('peserta_ids')) {
            $pembimbing->pesertaBimbingan()->syncWithPivotValues($request->peserta_ids, ['instansi_id' => $instansiId]);
        }

        // Generate OTP Aktivasi
        $otp = Otp::generate($pembimbing->email, 'aktivasi_pembimbing', $pembimbing->id);

        $msg = "Pembimbing Instansi {$pembimbing->name} berhasil didaftarkan! Kode OTP Aktivasi: {$otp->otp_code}.";
        if (isset($otp->mail_sent) && !$otp->mail_sent) {
            $msg .= " (Catatan: Pengiriman email terhambat firewall jaringan).";
        }

        return redirect()->route('admin.pembimbing.index')
            ->with('success', $msg);
    }

    /**
     * Tampilkan form edit Pembimbing Instansi
     */
    public function edit($id)
    {
        $instansiId = auth()->user()->instansi_id;

        $pembimbing = User::with('pesertaBimbingan')
            ->where('role', 'pembimbing_instansi')
            ->where('instansi_id', $instansiId)
            ->findOrFail($id);

        $pesertas = User::where('role', 'peserta')
            ->where('instansi_id', $instansiId)
            ->orderBy('name')
            ->get();

        $assignedPesertaIds = $pembimbing->pesertaBimbingan->pluck('id')->toArray();

        return view('admin_instansi.pembimbing.edit', compact('pembimbing', 'pesertas', 'assignedPesertaIds'));
    }

    /**
     * Update data Pembimbing Instansi
     */
    public function update(Request $request, $id)
    {
        $instansiId = auth()->user()->instansi_id;

        $pembimbing = User::where('role', 'pembimbing_instansi')
            ->where('instansi_id', $instansiId)
            ->findOrFail($id);

        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'nip'            => ['required', 'string', 'max:50'],
            'alamat'         => ['required', 'string'],
            'jabatan'        => ['required', 'string', 'max:100'],
            'nomor_telepon'  => ['required', 'string', 'max:25'],
            'email'          => ['required', 'email', 'unique:users,email,' . $pembimbing->id],
            'peserta_ids'    => ['nullable', 'array'],
            'peserta_ids.*'  => ['exists:users,id'],
        ]);

        $pembimbing->update([
            'name'          => $request->name,
            'nip'           => $request->nip,
            'alamat'        => $request->alamat,
            'jabatan'       => $request->jabatan,
            'nomor_telepon' => $request->nomor_telepon,
            'no_hp'         => $request->nomor_telepon,
            'email'         => $request->email,
        ]);

        // Sync Peserta Bimbingan
        $pembimbing->pesertaBimbingan()->syncWithPivotValues($request->peserta_ids ?? [], ['instansi_id' => $instansiId]);

        return redirect()->route('admin.pembimbing.index')
            ->with('success', "Data Pembimbing {$pembimbing->name} berhasil diperbarui.");
    }

    /**
     * Hapus Pembimbing Instansi
     */
    public function destroy($id)
    {
        $instansiId = auth()->user()->instansi_id;

        $pembimbing = User::where('role', 'pembimbing_instansi')
            ->where('instansi_id', $instansiId)
            ->findOrFail($id);

        $pembimbing->delete();

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Pembimbing Instansi berhasil dihapus.');
    }
}
