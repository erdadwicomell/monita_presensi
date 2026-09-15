<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.06);
        }

        .nav-active {
            background: rgba(255,255,255,0.08);
            border-right: 3px solid #60a5fa;
            color: white;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen text-gray-800 antialiased overflow-x-hidden">

@php
    $user = auth()->user();
    $instansi = $user->instansi ?? null;
@endphp

<!-- BACKDROP OVERLAY UNTUK MOBILE DRAWER -->
<div id="sidebarBackdrop"
     class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-[90] lg:hidden hidden transition-opacity duration-300">
</div>

<div class="flex min-h-screen w-full relative">

    <!-- SIDEBAR (RESPONSIVE DRAWER: HIDDEN DI MOBILE, SLIDE-IN KETIKA DIBUKA) -->
    <aside id="mainSidebar"
           class="w-64 h-screen bg-[#1e293b] text-gray-300 fixed top-0 left-0 flex flex-col z-[100] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">

        <!-- LOGO & CLOSE BUTTON -->
        <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-monita.png') }}?v={{ time() }}"
                     alt="Logo Monita"
                     class="h-10 w-auto object-contain flex-shrink-0">
                <div>
                    <h1 class="text-base font-extrabold text-white tracking-wider leading-tight">
                        MONITA
                    </h1>
                    <p class="text-[9px] text-gray-400 uppercase tracking-widest font-semibold">
                        Monitoring Magang
                    </p>
                </div>
            </div>

            <!-- TOMBOL TUTUP KHUSUS MOBILE -->
            <button type="button"
                    id="btnSidebarClose"
                    class="lg:hidden text-gray-400 hover:text-white p-1.5 rounded-xl hover:bg-white/10 transition focus:outline-none cursor-pointer"
                    aria-label="Tutup Menu">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- MENU ITEMS (SCROLLABLE) -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-3 text-xs sm:text-sm">

            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}"
               class="nav-link flex items-center gap-3 px-5 py-3 transition {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                <i class="fa-solid fa-house text-sm w-4 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- SUPER ADMIN MENU -->
            @if($user->role == 'super_admin')
                <div class="px-5 mt-5 mb-2 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                    Super Admin
                </div>

                <a href="{{ route('instansi.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('instansi.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-building text-sm w-4 text-center"></i>
                    <span class="font-medium">Data Instansi</span>
                </a>

                <a href="{{ route('admin-instansi.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('admin-instansi.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-user-tie text-sm w-4 text-center"></i>
                    <span class="font-medium">Admin Instansi</span>
                </a>

                <a href="{{ route('users.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('users.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-users text-sm w-4 text-center"></i>
                    <span class="font-medium">Data User</span>
                </a>

                <a href="{{ route('admin.audit.log') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('admin.audit.log') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-shield-halved text-sm w-4 text-center"></i>
                    <span class="font-medium">Audit Log GPS</span>
                </a>
            @endif

            <!-- ADMIN INSTANSI MENU -->
            @if($user->role == 'admin_instansi')
                <div class="px-5 mt-5 mb-2 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                    Admin Instansi
                </div>

                @if($instansi && in_array($instansi->jenis_instansi, ['kantor', 'pemerintahan']))
                    <a href="{{ route('divisi.index') }}"
                       class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('divisi.*') ? 'nav-active' : '' }}">
                        <i class="fa-solid fa-sitemap text-sm w-4 text-center"></i>
                        <span class="font-medium">Data Divisi</span>
                    </a>
                @endif

                @if($instansi && $instansi->jenis_instansi == 'lapangan')
                    <a href="{{ route('teknisi.index') }}"
                       class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('teknisi.*') ? 'nav-active' : '' }}">
                        <i class="fa-solid fa-screwdriver-wrench text-sm w-4 text-center"></i>
                        <span class="font-medium">Data Teknisi</span>
                    </a>
                @endif

                <a href="{{ route('admin.pembimbing.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('admin.pembimbing.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-chalkboard-user text-sm w-4 text-center"></i>
                    <span class="font-medium">Pembimbing Instansi</span>
                </a>

                <a href="{{ route('peserta.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('peserta.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-user-graduate text-sm w-4 text-center"></i>
                    <span class="font-medium">Data Peserta</span>
                </a>

                <a href="{{ route('rekap.absensi') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('rekap.absensi*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-chart-column text-sm w-4 text-center"></i>
                    <span class="font-medium">Rekap Presensi</span>
                </a>

                <a href="{{ route('admin.perizinan.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('admin.perizinan.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-envelope-open-text text-sm w-4 text-center"></i>
                    <span class="font-medium">Perizinan</span>
                </a>

                <a href="{{ route('admin.laporan.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('admin.laporan.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-book-bookmark text-sm w-4 text-center"></i>
                    <span class="font-medium">Laporan Kegiatan</span>
                </a>
            @endif

            <!-- PEMBIMBING INSTANSI MENU -->
            @if($user->role == 'pembimbing_instansi')
                <div class="px-5 mt-5 mb-2 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                    Pembimbing Instansi
                </div>

                <a href="{{ route('pembimbing.dashboard') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('pembimbing.dashboard') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-users text-sm w-4 text-center"></i>
                    <span class="font-medium">Peserta Bimbingan</span>
                </a>

                <a href="{{ route('pembimbing.laporan.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('pembimbing.laporan.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-book-bookmark text-sm w-4 text-center"></i>
                    <span class="font-medium">Validasi Laporan</span>
                </a>
            @endif

            <!-- PESERTA MENU -->
            @if($user->role == 'peserta')
                <div class="px-5 mt-5 mb-2 text-[10px] uppercase font-bold text-gray-500 tracking-wider">
                    Menu Magang
                </div>

                <a href="{{ route('absensi.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('absensi.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-location-crosshairs text-sm w-4 text-center"></i>
                    <span class="font-medium">Presensi GPS</span>
                </a>

                <a href="{{ route('absensi.riwayat') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('absensi.riwayat') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left text-sm w-4 text-center"></i>
                    <span class="font-medium">Riwayat Presensi</span>
                </a>

                <a href="{{ route('perizinan.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('perizinan.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-file-signature text-sm w-4 text-center"></i>
                    <span class="font-medium">Data Perizinan</span>
                </a>

                <a href="{{ route('laporan.index') }}"
                   class="nav-link flex items-center gap-3 px-5 py-2.5 transition {{ request()->routeIs('laporan.*') ? 'nav-active' : '' }}">
                    <i class="fa-solid fa-book-journal-whills text-sm w-4 text-center"></i>
                    <span class="font-medium">Laporan Kegiatan</span>
                </a>
            @endif

        </div>

        <!-- FOOTER USER / LOGOUT DI SIDEBAR -->
        <div class="p-4 border-t border-white/10 bg-slate-900/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-rose-600/90 hover:bg-rose-600 transition text-white py-2.5 rounded-xl text-xs font-bold shadow-sm cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar / Logout</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- CONTENT WRAPPER (RESPONSIVE MARGIN KIRI: lg:ml-64, ml-0 DI MOBILE) -->
    <div class="lg:ml-64 w-full min-h-screen flex flex-col min-w-0 bg-gray-100">

        <!-- TOP NAVBAR (RESPONSIVE PADDING & HAMBURGER BUTTON) -->
        <header class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4 flex justify-between items-center sticky top-0 z-30 shadow-xs">

            <!-- KIRI: HAMBURGER BUTTON & JUDUL HALAMAN -->
            <div class="flex items-center gap-3 min-w-0">
                <!-- Tombol Hamburger Mobile -->
                <button type="button"
                        id="btnSidebarToggle"
                        class="lg:hidden p-2 rounded-xl text-gray-600 hover:text-blue-600 hover:bg-gray-100 transition focus:outline-none cursor-pointer"
                        aria-label="Buka Menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <!-- JUDUL POJOK KIRI ATAS DENGAN IKON -->
                <h1 class="text-sm sm:text-lg lg:text-xl font-bold text-slate-800 truncate flex items-center gap-2 sm:gap-2.5 min-w-0">
                    @hasSection('page-icon')
                        <span class="text-blue-600 text-sm sm:text-base lg:text-lg flex-shrink-0">
                            @yield('page-icon')
                        </span>
                    @endif
                    <span class="truncate">@yield('page-title', 'Dashboard')</span>
                </h1>
            </div>

            <!-- KANAN: NOTIFIKASI & PROFIL PENGGUNA -->
            <div class="flex items-center gap-2.5 sm:gap-4 flex-shrink-0">

                <!-- NOTIFICATION BELL DROPDOWN -->
                <div class="relative" id="notifContainer">
                    <button type="button"
                            id="notifBellBtn"
                            class="relative p-2 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-xl transition focus:outline-none cursor-pointer"
                            title="Notifikasi">
                        <i class="fa-solid fa-bell text-base sm:text-lg"></i>
                        <span id="notifBadge"
                              class="hidden absolute top-0.5 right-0.5 bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.2 rounded-full ring-2 ring-white">
                            0
                        </span>
                    </button>

                    <!-- DROPDOWN MENU NOTIFIKASI -->
                    <div id="notifDropdown"
                         class="hidden absolute right-0 sm:right-auto sm:left-auto mt-3 w-[calc(100vw-2rem)] sm:w-88 max-w-sm bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden z-50 transition-all">
                        <div class="p-4 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-800 text-xs sm:text-sm">Notifikasi</h3>
                                <span id="notifHeaderCount" class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">0 Baru</span>
                            </div>
                            <button type="button"
                                    id="btnMarkAllRead"
                                    class="text-[11px] text-blue-600 hover:text-blue-800 font-semibold cursor-pointer">
                                Tandai Dibaca
                            </button>
                        </div>

                        <div id="notifList" class="max-h-72 sm:max-h-80 overflow-y-auto divide-y divide-gray-100">
                            <!-- Populated by JavaScript -->
                            <div class="p-6 text-center text-gray-400 text-xs">
                                <i class="fa-solid fa-bell-slash text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada notifikasi baru.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-200"></div>

                <!-- USER PROFILE DROPDOWN -->
                <div class="relative">
                    <button type="button"
                            id="btnUserMenu"
                            class="flex items-center gap-2.5 sm:gap-3 p-1 rounded-2xl hover:bg-gray-100 transition focus:outline-none cursor-pointer">
                        <div class="text-right hidden md:block">
                            <h2 class="font-bold text-gray-800 text-xs sm:text-sm leading-tight">
                                {{ $user->name }}
                            </h2>
                            <p class="text-[10px] text-gray-400 capitalize">
                                {{ str_replace('_', ' ', $user->role) }}
                            </p>
                        </div>

                        <!-- AVATAR / FOTO -->
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full overflow-hidden bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xs sm:text-sm shadow-sm border-2 border-white flex-shrink-0">
                            @if($user->foto_profil_url)
                                <img src="{{ $user->foto_profil_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>

                        <i class="fa-solid fa-chevron-down text-gray-400 text-[10px] transition duration-200" id="userMenuChevron"></i>
                    </button>

                    <!-- DROPDOWN MENU PROFIL -->
                    <div id="userDropdownMenu"
                         class="hidden absolute right-0 mt-3 w-64 max-w-[calc(100vw-2rem)] bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden z-50 transition-all duration-200">
                        
                        <!-- USER HEADER -->
                        <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full overflow-hidden bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm border-2 border-white flex-shrink-0">
                                @if($user->foto_profil_url)
                                    <img src="{{ $user->foto_profil_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1 text-left">
                                <h3 class="font-extrabold text-gray-800 text-xs truncate">{{ $user->name }}</h3>
                                <p class="text-[11px] text-gray-500 truncate">{{ $user->email }}</p>
                                <span class="inline-block mt-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 capitalize">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </div>
                        </div>

                        <!-- MENU ITEMS -->
                        <div class="p-2 space-y-1 text-xs text-left">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-blue-50 hover:text-blue-700 font-semibold transition">
                                <i class="fa-solid fa-user-circle text-blue-500 text-sm w-4 text-center"></i>
                                <span>Halaman Profil Pengguna</span>
                            </a>
                        </div>

                        <!-- LOGOUT -->
                        <div class="p-2 border-t border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 font-bold transition text-xs cursor-pointer">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-rose-500 text-sm w-4 text-center"></i>
                                    <span>Keluar / Logout</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

        </header>

        <!-- MAIN CONTENT CONTAINER (RESPONSIVE PADDING) -->
        <main class="p-4 sm:p-6 lg:p-8 flex-1 min-w-0">

            <!-- ALERT NOTIFIKASI SUCCESS -->
            @if(session('success'))
                <div class="mb-5 sm:mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl flex items-center gap-3 text-xs sm:text-sm shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- ALERT NOTIFIKASI ERROR -->
            @if(session('error'))
                <div class="mb-5 sm:mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl flex items-center gap-3 text-xs sm:text-sm shadow-xs">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- YIELD CONTENT DARI HALAMAN ANAK -->
            @yield('content')

        </main>

    </div>

