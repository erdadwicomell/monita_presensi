@extends('layouts.app')

@section('page-title', 'Laporan Kegiatan')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- FORM -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm p-8">

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Laporan Kegiatan</h1>
                <p class="text-gray-500 mt-2">Isi laporan kegiatan harian magang</p>
            </div>

            <form action="{{ route('laporan.store') }}" method="POST">
                @csrf

                <!-- JUDUL -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Kegiatan</label>
                    <input type="text" name="judul" required
                           value="{{ old('judul') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           placeholder="Contoh: Instalasi Jaringan">
                </div>

                <!-- DESKRIPSI -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Kegiatan</label>
                    <textarea name="deskripsi" rows="6" required
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              placeholder="Jelaskan kegiatan yang dilakukan hari ini...">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- LOKASI (hidden, diisi otomatis GPS) -->
                <input type="hidden" name="latitude"  id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <!-- FOTO -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Dokumentasi Realtime</label>

                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center">

                        <video id="video" autoplay playsinline
                               class="w-full rounded-2xl shadow mb-4 hidden"></video>

                        <canvas id="canvas" class="hidden"></canvas>

                        <img id="preview" class="hidden rounded-2xl shadow w-full mb-4"/>

                        <input type="hidden" name="foto" id="foto">

                        <!-- Placeholder sebelum kamera dibuka -->
                        <div id="kamera-placeholder" class="py-6 text-gray-400">
                            <svg class="mx-auto mb-3 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-sm">Klik tombol di bawah untuk membuka kamera</p>
                        </div>

                        <!-- Status foto berhasil -->
                        <div id="foto-status" class="hidden mb-3">
                            <span class="bg-green-100 text-green-700 text-sm px-3 py-1 rounded-full font-medium">
                                ✓ Foto berhasil diambil
                            </span>
                        </div>

                        <!-- Tombol kamera -->
                        <div class="flex gap-3 justify-center mt-2">
                            <button type="button" id="btn-buka-kamera"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm transition">
                                <i class="fa-solid fa-camera mr-2"></i>Buka Kamera
                            </button>
                            <button type="button" id="btn-ambil-foto"
                                    class="hidden bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-xl text-sm transition">
                                <i class="fa-solid fa-camera-rotate mr-2"></i>Ambil Foto
                            </button>
                            <button type="button" id="btn-ulangi-foto"
                                    class="hidden bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-xl text-sm transition">
                                <i class="fa-solid fa-rotate-right mr-2"></i>Ulangi
                            </button>
                        </div>

                    </div>
                </div>

                <!-- TOMBOL SUBMIT -->
                <button type="submit" id="btn-submit" disabled
                        class="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl font-medium transition">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Simpan Laporan
                </button>
                <p class="text-sm text-gray-400 mt-2" id="submit-hint">
                    GPS dan foto harus siap sebelum menyimpan.
                </p>

            </form>
        </div>
    </div>

    <!-- SIDE INFO -->
    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Informasi Realtime</h2>
            <div class="space-y-4 text-sm">

                <div class="bg-blue-50 p-4 rounded-xl">
                    <p class="text-gray-500 mb-1">Tanggal</p>
                    <h3 id="tanggal" class="font-semibold text-gray-800"></h3>
                </div>

                <div class="bg-green-50 p-4 rounded-xl">
                    <p class="text-gray-500 mb-1">Jam</p>
                    <h3 id="jam" class="font-semibold text-gray-800"></h3>
                </div>

                <div class="bg-yellow-50 p-4 rounded-xl">
                    <p class="text-gray-500 mb-1">GPS Status</p>
                    <h3 id="gps-status" class="font-semibold text-yellow-700">Mendeteksi lokasi...</h3>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl" id="lokasi-nama-box" style="display:none;">
                    <p class="text-gray-500 mb-1">Lokasi</p>
                    <h3 id="lokasi-nama" class="font-semibold text-gray-700 text-xs leading-relaxed"></h3>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- RIWAYAT LAPORAN SAYA -->
