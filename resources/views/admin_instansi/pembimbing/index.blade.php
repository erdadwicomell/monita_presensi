@extends('layouts.app')

@section('page-title', 'Data Pembimbing Instansi')
@section('title', 'Kelola Pembimbing Instansi')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Pembimbing Instansi
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Kelola data pembimbing lapangan dan alokasi peserta magang yang dibimbing.
            </p>
        </div>

        <a href="{{ route('admin.pembimbing.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white shadow-sm shadow-purple-200 rounded-xl px-5 py-2.5 font-semibold text-sm transition-all transform hover:-translate-y-0.5 self-start sm:self-auto cursor-pointer">
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

    <!-- DATA TABLE CONTAINER (PREMIUM ENTERPRISE UI) -->
    <div class="bg-white rounded-2xl shadow-md shadow-slate-100/80 border border-slate-100/50 p-5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <!-- THEAD (HEADER TABEL RESMI) -->
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-semibold text-xs tracking-wider uppercase border-b border-slate-100">
                        <th class="py-4 px-6 w-16 text-center">No</th>
                        <th class="py-4 px-6">Nama Pembimbing</th>
                        <th class="py-4 px-6">NIP & Jabatan</th>
                        <th class="py-4 px-6">Kontak</th>
                        <th class="py-4 px-6">Peserta Bimbingan</th>
                        <th class="py-4 px-6 text-center">Status Akun</th>
                        <th class="py-4 px-6 text-center w-28">Aksi</th>
                    </tr>
                </thead>

                <!-- TBODY & TR (ZEBRA STRIPING + HOVER LEMBUT) -->
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembimbings as $index => $p)
                        <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-blue-50/40 transition-colors duration-150">
                            <!-- NO -->
                            <td class="py-4 px-6 text-center text-slate-400 font-bold">
                                {{ $pembimbings->firstItem() + $index }}
                            </td>

                            <!-- NAMA PEMBIMBING -->
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 text-sm block">{{ $p->name }}</span>
                                @if(!empty($p->alamat))
                                    <span class="text-slate-400 text-xs font-normal mt-0.5 block max-w-[200px] truncate" title="{{ $p->alamat }}">
                                        {{ $p->alamat }}
                                    </span>
                                @endif
                            </td>

                            <!-- NIP & JABATAN -->
                            <td class="py-4 px-6">
                                <div class="text-sm font-medium text-slate-800 font-mono">
                                    {{ trim(preg_replace('/^nip[\.:\s]*/i', '', $p->nip ?? '')) ?: ($p->nip ?? '-') }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $p->jabatan ?? '-' }}
                                </div>
                            </td>

                            <!-- KONTAK -->
                            <td class="py-4 px-6 text-slate-600">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                        <i class="fa-solid fa-envelope text-slate-400 text-[11px] w-3.5"></i>
                                        <span>{{ $p->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                        <i class="fa-solid fa-phone text-slate-400 text-[11px] w-3.5"></i>
                                        <span>{{ $p->nomor_telepon ?? $p->no_hp ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- PESERTA BIMBINGAN -->
                            <td class="py-4 px-6">
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

                            <!-- STATUS AKUN -->
                            <td class="py-4 px-6 text-center">
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

                            <!-- AKSI -->
                            <td class="py-4 px-6 text-center">
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
            <div class="p-6 border-t border-slate-100 bg-slate-50/50 mt-4 rounded-b-xl">
                {{ $pembimbings->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
