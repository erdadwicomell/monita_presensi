<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>MONITA - Monitoring Internship Attendance</title>

        <!-- FAVICON -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- CDN Tailwind & Fonts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <style>
            * { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-sans text-slate-800 antialiased bg-gradient-to-b from-slate-50 via-blue-50/30 to-slate-100 min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-blue-600 selection:text-white">
        <!-- Ambient Soft Glow -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-200/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-full sm:max-w-md my-6 px-8 py-10 bg-white border border-slate-200/80 shadow-xl shadow-slate-200/60 rounded-3xl relative z-10">
            <div class="flex justify-center mb-6">
                <a href="/" class="hover:opacity-90 transition">
                    <img src="{{ asset('images/logo-monita.png') }}" alt="Logo Monita" style="max-height: 56px; width: auto;" class="h-12 sm:h-14 w-auto object-contain mx-auto">
                </a>
            </div>

            {{ $slot }}
        </div>
    </body>
</html>