<div class="mt-8 bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-base sm:text-lg font-black text-slate-800">
                Riwayat Laporan Kegiatan Saya
            </h2>
            <p class="text-slate-500 text-xs mt-0.5">
                Daftar laporan harian dan status verifikasi pembimbing instansi.
            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Tanggal & Jam</th>
                    <th class="px-6 py-4">Kegiatan Harian</th>
                    <th class="px-6 py-4">Dokumentasi</th>
                    <th class="px-6 py-4">Status Verifikasi</th>
                    <th class="px-6 py-4 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($laporans as $lap)
                    <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                        <td class="px-6 py-4 text-center text-slate-400 font-bold">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-800 text-sm">{{ $lap->tanggal->format('d M Y') }}</p>
                            <p class="text-slate-400 text-[11px] font-mono mt-0.5">{{ substr($lap->jam, 0, 5) }} WIB</p>
                        </td>
                        <td class="px-6 py-4 max-w-md">
                            <p class="text-slate-700 line-clamp-2 leading-relaxed text-xs">
                                {{ $lap->kegiatan }}
                            </p>
                            @if($lap->status === 'revisi' && $lap->catatan_revisi)
                                <div class="mt-2 p-2 bg-amber-50 border border-amber-200/60 rounded-xl text-xs text-amber-800">
                                    <strong><i class="fa-solid fa-triangle-exclamation mr-1"></i>Catatan Revisi:</strong> {{ $lap->catatan_revisi }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($lap->foto)
                                <a href="{{ asset($lap->foto) }}" target="_blank" class="inline-flex items-center gap-2 text-xs text-blue-600 font-semibold hover:underline">
                                    <img src="{{ asset($lap->foto) }}" class="w-10 h-10 object-cover rounded-xl border border-slate-200 shadow-xs">
                                    <span>Lihat Foto</span>
                                </a>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($lap->status === 'menunggu')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                    <i class="fa-solid fa-hourglass-half text-[10px]"></i> Menunggu
                                </span>
                            @elseif($lap->status === 'disetujui')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                    <i class="fa-solid fa-pen-ruler text-[10px]"></i> Perlu Revisi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('laporan.show', $lap->id) }}"
                                   class="text-blue-600 hover:text-blue-800 p-2 rounded-xl hover:bg-blue-50 transition"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>

                                @if($lap->status === 'revisi' || $lap->status === 'menunggu')
                                    <a href="{{ route('laporan.edit', $lap->id) }}"
                                       class="text-amber-600 hover:text-amber-800 p-2 rounded-xl hover:bg-amber-50 transition"
                                       title="Perbaiki / Edit Laporan">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-400">
                            <i class="fa-solid fa-file-circle-xmark text-4xl mb-3 block text-slate-300"></i>
                            <span class="font-medium text-slate-500">Belum ada laporan kegiatan yang dikirimkan.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>

// ================================================================
// VARIABEL GLOBAL
// ================================================================
var gpsValid        = false;
var fotoValid       = false;
var streamKamera    = null;
var currentLat      = null;
var currentLong     = null;

// ================================================================
// JAM & TANGGAL REALTIME
// ================================================================
function updateJam() {
    var now      = new Date();
    var hariArr  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var bulanArr = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('tanggal').textContent =
        hariArr[now.getDay()] + ', ' + now.getDate() + ' ' +
        bulanArr[now.getMonth()] + ' ' + now.getFullYear();
    document.getElementById('jam').textContent =
        String(now.getHours()).padStart(2,'0') + ':' +
        String(now.getMinutes()).padStart(2,'0') + ':' +
        String(now.getSeconds()).padStart(2,'0');
}
updateJam();
setInterval(updateJam, 1000);

// ================================================================
// STEP 1 — GPS (sama persis dengan absensi/index.blade.php)
// ================================================================
window.addEventListener('load', function() {

    if (!navigator.geolocation) {
        document.getElementById('gps-status').textContent = 'Browser tidak mendukung GPS';
        document.getElementById('gps-status').className  = 'font-semibold text-red-600';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            currentLat  = position.coords.latitude;
            currentLong = position.coords.longitude;

            document.getElementById('latitude').value  = currentLat;
            document.getElementById('longitude').value = currentLong;

            document.getElementById('gps-status').textContent = 'Lokasi terdeteksi ✓';
            document.getElementById('gps-status').className   = 'font-semibold text-green-700';

            gpsValid = true;
            cekTombolSubmit();

            // Reverse geocoding pakai Nominatim (gratis, sama dengan absensi)
            ambilNamaLokasi(currentLat, currentLong);
        },
        function(err) {
            var pesan = 'GPS tidak dapat diakses.';
            if (err.code === 1) pesan = 'Izin GPS ditolak. Aktifkan izin lokasi di browser.';
            if (err.code === 2) pesan = 'Lokasi tidak tersedia. Coba lagi.';
            if (err.code === 3) pesan = 'Permintaan GPS timeout. Coba lagi.';
            document.getElementById('gps-status').textContent = pesan;
            document.getElementById('gps-status').className   = 'font-semibold text-red-600';
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );

});

// ================================================================
// STEP 2 — NAMA LOKASI (Nominatim, gratis tanpa API key)
// ================================================================
function ambilNamaLokasi(lat, lng) {
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data && data.display_name) {
                var nama = data.display_name.substring(0, 150);
                document.getElementById('lokasi-nama').textContent = nama;
                document.getElementById('lokasi-nama-box').style.display = 'block';
            }
        })
        .catch(function() {}); // gagal tidak masalah
}

