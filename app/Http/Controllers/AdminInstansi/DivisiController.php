<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Divisi;
use App\Models\User;

class DivisiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $divisis = Divisi::where(
            'instansi_id',
            auth()->user()->instansi_id
        )
        ->with(['kepalaDivisi', 'pesertas'])
        ->latest()
        ->get();

        return view('admin_instansi.divisi.index', compact('divisis'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $pembimbings = User::where('instansi_id', auth()->user()->instansi_id)
            ->where('role', 'pembimbing_instansi')
            ->orderBy('name')
            ->get();

        return view('admin_instansi.divisi.create', compact('pembimbings'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $instansiId = auth()->user()->instansi_id;

        $request->validate([
            'nama_divisi'      => 'required|string|max:255',
            'kode_divisi'      => 'required|string|max:10',
            'kepala_divisi'    => 'nullable|string|max:255',
            'kepala_divisi_id' => 'nullable|exists:users,id',
            'lokasi_ruangan'   => 'nullable|string|max:100',
            'kuota_maksimal'   => 'required|integer|min:1|max:100',
            'deskripsi'        => 'nullable|string',
        ]);

        Divisi::create([
            'instansi_id'      => $instansiId,
            'nama_divisi'      => $request->nama_divisi,
            'kode_divisi'      => strtoupper(trim($request->kode_divisi)),
            'kepala_divisi'    => $request->kepala_divisi,
            'kepala_divisi_id' => $request->kepala_divisi_id,
            'lokasi_ruangan'   => $request->lokasi_ruangan,
            'kuota_maksimal'   => $request->kuota_maksimal ?? 5,
            'deskripsi'        => $request->deskripsi,
        ]);

        return redirect()->route('divisi.index')
            ->with('success', 'Divisi berhasil dibuat');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(string $id)
    {
        $divisi = Divisi::where('instansi_id', auth()->user()->instansi_id)
            ->findOrFail($id);

        $pembimbings = User::where('instansi_id', auth()->user()->instansi_id)
            ->where('role', 'pembimbing_instansi')
            ->orderBy('name')
            ->get();

        return view('admin_instansi.divisi.edit', compact('divisi', 'pembimbings'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, string $id)
    {
        $divisi = Divisi::where('instansi_id', auth()->user()->instansi_id)
            ->findOrFail($id);

        $request->validate([
            'nama_divisi'      => 'required|string|max:255',
            'kode_divisi'      => 'required|string|max:10',
            'kepala_divisi'    => 'nullable|string|max:255',
            'kepala_divisi_id' => 'nullable|exists:users,id',
            'lokasi_ruangan'   => 'nullable|string|max:100',
            'kuota_maksimal'   => 'required|integer|min:1|max:100',
            'deskripsi'        => 'nullable|string',
        ]);

        $divisi->update([
            'nama_divisi'      => $request->nama_divisi,
            'kode_divisi'      => strtoupper(trim($request->kode_divisi)),
            'kepala_divisi'    => $request->kepala_divisi,
            'kepala_divisi_id' => $request->kepala_divisi_id,
            'lokasi_ruangan'   => $request->lokasi_ruangan,
            'kuota_maksimal'   => $request->kuota_maksimal ?? 5,
            'deskripsi'        => $request->deskripsi,
        ]);

        return redirect()->route('divisi.index')
            ->with('success', 'Divisi berhasil diupdate');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(string $id)
    {
        $divisi = Divisi::findOrFail($id);

        $divisi->delete();

        return redirect('/divisi')
            ->with('success', 'Divisi berhasil dihapus');
    }
}