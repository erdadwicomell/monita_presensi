@extends('layouts.app')

@section('page-title', 'Tambah Divisi')
@section('title', 'Tambah Divisi')

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 max-w-3xl">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
        <div>
            <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-sitemap mr-1"></i> Struktur Organisasi
            </span>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight mt-2">
                Tambah Divisi Baru
            </h1>
            <p class="text-slate-500 text-xs mt-1">
                Kelola unit kerja, penanggung jawab (Kepala Divisi/Kasubag), dan kuota alokasi peserta magang.
            </p>
        </div>

        <a href="{{ route('divisi.index') }}"
           class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2.5 rounded-xl text-xs transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- ERROR ALERT -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            @foreach ($errors->all() as $err)
                <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-500"></i> {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('divisi.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- BARIS 1: NAMA DIVISI & KODE DIVISI (GRID 2 KOLOM) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Nama Divisi <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="nama_divisi"
                       value="{{ old('nama_divisi') }}"
                       required
                       placeholder="Contoh: Teknologi Informasi & Jaringan"
                       class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Kode Divisi <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="kode_divisi"
                       value="{{ old('kode_divisi') }}"
                       required
                       maxlength="10"
                       placeholder="Contoh: TI, KEU, SDM"
                       class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs uppercase font-mono rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
            </div>
        </div>

        <!-- BARIS 2: KEPALA DIVISI / KASUBAG & KUOTA MAKSIMAL (GRID 2 KOLOM) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Kepala Divisi / Kasubag
                </label>
                <input type="text"
                       name="kepala_divisi"
                       value="{{ old('kepala_divisi') }}"
                       placeholder="Masukkan nama kepala divisi atau kasubag"
                       class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Kuota Maksimal Peserta <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       name="kuota_maksimal"
                       value="{{ old('kuota_maksimal', 5) }}"
                       min="1"
                       max="100"
                       required
                       class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
            </div>
        </div>

        <!-- BARIS 3: LOKASI RUANGAN / POSISI FISIK -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Lokasi Ruangan / Posisi Fisik
            </label>
            <input type="text"
                   name="lokasi_ruangan"
                   value="{{ old('lokasi_ruangan') }}"
                   placeholder="Contoh: Gedung A, Lantai 2, Ruang IT Support"
                   class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
        </div>

        <!-- BARIS 4: DESKRIPSI / TUGAS POKOK DIVISI -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Deskripsi / Tugas Pokok Divisi
            </label>
            <textarea name="deskripsi"
                      rows="3"
                      placeholder="Jelaskan ruang lingkup, fungsi, atau tugas pokok divisi ini..."
                      class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">{{ old('deskripsi') }}</textarea>
        </div>

        <!-- BUTTONS -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md shadow-blue-600/20 transition cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Divisi</span>
            </button>

            <a href="{{ route('divisi.index') }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-5 py-3 rounded-xl transition">
                Batal
            </a>
        </div>

    </form>

</div>

@endsection