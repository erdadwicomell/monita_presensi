@extends('layouts.app')

@section('page-title', 'Validasi Laporan Peserta Bimbingan')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs bg-purple-50 text-purple-700 font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-book-bookmark mr-1"></i> Bimbingan Kegiatan
            </span>
            <h1 class="text-2xl font-black text-gray-800 mt-2">
                Validasi Laporan Kegiatan Peserta Magang
            </h1>
            <p class="text-gray-500 text-xs mt-1">
                Tinjau bukti foto, koordinat GPS, uraian kegiatan, dan berikan persetujuan atau catatan saran perbaikan.
            </p>
        </div>
    </div>

    <!-- STATISTIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menunggu Validasi</p>
                <h3 class="text-3xl font-black text-amber-600 mt-1">{{ $totalMenunggu }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Laporan Disetujui</p>
                <h3 class="text-3xl font-black text-emerald-600 mt-1">{{ $totalDisetujui }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Perlu Perbaikan</p>
                <h3 class="text-3xl font-black text-rose-600 mt-1">{{ $totalRevisi }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('pembimbing.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end text-xs">
            <div>
                <label class="block font-semibold text-gray-700 mb-1.5">Peserta Bimbingan</label>
                <select name="user_id" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Semua Peserta --</option>
                    @foreach($pesertas as $p)
                        <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1.5">Status Laporan</label>
                <select name="status" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Validasi</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="revisi" {{ request('status') === 'revisi' ? 'selected' : '' }}>Revisi</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1.5">Tanggal Kegiatan</label>
                <input type="date"
                       name="tanggal"
                       value="{{ request('tanggal') }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-xl transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('pembimbing.laporan.index') }}" class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- TABEL LAPORAN CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Peserta Bimbingan</th>
                        <th class="px-6 py-4">Tanggal Kegiatan</th>
                        <th class="px-6 py-4">Uraian & Aktivitas Harian</th>
                        <th class="px-6 py-4 text-center">Status Verifikasi</th>
                        <th class="px-6 py-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporans as $laporan)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm block">
                                    {{ $laporan->user->name ?? 'Peserta' }}
                                </span>
                                <span class="text-[11px] font-normal text-slate-400 mt-0.5 block">
                                    {{ $laporan->user->divisi->nama_divisi ?? $laporan->user->teknisi->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-bold">
                                {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 max-w-md">
                                <p class="line-clamp-2 leading-relaxed text-xs">{{ $laporan->kegiatan }}</p>
                                @if($laporan->catatan_revisi)
                                    <div class="mt-1.5 text-[11px] text-amber-800 bg-amber-50 p-2 rounded-xl border border-amber-200/60">
                                        <strong>Catatan Bimbingan:</strong> {{ $laporan->catatan_revisi }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($laporan->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-semibold px-3 py-1 rounded-full text-xs">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                    </span>
                                @elseif($laporan->status === 'revisi')
                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200/60 font-semibold px-3 py-1 rounded-full text-xs">
                                        <i class="fa-solid fa-pen-ruler text-[10px]"></i> Perlu Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/60 font-semibold px-3 py-1 rounded-full text-xs">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('pembimbing.laporan.show', $laporan->id) }}"
                                   class="bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-eye text-xs"></i> Periksa & Nilai
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-book-open text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Tidak ada laporan kegiatan dari peserta bimbingan yang sesuai dengan filter.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporans->hasPages())
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
