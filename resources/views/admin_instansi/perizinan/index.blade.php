@extends('layouts.app')

@section('title', 'Data Perizinan Peserta')
@section('page-title', 'Data Perizinan')

@section('content')

<div class="space-y-6 sm:space-y-8">

    <!-- 3 STATISTIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-amber-200 transition flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Menunggu Evaluasi</p>
                <h2 class="text-2xl sm:text-3xl font-black text-amber-600 mt-1.5">{{ $totalMenunggu }}</h2>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-emerald-200 transition flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Telah Disetujui</p>
                <h2 class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1.5">{{ $totalDisetujui }}</h2>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-gray-100 hover:border-rose-200 transition flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Telah Ditolak</p>
                <h2 class="text-2xl sm:text-3xl font-black text-rose-600 mt-1.5">{{ $totalDitolak }}</h2>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH PANEL -->
    <div class="bg-white rounded-3xl shadow-sm p-6 border border-gray-100">
        <form method="GET" action="{{ route('admin.perizinan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3.5">
            <!-- Search Nama -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Cari Peserta</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Nama atau email..."
                       class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Jenis Izin -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Jenis Izin</label>
                <select name="jenis_izin" class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
                    <option value="">Semua Jenis</option>
                    <option value="terlambat" {{ request('jenis_izin') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="tidak_hadir" {{ request('jenis_izin') === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                    <option value="pulang_awal" {{ request('jenis_izin') === 'pulang_awal' ? 'selected' : '' }}>Pulang Awal</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Tanggal Mulai</label>
                <input type="date"
                       name="tanggal_mulai"
                       value="{{ request('tanggal_mulai') }}"
                       class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
            </div>

            <!-- Tanggal Selesai & Button -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase">Tanggal Selesai</label>
                    <input type="date"
                           name="tanggal_selesai"
                           value="{{ request('tanggal_selesai') }}"
                           class="w-full border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50/50">
                </div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-2xl text-xs font-bold shadow-sm transition flex items-center justify-center cursor-pointer"
                        title="Filter Data">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="{{ route('admin.perizinan.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition flex items-center justify-center"
                   title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">
                    Daftar Pengajuan Perizinan
                </h2>
                <p class="text-slate-500 text-xs mt-0.5">
                    Evaluasi dan kelola perizinan peserta magang instansi Anda.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4">Jenis Izin</th>
                        <th class="px-6 py-4">Tanggal Izin</th>
                        <th class="px-6 py-4">Jam Izin</th>
                        <th class="px-6 py-4">Status Pengajuan</th>
                        <th class="px-6 py-4 text-center w-48">Aksi / Keputusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($perizinans as $izin)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center text-slate-400 font-bold">
                                {{ $loop->iteration + ($perizinans->currentPage() - 1) * $perizinans->perPage() }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-sm">{{ $izin->user->name ?? 'User terhapus' }}</p>
                                <p class="text-slate-400 text-[11px]">{{ $izin->user->email ?? '-' }}</p>
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
                            <td class="px-6 py-4 text-slate-800 font-bold">
                                {{ $izin->tanggal->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                @if($izin->jam_mulai_izin)
                                    <span class="font-mono">{{ substr($izin->jam_mulai_izin, 0, 5) }} - {{ substr($izin->jam_selesai_izin, 0, 5) }} WIB</span>
                                @else
                                    <span class="text-slate-400">Seharian</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($izin->status === 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> Menunggu
                                    </span>
                                @elseif($izin->status === 'disetujui')
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                        </span>
                                        @if($izin->disetujui_pada)
                                            <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $izin->disetujui_pada->format('d/m H:i') }}</p>
                                        @endif
                                    </div>
                                @else
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak
                                        </span>
                                        @if($izin->disetujui_pada)
                                            <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $izin->disetujui_pada->format('d/m H:i') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- DETAIL -->
                                    <a href="{{ route('admin.perizinan.show', $izin->id_perizinan) }}"
                                       class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl text-xs font-bold transition inline-flex items-center gap-1"
                                       title="Lihat Detail & Bukti">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>Detail</span>
                                    </a>

                                    @if($izin->status === 'menunggu')
                                        <!-- SETUJUI -->
                                        <form action="{{ route('admin.perizinan.setujui', $izin->id_perizinan) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Setujui perizinan untuk {{ $izin->user->name }}?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold shadow-xs transition inline-flex items-center gap-1 cursor-pointer"
                                                    title="Setujui Izin">
                                                <i class="fa-solid fa-check text-xs"></i>
                                                <span>Setujui</span>
                                            </button>
                                        </form>

                                        <!-- TOLAK -->
                                        <form action="{{ route('admin.perizinan.tolak', $izin->id_perizinan) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Tolak perizinan untuk {{ $izin->user->name }}?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold shadow-xs transition inline-flex items-center gap-1 cursor-pointer"
                                                    title="Tolak Izin">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                                <span>Tolak</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                                <span class="font-medium text-slate-500">Tidak ditemukan data perizinan sesuai filter yang dipilih.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($perizinans->hasPages())
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                {{ $perizinans->links() }}
            </div>
        @endif
    </div>

</div>

@endsection