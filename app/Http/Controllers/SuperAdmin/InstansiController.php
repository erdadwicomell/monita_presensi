<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instansi;

class InstansiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $instansi = Instansi::latest()->get();

        return view('super_admin.instansi.index', compact('instansi'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('super_admin.instansi.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'nama_instansi'    => 'required',
            'no_telp'          => 'required',
            'jenis_instansi'   => 'required',
            'alamat'           => 'required',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'radius'           => 'required|numeric',
            'jam_masuk_mulai'  => 'nullable',
            'jam_masuk_batas'  => 'nullable',
            'jam_pulang_mulai' => 'nullable',
            'jam_pulang_batas' => 'nullable',
        ]);

        Instansi::create([
            'nama_instansi'    => $request->nama_instansi,
            'no_telp'          => $request->no_telp,
            'jenis_instansi'   => $request->jenis_instansi,
            'alamat'           => $request->alamat,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'radius'           => $request->radius,
            'jam_masuk_mulai'  => $request->jam_masuk_mulai ?? '07:00:00',
            'jam_masuk_batas'  => $request->jam_masuk_batas ?? '09:00:00',
            'jam_pulang_mulai' => $request->jam_pulang_mulai ?? '16:00:00',
            'jam_pulang_batas' => $request->jam_pulang_batas ?? '18:00:00',
        ]);

        return redirect('/instansi')
            ->with('success', 'Instansi berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(string $id)
    {
        $instansi = Instansi::findOrFail($id);

        return view('super_admin.instansi.show', compact('instansi'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(string $id)
    {
        $instansi = Instansi::findOrFail($id);

        return view('super_admin.instansi.edit', compact('instansi'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_instansi'    => 'required',
            'no_telp'          => 'required',
            'jenis_instansi'   => 'required',
            'alamat'           => 'required',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'radius'           => 'required|numeric',
            'jam_masuk_mulai'  => 'nullable',
            'jam_masuk_batas'  => 'nullable',
            'jam_pulang_mulai' => 'nullable',
            'jam_pulang_batas' => 'nullable',
        ]);

        $instansi = Instansi::findOrFail($id);

        $instansi->update([
            'nama_instansi'    => $request->nama_instansi,
            'no_telp'          => $request->no_telp,
            'jenis_instansi'   => $request->jenis_instansi,
            'alamat'           => $request->alamat,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'radius'           => $request->radius,
            'jam_masuk_mulai'  => $request->jam_masuk_mulai ?? $instansi->jam_masuk_mulai ?? '07:00:00',
            'jam_masuk_batas'  => $request->jam_masuk_batas ?? $instansi->jam_masuk_batas ?? '09:00:00',
            'jam_pulang_mulai' => $request->jam_pulang_mulai ?? $instansi->jam_pulang_mulai ?? '16:00:00',
            'jam_pulang_batas' => $request->jam_pulang_batas ?? $instansi->jam_pulang_batas ?? '18:00:00',
        ]);

        return redirect('/instansi')
            ->with('success', 'Instansi berhasil diupdate');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(string $id)
    {
        $instansi = Instansi::findOrFail($id);

        $instansi->delete();

        return redirect('/instansi')
            ->with('success', 'Instansi berhasil dihapus');
    }
}