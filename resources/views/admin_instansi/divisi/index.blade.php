@extends('layouts.app')

@section('page-title', 'Data Divisi')
@section('page-icon')
    <i class="fa-solid fa-sitemap"></i>
@endsection
@section('title', 'Kelola Data Divisi')

@section('content')

<div class="space-y-6">

    <!-- ACTION & DESCRIPTION CARD -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Kolom Kiri: Judul & Deskripsi Tugas -->
        <div class="max-w-2xl">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">
                Kelola Unit & Departemen
            </h2>
            <p class="text-slate-500 text-sm mt-1.5 leading-relaxed">
                Kelola divisi dan penempatan departemen bagi peserta magang perkantoran/pemerintahan.
            </p>
        </div>

        <!-- Kolom Kanan: Tombol Tambah Divisi -->
        <div class="flex-shrink-0">
            <a href="{{ route('divisi.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Divisi</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 shadow-xs">
            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="font-medium leading-relaxed">{{ session('success') }}</div>
        </div>
    @endif

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 w-12 text-center">No</th>
                        <th class="px-5 py-4">Divisi & Kode</th>
                        <th class="px-5 py-4">Kepala Divisi / Kasubag</th>
                        <th class="px-5 py-4">Kuota Magang</th>
                        <th class="px-5 py-4">Lokasi & Deskripsi</th>
                        <th class="px-5 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($divisis as $divisi)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-5 py-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 text-sm">
                                        {{ $divisi->nama_divisi }}
                                    </span>
                                    @if($divisi->kode_divisi)
                                        <span class="bg-blue-50 text-blue-700 text-[10px] font-mono font-bold px-2 py-0.5 rounded-md border border-blue-200/60 uppercase">
                                            {{ $divisi->kode_divisi }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                @if($divisi->kepala_divisi)
                                    <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-tie text-blue-500 text-xs"></i>
                                        <span>{{ $divisi->kepala_divisi }}</span>
                                    </div>
                                    @if($divisi->kepalaDivisi)
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $divisi->kepalaDivisi->jabatan ?? ($divisi->kepalaDivisi->nip ? 'NIP. ' . $divisi->kepalaDivisi->nip : 'Pembimbing Instansi') }}
                                        </div>
                                    @endif
                                @elseif($divisi->kepalaDivisi)
                                    <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-tie text-blue-500 text-xs"></i>
                                        <span>{{ $divisi->kepalaDivisi->name }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $divisi->kepalaDivisi->jabatan ?? ($divisi->kepalaDivisi->nip ? 'NIP. ' . $divisi->kepalaDivisi->nip : 'Pembimbing Instansi') }}
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum Ditentukan</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                @php
                                    $terisi = $divisi->pesertas->count();
                                    $maks = $divisi->kuota_maksimal ?? 5;
                                    $isPenuh = $terisi >= $maks;
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $isPenuh ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' }}">
                                        <i class="fa-solid fa-users text-[10px]"></i>
                                        <span>{{ $terisi }} / {{ $maks }} Peserta</span>
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                @if($divisi->lokasi_ruangan)
                                    <div class="font-medium text-slate-700 flex items-center gap-1 text-[11px]">
                                        <i class="fa-solid fa-location-dot text-slate-400"></i>
                                        <span>{{ $divisi->lokasi_ruangan }}</span>
                                    </div>
                                @endif
                                <div class="text-slate-500 text-[11px] {{ $divisi->lokasi_ruangan ? 'mt-0.5' : '' }} max-w-xs truncate" title="{{ $divisi->deskripsi }}">
                                    {{ $divisi->deskripsi ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('divisi.edit', $divisi->id) }}"
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition"
                                       title="Edit Divisi">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>

                                    <form action="{{ route('divisi.destroy', $divisi->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                                title="Hapus Divisi">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-sitemap text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada data divisi yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection