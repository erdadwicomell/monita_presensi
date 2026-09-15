@extends('layouts.app')

@section('title', 'Evaluasi Laporan Kegiatan')
@section('page-title', 'Evaluasi Laporan Kegiatan')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER & BACK -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Evaluasi Laporan Kegiatan
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Tinjau rincian kegiatan dan foto bukti untuk memberikan persetujuan atau catatan revisi.
            </p>
        </div>

        <a href="{{ route('admin.laporan.index') }}"
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-xl transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-white rounded-2xl shadow p-8 space-y-6">

        <!-- STATUS BANNER -->
        <div class="flex items-center justify-between p-4 rounded-xl border
            @if($laporan->status === 'disetujui') bg-emerald-50 border-emerald-200 text-emerald-800
            @elseif($laporan->status === 'revisi') bg-amber-50 border-amber-200 text-amber-800
            @else bg-yellow-50 border-yellow-200 text-yellow-800 @endif">
            <div class="flex items-center gap-3">
                <i class="fa-solid text-xl
                    @if($laporan->status === 'disetujui') fa-circle-check text-emerald-600
                    @elseif($laporan->status === 'revisi') fa-pen-ruler text-amber-600
                    @else fa-hourglass-half text-yellow-600 @endif">
                </i>
                <div>
                    <h3 class="font-bold text-base uppercase">
                        Status: {{ $laporan->status === 'revisi' ? 'Perlu Revisi' : $laporan->status }}
                    </h3>
                    <p class="text-xs opacity-80 mt-0.5">
                        @if($laporan->status === 'disetujui')
                            Laporan telah disetujui.
                        @elseif($laporan->status === 'revisi')
                            Laporan dikembalikan ke peserta dengan instruksi revisi.
                        @else
                            Menunggu evaluasi pembimbing / admin.
                        @endif
                    </p>
                </div>
            </div>

            @if($laporan->diverifikasi_pada)
                <div class="text-right text-xs">
                    <p class="font-semibold">Evaluator:</p>
                    <p>{{ $laporan->verifikator->name ?? 'Pembimbing' }} ({{ $laporan->diverifikasi_pada->format('d M Y, H:i') }} WIB)</p>
                </div>
            @endif
        </div>

        <!-- REVISION NOTE IF REVISI -->
        @if($laporan->status === 'revisi' && $laporan->catatan_revisi)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-1">
                <p class="text-xs font-bold text-amber-800 uppercase tracking-wider">Catatan Revisi Terakhir:</p>
                <p class="text-sm text-amber-900 leading-relaxed">{{ $laporan->catatan_revisi }}</p>
            </div>
        @endif

        <!-- DETAIL GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Peserta</label>
                <p class="text-base font-bold text-gray-800 mt-1">
                    {{ $laporan->user->name ?? '-' }}
                </p>
                <p class="text-xs text-gray-500">{{ $laporan->user->email ?? '-' }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu Laporan</label>
                <p class="text-base font-bold text-gray-800 mt-1">
                    {{ $laporan->tanggal->format('d F Y') }}
                </p>
                <p class="text-xs text-gray-500">Pukul {{ substr($laporan->jam, 0, 5) }} WIB</p>
            </div>
        </div>

        <!-- URAIAN KEGIATAN -->
        <div class="pt-4 border-t border-gray-100">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Uraian Kegiatan</label>
            <div class="mt-2 bg-gray-50 p-5 rounded-xl text-gray-800 text-sm leading-relaxed whitespace-pre-line border border-gray-100 font-mono">
{{ $laporan->kegiatan }}
            </div>
        </div>

        <!-- FOTO DOKUMENTASI -->
        <div class="pt-4 border-t border-gray-100">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dokumentasi Foto Realtime + GPS</label>
            <div class="mt-3">
                @if($laporan->foto)
                    <div class="space-y-3">
                        <img src="{{ asset($laporan->foto) }}"
                             alt="Dokumentasi Laporan"
                             class="max-h-96 rounded-xl border object-contain shadow-md">
                        <a href="{{ asset($laporan->foto) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-blue-600 font-medium hover:underline">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Foto Ukuran Penuh
                        </a>
                    </div>
                @else
                    <p class="text-gray-400 text-sm italic">Peserta tidak menyertakan foto dokumentasi.</p>
                @endif
            </div>
        </div>

        <!-- EVALUATION ACTIONS -->
        <div class="pt-6 border-t border-gray-100 space-y-4">
            <h3 class="text-base font-bold text-gray-800">Form Keputusan Pembimbing</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- SETUJUI -->
                <div class="p-6 bg-emerald-50/50 rounded-2xl border border-emerald-200 flex flex-col justify-between space-y-4">
                    <div>
                        <h4 class="font-bold text-emerald-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-check"></i> Setujui Laporan Ini
                        </h4>
                        <p class="text-xs text-gray-600 mt-1">
                            Laporan kegiatan dinilai valid dan sesuai ketentuan magang harian.
                        </p>
                    </div>

                    <form action="{{ route('admin.laporan.setujui', $laporan->id) }}"
                          method="POST"
                          onsubmit="return confirm('Apakah Anda yakin menyetujui laporan kegiatan ini?');">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow transition text-sm">
                            <i class="fa-solid fa-circle-check mr-1.5"></i> Setujui Laporan
                        </button>
                    </form>
                </div>

                <!-- MINTA REVISI -->
                <div class="p-6 bg-amber-50/50 rounded-2xl border border-amber-200 space-y-4">
                    <div>
                        <h4 class="font-bold text-amber-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-pen-ruler"></i> Minta Revisi / Perbaikan
                        </h4>
                        <p class="text-xs text-gray-600 mt-1">
                            Berikan catatan yang jelas bagian mana yang harus diperbaiki oleh peserta.
                        </p>
                    </div>

                    <form action="{{ route('admin.laporan.revisi', $laporan->id) }}"
                          method="POST"
                          onsubmit="return confirm('Kirim instruksi revisi ke peserta?');">
                        @csrf
                        @method('PUT')
                        <textarea name="catatan_revisi"
                                  rows="3"
                                  required
                                  class="w-full border border-amber-300 rounded-xl p-3 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none bg-white"
                                  placeholder="Tuliskan catatan revisi untuk peserta..."></textarea>

                        <button type="submit"
                                class="mt-3 w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow transition text-sm">
                            <i class="fa-solid fa-paper-plane mr-1.5"></i> Kirim Catatan Revisi
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

</div>

@endsection
