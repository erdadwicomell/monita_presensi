@extends('layouts.app')

@section('title', 'Detail Laporan Kegiatan')
@section('page-title', 'Detail Laporan Kegiatan')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER & BACK -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Detail Laporan Kegiatan
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Informasi detail kegiatan harian dan status verifikasi pembimbing.
            </p>
        </div>

        <a href="{{ route('laporan.index') }}"
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
                            Laporan telah diverifikasi dan disetujui oleh pembimbing.
                        @elseif($laporan->status === 'revisi')
                            Pembimbing meminta revisi/perbaikan pada laporan ini.
                        @else
                            Laporan sedang menunggu evaluasi dan verifikasi pembimbing.
                        @endif
                    </p>
                </div>
            </div>

            @if($laporan->diverifikasi_pada)
                <div class="text-right text-xs">
                    <p class="font-semibold">Diverifikasi Oleh:</p>
                    <p>{{ $laporan->verifikator->name ?? 'Pembimbing' }} ({{ $laporan->diverifikasi_pada->format('d M Y, H:i') }} WIB)</p>
                </div>
            @endif
        </div>

        <!-- REVISION NOTE IF REVISI -->
        @if($laporan->status === 'revisi' && $laporan->catatan_revisi)
            <div class="p-5 bg-amber-50 border-2 border-amber-300 rounded-xl space-y-2">
                <div class="flex items-center gap-2 text-amber-800 font-bold text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Catatan Revisi dari Pembimbing:</span>
                </div>
                <p class="text-sm text-amber-900 leading-relaxed">
                    {{ $laporan->catatan_revisi }}
                </p>
                <div class="pt-2">
                    <a href="{{ route('laporan.edit', $laporan->id) }}"
                       class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                        <i class="fa-solid fa-pen-to-square"></i> Perbaiki Laporan Sekarang
                    </a>
                </div>
            </div>
        @endif

        <!-- DETAIL GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Laporan</label>
                <p class="text-base font-bold text-gray-800 mt-1">
                    {{ $laporan->tanggal->format('d F Y') }}
                </p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu Submit</label>
                <p class="text-base font-bold text-gray-800 mt-1">
                    {{ substr($laporan->jam, 0, 5) }} WIB
                </p>
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
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Foto Dokumentasi Kegiatan (Webcam + GPS Overlay)</label>
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
                    <p class="text-gray-400 text-sm italic">Tidak ada foto dokumentasi.</p>
                @endif
            </div>
        </div>

        <!-- ACTION IF EDITABLE -->
        @if($laporan->status === 'menunggu' || $laporan->status === 'revisi')
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end">
                <a href="{{ route('laporan.edit', $laporan->id) }}"
                   class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-medium px-5 py-2.5 rounded-xl transition">
                    <i class="fa-solid fa-pen-to-square"></i> Edit / Perbaiki Laporan
                </a>
            </div>
        @endif

    </div>

</div>

@endsection
