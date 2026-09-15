<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun & Verifikasi OTP - MONITA</title>

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
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-200/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/60 relative z-10 my-8 text-center">

        <!-- LOGO & ICON -->
        <a href="/" class="inline-block mb-4 hover:opacity-90 transition">
            <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" class="h-12 sm:h-14 w-auto mx-auto object-contain">
        </a>

        <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-3">
            <i class="fa-solid fa-envelope-circle-check"></i>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Aktivasi Akun & Verifikasi OTP
        </h1>

        @if(!empty($email))
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Masukkan 6 digit kode OTP yang telah dikirimkan ke email: <br>
                <strong class="text-blue-700 font-bold">{{ $email }}</strong>
            </p>
        @else
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Masukkan alamat email yang didaftarkan serta 6 digit kode OTP yang Anda terima.
            </p>
        @endif

        <!-- FLASH INFO -->
        @if (session('info'))
            <div class="mt-4 p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-700 text-xs text-left flex items-start gap-2">
                <i class="fa-solid fa-circle-info text-blue-600 mt-0.5"></i>
                <div>{{ session('info') }}</div>
            </div>
        @else
            <div class="mt-4 p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-600 text-xs text-left flex items-start gap-2.5">
                <i class="fa-solid fa-inbox text-blue-600 text-sm mt-0.5"></i>
                <div class="leading-relaxed">
                    Kode OTP 6-digit dikirimkan ke email Anda. Silakan periksa folder <strong class="text-slate-800">Kotak Masuk (Inbox)</strong> atau folder <strong class="text-amber-600">Spam</strong>.
                </div>
            </div>
        @endif

        <!-- ERROR ALERT -->
        @if ($errors->any())
            <div class="mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs text-left space-y-1">
                @foreach ($errors->all() as $err)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-500"></i> {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- OTP FORM -->
        <form method="POST" action="{{ route('otp.verify.submit') }}" class="mt-6 space-y-4 text-left">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            @if(empty($email))
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Email Terdaftar <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="nama@email.com"
                           class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>
            @else
                <input type="hidden" name="email" value="{{ $email }}">
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">
                    6 Digit Kode Verifikasi OTP <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="otp_code"
                       maxlength="6"
                       required
                       autofocus
                       value="{{ old('otp_code') }}"
                       placeholder="••••••"
                       class="w-full bg-slate-50/80 border border-slate-300 text-slate-900 text-2xl font-mono font-black tracking-[0.5em] text-center rounded-2xl py-3.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-inner">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs py-3.5 rounded-xl shadow-md shadow-blue-600/20 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 mt-3 cursor-pointer">
                <i class="fa-solid fa-shield-check"></i>
                <span>Verifikasi OTP & Lanjutkan</span>
            </button>
        </form>

        <!-- RESEND OTP FORM (Jika email ada) -->
        @if(!empty($email))
            <form method="POST" action="{{ route('otp.verify.resend') }}" class="mt-5 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="type" value="{{ $type }}">

                <span>Tidak menerima kode?</span>
                <button type="submit" class="text-blue-600 font-bold hover:underline cursor-pointer">
                    Kirim Ulang OTP
                </button>
            </form>
        @endif

        <div class="mt-4 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki password? 
            <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">
                Masuk di Sini
            </a>
        </div>

    </div>

</body>
</html>
