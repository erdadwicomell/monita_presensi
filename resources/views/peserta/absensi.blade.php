@extends('layouts.app')

@section('page-title', 'Presensi GPS')

@section('content')

<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    /* ISOLASI Z-INDEX LEAFLET TANPA MERUSAK TILE PANE */
    #map {
        width: 100% !important;
        min-height: 280px;
        z-index: 1 !important;
    }
    .leaflet-container {
        font-family: inherit;
        background-color: #f1f5f9;
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">

    <!-- LEFT COLUMN: CAMERA, PREVIEW & MAP -->
    <div class="lg:col-span-2 space-y-5 sm:space-y-6">

        <div class="bg-white rounded-3xl shadow-sm p-5 sm:p-7 border border-gray-100">

            <!-- HEADER INFO -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-gray-100">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight">
                        Presensi GPS Realtime
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-400 mt-0.5">
                        Sistem mendeteksi radius dan koordinat lokasi peserta magang secara otomatis.
                    </p>
                </div>
                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold self-start sm:self-auto border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span> Geofencing Aktif
                </span>
            </div>

            <!-- CAMERA & PREVIEW CONTAINER -->
            <div class="mt-5">
                <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden border border-gray-200 shadow-xs bg-black">
                    <video id="video"
                           autoplay
                           playsinline
                           muted
                           class="w-full aspect-video md:aspect-[4/3] object-cover bg-black block"></video>

                    <img id="previewImg"
                         class="w-full h-auto object-contain bg-black hidden"
                         alt="Pratinjau Foto Absensi">
                </div>

                <canvas id="canvas" style="display:none;"></canvas>

                <!-- CAMERA ACTION BUTTONS (RESPONSIVE FULL-WIDTH DI HP, AUTO DI LAPTOP) -->
                <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        <button type="button"
                                id="capture"
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold px-5 py-3 rounded-2xl shadow-sm hover:shadow-md transition text-xs sm:text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-camera text-base"></i>
                            <span>Ambil Foto Selfie</span>
                        </button>

                        <button type="button"
                                id="retake"
                                class="hidden w-full sm:w-auto bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold px-4 py-3 rounded-2xl shadow-sm transition text-xs sm:text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-rotate-left text-base"></i>
                            <span>Ulangi Foto</span>
                        </button>
                    </div>

                    <div id="photoStatus" class="hidden text-center sm:text-right">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Foto Selfie Siap
                        </span>
                    </div>
                </div>
            </div>

            <!-- LEAFLET MAP (RESPONSIVE CONTAINER DENGAN ISOLASI TATA LETAK) -->
            <div class="mt-6">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">
                    <i class="fa-solid fa-map-location-dot mr-1 text-blue-600"></i> Peta Radius & Lokasi Anda
                </label>
                <div class="relative w-full rounded-2xl sm:rounded-3xl overflow-hidden border border-gray-200 shadow-xs isolate z-0 bg-gray-100">
                    <div id="map"
                         class="w-full h-64 sm:h-80 md:h-[420px] lg:h-[460px]">
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- RIGHT COLUMN: STATUS LOKASI, DETAIL KOORDINAT & FORM SUBMIT -->
    <div class="space-y-5 sm:space-y-6">

        <div class="bg-white rounded-3xl shadow-sm p-5 sm:p-7 border border-gray-100">

            <h2 class="text-lg font-black text-gray-800 tracking-tight pb-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-satellite text-blue-600"></i>
                Status & Validasi Lokasi
            </h2>

            <!-- STATUS PERIZINAN HARI INI -->
            <div class="mt-4">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                    Status Perizinan Hari Ini
                </p>

                @if($perizinan)
                    @if($perizinan->jenis_izin === 'terlambat' && $absenMasuk)
                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-xs">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full font-bold border border-emerald-200 mb-2">
                                <i class="fa-solid fa-circle-check"></i> Izin Terlambat Selesai
                            </span>
                            <p class="text-gray-600 leading-relaxed">
                                Izin terlambat ({{ substr($perizinan->jam_mulai_izin, 0, 5) }} - {{ substr($perizinan->jam_selesai_izin, 0, 5) }} WIB) telah selesai digunakan.
                            </p>
                        </div>

                    @elseif($perizinan->jenis_izin === 'pulang_awal' && $absenPulang)
                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-xs">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full font-bold border border-emerald-200 mb-2">
                                <i class="fa-solid fa-circle-check"></i> Izin Pulang Awal Selesai
                            </span>
                            <p class="text-gray-600 leading-relaxed">
                                Izin pulang awal telah selesai digunakan untuk presensi hari ini.
                            </p>
                        </div>

                    @elseif($perizinan->jenis_izin === 'tidak_hadir')
                        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs">
                            <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full font-bold border border-blue-200 mb-2">
                                <i class="fa-solid fa-bed"></i> Izin Tidak Hadir Aktif
                            </span>
                            <p class="text-gray-700 mt-1"><strong>Alasan:</strong> {{ $perizinan->alasan }}</p>
                            <p class="text-blue-700 font-bold mt-2">
                                ✓ Anda dibebaskan dari kewajiban presensi masuk & pulang hari ini.
                            </p>
                        </div>

                    @else
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs">
                            <span class="inline-block bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-bold uppercase tracking-wider mb-2">
                                Perizinan Aktif Disetujui
                            </span>
                            <div class="space-y-1 text-gray-700 mt-2">
                                <p><strong>Jenis:</strong> {{ ucfirst(str_replace('_',' ', $perizinan->jenis_izin)) }}</p>
                                @if($perizinan->jam_mulai_izin)
                                    <p><strong>Waktu:</strong> {{ substr($perizinan->jam_mulai_izin, 0, 5) }} - {{ substr($perizinan->jam_selesai_izin, 0, 5) }} WIB</p>
                                @endif
                                <p><strong>Alasan:</strong> {{ $perizinan->alasan }}</p>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-3.5 text-xs text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle text-gray-400"></i>
                        <span>Tidak ada perizinan aktif untuk sesi ini.</span>
                    </div>
                @endif
            </div>

            <!-- STATUS GPS ALERT BOX -->
            <div id="statusBox" class="mt-5 bg-gray-100 rounded-2xl p-4 sm:p-5 transition-all text-center">
                <h3 class="text-sm sm:text-base font-bold text-gray-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin text-blue-600"></i>
                    <span>Menunggu deteksi sinyal GPS...</span>
                </h3>
            </div>

            <!-- GRID DETAIL KOORDINAT (RESPONSIVE 2 KOLOM RAPI DI HP & TABLET) -->
            <div class="mt-5 grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-gray-400 font-bold uppercase text-[10px]">Latitude</p>
                    <h4 id="latitude" class="font-black text-gray-800 text-xs sm:text-sm mt-0.5 truncate font-mono">-</h4>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-gray-400 font-bold uppercase text-[10px]">Longitude</p>
                    <h4 id="longitude" class="font-black text-gray-800 text-xs sm:text-sm mt-0.5 truncate font-mono">-</h4>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-gray-400 font-bold uppercase text-[10px]">Jarak Ke Radius</p>
                    <h4 id="distance" class="font-black text-gray-800 text-xs sm:text-sm mt-0.5 font-mono">-</h4>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-gray-400 font-bold uppercase text-[10px]">Akurasi GPS</p>
                    <h4 id="accuracy" class="font-black text-blue-600 text-xs sm:text-sm mt-0.5 font-mono">-</h4>
                </div>
            </div>

            <!-- FORM PRESENSI -->
            <form action="{{ route('absensi.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="latitude" id="inputLatitude">
                <input type="hidden" name="longitude" id="inputLongitude">
                <input type="hidden" name="accuracy" id="inputAccuracy">
                <input type="hidden" name="jarak" id="inputJarak">
                <input type="hidden" name="foto" id="fotoInput">

                <!-- INFORMASI PENEMPATAN & TEKNISI / DIVISI -->
                @if($isLapangan)
                    <div class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-2xl text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="bg-blue-600 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                Penempatan Lapangan
                            </span>
                            <span class="text-blue-700 font-semibold text-[11px]">
                                <i class="fa-solid fa-route mr-1"></i> Pulang Bebas Radius
                            </span>
                        </div>

                        @if(!$absenMasuk)
                            <!-- DROPDOWN PILIH TEKNISI PENDAMPING SAAT DATANG -->
                            <div>
                                <label class="block font-bold text-gray-700 text-[11px] mb-1">
                                    Pilih Teknisi Pendamping Hari Ini <span class="text-rose-500">*</span>
                                </label>
                                <select name="teknisi_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-2xs">
                                    <option value="">-- Pilih Teknisi Pendamping --</option>
                                    @foreach($teknisis as $t)
                                        <option value="{{ $t->id }}" {{ (old('teknisi_id', auth()->user()->teknisi_id) == $t->id) ? 'selected' : '' }}>
                                            {{ $t->nama_teknisi }} ({{ $t->spesialisasi ?? 'Teknisi' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <!-- INFORMASI TEKNISI TERKUNCI SAAT PULANG -->
                            <div class="flex items-center justify-between text-gray-700 bg-white p-2.5 rounded-xl border border-blue-100 shadow-2xs">
                                <span class="text-gray-500 font-medium">Teknisi Pendamping:</span>
                                <span class="font-bold text-blue-700">
                                    <i class="fa-solid fa-user-check mr-1 text-emerald-500"></i>
                                    {{ $absenMasuk->teknisi->nama_teknisi ?? (auth()->user()->teknisi->nama_teknisi ?? 'Teknisi Lapangan') }}
                                </span>
                            </div>
                            <p class="text-[10px] text-gray-400">
                                <i class="fa-solid fa-circle-info text-blue-500"></i> Teknisi terkunci otomatis dari presensi datang hari ini.
                            </p>
                        @endif
                    </div>
                @else
                    <!-- INFORMASI PENEMPATAN PERKANTORAN / PEMERINTAHAN -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-gray-400 block">Divisi Penempatan</span>
                            <span class="font-bold text-gray-800 text-xs">{{ auth()->user()->divisi->nama_divisi ?? 'Perkantoran' }}</span>
                        </div>
                        <span class="text-[10px] bg-gray-200 text-gray-700 font-semibold px-2 py-1 rounded-full">
                            Radius Kantor Wajib
                        </span>
                    </div>
                @endif

                <!-- TIPE ABSENSI SELECTOR -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1.5">Sesi Presensi</label>
                    <select name="tipe_absensi" class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-semibold bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @if(!$absenMasuk)
                            <option value="masuk">Absensi Masuk</option>
                        @endif
                        @if(!$absenPulang)
                            <option value="pulang">Absensi Pulang</option>
                        @endif
                    </select>
                </div>

                <!-- SUBMIT BUTTON (TOUCH-FRIENDLY HEIGHT) -->
                <button id="absenButton"
                        disabled
                        class="w-full mt-2 bg-gray-300 text-gray-500 py-3.5 sm:py-4 rounded-2xl font-black text-xs sm:text-sm shadow-sm transition-all duration-200 flex items-center justify-center gap-2 cursor-not-allowed">
                    <i class="fa-solid fa-lock"></i>
                    <span>Absensi Belum Memenuhi Syarat</span>
                </button>
            </form>

        </div>

    </div>

</div>

<script>

    /*
    |--------------------------------------------------------------------------
    | DATA KANTOR & PENEMPATAN
    |--------------------------------------------------------------------------
    */

    var officeLat = {{ $instansi->latitude }};
    var officeLng = {{ $instansi->longitude }};
    var officeRadius = {{ $instansi->radius }};
    var isLapangan = {{ $isLapangan ? 'true' : 'false' }};
    var isPulangSession = {{ ($absenMasuk && !$absenPulang) ? 'true' : 'false' }};

    /*
    |--------------------------------------------------------------------------
    | MAP & TILE LAYER INITIALIZATION
    |--------------------------------------------------------------------------
    */

    var map = L.map('map', {
        zoomControl: true,
        attributionControl: true,
        minZoom: 10,
        maxZoom: 19
    }).setView([officeLat, officeLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        minZoom: 1,
        maxZoom: 19,
        maxNativeZoom: 19,
        subdomains: ['a', 'b', 'c']
    }).addTo(map);

    /*
    |--------------------------------------------------------------------------
    | MARKER KANTOR
    |--------------------------------------------------------------------------
    */

    L.marker([officeLat, officeLng])
        .addTo(map)
        .bindPopup('<b>Titik Pusat Kantor</b><br>Radius: ' + officeRadius + 'm');

    /*
    |--------------------------------------------------------------------------
    | CIRCLE GEOFENCE (VARIABEL GLOBAL UNTUK UPDATE DINAMIS)
    |--------------------------------------------------------------------------
    */

    var geofenceCircle = L.circle([officeLat, officeLng], {
        radius: officeRadius,
        color: '#ef4444',
        fillColor: '#fee2e2',
        fillOpacity: 0.25,
        weight: 2
    }).addTo(map);

    /*
    |--------------------------------------------------------------------------
    | AUTO INVALIDATE SIZE (MEMASTIKAN TILE TER-RENDER DI CONTAINER DINAMIS)
    |--------------------------------------------------------------------------
    */
    setTimeout(function() { map.invalidateSize(); }, 200);
    setTimeout(function() { map.invalidateSize(); }, 500);
    setTimeout(function() { map.invalidateSize(); }, 1000);

    window.addEventListener('load', function() {
        map.invalidateSize();
    });

    window.addEventListener('resize', function() {
        map.invalidateSize();
    });

    /*
    |--------------------------------------------------------------------------
    | USER MARKER & REALTIME TRACKING VARIABLES
    |--------------------------------------------------------------------------
    */

    var userMarker = null;
    var currentLat = null;
    var currentLng = null;
    var currentAccuracy = null;
    var streamKamera = null;
    var gpsWatchId = null;

    var gpsOptionsHighAccuracy = {
        enableHighAccuracy: true,
        timeout: 10000,      // Batas waktu 10 detik
        maximumAge: 0        // Selalu ambil koordinat paling segar
    };

    var gpsOptionsLowAccuracy = {
        enableHighAccuracy: false, // Fallback jaringan Wi-Fi / IP jika chip GPS satelit tidak merespons
        timeout: 10000,
        maximumAge: 5000
    };

    /*
    |--------------------------------------------------------------------------
    | GPS GEOLOCATION ENGINE
    |--------------------------------------------------------------------------
    */

    function startGpsTracking() {
        if (gpsWatchId !== null && navigator.geolocation) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }

        // Tampilkan status pencarian pada UI
        document.getElementById('statusBox').innerHTML = `
            <div class="py-1">
                <h3 class="text-sm sm:text-base font-bold text-gray-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin text-blue-600"></i>
                    <span>Mencari koordinat GPS perangkat Anda...</span>
                </h3>
                <p class="text-[11px] text-gray-400 mt-1">Pastikan izin lokasi telah diizinkan pada browser.</p>
            </div>
        `;

        if (!navigator.geolocation) {
            showGpsError('Browser Anda tidak mendukung fitur Geolocation API.');
            return;
        }

        // Jalankan pelacakan GPS
        gpsWatchId = navigator.geolocation.watchPosition(
            onGpsSuccess,
            function(error) {
                // Jika High Accuracy gagal karena Timeout, coba sekali lagi dengan Low Accuracy (Wi-Fi/Network)
                if (error.code === error.TIMEOUT) {
                    console.warn('[GPS] High accuracy timeout, mencoba fallback low accuracy...');
                    navigator.geolocation.getCurrentPosition(
                        onGpsSuccess,
                        onGpsError,
                        gpsOptionsLowAccuracy
                    );
                } else {
                    onGpsError(error);
                }
            },
            gpsOptionsHighAccuracy
        );
    }

    // Callback Ketika GPS Berhasil Didapatkan
    function onGpsSuccess(position) {
        var lat = position.coords.latitude;
        // PAKSA ABSOLUT POSITIF: Wilayah Indonesia (Bujur Timur) wajib positif (+102.xxx)
        var lng = Math.abs(position.coords.longitude);
        var accuracy = position.coords.accuracy;

        currentLat = lat;
        currentLng = lng;
        currentAccuracy = accuracy;

        /*
        |--------------------------------------------------------------------------
        | ISI INPUT FORM & DETAIL KOORDINAT
        |--------------------------------------------------------------------------
        */

        document.getElementById('latitude').innerHTML = lat.toFixed(7);
        document.getElementById('longitude').innerHTML = lng.toFixed(7);
        document.getElementById('accuracy').innerHTML = accuracy ? accuracy.toFixed(0) + ' Meter' : 'Akurat';

        document.getElementById('inputLatitude').value = lat;
        document.getElementById('inputLongitude').value = lng;
        document.getElementById('inputAccuracy').value = accuracy || 0;

        /*
        |--------------------------------------------------------------------------
        | USER MARKER
        |--------------------------------------------------------------------------
        */

        if (userMarker) {
            map.removeLayer(userMarker);
        }

        userMarker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup('<b>Lokasi Anda Sekarang</b><br>Akurasi: ±' + (accuracy ? accuracy.toFixed(0) : '0') + 'm')
            .openPopup();

        setTimeout(function() {
            map.invalidateSize();
        }, 150);

        /*
        |--------------------------------------------------------------------------
        | HITUNG JARAK & UBAH WARNA RADIUS DINAMIS
        |--------------------------------------------------------------------------
        */

        var distance = map.distance(
            [lat, lng],
            [officeLat, officeLng]
        );

        document.getElementById('distance').innerHTML = distance.toFixed(1) + ' Meter';
        document.getElementById('inputJarak').value = distance.toFixed(2);

        // LOGIKA WARNA DINAMIS RADIUS (HIJAU vs MERAH)
        if (geofenceCircle) {
            if (distance <= officeRadius) {
                // DI DALAM KANTOR -> HIJAU
                geofenceCircle.setStyle({
                    color: '#10b981',
                    fillColor: '#d1fae5',
                    fillOpacity: 0.35,
                    weight: 2.5
                });
            } else {
                // DI LUAR KANTOR -> MERAH
                geofenceCircle.setStyle({
                    color: '#ef4444',
                    fillColor: '#fee2e2',
                    fillOpacity: 0.25,
                    weight: 2
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | AUTOMATIC MAP BOUNDS (FOKUS AREA LOKAL DENGAN BATAS ZOOM)
        |--------------------------------------------------------------------------
        */
        if (distance <= 3000) {
            var bounds = L.latLngBounds([
                [officeLat, officeLng],
                [lat, lng]
            ]);
            map.fitBounds(bounds, {
                padding: [45, 45],
                maxZoom: 17,
                minZoom: 13
            });
        } else {
            // Jika jarak cukup jauh, kunci fokus pada posisi user di zoom 16 tanpa zoom-out ke peta dunia
            map.setView([lat, lng], 16);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI GEOFENCE (SKENARIO A LAPANGAN vs SKENARIO B KANTOR)
        |--------------------------------------------------------------------------
        */

        var isValidLocation = (distance <= officeRadius) || (isLapangan && isPulangSession);

        if (isValidLocation) {

            var statusHtml = (isLapangan && isPulangSession && distance > officeRadius)
                ? `
                    <div class="py-1">
                        <h3 class="text-base sm:text-lg font-black text-blue-600 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-route text-blue-500"></i> LOKASI LAPANGAN (PULANG)
                        </h3>
                        <p class="mt-1 text-xs text-gray-600">
                            Presensi pulang penempatan lapangan bebas radius kantor.
                        </p>
                    </div>
                `
                : `
                    <div class="py-1">
                        <h3 class="text-base sm:text-lg font-black text-emerald-600 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i> DI DALAM AREA KANTOR
                        </h3>
                        <p class="mt-1 text-xs text-gray-600">
                            Lokasi Anda valid (\u00b1${distance.toFixed(0)}m dari titik pusat kantor).
                        </p>
                    </div>
                `;

            document.getElementById('statusBox').innerHTML = statusHtml;
            document.getElementById('absenButton').disabled = false;
            document.getElementById('absenButton').classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            document.getElementById('absenButton').classList.add('bg-blue-600', 'hover:bg-blue-700', 'text-white', 'cursor-pointer');
            document.getElementById('absenButton').innerHTML = '<i class="fa-solid fa-circle-check mr-1.5"></i> Kirim Presensi Sekarang';

        } else {

            document.getElementById('statusBox').innerHTML = `
                <div class="py-1">
                    <h3 class="text-base sm:text-lg font-black text-rose-600 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-xmark text-rose-500"></i> DI LUAR AREA KANTOR
                    </h3>
                    <p class="mt-1 text-xs text-gray-600">
                        Anda berada di luar radius kantor (${distance.toFixed(0)}m dari batas ${officeRadius}m).
                    </p>
                </div>
            `;

            document.getElementById('absenButton').disabled = true;
            document.getElementById('absenButton').classList.remove('bg-blue-600', 'hover:bg-blue-700', 'text-white', 'cursor-pointer');
            document.getElementById('absenButton').classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            document.getElementById('absenButton').innerHTML = '<i class="fa-solid fa-lock mr-1.5"></i> Di Luar Radius Kantor';

        }
    }

    // Callback Ketika GPS Mengalami Error
    function onGpsError(error) {
        var pesanError = '';
        var saran = '';

        if (error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    pesanError = 'Akses Lokasi / GPS Ditolak';
                    saran = 'Silakan klik ikon gembok/setelan di sebelah kiri URL browser Anda, pilih <b>Izinkan (Allow)</b> pada akses Lokasi, lalu coba muat ulang.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    pesanError = 'Sinyal GPS Tidak Tersedia';
                    saran = 'Perangkat tidak dapat menentukan posisi. Pastikan GPS aktif atau coba hubungkan ke koneksi internet lain.';
                    break;
                case error.TIMEOUT:
                    pesanError = 'Waktu Pencarian GPS Habis (Timeout)';
                    saran = 'Sinyal GPS membutuhkan waktu terlalu lama. Silakan klik tombol di bawah untuk mencoba kembali.';
                    break;
                default:
                    pesanError = 'Gagal Mendeteksi GPS';
                    saran = error.message || 'Terjadi kendala saat membaca sensor lokasi.';
                    break;
            }
        } else {
            pesanError = 'Gagal Membaca Lokasi';
            saran = 'Sensor lokasi perangkat tidak merespons.';
        }

        showGpsError(pesanError, saran);
    }

    // Tampilkan Peringatan Error Edukatif & Tombol Retry
    function showGpsError(judul, deskripsi) {
        document.getElementById('statusBox').innerHTML = `
            <div class="p-2 text-rose-800 text-left bg-rose-50 border border-rose-200 rounded-xl">
                <div class="flex items-center gap-2 font-bold text-xs text-rose-700">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                    <span>${judul}</span>
                </div>
                ${deskripsi ? `<p class="text-[11px] text-gray-600 mt-1 leading-relaxed">${deskripsi}</p>` : ''}
                <button type="button"
                        onclick="startGpsTracking()"
                        class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] rounded-lg transition-all shadow-2xs cursor-pointer">
                    <i class="fa-solid fa-rotate-right"></i> Coba Muat Ulang GPS
                </button>
            </div>
        `;

        document.getElementById('absenButton').disabled = true;
        document.getElementById('absenButton').classList.remove('bg-blue-600', 'hover:bg-blue-700', 'text-white', 'cursor-pointer');
        document.getElementById('absenButton').classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        document.getElementById('absenButton').innerHTML = '<i class="fa-solid fa-satellite-dish mr-1.5"></i> Menunggu Sinyal GPS';
    }

    // Inisialisasi GPS saat halaman dimuat
    startGpsTracking();

    /*
    |--------------------------------------------------------------------------
    | CAMERA
    |--------------------------------------------------------------------------
    */

    const video = document.getElementById('video');

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: 1280 }, height: { ideal: 720 }, facingMode: 'user' }
        })
        .then(function(stream) {
            streamKamera = stream;
            video.srcObject = stream;
            video.onloadedmetadata = function() {
                video.play().catch(function(e) {
                    console.warn('Autoplay video dicegah browser:', e);
                });
            };
        })
        .catch(function(error) {
            console.warn('Akses kamera gagal:', error.message);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: CONVERT DECIMAL KE DMS (DERAJAT, MENIT, DETIK)
    |--------------------------------------------------------------------------
    */
    function toDMS(decimal, isLat) {
        var abs  = Math.abs(decimal);
        var deg  = Math.floor(abs);
        var minF = (abs - deg) * 60;
        var min  = Math.floor(minF);
        var sec  = Math.round((minF - min) * 60);
        var dir  = isLat ? (decimal >= 0 ? 'N' : 'S') : (decimal >= 0 ? 'E' : 'W');
        return deg + '\u00b0' + min + "'" + sec + '" ' + dir;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: DRAW ROUNDED RECTANGLE PATH
    |--------------------------------------------------------------------------
    */
    function drawRoundedRectPath(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: DRAW MINI MAP ON CANVAS (3x3 TILE BUFFER FOR PERFECT CENTERING)
    |--------------------------------------------------------------------------
    */
    function drawMiniMap(ctx, mapX, mapY, mapW, mapH, lat, lng, zoom, callback) {
        var z = zoom || 16;
        var n = Math.pow(2, z);
        var xExact = (lng + 180) / 360 * n;
        var latRad = lat * Math.PI / 180;
        var yExact = (1 - Math.log(Math.tan(latRad) + 1 / Math.cos(latRad)) / Math.PI) / 2 * n;

        var tileX = Math.floor(xExact);
        var tileY = Math.floor(yExact);

        var fractX = xExact - tileX;
        var fractY = yExact - tileY;

        // 3x3 Tile buffer (768x768 px) - guarantees no negative offsets or clipping
        var tempCanvas = document.createElement('canvas');
        tempCanvas.width = 768;
        tempCanvas.height = 768;
        var tempCtx = tempCanvas.getContext('2d');

        // Neutral background
        tempCtx.fillStyle = '#e2e8f0';
        tempCtx.fillRect(0, 0, 768, 768);

        // Vector grid fallback lines
        tempCtx.strokeStyle = '#cbd5e1';
        tempCtx.lineWidth = 1;
        tempCtx.beginPath();
        for (var gx = 0; gx < 768; gx += 48) {
            tempCtx.moveTo(gx, 0);
            tempCtx.lineTo(gx, 768);
        }
        for (var gy = 0; gy < 768; gy += 48) {
            tempCtx.moveTo(0, gy);
            tempCtx.lineTo(768, gy);
        }
        tempCtx.stroke();

        var tiles = [
            { x: tileX - 1, y: tileY - 1, dx: 0,   dy: 0 },
            { x: tileX,     y: tileY - 1, dx: 256, dy: 0 },
            { x: tileX + 1, y: tileY - 1, dx: 512, dy: 0 },
            { x: tileX - 1, y: tileY,     dx: 0,   dy: 256 },
            { x: tileX,     y: tileY,     dx: 256, dy: 256 },
            { x: tileX + 1, y: tileY,     dx: 512, dy: 256 },
            { x: tileX - 1, y: tileY + 1, dx: 0,   dy: 512 },
            { x: tileX,     y: tileY + 1, dx: 256, dy: 512 },
            { x: tileX + 1, y: tileY + 1, dx: 512, dy: 512 }
        ];

        var loaded = 0;
        var total = tiles.length;
        var finished = false;

        function finish() {
            if (finished) return;
            finished = true;

            ctx.save();
            // Clip to mini map area
            ctx.beginPath();
            ctx.rect(mapX, mapY, mapW, mapH);
            ctx.clip();

            // Center of the 3x3 canvas where target coordinate is located
            var exactCenterX = 256 + (fractX * 256);
            var exactCenterY = 256 + (fractY * 256);

            var srcX = exactCenterX - (mapW / 2);
            var srcY = exactCenterY - (mapH / 2);

            ctx.drawImage(
                tempCanvas,
                srcX,
                srcY,
                mapW,
                mapH,
                mapX,
                mapY,
                mapW,
                mapH
            );

            // Subtle dark overlay gradient for readability
            var mapGrad = ctx.createLinearGradient(mapX, mapY, mapX, mapY + mapH);
            mapGrad.addColorStop(0, 'rgba(0, 0, 0, 0.05)');
            mapGrad.addColorStop(1, 'rgba(0, 0, 0, 0.35)');
            ctx.fillStyle = mapGrad;
            ctx.fillRect(mapX, mapY, mapW, mapH);

            // Pin marker strictly in the center of mini map
            var pinX = mapX + (mapW / 2);
            var pinY = mapY + (mapH / 2);

            // Ground shadow
            ctx.fillStyle = 'rgba(0, 0, 0, 0.35)';
            ctx.beginPath();
            ctx.ellipse(pinX, pinY + 2, 7, 3, 0, 0, Math.PI * 2);
            ctx.fill();

            // Pin body
            ctx.fillStyle = '#ef4444';
            ctx.beginPath();
            ctx.arc(pinX, pinY - 12, 9, 0, Math.PI * 2);
            ctx.fill();

            ctx.beginPath();
            ctx.moveTo(pinX - 8, pinY - 9);
            ctx.lineTo(pinX, pinY);
            ctx.lineTo(pinX + 8, pinY - 9);
            ctx.fill();

            // Pin center dot
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(pinX, pinY - 12, 3.5, 0, Math.PI * 2);
            ctx.fill();

            // Watermark on bottom left
            ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
            ctx.font = 'bold 10px Arial, sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText('© OpenStreetMap', mapX + 8, mapY + mapH - 8);

            ctx.restore();

            if (callback) callback();
        }

        tiles.forEach(function(t) {
            var img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                tempCtx.drawImage(img, t.dx, t.dy);
                loaded++;
                if (loaded >= total) finish();
            };
            img.onerror = function() {
                loaded++;
                if (loaded >= total) finish();
            };
            var sub = ['a', 'b', 'c'][Math.abs(t.x + t.y) % 3];
            img.src = 'https://' + sub + '.tile.openstreetmap.org/' + z + '/' + t.x + '/' + t.y + '.png';
        });

        setTimeout(function() {
            finish();
        }, 1200);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: DRAW GPS TABLE OVERLAY ON CANVAS
    |--------------------------------------------------------------------------
    */
    function drawGpsTable(ctx, x, y, w, h, lat, lng) {
        // Dark translucent background with sleek slate tint
        ctx.fillStyle = 'rgba(15, 23, 42, 0.88)';
        ctx.fillRect(x, y, w, h);

        var fHeader = Math.max(13, Math.min(20, Math.round(w / 28)));
        var fData   = Math.max(12, Math.min(18, Math.round(w / 30)));
        var fFooter = Math.max(11, Math.min(16, Math.round(w / 32)));
        var pad     = 14;

        var headerH = Math.round(h * 0.25);
        var latH    = Math.round(h * 0.25);
        var lngH    = Math.round(h * 0.25);
        var footH   = h - headerH - latH - lngH;

        var col1W = Math.floor(w * 0.34);
        var col2W = Math.floor(w * 0.33);
        var col3W = w - col1W - col2W;

        var col1X = x + pad;
        var col2X = x + col1W + col2W / 2;
        var col3X = x + col1W + col2W + col3W / 2;

        ctx.strokeStyle = 'rgba(255, 255, 255, 0.22)';
        ctx.lineWidth = 1;
        ctx.textBaseline = 'middle';

        // ROW 1: HEADERS
        var r1Center = y + headerH / 2;
        ctx.fillStyle = '#94a3b8';
        ctx.font = 'bold ' + fHeader + 'px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Decimal', col2X, r1Center);
        ctx.fillText('DMS', col3X, r1Center);

        ctx.beginPath();
        ctx.moveTo(x, y + headerH);
        ctx.lineTo(x + w, y + headerH);
        ctx.stroke();

        // ROW 2: LATITUDE
        var r2Center = y + headerH + latH / 2;
        ctx.textAlign = 'left';
        ctx.fillStyle = '#f8fafc';
        ctx.font = 'bold ' + fHeader + 'px Arial, sans-serif';
        ctx.fillText('Latitude', col1X, r2Center);

        ctx.textAlign = 'center';
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold ' + fData + 'px Arial, sans-serif';
        ctx.fillText(lat.toFixed(6), col2X, r2Center);
        ctx.font = fData + 'px Arial, sans-serif';
        ctx.fillText(toDMS(lat, true), col3X, r2Center);

        ctx.beginPath();
        ctx.moveTo(x, y + headerH + latH);
        ctx.lineTo(x + w, y + headerH + latH);
        ctx.stroke();

        // ROW 3: LONGITUDE
        var r3Center = y + headerH + latH + lngH / 2;
        ctx.textAlign = 'left';
        ctx.fillStyle = '#f8fafc';
        ctx.font = 'bold ' + fHeader + 'px Arial, sans-serif';
        ctx.fillText('Longitude', col1X, r3Center);

        ctx.textAlign = 'center';
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold ' + fData + 'px Arial, sans-serif';
        ctx.fillText(lng.toFixed(6), col2X, r3Center);
        ctx.font = fData + 'px Arial, sans-serif';
        ctx.fillText(toDMS(lng, false), col3X, r3Center);

        ctx.beginPath();
        ctx.moveTo(x, y + headerH + latH + lngH);
        ctx.lineTo(x + w, y + headerH + latH + lngH);
        ctx.stroke();

        // Vertical column divider lines
        ctx.beginPath();
        ctx.moveTo(x + col1W, y);
        ctx.lineTo(x + col1W, y + headerH + latH + lngH);
        ctx.moveTo(x + col1W + col2W, y);
        ctx.lineTo(x + col1W + col2W, y + headerH + latH + lngH);
        ctx.stroke();

        // TIMESTAMP FOOTER (ROW 4)
        var now = new Date();
        var yyyy = now.getFullYear();
        var mm = String(now.getMonth() + 1).padStart(2, '0');
        var dd = String(now.getDate()).padStart(2, '0');
        var hariArr = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        var hari = hariArr[now.getDay()];
        var hh = now.getHours();
        var minStr = String(now.getMinutes()).padStart(2, '0');
        var ampm = hh >= 12 ? 'PM' : 'AM';
        var hh12 = hh % 12 || 12;
        var hh12Str = String(hh12).padStart(2, '0');
        var timestamp = yyyy + '-' + mm + '-' + dd + ' (' + hari + ')  ' + hh12Str + ':' + minStr + ' ' + ampm + ' WIB';

        var r4Center = y + headerH + latH + lngH + footH / 2;
        ctx.textAlign = 'center';
        ctx.fillStyle = '#38bdf8';
        ctx.font = 'bold ' + fFooter + 'px Arial, sans-serif';
        ctx.fillText(timestamp, x + w / 2, r4Center);
    }

    /*
    |--------------------------------------------------------------------------
    | CAPTURE FOTO (DENGAN FLOATING WATERMARK CARD & CENTERED MINI MAP)
    |--------------------------------------------------------------------------
    */
    document.getElementById('capture').addEventListener('click', function() {

        const canvas = document.getElementById('canvas');
        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 720;

        const ctx = canvas.getContext('2d');

        // 1. Gambar frame video webcam
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        var lat = currentLat || officeLat;
        var lng = currentLng ? Math.abs(currentLng) : officeLng;

        // 2. Dimensi Watermark Card (Floating Card dengan Margin Aman agar tidak terpotong)
        var marginX = Math.max(16, Math.floor(canvas.width * 0.025));
        var marginB = Math.max(16, Math.floor(canvas.height * 0.035));
        var cardW = canvas.width - (marginX * 2);
        var cardH = Math.max(150, Math.floor(canvas.height * 0.28));
        var cardX = marginX;
        var cardY = canvas.height - cardH - marginB;
        var cardRadius = 16;

        var mapW = Math.max(160, Math.floor(cardW * 0.34));
        var mapH = cardH;
        var tableX = cardX + mapW;
        var tableW = cardW - mapW;
        var tableH = cardH;

        // 3. Render Card Shadow & Outer Clip
        ctx.save();
        ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
        ctx.shadowBlur = 18;
        ctx.shadowOffsetY = 6;
        drawRoundedRectPath(ctx, cardX, cardY, cardW, cardH, cardRadius);
        ctx.fillStyle = '#0f172a';
        ctx.fill();
        ctx.restore();

        // 4. Clip semua konten watermark di dalam rounded card
        ctx.save();
        drawRoundedRectPath(ctx, cardX, cardY, cardW, cardH, cardRadius);
        ctx.clip();

        // 5. Render Mini Map + GPS Table Overlay
        drawMiniMap(ctx, cardX, cardY, mapW, mapH, lat, lng, 16, function() {
            drawGpsTable(ctx, tableX, cardY, tableW, tableH, lat, lng);

            // Outline border pemanis
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.28)';
            ctx.lineWidth = 2;
            drawRoundedRectPath(ctx, cardX, cardY, cardW, cardH, cardRadius);
            ctx.stroke();

            ctx.restore();

            // Simpan hasil ke base64
            const finalImage = canvas.toDataURL('image/jpeg', 0.94);
            document.getElementById('fotoInput').value = finalImage;

            // Tampilkan preview foto pada UI
            const previewImg = document.getElementById('previewImg');
            previewImg.src = finalImage;
            previewImg.classList.remove('hidden');
            video.classList.add('hidden');

            document.getElementById('capture').classList.add('hidden');
            document.getElementById('retake').classList.remove('hidden');
            document.getElementById('photoStatus').classList.remove('hidden');
        });

    });

    /*
    |--------------------------------------------------------------------------
    | ULANGI FOTO
    |--------------------------------------------------------------------------
    */
    document.getElementById('retake').addEventListener('click', function() {
        document.getElementById('previewImg').classList.add('hidden');
        document.getElementById('previewImg').src = '';
        video.classList.remove('hidden');

        document.getElementById('capture').classList.remove('hidden');
        document.getElementById('retake').classList.add('hidden');
        document.getElementById('photoStatus').classList.add('hidden');

        document.getElementById('fotoInput').value = '';
    });

</script>

@endsection