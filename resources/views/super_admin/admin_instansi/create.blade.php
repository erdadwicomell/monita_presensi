@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl shadow p-8">

        <h1 class="text-3xl font-bold text-gray-800 mb-6">

            Tambah Admin Instansi

        </h1>

        <form action="{{ route('admin-instansi.store') }}"
              method="POST">

            @csrf

            <!-- NAMA -->
            <div class="mb-5">

                <label class="block mb-2 font-semibold text-gray-700">

                    Nama Admin

                </label>

                <input type="text"
                       name="name"
                       required
                       class="w-full border rounded-2xl px-5 py-3">

            </div>

            <!-- EMAIL -->
            <div class="mb-5">

                <label class="block mb-2 font-semibold text-gray-700">

                    Email

                </label>

                <input type="email"
                       name="email"
                       required
                       class="w-full border rounded-2xl px-5 py-3">

            </div>

            <!-- PASSWORD -->
            <div class="mb-5">

                <label class="block mb-2 font-semibold text-gray-700">

                    Password

                </label>

                <input type="password"
                       name="password"
                       required
                       class="w-full border rounded-2xl px-5 py-3">

            </div>

            <!-- INSTANSI -->
            <div class="mb-5">

                <label class="block mb-2 font-semibold text-gray-700">

                    Pilih Instansi

                </label>

                <select name="instansi_id"
                        required
                        class="w-full border rounded-2xl px-5 py-3">

                    <option value="">
                        -- Pilih Instansi --
                    </option>

                    @foreach($instansis as $instansi)

                        <option value="{{ $instansi->id }}">

                            {{ $instansi->nama_instansi }}

                        </option>

                    @endforeach

                </select>

            </div>

            <!-- BUTTON -->
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl">

                Simpan Admin

            </button>

        </form>

    </div>

</div>

@endsection