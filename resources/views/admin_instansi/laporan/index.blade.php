@extends('layouts.app')

@section('title', 'Verifikasi Laporan Kegiatan')
@section('page-title', 'Laporan Kegiatan Peserta')

@section('content')

<div class="space-y-6">

    <!-- STATISTIC CARDS (MINIMALIS & RESPONSIF) -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">

        <!-- 1. MENUNGGU EVALUASI -->
        <div class="bg-white p-4 sm:p-5 h-auto rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-yellow-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Evaluasi</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-0.5">{{ $totalMenunggu }}</h2>
            </div>
            <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-base flex-shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <!-- 2. TELAH DISETUJUI -->
        <div class="bg-white p-4 sm:p-5 h-auto rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">Telah Disetujui</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-0.5">{{ $totalDisetujui }}</h2>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base flex-shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- 3. PERLU REVISI -->
        <div class="col-span-2 lg:col-span-1 bg-white p-4 sm:p-5 h-auto rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-amber-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">Perlu Revisi</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-0.5">{{ $totalRevisi }}</h2>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base flex-shrink-0">
                <i class="fa-solid fa-pen-ruler"></i>
            </div>
        </div>

    </div>

    <!-- FILTER & SEARCH PANEL -->
    <div class="bg-white rounded-2xl shadow p-6">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Nama -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Cari Peserta</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Nama atau email..."
                       class="w-full border border-gray-300 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="revisi" {{ request('status') === 'revisi' ? 'selected' : '' }}>Revisi</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Tanggal Mulai</label>
                <input type="date"
                       name="tanggal_mulai"
                       value="{{ request('tanggal_mulai') }}"
                       class="w-full border border-gray-300 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Tanggal Selesai & Button -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase">Tanggal Selesai</label>
                    <input type="date"
                           name="tanggal_selesai"
                           value="{{ request('tanggal_selesai') }}"
                           class="w-full border border-gray-300 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow transition"
                        title="Filter Data">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="{{ route('admin.laporan.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3.5 py-2 rounded-xl text-sm transition"
                   title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">
                    Daftar Laporan Kegiatan Peserta
                </h2>
                <p class="text-slate-500 text-xs mt-0.5">
                    Evaluasi, verifikasi, dan berikan catatan bimbingan terhadap kegiatan harian peserta.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4">Tanggal & Jam</th>
                        <th class="px-6 py-4">Uraian Kegiatan</th>
                        <th class="px-6 py-4">Dokumentasi</th>
                        <th class="px-6 py-4">Status Verifikasi</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporans as $lap)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center text-slate-400 font-bold">
                                {{ $loop->iteration + ($laporans->currentPage() - 1) * $laporans->perPage() }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-sm">{{ $lap->user->name ?? 'User terhapus' }}</p>
                                <p class="text-slate-400 text-[11px]">{{ $lap->user->email ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-700">{{ $lap->tanggal->format('d M Y') }}</p>
                                <p class="text-slate-400 font-mono text-[11px] mt-0.5">{{ substr($lap->jam, 0, 5) }} WIB</p>
                            </td>
                            <td class="px-6 py-4 max-w-sm">
                                <p class="text-slate-700 line-clamp-2 leading-relaxed text-xs">
                                    {{ $lap->kegiatan }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if($lap->foto)
                                    <a href="{{ asset($lap->foto) }}" target="_blank" class="inline-flex items-center gap-2 text-xs text-blue-600 font-semibold hover:underline">
                                        <img src="{{ asset($lap->foto) }}" class="w-10 h-10 object-cover rounded-xl border border-slate-200 shadow-xs">
                                        <span>Lihat Foto</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($lap->status === 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> Menunggu
                                    </span>
                                @elseif($lap->status === 'disetujui')
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                        </span>
                                        @if($lap->diverifikasi_pada)
                                            <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $lap->diverifikasi_pada->format('d/m H:i') }}</p>
                                        @endif
                                    </div>
                                @else
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                            <i class="fa-solid fa-pen-ruler text-[10px]"></i> Revisi
                                        </span>
                                        @if($lap->diverifikasi_pada)
                                            <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $lap->diverifikasi_pada->format('d/m H:i') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.laporan.show', $lap->id) }}"
                                   class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/60 px-3.5 py-1.5 rounded-xl text-xs font-bold transition"
                                   title="Evaluasi Laporan">
                                    <i class="fa-solid fa-eye text-xs"></i> Evaluasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-book-open text-4xl mb-3 block text-slate-300"></i>
                                <span class="font-medium text-slate-500">Tidak ditemukan laporan kegiatan sesuai filter yang dipilih.</span>
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