// ================================================================
// STEP 3 — KAMERA
// ================================================================
document.getElementById('btn-buka-kamera').addEventListener('click', function() {
    navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'environment', width: 1280, height: 720 },
        audio: false
    })
    .then(function(stream) {
        streamKamera = stream;
        var video = document.getElementById('video');
        video.srcObject = stream;
        video.classList.remove('hidden');

        document.getElementById('kamera-placeholder').classList.add('hidden');
        document.getElementById('btn-buka-kamera').classList.add('hidden');
        document.getElementById('btn-ambil-foto').classList.remove('hidden');
    })
    .catch(function(err) {
        alert('Kamera tidak dapat diakses: ' + err.message);
    });
});

document.getElementById('btn-ambil-foto').addEventListener('click', function() {

    var video   = document.getElementById('video');
    var canvas  = document.getElementById('canvas');
    var ctx     = canvas.getContext('2d');

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;

    // Gambar frame video
    ctx.drawImage(video, 0, 0);

    // Tambahkan overlay Mini Map & GPS ke foto
    var lat = currentLat || -6.200000;
    var lng = currentLong || 106.816666;

    var overlayH = Math.max(150, Math.floor(canvas.height * 0.28));
    var overlayY = canvas.height - overlayH;
    var mapW = Math.max(140, Math.floor(canvas.width * 0.35));
    var tableX = mapW;
    var tableW = canvas.width - mapW;

    drawMiniMap(ctx, 0, overlayY, mapW, overlayH, lat, lng, 16, function() {
        drawGpsTable(ctx, tableX, overlayY, tableW, overlayH, lat, lng);

        // Simpan ke base64
        var base64 = canvas.toDataURL('image/jpeg', 0.92);
        document.getElementById('foto').value = base64;

        // Preview
        var preview = document.getElementById('preview');
        preview.src = base64;
        preview.classList.remove('hidden');
        video.classList.add('hidden');

        // Stop kamera
        if (streamKamera) {
            streamKamera.getTracks().forEach(function(t) { t.stop(); });
        }

        document.getElementById('btn-ambil-foto').classList.add('hidden');
        document.getElementById('btn-ulangi-foto').classList.remove('hidden');
        document.getElementById('foto-status').classList.remove('hidden');

        fotoValid = true;
        cekTombolSubmit();
    });
});

document.getElementById('btn-ulangi-foto').addEventListener('click', function() {
    document.getElementById('preview').classList.add('hidden');
    document.getElementById('preview').src = '';
    document.getElementById('btn-ulangi-foto').classList.add('hidden');
    document.getElementById('foto-status').classList.add('hidden');
    document.getElementById('kamera-placeholder').classList.remove('hidden');
    document.getElementById('btn-buka-kamera').classList.remove('hidden');
    document.getElementById('foto').value = '';
    fotoValid = false;
    cekTombolSubmit();
});

// ================================================================
// STEP 4 — OVERLAY MINI MAP & GPS DI FOTO
// ================================================================
function toDMS(decimal, isLat) {
    var abs  = Math.abs(decimal);
    var deg  = Math.floor(abs);
    var minF = (abs - deg) * 60;
    var min  = Math.floor(minF);
    var sec  = Math.round((minF - min) * 60);
    var dir  = isLat ? (decimal >= 0 ? 'N' : 'S') : (decimal >= 0 ? 'E' : 'W');
    return deg + '\u00b0' + min + "'" + sec + '" ' + dir;
}

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

    var tempCanvas = document.createElement('canvas');
    tempCanvas.width = 512;
    tempCanvas.height = 512;
    var tempCtx = tempCanvas.getContext('2d');

    tempCtx.fillStyle = '#e8ecef';
    tempCtx.fillRect(0, 0, 512, 512);

    tempCtx.strokeStyle = '#cbd5e1';
    tempCtx.lineWidth = 1.5;
    tempCtx.beginPath();
    for (var gx = 0; gx < 512; gx += 40) {
        tempCtx.moveTo(gx, 0);
        tempCtx.lineTo(gx, 512);
    }
    for (var gy = 0; gy < 512; gy += 40) {
        tempCtx.moveTo(0, gy);
        tempCtx.lineTo(512, gy);
    }
    tempCtx.stroke();

    var tiles = [
        { x: tileX, y: tileY, dx: 0, dy: 0 },
        { x: tileX + 1, y: tileY, dx: 256, dy: 0 },
        { x: tileX, y: tileY + 1, dx: 0, dy: 256 },
        { x: tileX + 1, y: tileY + 1, dx: 256, dy: 256 }
    ];

    var loaded = 0;
    var total = tiles.length;
    var finished = false;

    function finish() {
        if (finished) return;
        finished = true;

        ctx.save();
        ctx.beginPath();
        ctx.rect(mapX, mapY, mapW, mapH);
        ctx.clip();

        var centerTileX = fractX * 256;
        var centerTileY = fractY * 256;

        ctx.drawImage(
            tempCanvas,
            centerTileX - mapW / 2,
            centerTileY - mapH / 2,
            mapW,
            mapH,
            mapX,
            mapY,
            mapW,
            mapH
        );

        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.moveTo(mapX + mapW, mapY);
        ctx.lineTo(mapX + mapW, mapY + mapH);
        ctx.stroke();

        var pinX = mapX + mapW / 2;
        var pinY = mapY + mapH / 2;

        ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
        ctx.beginPath();
        ctx.ellipse(pinX, pinY + 1, 6, 2.5, 0, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = '#ea4335';
        ctx.beginPath();
        ctx.arc(pinX, pinY - 11, 8, 0, Math.PI * 2);
        ctx.fill();

        ctx.beginPath();
        ctx.moveTo(pinX - 7, pinY - 8);
        ctx.lineTo(pinX, pinY);
        ctx.lineTo(pinX + 7, pinY - 8);
        ctx.fill();

        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.arc(pinX, pinY - 11, 3, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = 'rgba(0, 0, 0, 0.65)';
        ctx.font = 'bold 9px Arial, sans-serif';
        ctx.fillText('OpenStreetMap', mapX + 6, mapY + mapH - 6);

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
        img.src = 'https://tile.openstreetmap.org/' + z + '/' + t.x + '/' + t.y + '.png';
    });

    setTimeout(function() {
        finish();
    }, 1000);
}

