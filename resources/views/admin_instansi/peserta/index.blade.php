@extends('layouts.app')

@section('page-title', 'Data Peserta')
@section('title', 'Kelola Peserta Magang')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-blue-100">
                <i class="fa-solid fa-user-graduate"></i> Anggota Magang
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Peserta Magang
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Kelola akun, nomor identitas (NIM), dan alokasi divisi/teknisi peserta aktif.
            </p>
        </div>

        <a href="{{ route('peserta.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5 self-start sm:self-auto cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Peserta</span>
        </a>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">NISN / NIM</th>
                        <th class="px-6 py-4">No HP</th>
                        <th class="px-6 py-4">Pembimbing</th>
                        <th class="px-6 py-4">Penempatan Divisi / Teknisi</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($pesertas as $peserta)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm block">
                                    {{ $peserta->name }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ $peserta->email }}
                            </td>

                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $peserta->nim ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <span class="inline-flex items-center gap-1.5 text-slate-700">
                                    <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                    {{ $peserta->no_hp ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if($peserta->pembimbing)
                                    <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 border border-purple-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                                        <span>{{ $peserta->pembimbing->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($peserta->divisi)
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-sitemap text-[10px]"></i>
                                        <span>{{ $peserta->divisi->nama_divisi }}</span>
                                    </span>
                                @elseif($peserta->teknisi)
                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-screwdriver-wrench text-[10px]"></i>
                                        <span>{{ $peserta->teknisi->nama }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum dialokasikan</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('peserta.edit', $peserta->id) }}"
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition"
                                       title="Edit Peserta">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>

                                    <form action="{{ route('peserta.destroy', $peserta->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                                title="Hapus Peserta">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada data peserta yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection