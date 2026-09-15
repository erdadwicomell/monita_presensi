<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Instansi;

use Illuminate\Support\Facades\Hash;

class AdminInstansiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $admins = User::where('role', 'admin_instansi')->get();

        return view('super_admin.admin_instansi.index', compact('admins'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $instansis = Instansi::all();

        return view('super_admin.admin_instansi.create', compact('instansis'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        User::create([

            'name' => $request->name,
            'email' => $request->email,

            'password' => Hash::make($request->password),

            'role' => 'admin_instansi',

            'instansi_id' => $request->instansi_id

        ]);

        return redirect('/admin-instansi')
            ->with('success', 'Admin instansi berhasil dibuat');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(string $id)
    {
        $admin = User::findOrFail($id);

        $instansis = Instansi::all();

        return view('super_admin.admin_instansi.edit', compact('admin', 'instansis'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, string $id)
    {
        $admin = User::findOrFail($id);

        $admin->update([

            'name' => $request->name,
            'email' => $request->email,
            'instansi_id' => $request->instansi_id

        ]);

        return redirect('/admin-instansi')
            ->with('success', 'Admin berhasil diupdate');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(string $id)
    {
        $admin = User::findOrFail($id);

        $admin->delete();

        return redirect('/admin-instansi')
            ->with('success', 'Admin berhasil dihapus');
    }
}