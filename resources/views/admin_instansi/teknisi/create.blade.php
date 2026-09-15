@extends('layouts.app')

@section('content')

<div class="bg-white rounded-2xl shadow p-8 max-w-3xl">

    <h1 class="text-3xl font-bold text-gray-800">
        Tambah Teknisi
    </h1>

    <p class="text-gray-500 mt-2">
        Tambahkan data teknisi pembimbing lapangan.
    </p>

    <form action="{{ route('teknisi.store') }}"
          method="POST"
          class="mt-8 space-y-6">

        @csrf

        <!-- NAMA -->
        <div>

            <label class="font-semibold text-gray-700">
                Nama Teknisi
            </label>

            <input type="text"
                   name="nama"
                   class="w-full border rounded-2xl p-4 mt-2"
                   required>

        </div>

        <!-- EMAIL -->
        <div>

            <label class="font-semibold text-gray-700">
                Email
            </label>

            <input type="email"
                   name="email"
                   class="w-full border rounded-2xl p-4 mt-2"
                   required>

        </div>

        <!-- NO HP -->
        <div>

            <label class="font-semibold text-gray-700">
                Nomor HP
            </label>

            <input type="text"
                   name="no_hp"
                   class="w-full border rounded-2xl p-4 mt-2"
                   required>

        </div>

        <!-- NIK -->
        <div>

            <label class="font-semibold text-gray-700">
                NIK
            </label>

            <input type="text"
                   name="nik"
                   class="w-full border rounded-2xl p-4 mt-2"
                   required>

        </div>

        <!-- ALAMAT KERJA -->
        <div>

            <label class="font-semibold text-gray-700">
                Alamat Kerja
            </label>

            <textarea name="alamat_kerja"
                      rows="4"
                      class="w-full border rounded-2xl p-4 mt-2"
                      required></textarea>

        </div>

        <!-- BUTTON -->
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-2xl">

            Simpan Teknisi

        </button>

    </form>

</div>

@endsection