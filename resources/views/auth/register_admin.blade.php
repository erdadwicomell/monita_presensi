<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin Instansi - MONITA</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 via-blue-50/30 to-slate-100 text-slate-800 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-blue-600 selection:text-white">

    <!-- Ambient Soft Glow Background -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <!-- MAIN FORM CARD (SOFT LIGHT MODE) -->
    <div class="w-full max-w-xl bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/60 relative z-10 my-8">

        <!-- LOGO & HEADER -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block mb-3 hover:opacity-90 transition">
                <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" class="h-12 sm:h-14 w-auto mx-auto object-contain">
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Registrasi Akun Admin Instansi
            </h1>
            <p class="text-xs text-slate-500 mt-1.5 max-w-md mx-auto leading-relaxed">
                Daftarkan akun administrator untuk mengelola presensi, peserta, dan pembimbing instansi Anda.
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

        <!-- FORM -->
        <form method="POST" action="{{ route('register.admin.submit') }}" class="space-y-4">
            @csrf

            <!-- NAMA LENGKAP -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Nama Lengkap Admin <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Contoh: Budi Santoso, S.Kom"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NOMOR TELEPON -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input type="text"
                               name="nomor_telepon"
                               value="{{ old('nomor_telepon') }}"
                               required
                               placeholder="081234567890"
                               class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>

                <!-- JABATAN -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jabatan di Instansi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm">
                            <i class="fa-solid fa-briefcase"></i>
                        </span>
                        <input type="text"
                               name="jabatan"
                               value="{{ old('jabatan') }}"
                               required
                               placeholder="Contoh: Staff HRD / Koordinator"
                               class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- ALAMAT -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alamat Domisili / Alamat Instansi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute top-3 left-3.5 text-slate-400 text-sm">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <textarea name="alamat"
                              rows="2"
                              required
                              placeholder="Alamat lengkap..."
                              class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <!-- EMAIL -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Email Akun (Untuk Kode OTP & Login) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="admin@instansi.com"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- PASSWORD -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm pointer-events-none">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password"
                               name="password"
                               id="passwordInput"
                               required
                               placeholder="Minimal 8 karakter"
                               class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                        <button type="button"
                                id="btnTogglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer transition p-1"
                                aria-label="Tampilkan atau sembunyikan password">
                            <i class="fa-solid fa-eye text-sm" id="iconPassword"></i>
                        </button>
                    </div>
                </div>

                <!-- KONFIRMASI PASSWORD -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Konfirmasi Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm pointer-events-none">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <input type="password"
                               name="password_confirmation"
                               id="passwordConfirmationInput"
                               required
                               placeholder="Ulangi password"
                               class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                        <button type="button"
                                id="btnTogglePasswordConfirmation"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer transition p-1"
                                aria-label="Tampilkan atau sembunyikan konfirmasi password">
                            <i class="fa-solid fa-eye text-sm" id="iconPasswordConfirmation"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- BUTTON -->
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs py-3.5 rounded-xl shadow-md shadow-blue-600/20 transition transform hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2 cursor-pointer">
                <span>Daftar & Minta Kode OTP</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <!-- FOOTER LINKS -->
        <div class="text-center mt-6 pt-6 border-t border-slate-100 text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">
                Masuk di Sini
            </a>
            <span class="mx-2 text-slate-300">•</span>
            <a href="{{ route('otp.verify.form') }}" class="text-emerald-600 font-bold hover:underline">
                Verifikasi OTP / Aktivasi Akun
            </a>
        </div>

    </div>

    <!-- SKRIP TOGGLE SHOW/HIDE PASSWORD -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function initPasswordToggle(btnId, inputId, iconId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (btn && input && icon) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isPassword = input.type === 'password';
                    
                    input.type = isPassword ? 'text' : 'password';

                    if (isPassword) {
                        icon.classList.remove('fa-eye', 'text-slate-400');
                        icon.classList.add('fa-eye-slash', 'text-blue-600');
                    } else {
                        icon.classList.remove('fa-eye-slash', 'text-blue-600');
                        icon.classList.add('fa-eye', 'text-slate-400');
                    }
                });
            }
        }

        initPasswordToggle('btnTogglePassword', 'passwordInput', 'iconPassword');
        initPasswordToggle('btnTogglePasswordConfirmation', 'passwordConfirmationInput', 'iconPasswordConfirmation');
    });
    </script>

</body>
</html>
