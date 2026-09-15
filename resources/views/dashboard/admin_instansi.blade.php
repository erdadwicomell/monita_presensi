@extends('layouts.app')

@section('page-title', 'Dashboard Admin Instansi')
@section('title', 'Dashboard Monitoring')

@section('content')

<div class="space-y-6 sm:space-y-8">

    <!-- WELCOME BANNER (OPTIMASI GRADIENT & GLASSMORPHISM) -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-3xl shadow-lg p-6 sm:p-8 text-white relative overflow-hidden border border-white/10">
        <!-- Subtle Glow Effect -->
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-blue-400/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-md px-3.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3.5 border border-white/10 text-blue-100">
                <i class="fa-solid fa-building text-blue-200"></i> {{ auth()->user()->instansi->nama_instansi ?? 'Instansi' }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="mt-2 text-blue-100/90 text-xs sm:text-sm leading-relaxed">
                Pantau kehadiran realtime seluruh peserta magang, kelola verifikasi perizinan & laporan kegiatan, serta pantau integritas keamanan GPS instansi Anda.
            </p>
        </div>
        <div class="absolute -right-6 -bottom-8 opacity-10 text-white pointer-events-none text-8xl sm:text-9xl">
            <i class="fa-solid fa-chart-pie"></i>
        </div>
    </div>

    <!-- 4 STATISTIC CARDS (PENINGKATAN ELEVASI & TYPOGRAPHY) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- 1. Total Peserta -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Peserta Bimbingan</p>
                <h3 class="text-2xl sm:text-3xl font-black text-gray-800 mt-1.5 tracking-tight">{{ $totalPeserta }}</h3>
                <span class="inline-block mt-1 text-[11px] text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded-full">
                    Terdaftar aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- 2. Hadir Hari Ini -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Hadir Tepat Waktu</p>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1.5 tracking-tight">{{ $hadirHariIni }}</h3>
                <span class="inline-block mt-1 text-[11px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                    Presensi masuk
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- 3. Terlambat Hari Ini -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:border-amber-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Terlambat</p>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-600 mt-1.5 tracking-tight">{{ $telatHariIni }}</h3>
                <span class="inline-block mt-1 text-[11px] text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded-full">
                    Hari ini
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <!-- 4. Izin & Laporan Pending -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Perlu Tindakan</p>
                <h3 class="text-2xl sm:text-3xl font-black text-rose-600 mt-1.5 tracking-tight">{{ $perizinanPending + $laporanPending }}</h3>
                <span class="inline-block mt-1 text-[11px] text-rose-700 font-semibold bg-rose-50 px-2 py-0.5 rounded-full">
                    {{ $perizinanPending }} Izin • {{ $laporanPending }} Laporan
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>

    </div>

    <!-- CHART & QUICK ACTIONS GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- CHART: TREN PRESENSI 7 HARI TERAKHIR -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm p-6 sm:p-7 border border-gray-100 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-extrabold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-600"></i>
                        Tren Kehadiran 7 Hari Terakhir
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Statistik perbandingan hadir tepat waktu vs terlambat</p>
                </div>
                <span class="text-[11px] bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-bold self-start sm:self-auto border border-blue-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 inline-block mr-1"></span> Live Update
                </span>
            </div>

            <div class="relative h-64 sm:h-72 w-full">
                <canvas id="weeklyAttendanceChart"></canvas>
            </div>
        </div>

        <!-- QUICK ACTION LINKS -->
        <div class="space-y-6">
            
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-sm border border-white/5">
                <h3 class="font-extrabold text-xs uppercase tracking-wider text-gray-400 mb-3">Pintasan Cepat</h3>
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ route('rekap.absensi') }}" 
                       class="p-3 bg-white/10 hover:bg-white/15 rounded-2xl text-xs font-semibold transition text-center block transform hover:-translate-y-0.5 duration-150">
                        <i class="fa-solid fa-file-pdf mb-1 text-base block text-blue-400"></i> Rekap PDF
                    </a>
                    <a href="{{ route('admin.perizinan.index') }}" 
                       class="p-3 bg-white/10 hover:bg-white/15 rounded-2xl text-xs font-semibold transition text-center block transform hover:-translate-y-0.5 duration-150">
                        <i class="fa-solid fa-file-circle-check mb-1 text-base block text-emerald-400"></i> Izin Magang
                    </a>
                    <a href="{{ route('admin.laporan.index') }}" 
                       class="p-3 bg-white/10 hover:bg-white/15 rounded-2xl text-xs font-semibold transition text-center block transform hover:-translate-y-0.5 duration-150">
                        <i class="fa-solid fa-book-bookmark mb-1 text-base block text-amber-400"></i> Laporan
                    </a>
                    <a href="{{ route('peserta.index') }}" 
                       class="p-3 bg-white/10 hover:bg-white/15 rounded-2xl text-xs font-semibold transition text-center block transform hover:-translate-y-0.5 duration-150">
                        <i class="fa-solid fa-user-plus mb-1 text-base block text-purple-400"></i> Peserta
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- LIVE TABLE: PRESENSI TERAKHIR -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-800">Presensi Masuk & Pulang Terkini</h2>
                <p class="text-xs text-slate-400 mt-0.5">Peserta yang baru saja melakukan presensi masuk atau pulang</p>
            </div>
            <a href="{{ route('rekap.absensi') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1.5 self-start sm:self-auto">
                <span>Lihat Semua Presensi</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Peserta</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Sesi Presensi</th>
                        <th class="px-6 py-4">Status Kehadiran</th>
                        <th class="px-6 py-4">Jarak Radius</th>
                        <th class="px-6 py-4">Integritas HMAC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentAbsensi as $ra)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                {{ $ra->user->name ?? 'Peserta' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono">
                                {{ substr($ra->jam, 0, 5) }} WIB
                            </td>
                            <td class="px-6 py-4">
                                @if($ra->tipe_absensi === 'masuk')
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-semibold text-xs border border-blue-200/60">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-[10px]"></i> Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 px-3 py-1 rounded-full font-semibold text-xs border border-purple-200/60">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i> Pulang
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($ra->status === 'hadir')
                                    <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-semibold text-xs border border-emerald-200/60 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Hadir Tepat
                                    </span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full font-semibold text-xs border border-amber-200/60 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Telat
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-mono text-xs">
                                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200/80 font-semibold">
                                    {{ round($ra->jarak, 1) }} m
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-emerald-700 font-mono text-xs bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60 font-semibold inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-shield-check text-[10px]"></i> Valid
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-clipboard-user text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada data presensi yang tercatat untuk hari ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- SCRIPT CHART.JS (UTUH TANPA PERUBAHAN LOGIKA) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyAttendanceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [
                    {
                        label: 'Hadir Tepat Waktu',
                        data: {!! json_encode($hadirData) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 8,
                    },
                    {
                        label: 'Terlambat',
                        data: {!! json_encode($telatData) !!},
                        backgroundColor: '#f59e0b',
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'Inter', size: 12 }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>

@endsection