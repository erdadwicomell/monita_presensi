@extends('layouts.app')

@section('page-title', 'Data Instansi')
@section('title', 'Kelola Data Instansi')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-blue-100">
                <i class="fa-solid fa-building"></i> Master Data
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Instansi Magang
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Kelola daftar mitra instansi, aturan klasifikasi penempatan, dan radius geofencing presensi.
            </p>
        </div>

        <a href="{{ route('instansi.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5 self-start md:self-auto cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Instansi</span>
        </a>
    </div>

    <!-- FLASH ALERT SUCCESS -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 shadow-xs">
            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="font-medium leading-relaxed">{{ session('success') }}</div>
        </div>
    @endif

    <!-- DATA TABLE CONTAINER (MODERN LUXURY CARD) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <!-- THEAD (HEADER TABEL RESMI) -->
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Nama Instansi</th>
                        <th class="px-6 py-4">Jenis Penempatan</th>
                        <th class="px-6 py-4">Radius Geofence</th>
                    </tr>
                </thead>

                <!-- TBODY & TR (ZEBRA STRIPING + HOVER LEMBUT) -->
                <tbody class="divide-y divide-slate-100">
                    @forelse($instansi as $item)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <!-- NO -->
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <!-- NAMA INSTANSI -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm block">
                                    {{ $item->nama_instansi }}
                                </span>
                                @if(!empty($item->alamat))
                                    <span class="text-[11px] text-slate-400 font-normal mt-0.5 block truncate max-w-md">
                                        <i class="fa-solid fa-location-dot text-[10px] mr-1 text-slate-400"></i>{{ $item->alamat }}
                                    </span>
                                @endif
                            </td>

                            <!-- BADGE STATUS / KLASIFIKASI -->
                            <td class="px-6 py-4">
                                @if($item->jenis_instansi == 'kantor')
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-building text-[10px]"></i>
                                        <span>Kantor Swasta</span>
                                    </span>
                                @elseif($item->jenis_instansi == 'pemerintahan')
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-landmark text-[10px]"></i>
                                        <span>Pemerintahan / BUMN</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-screwdriver-wrench text-[10px]"></i>
                                        <span>Lapangan / Teknisi</span>
                                    </span>
                                @endif
                            </td>

                            <!-- RADIUS GEOFENCE -->
                            <td class="px-6 py-4 font-mono text-slate-600 font-semibold">
                                <span class="inline-flex items-center gap-1.5 bg-slate-100 border border-slate-200/80 px-2.5 py-1 rounded-xl text-xs text-slate-700">
                                    <i class="fa-solid fa-circle-notch text-[10px] text-slate-400"></i>
                                    {{ $item->radius }} Meter
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-building-circle-xmark text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada data instansi yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection