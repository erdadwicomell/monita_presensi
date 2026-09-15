@extends('layouts.app')

@section('page-title', 'Riwayat Absensi')
@section('title', 'Riwayat Absensi Saya')

@section('content')

<div class="space-y-6 sm:space-y-8">

    <!-- HEADER -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-clock-rotate-left"></i> Log Kehadiran
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-800 tracking-tight">
                Riwayat Presensi Saya
            </h1>
            <p class="text-gray-400 text-xs sm:text-sm mt-1">
                Catatan seluruh presensi masuk, pulang, status ketepatan waktu, dan validasi perizinan.
            </p>
        </div>

        <a href="{{ route('absensi.index') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-5 py-3 rounded-2xl shadow-md hover:shadow-lg transition-all duration-150 text-xs self-start md:self-auto transform hover:-translate-y-0.5">
            <i class="fa-solid fa-location-dot"></i>
            <span>Halaman Presensi GPS</span>
        </a>
    </div>

    <!-- TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Tanggal & Jam</th>
                        <th class="px-6 py-4">Sesi Presensi</th>
                        <th class="px-6 py-4">Status Kehadiran</th>
                        <th class="px-6 py-4">Keterangan Izin</th>
                        <th class="px-6 py-4">Jarak Radius</th>
                        <th class="px-6 py-4">Keamanan HMAC</th>
                        <th class="px-6 py-4 text-center">Foto Selfie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $item)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center text-slate-400 font-bold">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-sm">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</p>
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
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block text-slate-300"></i>
                                <span class="font-medium text-slate-500">Belum ada riwayat absensi yang tercatat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection