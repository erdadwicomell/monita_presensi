@extends('layouts.app')

@section('page-title', 'Dashboard Super Admin')

@section('content')

<div class="space-y-6">

    <!-- WELCOME HERO BANNER -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden border border-white/10">
        <!-- Ambient Glow -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider text-blue-300 mb-3 border border-white/10">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    <span>Pusat Monitoring & Keamanan Global</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                    Dashboard Super Admin
                </h1>
                <p class="mt-2 text-gray-300 text-xs sm:text-sm leading-relaxed">
                    Pantau data instansi, perkembangan kehadiran magang lintas organisasi, dan integritas keamanan sistem MONITA secara terpusat.
                </p>
            </div>

            <div class="flex items-center gap-3 bg-white/5 border border-white/10 backdrop-blur-md p-4 rounded-2xl self-start md:self-auto">
                <div class="w-12 h-12 rounded-xl bg-blue-600/30 text-blue-400 border border-blue-500/30 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div>
                    <span class="text-[11px] text-gray-400 block font-medium">Status Sistem</span>
                    <strong class="text-emerald-400 text-xs font-bold flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Semua Node Aktif & Normal
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIC CARDS (5 METRICS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        
        <!-- 1. Total Instansi -->
        <a href="{{ route('instansi.index') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:border-blue-200 cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Instansi</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-gray-800">{{ $totalInstansi }}</h3>
                <p class="text-[11px] text-gray-400 mt-1">
                    <span class="text-blue-600 font-semibold">{{ $totalKantor }}</span> Kantor • 
                    <span class="text-indigo-600 font-semibold">{{ $totalPemerintahan }}</span> Pem • 
                    <span class="text-amber-600 font-semibold">{{ $totalLapangan }}</span> Lap
                </p>
            </div>
        </a>

        <!-- 2. Total Pengguna Global -->
        <a href="{{ route('users.index') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:border-indigo-200 cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total User</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-gray-800">{{ $totalUserGlobal }}</h3>
                <p class="text-[11px] text-gray-400 mt-1">
                    <strong class="text-blue-600">{{ $totalAdminInstansi }}</strong> Admin • 
                    <strong class="text-purple-600">{{ $totalPembimbing }}</strong> Pemb • 
                    <strong class="text-emerald-600">{{ $totalPeserta }}</strong> Peserta
                </p>
            </div>
        </a>

        <!-- 3. Presensi Hari Ini -->
        <a href="{{ route('rekap.absensi') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:border-emerald-200 cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Presensi Hari Ini</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-emerald-600">{{ $totalPresensiHariIni }}</h3>
                <p class="text-[11px] text-gray-400 mt-1">
                    <span class="text-emerald-600 font-semibold">{{ $hadirHariIni }} Tepat Waktu</span> • 
                    <span class="text-amber-600 font-semibold">{{ $telatHariIni }} Telat</span>
                </p>
            </div>
        </a>

        <!-- 4. Laporan Kegiatan -->
        <a href="{{ route('admin.laporan.index') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:border-purple-200 cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Laporan Magang</span>
                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-gray-800">{{ $totalLaporan }}</h3>
                <p class="text-[11px] text-gray-400 mt-1">
                    <span class="text-amber-600 font-semibold">{{ $laporanPending }} Menunggu Validasi</span>
                </p>
            </div>
        </a>

        <!-- 5. Audit Keamanan & Spoofing -->
        <a href="{{ route('admin.audit.log') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:border-rose-200 cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Audit Keamanan</span>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-rose-600">{{ $totalAnomaliKeamanan }}</h3>
                <p class="text-[11px] text-gray-400 mt-1">
                    <span class="text-rose-500 font-semibold">{{ $anomaliHariIni }} Insiden Hari Ini</span>
                </p>
            </div>
        </a>

    </div>

    <!-- CHARTS SECTION (2 KOLOM) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- TREN PRESENSI 7 HARI TERAKHIR GLOBAL -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="font-black text-gray-800 text-base flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-600"></i>
                        Tren Kehadiran Global (7 Hari Terakhir)
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbandingan presensi hadir tepat waktu vs terlambat lintas seluruh instansi</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 font-semibold text-emerald-600">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Hadir Tepat
                    </span>
                    <span class="flex items-center gap-1.5 font-semibold text-amber-600">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span> Terlambat
                    </span>
                </div>
            </div>

            <div class="h-64 sm:h-72 w-full">
                <canvas id="superAdminTrendChart"></canvas>
            </div>
        </div>

        <!-- DISTRIBUSI JENIS INSTANSI -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <h3 class="font-black text-gray-800 text-base flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-chart-pie text-indigo-600"></i>
                    Kategori Instansi
                </h3>
                <p class="text-xs text-gray-400 mb-4">Distribusi klasifikasi instansi terdaftar</p>
            </div>

            <div class="h-52 w-full relative flex items-center justify-center">
                <canvas id="superAdminCategoryChart"></canvas>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-xs">
                <div class="flex items-center justify-between text-gray-600">
                    <span class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Perkantoran Swasta
                    </span>
                    <strong class="text-gray-800">{{ $totalKantor }} Instansi</strong>
                </div>
                <div class="flex items-center justify-between text-gray-600">
                    <span class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Pemerintahan / BUMN
                    </span>
                    <strong class="text-gray-800">{{ $totalPemerintahan }} Instansi</strong>
                </div>
                <div class="flex items-center justify-between text-gray-600">
                    <span class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Lapangan / Teknisi
                    </span>
                    <strong class="text-gray-800">{{ $totalLapangan }} Instansi</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- TABEL MONITORING INSTANSI TERDAFTAR -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2 border border-blue-100">
                    <i class="fa-solid fa-building-circle-check"></i> Data Organisasi
                </span>
                <h3 class="font-black text-slate-800 text-lg sm:text-xl">
                    Monitoring Instansi & Admin Terdaftar
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar instansi terkini beserta jumlah alokasi peserta dan pembimbing</p>
            </div>

            <a href="{{ route('instansi.index') }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2.5 rounded-xl text-xs transition flex items-center gap-2 self-start sm:self-auto">
                <span>Kelola Semua Instansi</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Nama Instansi</th>
                        <th class="px-6 py-4">Jenis Instansi</th>
                        <th class="px-6 py-4">Admin Penanggung Jawab</th>
                        <th class="px-6 py-4 text-center">Peserta</th>
                        <th class="px-6 py-4 text-center">Pembimbing</th>
                        <th class="px-6 py-4">Geofence Radius</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($instansiOverview as $ins)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $ins->nama_instansi }}
                                <span class="block text-[11px] font-normal text-slate-400 truncate max-w-xs mt-0.5">{{ $ins->alamat }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase inline-flex items-center gap-1.5
                                    @if($ins->jenis_instansi === 'kantor') bg-blue-50 text-blue-700 border border-blue-200/60
                                    @elseif($ins->jenis_instansi === 'pemerintahan') bg-indigo-50 text-indigo-700 border border-indigo-200/60
                                    @else bg-amber-50 text-amber-700 border border-amber-200/60 @endif">
                                    <i class="fa-solid fa-building text-[10px]"></i>
                                    {{ $ins->jenis_instansi }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($ins->users && $ins->users->count() > 0)
                                    <div class="font-bold text-slate-800">{{ $ins->users->first()->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $ins->users->first()->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Belum terasosiasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-50 text-blue-700 font-semibold px-3 py-1 rounded-full text-xs border border-blue-200/60">
                                    {{ $ins->peserta_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-purple-50 text-purple-700 font-semibold px-3 py-1 rounded-full text-xs border border-purple-200/60">
                                    {{ $ins->pembimbing_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-mono text-xs">
                                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200/80 font-semibold">
                                    {{ $ins->radius }} m
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-semibold px-3 py-1 rounded-full text-xs inline-flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-building-circle-xmark text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada instansi yang terdaftar di sistem.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- GRID BAWAH: AUDIT LOG KEAMANAN & STREAM PRESENSI TERBARU (2 KOLOM) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- AUDIT LOG & STATUS KEAMANAN GPS GLOBAL -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-800 text-base">Status Keamanan GPS & Anti-Spoofing</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Integritas Kriptografi HMAC & Deteksi Manipulasi Lokasi</p>
                        </div>
                    </div>
                    <span class="bg-rose-100 text-rose-700 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $totalAnomaliKeamanan }} Total Insiden
                    </span>
                </div>

                <!-- Metric Badges Status Keamanan -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-gray-500 font-medium block">Spoofing Dicegah</span>
                            <span class="font-black text-rose-600 text-sm">{{ $totalSpoofingGlobal ?? $totalAnomaliKeamanan }} Insiden</span>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold">
                            <i class="fa-solid fa-satellite-dish"></i>
                        </div>
                    </div>
                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-gray-500 font-medium block">Proteksi HMAC-SHA256</span>
                            <span class="font-black text-emerald-600 text-sm flex items-center gap-1">
                                <i class="fa-solid fa-check text-xs"></i> Aktif & Valid
                            </span>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                    </div>
                </div>

                <!-- Daftar Log Anomali Terbaru -->
                <div class="space-y-3">
                    @forelse($recentAuditLogs as $log)
                        <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100 flex items-start justify-between gap-3 text-xs">
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs mt-0.5 flex-shrink-0
                                    @if($log->tingkat_risiko === 'bahaya' || $log->tingkat_risiko === 'tinggi') bg-rose-100 text-rose-600 border border-rose-200
                                    @elseif($log->tingkat_risiko === 'warning' || $log->tingkat_risiko === 'sedang') bg-amber-100 text-amber-600 border border-amber-200
                                    @else bg-blue-100 text-blue-600 border border-blue-200 @endif">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-gray-800">{{ $log->judul }}</h4>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                                            @if($log->tingkat_risiko === 'bahaya' || $log->tingkat_risiko === 'tinggi') bg-rose-100 text-rose-700
                                            @else bg-amber-100 text-amber-700 @endif">
                                            {{ $log->tingkat_risiko }}
                                        </span>
                                    </div>
                                    <p class="text-gray-500 text-[11px] mt-0.5 line-clamp-1">{{ $log->deskripsi }}</p>
                                    <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-2">
                                        <span><i class="fa-solid fa-user mr-1"></i> {{ $log->user->name ?? 'System' }}</span>
                                        <span>•</span>
                                        <span><i class="fa-solid fa-building mr-1"></i> {{ $log->instansi->nama_instansi ?? 'Umum' }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap">
                                {{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-xs">
                            <i class="fa-solid fa-shield-check text-2xl mb-1 text-emerald-500 block"></i>
                            Semua koordinat GPS dan payload HMAC normal.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tombol Navigasi ke Halaman Audit Log Khusus Super Admin -->
            <a href="{{ route('admin.audit.log') }}" 
               class="mt-5 flex items-center justify-center gap-2 w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-2xl transition shadow-md shadow-slate-900/10 cursor-pointer">
                <i class="fa-solid fa-shield-halved text-xs"></i>
                <span>Lihat Audit Log Keamanan Lengkap</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- STREAM PRESENSI TERBARU LINTAS INSTANSI -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-black text-gray-800 text-base flex items-center gap-2">
                            <i class="fa-solid fa-satellite-dish text-emerald-600"></i>
                            Aktivitas Presensi Terkini
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Aliran presensi realtime peserta dari berbagai instansi</p>
                    </div>
                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">
                        Live Stream
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($recentPresensi as $absen)
                        <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($absen->user->name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-gray-800">{{ $absen->user->name ?? 'Peserta' }}</h4>
                                        <span class="text-[10px] text-gray-400">({{ $absen->user->instansi->nama_instansi ?? '-' }})</span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        Presensi <strong>{{ ucfirst($absen->tipe_absensi) }}</strong> • Jam <strong>{{ $absen->jam }}</strong>
                                    </div>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold capitalize
                                @if($absen->status === 'hadir') bg-emerald-100 text-emerald-800
                                @elseif($absen->status === 'telat') bg-amber-100 text-amber-800
                                @else bg-rose-100 text-rose-800 @endif">
                                {{ $absen->status }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 text-xs">
                            <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 text-gray-300 block"></i>
                            Belum ada aktivitas presensi tercatat hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

<!-- CHART.JS INTEGRATION -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart Tren 7 Hari Terakhir
    const ctxTrend = document.getElementById('superAdminTrendChart');
    if (ctxTrend) {
        new Chart(ctxTrend.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [
                    {
                        label: 'Hadir Tepat Waktu',
                        data: {!! json_encode($hadirData) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4
                    },
                    {
                        label: 'Terlambat',
                        data: {!! json_encode($telatData) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#94a3b8' },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Doughnut Chart Kategori Instansi
    const ctxCat = document.getElementById('superAdminCategoryChart');
    if (ctxCat) {
        new Chart(ctxCat.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Kantor', 'Pemerintahan', 'Lapangan'],
                datasets: [{
                    data: [{{ $totalKantor }}, {{ $totalPemerintahan }}, {{ $totalLapangan }}],
                    backgroundColor: ['#3b82f6', '#6366f1', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>

@endsection