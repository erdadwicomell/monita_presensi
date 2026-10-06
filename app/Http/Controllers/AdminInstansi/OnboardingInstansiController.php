<?php

namespace App\Http\Controllers\AdminInstansi;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use Illuminate\Http\Request;

class OnboardingInstansiController extends Controller
{
    /**
     * Tampilkan formulir pendataan instansi pertama kali
     */
    public function create()
    {
        $user = auth()->user();

        // Jika sudah memiliki instansi, langsung ke dashboard
        if ($user->instansi_id) {
            return redirect()->route('dashboard');
        }

        return view('admin_instansi.onboarding');
    }

    /**
     * Simpan data instansi baru dan kaitkan ke admin instansi yang login
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_instansi'     => ['required', 'string', 'max:255'],
            'jenis_instansi'    => ['required', 'in:kantor,lapangan,pemerintahan'],
            'alamat'            => ['required', 'string'],
            'latitude'          => ['required', 'numeric', 'between:-90,90'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
            'radius'            => ['required', 'integer', 'min:10', 'max:5000'],
            'jam_masuk_mulai'   => ['required', 'date_format:H:i'],
            'jam_masuk_batas'   => ['required', 'date_format:H:i', 'after:jam_masuk_mulai'],
            'jam_pulang_mulai'  => ['required', 'date_format:H:i', 'after:jam_masuk_batas'],
            'jam_pulang_batas'  => ['required', 'date_format:H:i', 'after:jam_pulang_mulai'],
        ]);

        $user = auth()->user();

        // Normalisasi koordinat & jenis instansi (menjamin hanya 'kantor' atau 'lapangan')
        $lat = round((float) $request->latitude, 7);
        $lng = round((float) $request->longitude, 7);
        $jenisInstansi = ($request->jenis_instansi === 'lapangan') ? 'lapangan' : 'kantor';

        // Buat instansi baru
        $instansi = Instansi::create([
            'nama_instansi'     => $request->nama_instansi,
            'jenis_instansi'    => $jenisInstansi,
            'alamat'            => $request->alamat,
            'latitude'          => $lat,
            'longitude'         => $lng,
            'radius'            => (int) $request->radius,
            'jam_masuk_mulai'   => $request->jam_masuk_mulai,
            'jam_masuk_batas'   => $request->jam_masuk_batas,
            'jam_pulang_mulai'  => $request->jam_pulang_mulai,
            'jam_pulang_batas'  => $request->jam_pulang_batas,
        ]);

        // Kaitkan admin ke instansi ini
        $user->update([
            'instansi_id' => $instansi->id,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Pendataan instansi berhasil disimpan! Selamat datang di MONITA.');
    }
}
