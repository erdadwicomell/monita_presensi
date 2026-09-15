@extends('layouts.app')

@section('page-title', 'Dashboard Peserta')
@section('title', 'Dashboard Magang Saya')

@section('content')

<div class="space-y-6 sm:space-y-8">

    <!-- WELCOME BANNER (OPTIMASI GRADIENT & GLASSMORPHISM) -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-800 rounded-3xl shadow-lg p-6 sm:p-8 text-white relative overflow-hidden border border-white/10">
        <!-- Ambient Glow -->
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-blue-400/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-md px-3.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3.5 border border-white/10 text-blue-100">
                <i class="fa-solid fa-graduation-cap text-blue-200"></i> Peserta Magang
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                Halo, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="mt-2 text-blue-100/90 text-xs sm:text-sm leading-relaxed">
                Selamat datang di sistem absensi magang Monita. Pastikan untuk selalu melakukan presensi masuk & pulang tepat waktu serta melaporkan kegiatan harian Anda.
            </p>
        </div>
        <div class="absolute -right-6 -bottom-8 opacity-10 text-white pointer-events-none text-8xl sm:text-9xl">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
    </div>

    <!-- STATUS PRESENSI HARI INI (3 CARDS) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
        
        <!-- 1. Status Masuk -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div class="min-w-0 flex-1 mr-3">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Presensi Masuk Hari Ini</p>
                @if($absenMasuk)
                    <h3 class="text-xl font-black text-emerald-600 mt-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ substr($absenMasuk->jam, 0, 5) }} WIB
                    </h3>
                    <span class="inline-block mt-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full capitalize
                        {{ $absenMasuk->status === 'hadir' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        Status: {{ $absenMasuk->status }}
                    </span>
                @else
                    <h3 class="text-base font-bold text-gray-400 mt-1.5">Belum Presensi</h3>
                    <a href="{{ route('absensi.index') }}" 
                       class="inline-flex items-center gap-1 mt-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-full transition">
                        <span>Absen Masuk</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0 shadow-inner">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </div>
        </div>

        <!-- 2. Status Pulang -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:border-purple-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div class="min-w-0 flex-1 mr-3">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Presensi Pulang Hari Ini</p>
                @if($absenPulang)
                    <h3 class="text-xl font-black text-purple-600 mt-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-purple-500"></i> {{ substr($absenPulang->jam, 0, 5) }} WIB
                    </h3>
                    <span class="inline-block mt-1 text-[11px] text-purple-700 font-bold bg-purple-50 px-2.5 py-0.5 rounded-full border border-purple-200">
                        Selesai Bertugas
                    </span>
                @else
                    <h3 class="text-base font-bold text-gray-400 mt-1.5">Belum Presensi</h3>
                    <span class="inline-block mt-1 text-[11px] text-gray-400 bg-gray-50 px-2.5 py-0.5 rounded-full">
                        Sesuai jam pulang kantor
                    </span>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl flex-shrink-0 shadow-inner">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>
        </div>

        <!-- 3. Status Perizinan -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 hover:border-amber-200 hover:shadow-md transition-all duration-200 flex items-center justify-between">
            <div class="min-w-0 flex-1 mr-3">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Izin Aktif Hari Ini</p>
                @if($perizinanHariIni)
                    <h3 class="text-base font-black text-emerald-700 mt-1.5 capitalize truncate">
                        {{ str_replace('_', ' ', $perizinanHariIni->jenis_izin) }}
                    </h3>
                    <span class="text-xs text-gray-500 truncate block mt-0.5">{{ $perizinanHariIni->alasan }}</span>
                @else
                    <h3 class="text-base font-bold text-gray-700 mt-1.5">Tidak Ada Izin</h3>
                    <a href="{{ route('perizinan.create') }}" 
                       class="inline-flex items-center gap-1 mt-1 text-xs font-bold text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded-full transition">
                        <span>Ajukan Izin</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0 shadow-inner">
                <i class="fa-solid fa-file-signature"></i>
            </div>
        </div>

    </div>

    <!-- STATISTIK BULAN INI & LAPORAN KEGIATAN (2 KOLOM) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- DONUT CHART: STATISTIK KEHADIRAN BULAN INI -->
        <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-7 border border-gray-100 flex flex-col justify-between">
            <div>
                <h3 class="font-extrabold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-blue-600"></i>
                    Rasio Kehadiran Bulan Ini
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ now()->translatedFormat('F Y') }}</p>

                <div class="relative h-48 sm:h-52 flex items-center justify-center my-4">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center pt-4 border-t border-gray-100">
                <div class="p-2 bg-emerald-50/60 rounded-2xl border border-emerald-100/60">
                    <span class="text-emerald-700 block text-[10px] font-bold uppercase tracking-wider">Hadir</span>
                    <strong class="text-emerald-700 text-lg font-black">{{ $totalHadirBulan }}</strong>
                </div>
                <div class="p-2 bg-amber-50/60 rounded-2xl border border-amber-100/60">
                    <span class="text-amber-700 block text-[10px] font-bold uppercase tracking-wider">Telat</span>
                    <strong class="text-amber-700 text-lg font-black">{{ $totalTelatBulan }}</strong>
                </div>
                <div class="p-2 bg-blue-50/60 rounded-2xl border border-blue-100/60">
                    <span class="text-blue-700 block text-[10px] font-bold uppercase tracking-wider">Izin</span>
                    <strong class="text-blue-700 text-lg font-black">{{ $totalIzinBulan }}</strong>
                </div>
            </div>
        </div>

        <!-- LAPORAN KEGIATAN SUMMARY & RECENT LIST -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm p-6 sm:p-7 border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-extrabold text-gray-800 text-sm sm:text-base flex items-center gap-2">
                            <i class="fa-solid fa-book-bookmark text-indigo-600"></i>
                            Status Laporan Kegiatan
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Verifikasi bimbingan oleh pembimbing instansi</p>
                    </div>
                    <a href="{{ route('laporan.index') }}" 
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3.5 py-1.5 rounded-full transition self-start sm:self-auto border border-blue-100">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Buat Laporan</span>
                    </a>
                </div>

                <!-- 3 CARDS STATUS LAPORAN -->
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="p-3.5 bg-emerald-50/80 rounded-2xl border border-emerald-100 text-center">
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Disetujui</span>
                        <strong class="text-2xl font-black text-emerald-600 mt-0.5 block">{{ $laporanDisetujui }}</strong>
                    </div>
                    <div class="p-3.5 bg-amber-50/80 rounded-2xl border border-amber-100 text-center">
                        <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Perlu Revisi</span>
                        <strong class="text-2xl font-black text-amber-600 mt-0.5 block">{{ $laporanRevisi }}</strong>
                    </div>
                    <div class="p-3.5 bg-blue-50/80 rounded-2xl border border-blue-100 text-center">
                        <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">Menunggu</span>
                        <strong class="text-2xl font-black text-blue-600 mt-0.5 block">{{ $laporanMenunggu }}</strong>
                    </div>
                </div>

                <!-- DAFTAR LAPORAN TERAKHIR -->
                <div class="space-y-2.5">
                    @forelse($recentLaporan as $lap)
                        <div class="p-3.5 bg-gray-50/80 hover:bg-gray-100/70 transition rounded-2xl border border-gray-100 flex items-center justify-between gap-3 text-xs">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-800 truncate">{{ $lap->kegiatan }}</p>
                                <span class="text-gray-400 text-[11px] mt-0.5 block">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($lap->tanggal)->translatedFormat('d F Y') }}
                                </span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold capitalize flex-shrink-0 border
                                @if($lap->status === 'disetujui') bg-emerald-50 text-emerald-700 border-emerald-200
                                @elseif($lap->status === 'revisi') bg-amber-50 text-amber-700 border-amber-200
                                @else bg-blue-50 text-blue-700 border-blue-200 @endif">
                                {{ $lap->status }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 text-xs">
                            <i class="fa-solid fa-file-pen text-3xl mb-2 text-gray-300 block"></i>
                            Belum ada laporan kegiatan yang dikirimkan.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 text-right">
                <a href="{{ route('laporan.index') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 inline-flex items-center gap-1.5 transition">
                    <span>Lihat Semua Riwayat Laporan</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

</div>

<!-- SCRIPT CHART.JS (UTUH TANPA PERUBAHAN LOGIKA) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart');
    if (ctx) {
        const total = {{ $totalHadirBulan + $totalTelatBulan + $totalIzinBulan }};
        const dataValues = total === 0 ? [1, 0, 0] : [{{ $totalHadirBulan }}, {{ $totalTelatBulan }}, {{ $totalIzinBulan }}];
        const colors = total === 0 ? ['#e2e8f0'] : ['#10b981', '#f59e0b', '#3b82f6'];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir Tepat Waktu', 'Terlambat', 'Izin'],
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    }
});
</script>

@endsection