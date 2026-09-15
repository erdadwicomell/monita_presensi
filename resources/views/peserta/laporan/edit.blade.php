@extends('layouts.app')

@section('title', 'Perbaiki Laporan Kegiatan')
@section('page-title', 'Perbaiki Laporan')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- REVISION NOTE IF REVISI -->
    @if($laporan->status === 'revisi' && $laporan->catatan_revisi)
        <div class="p-5 bg-amber-50 border-2 border-amber-300 rounded-2xl space-y-2">
            <div class="flex items-center gap-2 text-amber-800 font-bold text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Catatan Revisi dari Pembimbing:</span>
            </div>
            <p class="text-sm text-amber-900 leading-relaxed">
                {{ $laporan->catatan_revisi }}
            </p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow overflow-hidden">

        <div class="border-b px-8 py-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Edit / Perbaiki Laporan Kegiatan
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Lakukan perbaikan pada uraian kegiatan atau foto sesuai instruksi pembimbing.
            </p>
        </div>

        <form action="{{ route('laporan.update', $laporan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-8 space-y-6">

                <!-- JUDUL -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Kegiatan</label>
                    <input type="text"
                           name="judul"
                           value="{{ old('judul', $judul) }}"
                           required
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           placeholder="Contoh: Instalasi Jaringan">
                </div>

                <!-- DESKRIPSI -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Kegiatan</label>
                    <textarea name="deskripsi"
                              rows="6"
                              required
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              placeholder="Jelaskan kegiatan yang dilakukan hari ini...">{{ old('deskripsi', $deskripsi) }}</textarea>
                </div>

                <!-- FOTO SAAT INI -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dokumentasi Foto Saat Ini</label>
                    @if($laporan->foto)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <img src="{{ asset($laporan->foto) }}" class="w-24 h-24 object-cover rounded-lg border shadow-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-700">Foto tersimpan sebelumnya</p>
                                <p class="text-xs text-gray-400 mt-1">Foto ini akan tetap digunakan jika Anda tidak mengambil foto baru.</p>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <div class="border-t border-gray-100 px-8 py-5 flex justify-end gap-3 bg-gray-50/50">
                <a href="{{ route('laporan.index') }}"
                   class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition">
                    Kirim Ulang Laporan
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
