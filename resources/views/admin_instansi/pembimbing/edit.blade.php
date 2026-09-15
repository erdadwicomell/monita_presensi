@extends('layouts.app')

@section('page-title', 'Edit Pembimbing Instansi')

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
        <div>
            <span class="text-xs bg-purple-50 text-purple-700 font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Data
            </span>
            <h1 class="text-2xl font-black text-gray-800 mt-2">
                Edit Data Pembimbing: {{ $pembimbing->name }}
            </h1>
            <p class="text-gray-500 text-xs mt-1">
                Perbarui informasi pembimbing dan alokasi peserta magang.
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
    <form action="{{ route('admin.pembimbing.update', $pembimbing->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama Lengkap Pembimbing</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $pembimbing->name) }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">NIP</label>
                <input type="text"
                       name="nip"
                       value="{{ old('nip', $pembimbing->nip) }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Jabatan</label>
                <input type="text"
                       name="jabatan"
                       value="{{ old('jabatan', $pembimbing->jabatan) }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">No. Telepon / WhatsApp</label>
                <input type="text"
                       name="nomor_telepon"
                       value="{{ old('nomor_telepon', $pembimbing->nomor_telepon ?? $pembimbing->no_hp) }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email', $pembimbing->email) }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Alamat</label>
                <input type="text"
                       name="alamat"
                       value="{{ old('alamat', $pembimbing->alamat) }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

        </div>

        <!-- ALOKASI PESERTA BIMBINGAN -->
        <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-users-line text-purple-600"></i>
                        Peserta Magang yang Dibimbing
                    </h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                        Pilih peserta magang yang dibimbing oleh pembimbing ini.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-2">
                @foreach($pesertas as $peserta)
                    <label class="p-3 bg-white rounded-xl border border-gray-200 hover:border-purple-300 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox"
                               name="peserta_ids[]"
                               value="{{ $peserta->id }}"
                               {{ in_array($peserta->id, old('peserta_ids', $assignedPesertaIds)) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 mt-0.5">
                        <div class="text-xs">
                            <span class="font-bold text-gray-800 block">{{ $peserta->name }}</span>
                            <span class="text-gray-400 text-[11px] block">{{ $peserta->nim_nisn ?? $peserta->nim ?? '-' }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- BUTTON -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.pembimbing.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-lg shadow-purple-600/30 transition flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>

    </form>

</div>

@endsection
