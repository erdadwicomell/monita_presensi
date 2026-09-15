@extends('layouts.app')

@section('page-title', 'Data Perizinan')
@section('title', 'Data Perizinan')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-amber-100">
                <i class="fa-solid fa-file-signature"></i> Layanan Izin Magang
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Perizinan Saya
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Ajukan izin apabila tidak dapat melakukan presensi normal sesuai ketentuan.
            </p>
        </div>

        <a href="{{ route('perizinan.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5 self-start sm:self-auto cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Ajukan Perizinan</span>
        </a>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Tanggal Izin</th>
                        <th class="px-6 py-4">Jenis Izin</th>
                        <th class="px-6 py-4">Jam Izin</th>
                        <th class="px-6 py-4">Status Pengajuan</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($perizinans as $izin)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center text-slate-400 font-bold">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                {{ $izin->tanggal->format('d-m-Y') }}
                            </td>

                            <td class="px-6 py-4">
                                @if($izin->jenis_izin === 'terlambat')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Terlambat
                                    </span>
                                @elseif($izin->jenis_izin === 'tidak_hadir')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                        <i class="fa-solid fa-user-xmark text-[10px]"></i> Tidak Hadir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200/60">
                                        <i class="fa-solid fa-person-walking-arrow-right text-[10px]"></i> Pulang Awal
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-slate-600 font-medium">
                                @if($izin->jam_mulai_izin)
                                    <span class="font-mono">{{ $izin->jam_mulai_izin }} - {{ $izin->jam_selesai_izin }}</span>
                                @else
                                    <span class="text-slate-400">Seharian</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($izin->status == 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> Menunggu
                                    </span>
                                @elseif($izin->status == 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('perizinan.show', $izin->id_perizinan ?? $izin->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-xl transition">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                                <span class="font-medium text-slate-500">Belum ada data perizinan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection