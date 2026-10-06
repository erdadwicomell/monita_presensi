@extends('layouts.app')

@section('page-title', 'Audit Log Keamanan & Anti-Spoofing')
@section('title', 'Audit Log Keamanan Presensi')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-blue-600"></i>
                Audit Log & Keamanan GPS
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Catatan otomatis deteksi anti-spoofing GPS, anomali perpindahan lokasi ekstrim, dan integritas data HMAC.
            </p>
        </div>
    </div>

    <!-- CARDS STATISTIK -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Audit Log</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-0.5">{{ $totalLogs }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tingkat Bahaya / Ditolak</p>
                <h3 class="text-2xl font-extrabold text-rose-600 mt-0.5">{{ $totalBahaya }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-satellite-dish"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Percobaan Spoofing GPS</p>
                <h3 class="text-2xl font-extrabold text-amber-600 mt-0.5">{{ $totalSpoofing }}</h3>
            </div>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <form method="GET" action="{{ route('admin.audit.log') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <!-- Filter Kategori -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Kategori</label>
                <select name="kategori" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    <option value="gps_spoofing" {{ request('kategori') == 'gps_spoofing' ? 'selected' : '' }}>GPS Spoofing</option>
                    <option value="hmac_manipulation" {{ request('kategori') == 'hmac_manipulation' ? 'selected' : '' }}>Manipulasi Data HMAC</option>
                    <option value="perizinan" {{ request('kategori') == 'perizinan' ? 'selected' : '' }}>Aktivitas Perizinan</option>
                    <option value="laporan" {{ request('kategori') == 'laporan' ? 'selected' : '' }}>Aktivitas Laporan</option>
                </select>
            </div>

            <!-- Filter Tingkat Risiko -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Tingkat Risiko</label>
                <select name="tingkat_risiko" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Risiko</option>
                    <option value="info" {{ request('tingkat_risiko') == 'info' ? 'selected' : '' }}>Info / Normal</option>
                    <option value="warning" {{ request('tingkat_risiko') == 'warning' ? 'selected' : '' }}>Peringatan</option>
                    <option value="bahaya" {{ request('tingkat_risiko') == 'bahaya' ? 'selected' : '' }}>Bahaya / Ilegal</option>
                </select>
            </div>

            <!-- Filter Peserta -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Peserta</label>
                <select name="user_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Peserta</option>
                    @foreach($pesertas as $p)
                        <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tanggal Mulai -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Tombol Filter -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-xl text-sm transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['kategori', 'tingkat_risiko', 'user_id', 'tanggal_mulai']))
                    <a href="{{ route('admin.audit.log') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm transition flex items-center justify-center" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLE CONTAINER (PREMIUM ENTERPRISE UI) -->
    <div class="bg-white rounded-2xl shadow-md shadow-slate-100/80 border border-slate-100/50 p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <!-- THEAD (HEADER TABEL RESMI) -->
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-semibold text-xs tracking-wider uppercase border-b border-slate-100">
                        <th class="py-4 px-6">Waktu Insiden</th>
                        <th class="py-4 px-6">Peserta / Pengguna</th>
                        <th class="py-4 px-6">Kategori & Tingkat Risiko</th>
                        <th class="py-4 px-6">Aktivitas / Detail Alasan</th>
                        <th class="py-4 px-6">Koordinat GPS Terdeteksi</th>
                        <th class="py-4 px-6">IP & User Agent Perangkat</th>
                    </tr>
                </thead>

                <!-- TBODY & TR (ZEBRA STRIPING + HOVER LEMBUT) -->
                <tbody class="divide-y divide-slate-100">
                    @forelse($auditLogs as $log)
                        @php
                            $lat = $log->latitude ?? ($log->payload_extra['latitude'] ?? ($log->payload_extra['lat'] ?? null));
                            $lng = $log->longitude ?? ($log->payload_extra['longitude'] ?? ($log->payload_extra['lng'] ?? ($log->payload_extra['lon'] ?? null)));
                        @endphp
                        <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-blue-50/40 transition-colors duration-150">
                            <!-- Waktu Insiden -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <p class="font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</p>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $log->created_at->format('H:i:s') }} WIB</p>
                            </td>

                            <!-- Peserta / Pengguna -->
                            <td class="py-4 px-6">
                                @if($log->user)
                                    <p class="font-bold text-slate-800">{{ $log->user->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $log->user->email }}</p>
                                @else
                                    <span class="text-slate-400 italic">Sistem / Otomatis</span>
                                @endif
                            </td>

                            <!-- Kategori & Tingkat Risiko -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold capitalize
                                        @if($log->tingkat_risiko === 'bahaya' || $log->tingkat_risiko === 'tinggi') bg-rose-50 text-rose-700 border border-rose-200/60
                                        @elseif($log->tingkat_risiko === 'warning' || $log->tingkat_risiko === 'sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                        @else bg-blue-50 text-blue-700 border border-blue-200/60 @endif">
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                        {{ $log->tingkat_risiko }}
                                    </span>
                                    <p class="text-[10px] text-slate-500 font-mono">{{ str_replace('_', ' ', $log->kategori) }}</p>
                                </div>
                            </td>

                            <!-- Aktivitas / Detail Alasan -->
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-800">{{ $log->judul }}</p>
                                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $log->deskripsi }}</p>
                            </td>

                            <!-- Koordinat GPS Terdeteksi -->
                            <td class="py-4 px-6 whitespace-nowrap text-xs font-mono text-slate-600">
                                @if(!is_null($lat) && !is_null($lng))
                                    <a href="https://maps.google.com/?q={{ $lat }},{{ $lng }}" target="_blank" class="inline-flex items-center gap-1.5 bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 px-3 py-1.5 rounded-xl border border-slate-200/60 transition text-xs font-mono group">
                                        <i class="fa-solid fa-map-marker-alt text-red-500"></i>
                                        <span class="group-hover:underline">{{ $lat }}, {{ $lng }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>

                            <!-- IP & User Agent Perangkat -->
                            <td class="py-4 px-6 text-xs text-slate-500 max-w-xs">
                                <p class="font-mono font-bold text-slate-700">{{ $log->ip_address ?? '127.0.0.1' }}</p>
                                <p class="text-[10px] text-slate-400 truncate mt-0.5" title="{{ $log->user_agent }}">{{ $log->user_agent }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-shield-check text-4xl mb-3 block text-emerald-400"></i>
                                <span class="font-medium text-slate-500">Tidak ditemukan catatan anomali atau audit log keamanan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditLogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 mt-4 rounded-xl">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
