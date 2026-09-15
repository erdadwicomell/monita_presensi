@extends('layouts.app')

@section('page-title', 'Data Teknisi')
@section('title', 'Kelola Data Teknisi Lapangan')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-amber-100">
                <i class="fa-solid fa-screwdriver-wrench"></i> Tim Lapangan
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Teknisi Lapangan
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Daftar teknisi penanggung jawab pendampingan magang di area operasional lapangan.
            </p>
        </div>

        <a href="{{ route('teknisi.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5 self-start md:self-auto cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Teknisi</span>
        </a>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Nama Teknisi</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Nomor HP / WhatsApp</th>
                        <th class="px-6 py-4">NIK</th>
                        <th class="px-6 py-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($teknisis as $item)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm block">
                                    {{ $item->nama }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ $item->email }}
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                    <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                    {{ $item->no_hp ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $item->nik ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-center">
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