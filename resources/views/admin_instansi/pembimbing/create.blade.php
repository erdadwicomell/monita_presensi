@extends('layouts.app')

@section('page-title', 'Tambah Pembimbing Instansi')

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
        <div>
            <span class="text-xs bg-purple-50 text-purple-700 font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-user-tie mr-1"></i> Pembimbing Instansi
            </span>
            <h1 class="text-2xl font-black text-gray-800 mt-2">
                Pendaftaran Pembimbing & Alokasi Peserta
            </h1>
            <p class="text-gray-500 text-xs mt-1">
                Daftarkan akun pembimbing lapangan dan tentukan daftar peserta magang yang dibimbing.
            </p>
        </div>

        <a href="{{ route('admin.pembimbing.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-5 py-2.5 rounded-xl text-xs transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- ERROR ALERT -->
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- FORM -->
    <form action="{{ route('admin.pembimbing.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- NAMA PEMBIMBING -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Nama Lengkap Pembimbing <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       placeholder="Contoh: Ir. Hendra Wijaya, M.Kom"
                       required>
            </div>

            <!-- NIP -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    NIP / No. Induk Pegawai <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="nip"
                       value="{{ old('nip') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       placeholder="Contoh: 198507152010011002"
                       required>
            </div>

            <!-- JABATAN -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Jabatan / Posisi di Instansi <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="jabatan"
                       value="{{ old('jabatan') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       placeholder="Contoh: Senior Network Engineer / Supervisor"
                       required>
            </div>

            <!-- NOMOR TELEPON -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="nomor_telepon"
                       value="{{ old('nomor_telepon') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       placeholder="081345678901"
                       required>
            </div>

            <!-- EMAIL -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Email Pembimbing (Untuk OTP & Login) <span class="text-rose-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       placeholder="pembimbing@instansi.com"
                       required>
            </div>

            <!-- ALAMAT -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Alamat Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="alamat"
                       value="{{ old('alamat') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       placeholder="Alamat domisili atau kantor..."
                       required>
            </div>

        </div>

        <!-- ALOKASI PESERTA BIMBINGAN -->
        <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-users-line text-purple-600"></i>
                        Pilih Peserta Magang yang Dibimbing
                    </h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                        Centang peserta yang menjadi tanggung jawab monitoring dan verifikasi laporan bagi pembimbing ini.
                    </p>
                </div>
                <span class="text-xs font-semibold text-purple-600 bg-purple-100 px-2.5 py-0.5 rounded-full">
                    {{ $pesertas->count() }} Peserta Tersedia
                </span>
            </div>

            @if($pesertas->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-2">
                    @foreach($pesertas as $peserta)
                        <label class="p-3 bg-white rounded-xl border border-gray-200 hover:border-purple-300 cursor-pointer flex items-start gap-2.5 transition">
                            <input type="checkbox"
                                   name="peserta_ids[]"
                                   value="{{ $peserta->id }}"
                                   {{ is_array(old('peserta_ids')) && in_array($peserta->id, old('peserta_ids')) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 mt-0.5">
                            <div class="text-xs">
                                <span class="font-bold text-gray-800 block">{{ $peserta->name }}</span>
                                <span class="text-gray-400 text-[11px] block">{{ $peserta->nim_nisn ?? $peserta->nim ?? '-' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 py-3 text-center">
                    Belum ada peserta magang pada instansi ini. Anda dapat menambahkan alokasi nanti.
                </p>
            @endif
        </div>

        <!-- INFO OTP AKTIVASI -->
        <div class="p-4 bg-purple-50 rounded-2xl border border-purple-100 flex items-start gap-3">
            <i class="fa-solid fa-shield-halved text-purple-600 text-lg mt-0.5"></i>
            <div>
                <h4 class="text-xs font-bold text-purple-900">Aktivasi Akun Mandiri Pembimbing</h4>
                <p class="text-[11px] text-purple-700 mt-0.5 leading-relaxed">
                    Pembimbing akan menerima <strong>Kode OTP Aktivasi</strong>. Pembimbing dapat mengaktifkan akun dan mengatur password akunnya sendiri melalui halaman verifikasi OTP.
                </p>
            </div>
        </div>

        <!-- BUTTON -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.pembimbing.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-lg shadow-purple-600/30 transition flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Daftarkan Pembimbing & Terbitkan OTP</span>
            </button>
        </div>

    </form>

</div>

@endsection
