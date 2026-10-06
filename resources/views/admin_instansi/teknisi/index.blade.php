@extends('layouts.app')

@section('page-title', 'Data Teknisi')
@section('title', 'Kelola Data Teknisi Lapangan')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Teknisi Lapangan
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Daftar teknisi penanggung jawab pendampingan magang di area operasional lapangan.
            </p>
        </div>

        <a href="{{ route('teknisi.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-sm shadow-blue-200 rounded-xl px-5 py-2.5 font-semibold text-sm transition-all transform hover:-translate-y-0.5 self-start sm:self-auto cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Teknisi</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 shadow-xs">
            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="font-medium leading-relaxed">{{ session('success') }}</div>
        </div>
    @endif

    <!-- DATA TABLE CONTAINER (PREMIUM ENTERPRISE UI) -->
    <div class="bg-white rounded-2xl shadow-md shadow-slate-100/80 border border-slate-100/50 p-5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <!-- THEAD (HEADER TABEL RESMI) -->
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-semibold text-xs tracking-wider uppercase border-b border-slate-100">
                        <th class="py-4 px-6 w-16 text-center">No</th>
                        <th class="py-4 px-6">Nama Teknisi</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6">Nomor HP / WhatsApp</th>
                        <th class="py-4 px-6">NIK</th>
                        <th class="py-4 px-6 text-center w-36">Aksi</th>
                    </tr>
                </thead>

                <!-- TBODY & TR (ZEBRA STRIPING + HOVER LEMBUT) -->
                <tbody class="divide-y divide-slate-100">
                    @forelse($teknisis as $item)
                        <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-blue-50/40 transition-colors duration-150">
                            <!-- NO -->
                            <td class="py-4 px-6 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <!-- NAMA TEKNISI -->
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 text-sm block">
                                    {{ $item->nama }}
                                </span>
                            </td>

                            <!-- EMAIL -->
                            <td class="py-4 px-6 text-slate-600 font-medium">
                                {{ $item->email }}
                            </td>

                            <!-- NOMOR HP -->
                            <td class="py-4 px-6 text-slate-600">
                                <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                    <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                    {{ $item->no_hp ?? '-' }}
                                </span>
                            </td>

                            <!-- NIK -->
                            <td class="py-4 px-6 font-mono text-slate-600">
                                {{ $item->nik ?? '-' }}
                            </td>

                            <!-- AKSI -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('teknisi.edit', $item->id) }}"
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition"
                                       title="Edit Teknisi">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>

                                    <form action="{{ route('teknisi.destroy', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus teknisi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                                title="Hapus Teknisi">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-screwdriver-wrench text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada data teknisi yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection