<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Password Akun Baru - MONITA</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 via-blue-50/30 to-slate-100 text-slate-800 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-blue-600 selection:text-white">

    <!-- Ambient Soft Glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/60 relative z-10 my-8">

        <!-- LOGO & ICON -->
        <div class="text-center mb-6">
            <a href="/" class="inline-block mb-3 hover:opacity-90 transition">
                <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" class="h-12 sm:h-14 w-auto mx-auto object-contain">
            </a>

            <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-key"></i>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Atur Password Akun Anda
            </h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Verifikasi OTP berhasil! Silakan tentukan password rahasia untuk akun Anda (<strong class="text-blue-700 font-bold">{{ $email }}</strong>).
            </p>
        </div>

        <!-- ERROR ALERT -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                @foreach ($errors->all() as $err)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-500"></i> {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('otp.set-password.submit') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- PASSWORD BARU -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Password Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password"
                           name="password"
                           required
                           autofocus
                           placeholder="Minimal 8 karakter"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>
            </div>

            <!-- KONFIRMASI PASSWORD -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Ulangi Password Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <input type="password"
                           name="password_confirmation"
                           required
                           placeholder="Ketik ulang password baru"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs py-3.5 rounded-xl shadow-md shadow-emerald-600/20 transition transform hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-check-circle"></i>
                <span>Simpan Password & Masuk</span>
            </button>
        </form>

    </div>

</body>
</html>