</div>

<!-- SCRIPT HANDLERS (MOBILE DRAWER, DROPDOWNS, PUSH NOTIFIKASI) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. MOBILE SIDEBAR DRAWER TOGGLE
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const btnToggle = document.getElementById('btnSidebarToggle');
    const btnClose = document.getElementById('btnSidebarClose');

    function openSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        }
    }

    function closeSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }
    }

    if (btnToggle) btnToggle.addEventListener('click', openSidebar);
    if (btnClose) btnClose.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    // 2. TOGGLE DROPDOWN NOTIFIKASI
    const bellBtn = document.getElementById('notifBellBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const userBtn = document.getElementById('btnUserMenu');
    const userDropdown = document.getElementById('userDropdownMenu');
    const chevron = document.getElementById('userMenuChevron');

    if (bellBtn && notifDropdown) {
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
            if (userDropdown) userDropdown.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        });

        document.addEventListener('click', function(e) {
            if (!notifDropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }

    // 3. TOGGLE DROPDOWN USER PROFILE
    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = userDropdown.classList.toggle('hidden');
            if (chevron) {
                chevron.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
            }
            if (notifDropdown) notifDropdown.classList.add('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !userBtn.contains(e.target)) {
                userDropdown.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
    }

    // 4. REALTIME NOTIFIKASI POLLING & WEB PUSH API
    const badge = document.getElementById('notifBadge');
    const headerCount = document.getElementById('notifHeaderCount');
    const notifList = document.getElementById('notifList');
    const btnMarkAllRead = document.getElementById('btnMarkAllRead');
    var lastNotifIds = new Set();
    var isFirstLoad = true;

    if ("Notification" in window && Notification.permission === "default") {
        Notification.requestPermission();
    }

    function getNotifMeta(tipe) {
        switch(tipe) {
            case 'success':
                return { bg: 'bg-emerald-50 text-emerald-600 border-emerald-200', icon: 'fa-circle-check' };
            case 'danger':
                return { bg: 'bg-rose-50 text-rose-600 border-rose-200', icon: 'fa-circle-xmark' };
            case 'warning':
                return { bg: 'bg-amber-50 text-amber-600 border-amber-200', icon: 'fa-triangle-exclamation' };
            default:
                return { bg: 'bg-blue-50 text-blue-600 border-blue-200', icon: 'fa-circle-info' };
        }
    }

    function loadNotifications() {
        fetch("{{ route('notifikasi.latest') }}")
            .then(res => res.json())
            .then(data => {
                if (data.unread_count > 0) {
                    if (badge) {
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.classList.remove('hidden');
                    }
                    if (headerCount) {
                        headerCount.textContent = data.unread_count + ' Baru';
                    }
                } else {
                    if (badge) badge.classList.add('hidden');
                    if (headerCount) headerCount.textContent = '0 Baru';
                }

                if (notifList && data.notifikasi && data.notifikasi.length > 0) {
                    let html = '';
                    data.notifikasi.forEach(n => {
                        const meta = getNotifMeta(n.tipe);
                        const isReadClass = n.is_read ? 'opacity-60 bg-white' : 'bg-blue-50/30';
                        
                        html += `
                            <div class="p-3.5 hover:bg-gray-50 transition flex items-start gap-3 cursor-pointer notif-item ${isReadClass}"
                                 data-id="${n.id}"
                                 data-link="${n.url_terkait || ''}">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-xs border ${meta.bg}">
                                    <i class="fa-solid ${meta.icon}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 text-xs truncate">${n.judul}</h4>
                                    <p class="text-gray-500 text-[11px] line-clamp-2 mt-0.5">${n.pesan}</p>
                                    <span class="text-[10px] text-gray-400 mt-1 block">${n.created_at_human}</span>
                                </div>
                                ${!n.is_read ? '<span class="w-2 h-2 rounded-full bg-blue-600 flex-shrink-0 mt-1.5"></span>' : ''}
                            </div>
                        `;

                        if (!isFirstLoad && !n.is_read && !lastNotifIds.has(n.id)) {
                            triggerBrowserNotification(n.judul, n.pesan, n.url_terkait);
                        }
                    });

                    notifList.innerHTML = html;

                    lastNotifIds = new Set(data.notifikasi.map(n => n.id));

                    document.querySelectorAll('.notif-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const notifId = this.getAttribute('data-id');
                            const link = this.getAttribute('data-link');

                            fetch(`/notifikasi/${notifId}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            }).then(() => {
                                if (link) {
                                    window.location.href = link;
                                } else {
                                    loadNotifications();
                                }
                            });
                        });
                    });
                }

                isFirstLoad = false;
            })
            .catch(err => console.error('Gagal memuat notifikasi:', err));
    }

    function triggerBrowserNotification(title, body, url) {
        if ("Notification" in window && Notification.permission === "granted") {
            try {
                const notif = new Notification(title, {
                    body: body,
                    icon: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'
                });

                notif.onclick = function() {
                    window.focus();
                    if (url) window.location.href = url;
                    notif.close();
                };
            } catch (e) {
                console.log('Browser notification error:', e);
            }
        }
    }

    if (btnMarkAllRead) {
        btnMarkAllRead.addEventListener('click', function() {
            fetch("{{ route('notifikasi.readAll') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                loadNotifications();
            });
        });
    }

    loadNotifications();
    setInterval(loadNotifications, 8000);
});
</script>

</body>
</html>