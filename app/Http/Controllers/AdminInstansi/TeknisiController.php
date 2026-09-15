<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Teknisi;

class TeknisiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $teknisis = Teknisi::where(

            'instansi_id',
            auth()->user()->instansi_id

        )->latest()->get();

        return view('admin_instansi.teknisi.index', compact('teknisis'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin_instansi.teknisi.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'nama' => 'required',
            'email' => 'required|email',
            'no_hp' => 'required',
            'nik' => 'required',
            'alamat_kerja' => 'required',

        ]);

        Teknisi::create([

            'instansi_id' => auth()->user()->instansi_id,

            'nama' => $request->nama,

            'email' => $request->email,

            'no_hp' => $request->no_hp,

            'nik' => $request->nik,

            'alamat_kerja' => $request->alamat_kerja,

        ]);

        return redirect()
            ->route('teknisi.index')
            ->with('success', 'Teknisi berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(string $id)
    {
        $teknisi = Teknisi::findOrFail($id);

        return view('admin_instansi.teknisi.edit', compact('teknisi'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, string $id)
    {
        $request->validate([

            'nama' => 'required',
            'email' => 'required|email',
            'no_hp' => 'required',
            'nik' => 'required',
            'alamat_kerja' => 'required',

        ]);

        $teknisi = Teknisi::findOrFail($id);

        $teknisi->update([

            'nama' => $request->nama,

            'email' => $request->email,

            'no_hp' => $request->no_hp,

            'nik' => $request->nik,

            'alamat_kerja' => $request->alamat_kerja,

        ]);

        return redirect()
            ->route('teknisi.index')
            ->with('success', 'Teknisi berhasil diupdate');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(string $id)
    {
        $teknisi = Teknisi::findOrFail($id);

        $teknisi->delete();

        return redirect()
            ->route('teknisi.index')
            ->with('success', 'Teknisi berhasil dihapus');
    }
}