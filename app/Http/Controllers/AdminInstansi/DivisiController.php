<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Divisi;

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

        )->latest()->get();

        return view('admin_instansi.divisi.index', compact('divisis'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin_instansi.divisi.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'nama_divisi' => 'required'

        ]);

        Divisi::create([

            'instansi_id' => auth()->user()->instansi_id,

            'nama_divisi' => $request->nama_divisi,

            'deskripsi' => $request->deskripsi

        ]);

        return redirect('/divisi')
            ->with('success', 'Divisi berhasil dibuat');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(string $id)
    {
        $divisi = Divisi::findOrFail($id);

        return view('admin_instansi.divisi.edit', compact('divisi'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, string $id)
    {
        $divisi = Divisi::findOrFail($id);

        $divisi->update([

            'nama_divisi' => $request->nama_divisi,

            'deskripsi' => $request->deskripsi

        ]);

        return redirect('/divisi')
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