function drawGpsTable(ctx, x, y, w, h, lat, lng) {
    ctx.fillStyle = 'rgba(0, 0, 0, 0.68)';
    ctx.fillRect(x, y, w, h);

    var fHeader = Math.max(14, Math.min(22, Math.round(w / 26)));
    var fData   = Math.max(13, Math.min(20, Math.round(w / 28)));
    var fFooter = Math.max(12, Math.min(18, Math.round(w / 30)));
    var pad     = 12;

    var headerH = Math.round(h * 0.26);
    var latH    = Math.round(h * 0.26);
    var lngH    = Math.round(h * 0.26);
    var footH   = h - headerH - latH - lngH;

    var col1W = Math.floor(w * 0.35);
    var col2W = Math.floor(w * 0.32);
    var col3W = w - col1W - col2W;

    var col1X = x + pad;
    var col2X = x + col1W + col2W / 2;
    var col3X = x + col1W + col2W + col3W / 2;

    ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
    ctx.lineWidth = 1.5;
    ctx.textBaseline = 'middle';

    // ROW 1: HEADERS
    var r1Center = y + headerH / 2;
    ctx.fillStyle = '#ffffff';
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
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold ' + fHeader + 'px Arial, sans-serif';
    ctx.fillText('Latitude', col1X, r2Center);

    ctx.textAlign = 'center';
    ctx.font = fData + 'px Arial, sans-serif';
    ctx.fillText(lat.toFixed(6), col2X, r2Center);
    ctx.fillText(toDMS(lat, true), col3X, r2Center);

    ctx.beginPath();
    ctx.moveTo(x, y + headerH + latH);
    ctx.lineTo(x + w, y + headerH + latH);
    ctx.stroke();

    // ROW 3: LONGITUDE
    var r3Center = y + headerH + latH + lngH / 2;
    ctx.textAlign = 'left';
    ctx.font = 'bold ' + fHeader + 'px Arial, sans-serif';
    ctx.fillText('Longitude', col1X, r3Center);

    ctx.textAlign = 'center';
    ctx.font = fData + 'px Arial, sans-serif';
    ctx.fillText(lng.toFixed(6), col2X, r3Center);
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
    var timestamp = yyyy + '-' + mm + '-' + dd + '(' + hari + ')  ' + hh12Str + ':' + minStr + '(' + ampm + ')';

    var r4Center = y + headerH + latH + lngH + footH / 2;
    ctx.textAlign = 'center';
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold ' + fFooter + 'px Arial, sans-serif';
    ctx.fillText(timestamp, x + w / 2, r4Center);
}

// ================================================================
// CEK TOMBOL SUBMIT
// ================================================================
function cekTombolSubmit() {
    var btn  = document.getElementById('btn-submit');
    var hint = document.getElementById('submit-hint');
    if (gpsValid && fotoValid) {
        btn.disabled = false;
        hint.textContent = 'Klik tombol di atas untuk menyimpan laporan.';
    } else {
        btn.disabled = true;
        if (!gpsValid && !fotoValid) {
            hint.textContent = 'Tunggu GPS terdeteksi dan ambil foto terlebih dahulu.';
        } else if (!gpsValid) {
            hint.textContent = 'Tunggu GPS terdeteksi.';
        } else {
            hint.textContent = 'Ambil foto terlebih dahulu.';
        }
    }
}

</script>

@endsection