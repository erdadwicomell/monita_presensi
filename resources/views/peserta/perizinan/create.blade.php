@extends('layouts.app')

@section('title','Ajukan Perizinan')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-xl shadow">

        <div class="border-b px-6 py-5">

            <h2 class="text-2xl font-bold text-gray-800">

                Form Pengajuan Perizinan

            </h2>

            <p class="text-gray-500 text-sm mt-1">

                Silakan lengkapi data berikut untuk mengajukan perizinan.

            </p>

        </div>

        <form action="{{ route('perizinan.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="grid grid-cols-2 gap-6 p-6">

                {{-- Jenis Izin --}}
                <div>

                    <label class="block text-sm font-medium mb-2">

                        Jenis Izin

                    </label>

                    <select name="jenis_izin"
                            class="w-full border rounded-lg px-4 py-2"
                            required>

                        <option value="">-- Pilih --</option>

                        <option value="terlambat">
                            Terlambat
                        </option>

                        <option value="tidak_hadir">
                            Tidak Hadir
                        </option>

                        <option value="pulang_awal">
                            Pulang Awal
                        </option>

                    </select>

                </div>

                {{-- Tanggal --}}
                <div>

                    <label class="block text-sm font-medium mb-2">

                        Tanggal

                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        class="w-full border rounded-lg px-4 py-2"
                        required>

                </div>

                {{-- Jam Mulai --}}
                <div>

                    <label class="block text-sm font-medium mb-2">

                        Jam Mulai Izin

                    </label>

                    <input
                        type="time"
                        name="jam_mulai_izin"
                        class="w-full border rounded-lg px-4 py-2">

                </div>

                {{-- Jam Selesai --}}
                <div>

                    <label class="block text-sm font-medium mb-2">

                        Jam Selesai Izin

                    </label>

                    <input
                        type="time"
                        name="jam_selesai_izin"
                        class="w-full border rounded-lg px-4 py-2">

                </div>

                {{-- Alasan --}}
                <div class="col-span-2">

                    <label class="block text-sm font-medium mb-2">

                        Alasan

                    </label>

                    <textarea
                        name="alasan"
                        rows="5"
                        class="w-full border rounded-lg px-4 py-2"
                        placeholder="Masukkan alasan perizinan..."
                        required></textarea>

                </div>

                {{-- Bukti --}}
                <div class="col-span-2">

                    <label class="block text-sm font-medium mb-2">

                        Bukti Pendukung

                    </label>

                    <input
                        type="file"
                        name="bukti"
                        class="w-full border rounded-lg px-4 py-2">

                    <small class="text-gray-500">

                        Format: JPG, PNG atau PDF (Opsional)

                    </small>

                </div>

            </div>

            <div class="border-t px-6 py-5 flex justify-end gap-3">

                <a href="{{ route('perizinan.index') }}"
                   class="px-5 py-2 bg-gray-400 text-white rounded-lg">

                    Kembali

                </a>

                <button
                    type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">

                    Ajukan Perizinan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection