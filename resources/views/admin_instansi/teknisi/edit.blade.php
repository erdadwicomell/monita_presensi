@extends('layouts.app')

@section('content')

<div class="bg-white rounded-2xl shadow p-8 max-w-3xl">

    <h1 class="text-3xl font-bold text-gray-800">
        Edit Teknisi
    </h1>

    <p class="text-gray-500 mt-2">
        Update data teknisi.
    </p>

    <form action="{{ route('teknisi.update', $teknisi->id) }}"
          method="POST"
          class="mt-8 space-y-6">

        @csrf
        @method('PUT')

        <!-- NAMA -->
        <div>

            <label class="font-semibold text-gray-700">
                Nama Teknisi
            </label>

            <input type="text"
                   name="nama_teknisi"
                   value="{{ $teknisi->nama_teknisi }}"
                   class="w-full border rounded-2xl p-4 mt-2"
                   required>

        </div>

        <!-- JABATAN -->
        <div>

            <label class="font-semibold text-gray-700">
                Jabatan
            </label>

            <input type="text"
                   name="jabatan"
                   value="{{ $teknisi->jabatan }}"
                   class="w-full border rounded-2xl p-4 mt-2"
                   required>

        </div>

        <!-- NOMOR HP -->
        <div>

            <label class="font-semibold text-gray-700">
                Nomor HP
            </label>

            <input type="text"
                   name="nomor_hp"
                   value="{{ $teknisi->nomor_hp }}"
                   class="w-full border rounded-2xl p-4 mt-2"
                   required>

        </div>

        <!-- BUTTON -->
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-2xl">

            Update Teknisi

        </button>

    </form>

</div>

@endsection