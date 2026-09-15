@extends('layouts.app')

@section('page-title', 'Dashboard Pembimbing Instansi')
@section('title', 'Dashboard Bimbingan Magang')

@section('content')

<div class="space-y-6">

    <!-- WELCOME BANNER -->
    <div class="bg-gradient-to-r from-purple-700 via-indigo-700 to-slate-900 rounded-3xl shadow-lg p-8 text-white relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="fa-solid fa-user-tie text-purple-200"></i> Pembimbing Lapangan
            </span>
            <h1 class="text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="mt-2 text-purple-100 text-sm leading-relaxed">
                Pantau perkembangan presensi dan verifikasi laporan kegiatan harian dari seluruh peserta magang yang Anda bimbing di {{ auth()->user()->instansi->nama_instansi ?? 'Instansi' }}.
            </p>
        </div>
        <div class="absolute -right-8 -bottom-10 opacity-10 text-white pointer-events-none text-9xl">
            <i class="fa-solid fa-book-open-reader"></i>
        </div>
    </div>

    <!-- STATISTIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Peserta Bimbingan -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Peserta Bimbingan</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $totalPesertaBimbingan }}</h3>
                <span class="text-xs text-purple-600 font-medium">Dalam tanggung jawab</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Hadir Hari Ini</p>
                <h3 class="text-3xl font-black text-emerald-600 mt-1">{{ $hadirHariIni }}</h3>
                <span class="text-xs text-gray-400">Tepat waktu</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- Terlambat Hari Ini -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Terlambat Hari Ini</p>
                <h3 class="text-3xl font-black text-amber-600 mt-1">{{ $telatHariIni }}</h3>
                <span class="text-xs text-gray-400">Presensi masuk</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <!-- Laporan Menunggu Bimbingan -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Perlu Validasi</p>
                <h3 class="text-3xl font-black text-rose-600 mt-1">{{ $laporanPending }}</h3>
                <span class="text-xs text-rose-500 font-medium">Laporan Menunggu</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
        </div>
    </div>

    <!-- GRID CONTENT: DAFTAR PESERTA & LAPORAN TERAKHIR -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- DAFTAR PESERTA BIMBINGAN -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Peserta Magang Bimbingan Anda</h3>
                    <p class="text-xs text-gray-400">Daftar peserta yang dialokasikan kepada Anda</p>
                </div>
                <span class="text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-bold">
                    {{ $totalPesertaBimbingan }} Peserta
                </span>
            </div>

            <div class="space-y-3">
                @forelse($pesertaBimbingans as $pb)
                    <div class="p-3.5 bg-gray-50 rounded-2xl flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($pb->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $pb->name }}</h4>
                                <p class="text-[11px] text-gray-400">
                                    {{ $pb->asal_sekolah_pt ?? '-' }} • {{ $pb->nim_nisn ?? $pb->nim ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                            {{ $pb->divisi->nama_divisi ?? $pb->teknisi->nama ?? 'Umum' }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-8 text-xs">
                        Belum ada peserta magang yang dialokasikan ke bimbingan Anda.
                    </p>
                @endforelse
            </div>
        </div>

        <!-- LAPORAN KEGIATAN TERBARU -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">Laporan Kegiatan Terbaru</h3>
                        <p class="text-xs text-gray-400">Aktivitas yang dikirimkan oleh peserta bimbingan</p>
                    </div>
                    <a href="{{ route('pembimbing.laporan.index') }}" class="text-xs font-semibold text-purple-600 hover:text-purple-800">
                        Buka Semua Laporan →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentLaporan as $rl)
                        <div class="p-3.5 bg-gray-50 rounded-2xl flex items-center justify-between gap-3 text-xs">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800 truncate">{{ $rl->user->name ?? 'Peserta' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($rl->tanggal)->format('d M') }}</span>
                                </div>
                                <p class="text-gray-600 text-[11px] truncate mt-0.5">{{ $rl->kegiatan }}</p>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($rl->status === 'menunggu')
                                    <a href="{{ route('pembimbing.laporan.show', $rl->id) }}"
                                       class="bg-purple-600 hover:bg-purple-700 text-white font-bold text-[10px] px-3 py-1.5 rounded-xl transition">
                                        Periksa
                                    </a>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize
                                        @if($rl->status === 'disetujui') bg-emerald-100 text-emerald-800
                                        @else bg-amber-100 text-amber-800 @endif">
                                        {{ $rl->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-8 text-xs">
                            Belum ada laporan kegiatan yang dikirimkan oleh peserta.
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 text-right">
                <a href="{{ route('pembimbing.laporan.index') }}" class="text-xs font-semibold text-purple-600 hover:underline">
                    Kelola Seluruh Laporan Bimbingan →
                </a>
            </div>
        </div>

    </div>

</div>

@endsection
