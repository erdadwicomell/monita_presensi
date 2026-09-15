@extends('layouts.app')

@section('content')

<!-- SUPER ADMIN -->
@if(auth()->user()->role == 'super_admin')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- HEADER -->
        <div class="lg:col-span-3">

            <div class="bg-white rounded-2xl shadow p-8">

                <h1 class="text-4xl font-bold text-gray-800">

                    Dashboard Super Admin

                </h1>

                <p class="text-gray-500 mt-3 text-lg">

                    Selamat datang di sistem absensi magang multi instansi.

                </p>

            </div>

        </div>

        <!-- TOTAL INSTANSI -->
        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Total Instansi
            </p>

            <h1 class="text-5xl font-bold mt-4 text-blue-700">

                {{ $totalInstansi }}

            </h1>

        </div>

        <!-- TOTAL PESERTA -->
        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Total Peserta
            </p>

            <h1 class="text-5xl font-bold mt-4 text-green-700">

                {{ $totalPeserta }}

            </h1>

        </div>

        <!-- TOTAL ABSENSI -->
        <div class="bg-white rounded-2xl shadow p-6">

            <p class="text-gray-500">
                Total Absensi
            </p>

            <h1 class="text-5xl font-bold mt-4 text-red-700">

                {{ $totalAbsensi }}

            </h1>

        </div>

    </div>

@endif

<!-- PESERTA -->
@if(auth()->user()->role == 'peserta')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- HEADER -->
        <div class="lg:col-span-2">

            <div class="bg-white rounded-2xl shadow p-8">

                <h1 class="text-4xl font-bold text-gray-800">

                    Dashboard Peserta Magang

                </h1>

                <p class="text-gray-500 mt-3 text-lg">

                    Selamat datang di sistem absensi magang.

                </p>

            </div>

        </div>

        <!-- ABSENSI -->
        <div class="bg-white rounded-2xl shadow p-6">

            <h1 class="text-2xl font-bold text-blue-700">

                Absensi GPS

            </h1>

            <p class="text-gray-500 mt-3">

                Lakukan absensi masuk dan pulang menggunakan GPS realtime.

            </p>

            <a href="/absensi"
               class="inline-block mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl">

                Buka Absensi

            </a>

        </div>

        <!-- LAPORAN -->
        <div class="bg-white rounded-2xl shadow p-6">

            <h1 class="text-2xl font-bold text-green-700">

                Laporan Harian

            </h1>

            <p class="text-gray-500 mt-3">

                Isi laporan kegiatan harian selama magang.

            </p>

            <a href="/laporan"
               class="inline-block mt-6 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl">

                Isi Laporan

            </a>

        </div>

    </div>

@endif

@endsection