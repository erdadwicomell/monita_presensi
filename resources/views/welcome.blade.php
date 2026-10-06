<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MONITA - Monitoring Internship Attendance</title>

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- GOOGLE FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 via-blue-50/30 to-slate-100 text-slate-800 min-h-screen flex flex-col justify-between selection:bg-blue-600 selection:text-white antialiased">

    <!-- NAVBAR (MEWAH, CERAH & TRANSPARAN ALAMI) -->
    <header class="border-b border-slate-200/80 backdrop-blur-md bg-white/90 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-12 h-20 flex items-center justify-between">
            <!-- 1. Brand Logo di Kiri (Murni Transparan, Diperbesar & Sejajar Elegan) -->
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('images/logo-monita.png') }}" 
                     alt="Logo Monita" 
                     class="w-14 h-14 sm:w-16 sm:h-16 object-contain">
                <div class="flex flex-col justify-center">
                    <span class="text-xl sm:text-2xl font-extrabold text-blue-900 tracking-wide leading-tight block">MONITA</span>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase leading-none mt-0.5">Monitoring Internship Attendance</span>
                </div>
            </div>

            <!-- 2. Menu Desktop (Tema Cerah Bergradasi & Tombol Sekunder Segar: md:flex) -->
            <div class="hidden md:flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md shadow-blue-200 transition-all duration-200 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-gauge-high"></i> Buka Dashboard
                        </a>
                    @else
                        <!-- Tombol 1: Aktivasi Akun (Soft Colored Shadow & Pastel Hover) -->
                        <a href="{{ route('otp.verify.form') }}" class="bg-white border border-blue-100 hover:border-blue-300 hover:bg-blue-50/50 text-blue-600 hover:text-blue-700 font-semibold py-2 px-4 rounded-lg shadow-sm shadow-blue-100/80 hover:shadow-md hover:shadow-blue-100 transition-all duration-200 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-key text-blue-600"></i> Aktivasi Akun
                        </a>

                        <!-- Tombol 2: Registrasi Instansi (Soft Colored Shadow & Pastel Hover) -->
                        <a href="{{ route('register.admin.form') }}" class="bg-white border border-blue-100 hover:border-blue-300 hover:bg-blue-50/50 text-blue-600 hover:text-blue-700 font-semibold py-2 px-4 rounded-lg shadow-sm shadow-blue-100/80 hover:shadow-md hover:shadow-blue-100 transition-all duration-200 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-building-user text-blue-600"></i> Registrasi Instansi
                        </a>

                        <!-- Tombol 3: Masuk (Biru Cerah Bergradasi Mewah & Bercahaya) -->
                        <a href="{{ route('login') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md shadow-blue-200 hover:shadow-lg hover:shadow-blue-300 transition-all duration-200 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
                        </a>
                    @endauth
                @endif
            </div>

            <!-- Tombol Hamburger Mobile (Layar HP: md:hidden) -->
            <button type="button" 
                    id="btnLandingMobileToggle" 
                    class="md:hidden p-2 rounded-xl text-slate-600 hover:text-blue-600 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                    aria-label="Buka Menu Navigasi">
                <i class="fa-solid fa-bars text-xl" id="iconLandingHamburger"></i>
            </button>
        </div>

        <!-- Panel Menu Dropdown Mobile (Layar HP) -->
        <div id="landingMobileMenu" class="hidden md:hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-md px-4 py-3.5 transition-all">
            @if (Route::has('login'))
                @auth
                    <div class="flex flex-col items-center justify-center gap-2.5 w-full">
                        <a href="{{ url('/dashboard') }}" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md shadow-blue-200 transition-all text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-gauge-high"></i> Buka Dashboard
                        </a>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center gap-2.5 w-full">
                        <a href="{{ route('otp.verify.form') }}" class="w-full bg-white border border-blue-100 hover:border-blue-300 hover:bg-blue-50/50 text-blue-600 hover:text-blue-700 font-semibold py-2.5 px-4 rounded-lg shadow-sm shadow-blue-100/80 transition-all duration-200 text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-key text-blue-600"></i> Aktivasi Akun
                        </a>
                        <a href="{{ route('register.admin.form') }}" class="w-full bg-white border border-blue-100 hover:border-blue-300 hover:bg-blue-50/50 text-blue-600 hover:text-blue-700 font-semibold py-2.5 px-4 rounded-lg shadow-sm shadow-blue-100/80 transition-all duration-200 text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-building-user text-blue-600"></i> Registrasi Instansi
                        </a>
                        <a href="{{ route('login') }}" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md shadow-blue-200 transition-all text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
                        </a>
                    </div>
                @endauth
            @endif
        </div>
    </header>

    <!-- HERO SECTION (BEBAS TABRAKAN DENGAN JARAK ATAS pt-24 sm:pt-28 lg:pt-32) -->
    <main class="flex-1 flex items-center justify-center pt-24 pb-12 sm:pt-28 sm:pb-16 lg:pt-32 lg:pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Soft Ambient Background Glows -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10 space-y-6 sm:space-y-8">
            <!-- Center Logo Showcase: w-28 di HP -> w-44 di Laptop -->
            <div class="inline-block transform hover:scale-105 transition duration-300">
                <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" class="w-28 sm:w-36 md:w-44 h-auto object-contain mx-auto drop-shadow-md">
            </div>

            <div class="space-y-2 sm:space-y-4">
                <!-- Clean Dark Heading with Vibrant Gradient Accent: text-xl di HP -> text-4xl di Laptop -->
                <h1 class="text-xl sm:text-2xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-snug sm:leading-tight max-w-2xl mx-auto text-center mt-3 sm:mt-4">
                    Kelola Kehadiran dan Aktivitas Magang <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-600 bg-clip-text text-transparent">
                        Lebih Akurat & Terintegrasi
                    </span>
                </h1>

                <!-- Readable Soft Slate Paragraph -->
                <p class="text-slate-600 text-xs sm:text-sm md:text-base max-w-2xl mx-auto text-center leading-relaxed font-normal pt-1">
                    Memudahkan peserta magang melakukan absensi masuk dan pulang sesuai lokasi, mencatat laporan kegiatan harian secara praktis, serta mempercepat proses pemantauan oleh pembimbing lapangan.
                </p>
            </div>

            <!-- Feature Cards: Rata Tengah Elegan di Layar HP -> Rata Kiri di Layar Laptop -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 pt-6 sm:pt-8">
                <!-- 1. PRESENSI BERBASIS LOKASI -->
                <div class="py-6 px-3 sm:p-5 rounded-2xl bg-white/90 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-200 transition duration-200 text-center lg:text-left flex flex-col items-center lg:items-start justify-center lg:justify-between">
                    <div class="w-9 h-9 sm:w-8 sm:h-8 rounded-xl bg-blue-50 flex items-center justify-center mb-2.5">
                        <i class="fa-solid fa-map-location-dot text-blue-600 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-800 leading-tight">Presensi Berbasis Lokasi</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-1.5 leading-relaxed">Absensi masuk & pulang akurat sesuai wilayah instansi.</p>
                </div>

                <!-- 2. RIWAYAT & REKAP ABSENSI -->
                <div class="py-6 px-3 sm:p-5 rounded-2xl bg-white/90 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-200 transition duration-200 text-center lg:text-left flex flex-col items-center lg:items-start justify-center lg:justify-between">
                    <div class="w-9 h-9 sm:w-8 sm:h-8 rounded-xl bg-emerald-50 flex items-center justify-center mb-2.5">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-600 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-800 leading-tight">Riwayat & Rekap Absensi</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-1.5 leading-relaxed">Pantau total kehadiran dan keterlambatan secara transparan.</p>
                </div>

                <!-- 3. PENCATATAN LAPORAN KEGIATAN -->
                <div class="py-6 px-3 sm:p-5 rounded-2xl bg-white/90 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-purple-200 transition duration-200 text-center lg:text-left flex flex-col items-center lg:items-start justify-center lg:justify-between">
                    <div class="w-9 h-9 sm:w-8 sm:h-8 rounded-xl bg-purple-50 flex items-center justify-center mb-2.5">
                        <i class="fa-solid fa-clipboard-list text-purple-600 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-800 leading-tight">Pencatatan Laporan Kegiatan</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-1.5 leading-relaxed">Input logbook aktivitas harian magang dengan praktis.</p>
                </div>

                <!-- 4. PENGAJUAN IZIN DIGITAL -->
                <div class="py-6 px-3 sm:p-5 rounded-2xl bg-white/90 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-200 transition duration-200 text-center lg:text-left flex flex-col items-center lg:items-start justify-center lg:justify-between">
                    <div class="w-9 h-9 sm:w-8 sm:h-8 rounded-xl bg-amber-50 flex items-center justify-center mb-2.5">
                        <i class="fa-solid fa-envelope-open-text text-amber-600 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-800 leading-tight">Pengajuan Izin Digital</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-1.5 leading-relaxed">Kirim permohonan izin atau sakit langsung dari sistem.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER (SOFT LIGHT) -->
    <footer class="border-t border-slate-200 py-6 text-center text-xs text-slate-500 bg-white/50 px-4">
        <p>&copy; {{ date('Y') }} MONITA - Monitoring Internship Attendance. Hak Cipta Dilindungi.</p>
    </footer>

    <!-- SCRIPT HAMBURGER MOBILE -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnLandingMobileToggle');
        const menu = document.getElementById('landingMobileMenu');
        const icon = document.getElementById('iconLandingHamburger');

        if (btn && menu) {
            btn.addEventListener('click', function() {
                menu.classList.toggle('hidden');
                if (icon) {
                    icon.classList.toggle('fa-bars');
                    icon.classList.toggle('fa-xmark');
                }
            });
        }
    });
    </script>

</body>
</html>
