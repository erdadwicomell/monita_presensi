<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - MONITA</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 via-blue-50/30 to-slate-100 text-slate-800 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-blue-600 selection:text-white">

    <!-- Ambient Soft Glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/60 relative z-10 my-6 sm:my-8">

        <!-- LOGO & HEADER -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block mb-3 hover:opacity-90 transition">
                <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" style="max-height: 56px; width: auto;" class="h-12 sm:h-14 w-auto mx-auto object-contain">
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Masuk ke Akun Anda
            </h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Sistem Presensi & Monitoring Magang Terintegrasi
            </p>
        </div>

        <!-- SESSION STATUS -->
        @if (session('status'))
            <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- ERROR ALERT -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                @foreach ($errors->all() as $err)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-500"></i> {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- EMAIL -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="nama@email.com"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>
            </div>

            <!-- PASSWORD -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700" for="password">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[11px] text-blue-600 font-semibold hover:underline">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm pointer-events-none">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="Masukkan password Anda"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    <button type="button"
                            id="btnTogglePassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer transition p-1"
                            aria-label="Tampilkan atau sembunyikan password">
                        <i class="fa-solid fa-eye text-sm" id="iconPassword"></i>
                    </button>
                </div>
            </div>

            <!-- REMEMBER ME -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                    <input id="remember_me"
                           type="checkbox"
                           name="remember"
                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    <span class="text-xs text-slate-600 font-medium">Ingat Saya</span>
                </label>
            </div>

            <!-- SUBMIT BUTTON -->
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs py-3.5 rounded-xl shadow-md shadow-blue-600/20 transition transform hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2 cursor-pointer">
                <span>Masuk Sekarang</span>
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>

        <!-- FOOTER LINKS -->
        <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
            <a href="{{ route('register.admin.form') }}" class="text-blue-600 font-bold hover:underline flex items-center gap-1.5">
                <i class="fa-solid fa-building-user"></i> Daftar Admin Instansi
            </a>
            <a href="{{ route('otp.verify.form') }}" class="text-emerald-600 font-bold hover:underline flex items-center gap-1.5">
                <i class="fa-solid fa-key"></i> Aktivasi Akun (OTP)
            </a>
        </div>

    </div>

    <!-- SKRIP TOGGLE SHOW/HIDE PASSWORD -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnTogglePassword');
        const input = document.getElementById('password');
        const icon = document.getElementById('iconPassword');

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
    });
    </script>

</body>
</html>
