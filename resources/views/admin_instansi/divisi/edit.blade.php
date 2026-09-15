@extends('layouts.app')

@section('content')

<div class="bg-white rounded-2xl shadow p-8 max-w-3xl">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">
                Edit Divisi
            </h1>

            <p class="text-gray-500 mt-2">
                Update data divisi instansi.
            </p>

        </div>

    </div>

    <!-- FORM -->
    <form action="{{ route('divisi.update', $divisi->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <!-- NAMA DIVISI -->
        <div class="mb-6">

            <label class="block text-gray-700 font-semibold mb-2">

                Nama Divisi

            </label>

            <input type="text"
                   name="nama_divisi"
                   value="{{ $divisi->nama_divisi }}"
                   required
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

        </div>

        <!-- BUTTON -->
        <div class="flex gap-4">

            <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-xl">

                Update Divisi

            </button>

            <a href="{{ route('divisi.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-xl">

                Kembali

            </a>

        </div>

    </form>

</div>

@endsection