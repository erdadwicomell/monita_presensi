@extends('layouts.app')

@section('page-title', 'Data Pembimbing Instansi')
@section('title', 'Kelola Pembimbing Instansi')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-purple-100">
                <i class="fa-solid fa-chalkboard-user"></i> Tim Pembimbing
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Pembimbing Instansi
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Kelola data pembimbing lapangan dan alokasi peserta magang yang dibimbing.
            </p>
        </div>

        <a href="{{ route('admin.pembimbing.create') }}"
           class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5 self-start sm:self-auto cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Pembimbing</span>
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

    <!-- TABLE CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Nama Pembimbing</th>
                        <th class="px-6 py-4">NIP & Jabatan</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Peserta Bimbingan</th>
                        <th class="px-6 py-4 text-center">Status Akun</th>
                        <th class="px-6 py-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($pembimbings as $index => $p)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center text-slate-400 font-bold">
                                {{ $pembimbings->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm block">{{ $p->name }}</span>
                                @if(!empty($p->alamat))
                                    <span class="text-slate-400 text-[11px] font-normal mt-0.5 block truncate max-w-xs">{{ $p->alamat }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">NIP: {{ $p->nip ?? '-' }}</div>
                                <div class="text-purple-600 font-medium text-[11px] mt-0.5">{{ $p->jabatan ?? '-' }}</div>
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <div><i class="fa-solid fa-envelope text-slate-400 mr-1 text-[10px]"></i> {{ $p->email }}</div>
                                <div class="mt-0.5 text-slate-500"><i class="fa-solid fa-phone text-slate-400 mr-1 text-[10px]"></i> {{ $p->nomor_telepon ?? $p->no_hp ?? '-' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                @if($p->pesertaBimbingan->count() > 0)
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach($p->pesertaBimbingan as $pb)
                                            <span class="bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border border-blue-200/60">
                                                {{ $pb->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Belum ada peserta</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($p->is_active)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-clock text-[9px]"></i> Menunggu OTP
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.pembimbing.edit', $p->id) }}"
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition"
                                       title="Edit Pembimbing">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>

                                    <form action="{{ route('admin.pembimbing.destroy', $p->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembimbing ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                                title="Hapus Pembimbing">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-chalkboard-user text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada data Pembimbing Instansi yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembimbings->hasPages())
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                {{ $pembimbings->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
