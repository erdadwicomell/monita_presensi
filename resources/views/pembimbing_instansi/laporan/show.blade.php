@extends('layouts.app')

@section('page-title', 'Detail Laporan Kegiatan Peserta')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('pembimbing.laporan.index') }}" class="text-xs font-semibold text-purple-600 hover:underline flex items-center gap-1.5 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Laporan
            </a>
            <h1 class="text-2xl font-black text-gray-800">
                Laporan Kegiatan Peserta Magang
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Pengirim: <strong>{{ $laporan->user->name ?? 'Peserta' }}</strong> ({{ $laporan->user->asal_sekolah_pt ?? '-' }})
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($laporan->status === 'disetujui')
                <span class="bg-emerald-100 text-emerald-800 font-bold px-4 py-2 rounded-2xl text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i> Disetujui Pembimbing
                </span>
            @elseif($laporan->status === 'revisi')
                <span class="bg-amber-100 text-amber-800 font-bold px-4 py-2 rounded-2xl text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i> Menunggu Perbaikan Peserta
                </span>
            @else
                <span class="bg-blue-100 text-blue-800 font-bold px-4 py-2 rounded-2xl text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> Menunggu Validasi Anda
                </span>
            @endif
        </div>
    </div>

    <!-- MAIN CONTENT CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">

        <!-- META INFO -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-2xl text-xs">
            <div>
                <span class="text-gray-400 block mb-0.5">Tanggal Kegiatan:</span>
                <strong class="text-gray-800 text-sm">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d F Y') }}</strong>
            </div>
            <div>
                <span class="text-gray-400 block mb-0.5">Waktu Dikirim:</span>
                <strong class="text-gray-800 text-sm">{{ $laporan->created_at ? $laporan->created_at->format('H:i') . ' WIB' : '-' }}</strong>
            </div>
            <div>
                <span class="text-gray-400 block mb-0.5">Status Validasi:</span>
                <strong class="capitalize {{ $laporan->status === 'disetujui' ? 'text-emerald-600' : ($laporan->status === 'revisi' ? 'text-amber-600' : 'text-blue-600') }}">
                    {{ $laporan->status }}
                </strong>
            </div>
        </div>

        <!-- DESKRIPSI KEGIATAN -->
        <div>
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                Uraian Aktivitas & Hasil Pekerjaan
            </h3>
            <div class="p-5 bg-gray-50/70 rounded-2xl border border-gray-100 text-gray-800 text-sm leading-relaxed whitespace-pre-line">
                {{ $laporan->kegiatan }}
            </div>
        </div>

        <!-- CATATAN REVISI JIKA ADA -->
        @if($laporan->catatan_revisi)
            <div class="p-5 bg-amber-50 rounded-2xl border border-amber-200 text-xs">
                <h4 class="font-bold text-amber-900 flex items-center gap-1.5 mb-1">
                    <i class="fa-solid fa-circle-exclamation text-amber-600"></i>
                    Catatan Saran Perbaikan Sebelumnya:
                </h4>
                <p class="text-amber-800 leading-relaxed">{{ $laporan->catatan_revisi }}</p>
                @if($laporan->verifikator)
                    <span class="text-[10px] text-amber-600 mt-2 block">
                        Oleh: {{ $laporan->verifikator->name }} ({{ $laporan->diverifikasi_pada ? \Carbon\Carbon::parse($laporan->diverifikasi_pada)->format('d/m/Y H:i') : '' }})
                    </span>
                @endif
            </div>
        @endif

        <!-- FOTO DOKUMENTASI & MINI-MAP -->
        @if($laporan->foto)
            <div>
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Bukti Dokumentasi Foto (Snapshot Mini-Map & GPS)
                </h3>
                <div class="rounded-2xl overflow-hidden border border-gray-200 bg-black/5 flex items-center justify-center p-2 max-w-xl mx-auto">
                    <img src="{{ asset('storage/' . $laporan->foto) }}"
                         alt="Foto Kegiatan"
                         class="w-full h-auto object-contain rounded-xl shadow-sm">
                </div>
            </div>
        @endif

        <!-- ACTION BUTTONS UNTUK PEMBIMBING -->
        <div class="pt-6 border-t border-gray-100 flex flex-wrap items-center justify-end gap-3">
            <!-- Form Minta Revisi -->
            <button type="button"
                    onclick="document.getElementById('modalRevisi').classList.remove('hidden')"
                    class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-5 py-3 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Minta Revisi / Berikan Catatan</span>
            </button>

            <!-- Form Setujui -->
            <form action="{{ route('pembimbing.laporan.setujui', $laporan->id) }}"
                  method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menyetujui laporan kegiatan ini?')">
                @csrf
                @method('PUT')
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Setujui Laporan</span>
                </button>
            </form>
        </div>

    </div>

</div>

<!-- MODAL REVISI -->
<div id="modalRevisi" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-500"></i> Catatan Perbaikan Laporan
            </h3>
            <button type="button"
                    onclick="document.getElementById('modalRevisi').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-lg">
                &times;
            </button>
        </div>

        <p class="text-xs text-gray-500 leading-relaxed">
            Berikan masukan/arahan yang jelas mengenai bagian yang perlu diperbaiki oleh peserta magang.
        </p>

        <form action="{{ route('pembimbing.laporan.revisi', $laporan->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Saran & Catatan Revisi <span class="text-rose-500">*</span></label>
                <textarea name="catatan_revisi"
                          rows="4"
                          required
                          placeholder="Contoh: Tolong jelaskan lebih rinci modul apa yang dikonfigurasi hari ini dan sertakan screenshot pendukung."
                          class="w-full border border-gray-300 rounded-2xl p-3 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('catatan_revisi', $laporan->catatan_revisi) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button"
                        onclick="document.getElementById('modalRevisi').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Catatan Revisi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
