@extends('layouts.app')

@section('page-title', 'Rekap Absensi')
@section('title', 'Rekap Absensi Peserta')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 flex items-center justify-between">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-blue-100">
                <i class="fa-solid fa-clipboard-user"></i> Log Kehadiran
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Rekap Log Absensi Peserta
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Monitoring seluruh catatan absensi peserta magang dan validasi radius.
            </p>
        </div>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Peserta</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Jam</th>
                        <th class="px-6 py-4">Sesi Presensi</th>
                        <th class="px-6 py-4">Status Radius</th>
                        <th class="px-6 py-4">Jarak</th>
                        <th class="px-6 py-4">Lokasi GPS</th>
                        <th class="px-6 py-4 text-center">Foto Bukti</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($rekap as $item)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm block">
                                    {{ $item->user->name }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    {{ $item->user->email }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ $item->tanggal }}
                            </td>

                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $item->jam }}
                            </td>

                            <td class="px-6 py-4">
                                @if($item->tipe_absensi == 'masuk')
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-[10px]"></i> Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 border border-purple-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i> Pulang
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($item->status == 'di_dalam_radius' || $item->status == 'hadir')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Valid Radius
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Diluar Radius
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 font-mono text-slate-600">
                                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200/80 font-semibold">
                                    {{ $item->jarak }} m
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-semibold text-xs">
                                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                                    <span>Peta Google</span>
                                </a>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($item->foto)
                                    <a href="{{ asset($item->foto) }}" target="_blank" class="inline-block group">
                                        <img src="{{ asset($item->foto) }}"
                                             class="w-10 h-10 object-cover rounded-xl border border-slate-200 shadow-xs group-hover:scale-110 transition duration-150">
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-clipboard-question text-4xl mb-3 block text-slate-300"></i>
                                <span class="font-medium text-slate-500">Belum ada data absensi yang tercatat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection