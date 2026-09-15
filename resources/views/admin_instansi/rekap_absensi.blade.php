@extends('layouts.app')

@section('page-title', 'Rekap Presensi')
@section('title', 'Rekap Presensi Peserta')

@section('content')

<div class="space-y-6 sm:space-y-8">

    <!-- HEADER & EXPORT ACTIONS -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-chart-column"></i> Data Rekapitulasi
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-800 tracking-tight">
                Rekapitulasi Presensi Peserta
            </h1>
            <p class="text-gray-400 text-xs sm:text-sm mt-1">
                Laporan komprehensif kehadiran, keterlambatan, dan validasi perizinan seluruh peserta magang.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start md:self-auto">
            <!-- EXPORT CSV / EXCEL -->
            <a href="{{ route('rekap.absensi.csv', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-2xl font-bold text-xs shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>

            <!-- EXPORT PDF -->
            <a href="{{ route('rekap.absensi.pdf', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-2xl font-bold text-xs shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Export PDF</span>
            </a>
        </div>
    </div>

    <!-- 4 STATISTIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-blue-200 transition flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Log Presensi</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-800 mt-1.5">{{ $totalPresensi }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-emerald-200 transition flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Hadir Tepat Waktu</p>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1.5">{{ $totalHadir }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-amber-200 transition flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Terlambat</p>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-600 mt-1.5">{{ $totalTelat }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-purple-200 transition flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sesi Masuk / Pulang</p>
                <h3 class="text-2xl sm:text-3xl font-black text-purple-600 mt-1.5">{{ $totalMasuk }} / {{ $totalPulang }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-arrows-left-right"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH PANEL -->
    <div class="bg-white rounded-3xl shadow-sm p-6 border border-gray-100">
        <form method="GET" action="{{ route('rekap.absensi') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- Filter Peserta -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Peserta</label>
                <select name="user_id" class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
                    <option value="">Semua Peserta</option>
                    @foreach($pesertas as $p)
                        <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Status Kehadiran</label>
                <select name="status" class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                    <option value="telat" {{ request('status') === 'telat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>

            <!-- Filter Tipe -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Tipe Presensi</label>
                <select name="tipe_absensi" class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
                    <option value="">Semua Sesi</option>
                    <option value="masuk" {{ request('tipe_absensi') === 'masuk' ? 'selected' : '' }}>Masuk</option>
                    <option value="pulang" {{ request('tipe_absensi') === 'pulang' ? 'selected' : '' }}>Pulang</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Tanggal Mulai</label>
                <input type="date"
                       name="tanggal_mulai"
                       value="{{ request('tanggal_mulai') }}"
                       class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
            </div>

            <!-- Tanggal Selesai & Button -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Tanggal Selesai</label>
                    <input type="date"
                           name="tanggal_selesai"
                           value="{{ request('tanggal_selesai') }}"
                           class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
                </div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-2xl text-xs font-bold shadow-sm transition flex items-center justify-center cursor-pointer"
                        title="Terapkan Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="{{ route('rekap.absensi') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition flex items-center justify-center"
                   title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Peserta</th>
                        <th class="px-6 py-4">Tanggal & Jam</th>
                        <th class="px-6 py-4">Sesi Presensi</th>
                        <th class="px-6 py-4">Status Kehadiran</th>
                        <th class="px-6 py-4">Perizinan Terkait</th>
                        <th class="px-6 py-4">Jarak Radius</th>
                        <th class="px-6 py-4">HMAC Status</th>
                        <th class="px-6 py-4 text-center">Foto Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rekap as $item)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center text-slate-400 font-bold">
                                {{ $loop->iteration + ($rekap->currentPage() - 1) * $rekap->perPage() }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-sm">{{ $item->user->name ?? 'User Dihapus' }}</p>
                                <p class="text-slate-400 text-[11px]">{{ $item->user->email ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</p>
                                <p class="text-slate-400 text-[11px] mt-0.5 font-mono"><i class="fa-regular fa-clock mr-1"></i>{{ substr($item->jam, 0, 5) }} WIB</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->tipe_absensi === 'masuk')
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold border border-blue-200/60">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-[10px]"></i> Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold border border-purple-200/60">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i> Pulang
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status === 'hadir')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold border border-emerald-200/60">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Hadir Tepat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-semibold border border-amber-200/60">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Telat
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item->perizinan)
                                    <div>
                                        <span class="font-bold text-emerald-700 capitalize text-xs">
                                            <i class="fa-solid fa-certificate text-emerald-500 mr-1"></i>Izin {{ str_replace('_', ' ', $item->perizinan->jenis_izin) }}
                                        </span>
                                        <p class="text-slate-400 text-[11px] truncate max-w-xs mt-0.5">{{ $item->perizinan->alasan }}</p>
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-mono text-xs">
                                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200/80 font-semibold">
                                    {{ round($item->jarak, 1) }} m
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->hmac_signature)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-3 py-0.5 rounded-full" title="Signature: {{ $item->hmac_signature }}">
                                        <i class="fa-solid fa-shield-check text-[10px]"></i> Valid
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Standar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->foto)
                                    <a href="{{ asset($item->foto) }}" target="_blank" class="inline-block group">
                                        <img src="{{ asset($item->foto) }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-xs group-hover:scale-110 transition duration-150">
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block text-slate-300"></i>
                                <span class="font-medium text-slate-500">Tidak ditemukan data presensi sesuai kriteria filter.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rekap->hasPages())
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                {{ $rekap->links() }}
            </div>
        @endif
    </div>

</div>

@endsection