@extends('layouts.app')

@section('page-title', 'Edit Peserta')

@section('content')

<div class="bg-white rounded-2xl shadow-sm p-8">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">

        <div>

            <h1 class="text-2xl font-bold text-gray-800">
                Edit Peserta
            </h1>

            <p class="text-gray-500 mt-1 text-sm">
                Edit data peserta magang
            </p>

        </div>

        <a href="{{ route('peserta.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-3 rounded-xl text-sm transition">

            Kembali

        </a>

    </div>

    <!-- FORM -->
    <form action="{{ route('peserta.update', $peserta->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- NAMA -->
            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Peserta
                </label>

                <input type="text"
                       name="name"
                       value="{{ $peserta->name }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <!-- EMAIL -->
            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Email
                </label>

                <input type="email"
                       name="email"
                       value="{{ $peserta->email }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <!-- PASSWORD -->
            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Password Baru
                </label>

                <input type="password"
                       name="password"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Kosongkan jika tidak diubah">

            </div>

            <!-- NIM -->
            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    NIM
                </label>

                <input type="text"
                       name="nim"
                       value="{{ $peserta->nim }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <!-- NO HP -->
            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nomor HP
                </label>

                <input type="text"
                       name="no_hp"
                       value="{{ $peserta->no_hp }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <!-- ALAMAT -->
            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alamat
                </label>

                <input type="text"
                       name="alamat"
                       value="{{ $peserta->alamat }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <!-- PEMBIMBING INSTANSI / LAPANGAN -->
            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Pembimbing Instansi / Lapangan
                </label>

                <select name="pembimbing_id"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <option value="">
                        -- Pilih Pembimbing --
                    </option>

                    @foreach($pembimbings as $pembimbing)
                        <option value="{{ $pembimbing->id }}" {{ old('pembimbing_id', $peserta->pembimbing_id) == $pembimbing->id ? 'selected' : '' }}>
                            {{ $pembimbing->name }} {{ $pembimbing->jabatan ? '('.$pembimbing->jabatan.')' : '' }}
                        </option>
                    @endforeach

                </select>

            </div>

            <!-- DIVISI -->
            @if(auth()->user()->instansi->jenis_instansi == 'kantor')

            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Divisi
                </label>

                <select name="divisi_id"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <option value="">
                        -- Pilih Divisi --
                    </option>

                    @foreach($divisis as $divisi)

                        <option value="{{ $divisi->id }}"
                            {{ $peserta->divisi_id == $divisi->id ? 'selected' : '' }}>

                            {{ $divisi->nama_divisi }}

                        </option>

                    @endforeach

                </select>

            </div>

            @endif

            <!-- TEKNISI -->
            @if(auth()->user()->instansi->jenis_instansi == 'lapangan')

            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Teknisi Pembimbing
                </label>

                <select name="teknisi_id"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3">

                    <option value="">
                        -- Pilih Teknisi --
                    </option>

                    @foreach($teknisis as $teknisi)

                        <option value="{{ $teknisi->id }}"
                            {{ $peserta->teknisi_id == $teknisi->id ? 'selected' : '' }}>

                            {{ $teknisi->nama }}

                        </option>

                    @endforeach

                </select>

            </div>

            @endif

        </div>

        <!-- BUTTON -->
        <div class="mt-8">

            <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-xl font-medium transition">

                <i class="fa-solid fa-pen-to-square mr-2"></i>

                Update Peserta

            </button>

        </div>

    </form>

</div>

@endsection