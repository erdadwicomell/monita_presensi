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

    <!-- NAVBAR (SOFT LIGHT & RESPONSIVE) -->
    <header class="border-b border-slate-200/80 backdrop-blur-md bg-white/80 sticky top-0 z-50 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4 flex items-center justify-between">
            <!-- Brand Logo & Title -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" class="w-8 sm:w-10 h-8 sm:h-10 object-contain">
                <div>
                    <span class="text-lg sm:text-xl font-black text-slate-900 tracking-wide leading-none block">MONITA</span>
                    <span class="text-[9px] sm:text-[10px] text-blue-600 font-bold tracking-wider uppercase">Monitoring Internship Attendance</span>
                </div>
            </div>

            <!-- Menu Desktop (Layar Laptop/PC: md:flex) -->
            <div class="hidden md:flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-gauge-high"></i> Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('otp.verify.form') }}" class="text-slate-600 hover:text-blue-600 text-xs font-semibold px-3 py-2 transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-key text-blue-600"></i> Aktivasi Akun
                        </a>
                        <a href="{{ route('register.admin.form') }}" class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-building-user"></i> Daftar Admin
                        </a>
                        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm hover:shadow-md flex items-center gap-1.5">
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
        <div id="landingMobileMenu" class="hidden md:hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-md px-4 py-3 space-y-2 transition-all">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-gauge-high"></i> Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('otp.verify.form') }}" class="w-full text-slate-700 hover:bg-blue-50 hover:text-blue-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                        <i class="fa-solid fa-key text-blue-600 w-4 text-center"></i> Aktivasi Akun
                    </a>
                    <a href="{{ route('register.admin.form') }}" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-building-user"></i> Daftar Admin
                    </a>
                    <a href="{{ route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
                    </a>
                @endauth
            @endif
        </div>
    </header>

    <!-- HERO SECTION (CERAH, MINIMALIS, PROPORSI RESPOCIF HP-LAPTOP) -->
    <main class="flex-1 flex items-center justify-center py-10 sm:py-14 lg:py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
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
