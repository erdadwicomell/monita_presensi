@extends('layouts.app')

@section('content')

<div class="bg-white rounded-2xl shadow p-8 max-w-3xl">

    <div class="flex justify-between items-center mb-8">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">
                Tambah Divisi
            </h1>

            <p class="text-gray-500 mt-2">
                Tambahkan divisi baru untuk instansi.
            </p>

        </div>

    </div>

    <form action="{{ route('divisi.store') }}"
          method="POST">

        @csrf

        <!-- NAMA DIVISI -->
        <div class="mb-6">

            <label class="block text-gray-700 font-semibold mb-2">

                Nama Divisi

            </label>

            <input type="text"
                   name="nama_divisi"
                   required
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

        </div>

        <!-- BUTTON -->
        <div class="flex gap-4">

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl">

                Simpan

            </button>

            <a href="{{ route('divisi.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-xl">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection