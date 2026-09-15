@extends('layouts.app')

@section('title', 'Edit Perizinan')
@section('page-title', 'Edit Perizinan')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow overflow-hidden">

        <div class="border-b px-8 py-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Edit Pengajuan Perizinan
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Perbarui data izin Anda sebelum dievaluasi oleh admin instansi.
            </p>
        </div>

        <form action="{{ route('perizinan.update', $perizinan->id_perizinan) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8">

                {{-- Jenis Izin --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Jenis Izin <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_izin"
                            id="jenisIzinSelect"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required>
                        <option value="terlambat" {{ $perizinan->jenis_izin === 'terlambat' ? 'selected' : '' }}>
                            Terlambat
                        </option>
                        <option value="tidak_hadir" {{ $perizinan->jenis_izin === 'tidak_hadir' ? 'selected' : '' }}>
                            Tidak Hadir
                        </option>
                        <option value="pulang_awal" {{ $perizinan->jenis_izin === 'pulang_awal' ? 'selected' : '' }}>
                            Pulang Awal
                        </option>
                    </select>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Izin <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="tanggal"
                           value="{{ $perizinan->tanggal->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           required>
                </div>

                {{-- Jam Mulai --}}
                <div id="jamMulaiBox">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Jam Mulai Izin
                    </label>
                    <input type="time"
                           name="jam_mulai_izin"
                           value="{{ $perizinan->jam_mulai_izin }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                {{-- Jam Selesai --}}
                <div id="jamSelesaiBox">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Jam Selesai Izin
                    </label>
                    <input type="time"
                           name="jam_selesai_izin"
                           value="{{ $perizinan->jam_selesai_izin }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                {{-- Alasan --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Alasan / Keterangan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan"
                              rows="4"
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                              placeholder="Jelaskan alasan pengajuan perizinan secara rinci..."
                              required>{{ $perizinan->alasan }}</textarea>
                </div>

                {{-- Bukti --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Bukti Pendukung Baru (Opsional)
                    </label>
                    <input type="file"
                           name="bukti"
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">
                        Format yang didukung: JPG, PNG, atau PDF (Maksimal 2MB). Kosongkan jika tidak ingin mengganti bukti.
                    </p>

                    @if($perizinan->bukti)
                        <div class="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-paperclip text-blue-600"></i>
                            <span class="text-gray-600 font-medium">Bukti saat ini:</span>
                            <a href="{{ asset('storage/' . $perizinan->bukti) }}" target="_blank" class="text-blue-600 hover:underline">
                                {{ basename($perizinan->bukti) }}
                            </a>
                        </div>
                    @endif
                </div>

            </div>

            <div class="border-t border-gray-100 px-8 py-5 flex justify-end gap-3 bg-gray-50/50">
                <a href="{{ route('perizinan.index') }}"
                   class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
