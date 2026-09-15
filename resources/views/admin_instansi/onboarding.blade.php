<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendataan Instansi - MONITA</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 via-blue-50/30 to-slate-100 text-slate-800 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-blue-600 selection:text-white">

    <!-- Ambient Soft Glow Background -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <!-- MAIN FORM CARD (SOFT LIGHT MODE) -->
    <div class="w-full max-w-2xl bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/60 relative z-10 my-8">

        <!-- HEADER -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" class="h-12 sm:h-14 w-auto mx-auto object-contain mb-3 sm:mb-4">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Pendataan Profil & Lokasi Instansi
            </h1>
            <p class="text-xs text-slate-500 mt-1.5 max-w-lg mx-auto leading-relaxed">
                Halo, <strong class="text-slate-700">{{ auth()->user()->name }}</strong>! Silakan daftarkan rincian instansi dan koordinat GPS Geofencing untuk mulai mengelola kehadiran magang.
            </p>
        </div>

        <!-- ERROR ALERT -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                @foreach ($errors->all() as $err)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-500"></i> {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.onboarding.store') }}" class="space-y-5">
            @csrf

            <!-- NAMA INSTANSI & JENIS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Instansi / Kantor <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama_instansi"
                           value="{{ old('nama_instansi') }}"
                           required
                           placeholder="Contoh: PT Teknologi Bangsa Indonesia"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jenis Instansi <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_instansi"
                            required
                            class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="kantor" {{ old('jenis_instansi') == 'kantor' ? 'selected' : '' }}>Perkantoran / Swasta</option>
                        <option value="pemerintahan" {{ old('jenis_instansi') == 'pemerintahan' ? 'selected' : '' }}>Instansi Pemerintahan / BUMN</option>
                        <option value="lapangan" {{ old('jenis_instansi') == 'lapangan' ? 'selected' : '' }}>Instansi Lapangan / Teknisi</option>
                    </select>
                </div>
            </div>

            <!-- ALAMAT LENGKAP -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alamat Lengkap Instansi <span class="text-rose-500">*</span>
                </label>
                <textarea name="alamat"
                          rows="2"
                          required
                          placeholder="Jalan, nomor, kelurahan, kecamatan, kota/kabupaten..."
                          class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">{{ old('alamat') }}</textarea>
            </div>

            <!-- KOORDINAT GPS & RADIUS -->
            <div class="p-4 sm:p-5 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-crosshairs text-blue-600"></i>
                            Koordinat GPS & Radius Presensi
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Titik pusat kantor yang menjadi acuan validasi jarak absensi</p>
                    </div>
                    <button type="button"
                            id="btnGetCurrentLoc"
                            class="bg-white hover:bg-blue-50 text-blue-600 border border-blue-200 text-[11px] font-bold px-3 py-1.5 rounded-xl transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer w-fit">
                        <i class="fa-solid fa-crosshairs"></i> Gunakan Lokasi Saat Ini
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Latitude</label>
                        <input type="text"
                               id="latitude"
                               name="latitude"
                               value="{{ old('latitude', '-6.2000000') }}"
                               required
                               placeholder="-6.2000000"
                               class="w-full bg-white border border-slate-200 text-slate-800 text-xs font-mono rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Longitude</label>
                        <input type="text"
                               id="longitude"
                               name="longitude"
                               value="{{ old('longitude', '106.8166667') }}"
                               required
                               placeholder="106.8166667"
                               class="w-full bg-white border border-slate-200 text-slate-800 text-xs font-mono rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Radius (Meter)</label>
                        <input type="number"
                               name="radius"
                               value="{{ old('radius', '100') }}"
                               required
                               min="10"
                               max="5000"
                               placeholder="100"
                               class="w-full bg-white border border-slate-200 text-slate-800 text-xs font-mono rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- JAM OPERASIONAL PRESENSI -->
            <div class="p-4 sm:p-5 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3">
                <h3 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock text-amber-500"></i>
                    Aturan Jam Kerja & Presensi
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Mulai Masuk</label>
                        <input type="time"
                               name="jam_masuk_mulai"
                               value="{{ old('jam_masuk_mulai', '07:00') }}"
                               required
                               class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl px-2.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Batas Tepat Waktu</label>
                        <input type="time"
                               name="jam_masuk_batas"
                               value="{{ old('jam_masuk_batas', '08:00') }}"
                               required
                               class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl px-2.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Mulai Pulang</label>
                        <input type="time"
                               name="jam_pulang_mulai"
                               value="{{ old('jam_pulang_mulai', '16:00') }}"
                               required
                               class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl px-2.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Batas Pulang</label>
                        <input type="time"
                               name="jam_pulang_batas"
                               value="{{ old('jam_pulang_batas', '18:00') }}"
                               required
                               class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl px-2.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- BUTTON SUBMIT -->
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs py-3.5 rounded-xl shadow-md shadow-blue-600/20 transition transform hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-check"></i>
                <span>Simpan Data Instansi & Masuk ke Dashboard</span>
            </button>
        </form>

    </div>

    <!-- GEOLOCATION SCRIPT -->
    <script>
    document.getElementById('btnGetCurrentLoc').addEventListener('click', function() {
        if (navigator.geolocation) {
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mendeteksi...';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    document.getElementById('latitude').value = pos.coords.latitude.toFixed(7);
                    document.getElementById('longitude').value = pos.coords.longitude.toFixed(7);
                    this.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i> Lokasi Terpasang';
                },
                (err) => {
                    alert('Gagal mengambil koordinat: ' + err.message);
                    this.innerHTML = '<i class="fa-solid fa-crosshairs"></i> Gunakan Lokasi Saat Ini';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            alert('Browser tidak mendukung Geolocation.');
        }
    });
    </script>

</body>
</html>
