@extends('layouts.app')

@section('title', 'Ajukan Perizinan')
@section('page-title', 'Form Pengajuan Izin')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8">

        <!-- HEADER CARD -->
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs bg-amber-50 text-amber-700 font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-file-signature"></i> Layanan Perizinan
                </span>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                    Ajukan Perizinan Baru
                </h1>
                <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                    Silakan lengkapi formulir di bawah ini dengan benar untuk dievaluasi oleh pembimbing & instansi.
                </p>
            </div>

            <a href="{{ route('perizinan.index') }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2.5 rounded-lg text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <!-- ALERT VALIDASI ERROR -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                @foreach ($errors->all() as $err)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-rose-500"></i> {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- FORM PENGAJUAN -->
        <form action="{{ route('perizinan.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            <!-- BARIS 1: JENIS IZIN & TANGGAL (GRID 2 KOLOM) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jenis Izin <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_izin"
                            required
                            class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="">-- Pilih Jenis Izin --</option>
                        <option value="terlambat" {{ old('jenis_izin') == 'terlambat' ? 'selected' : '' }}>
                            Terlambat Masuk
                        </option>
                        <option value="tidak_hadir" {{ old('jenis_izin') == 'tidak_hadir' ? 'selected' : '' }}>
                            Tidak Hadir / Izin / Sakit
                        </option>
                        <option value="pulang_awal" {{ old('jenis_izin') == 'pulang_awal' ? 'selected' : '' }}>
                            Pulang Lebih Awal
                        </option>
                    </select>
                    @error('jenis_izin')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tanggal <span class="text-rose-500">*</span>
                    </label>
                    <input type="date"
                           name="tanggal"
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required
                           class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    @error('tanggal')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- BARIS 2: JAM MULAI & JAM SELESAI (GRID 2 KOLOM) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jam Mulai Izin
                    </label>
                    <input type="time"
                           name="jam_mulai_izin"
                           value="{{ old('jam_mulai_izin') }}"
                           class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    <p class="text-[11px] text-slate-400 mt-1">Wajib diisi jika memilih izin terlambat/pulang awal.</p>
                    @error('jam_mulai_izin')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jam Selesai Izin
                    </label>
                    <input type="time"
                           name="jam_selesai_izin"
                           value="{{ old('jam_selesai_izin') }}"
                           class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                    <p class="text-[11px] text-slate-400 mt-1">Estimasi jam kembali atau batas jam izin berakhir.</p>
                    @error('jam_selesai_izin')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- BARIS 3: UNGGAH BUKTI PENDUKUNG (POIN 6 BACKEND INTEGRATION) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Unggah Bukti Pendukung (Surat Dokter / Surat Keterangan)
                </label>
                <input type="file"
                       name="bukti"
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer border border-slate-200 rounded-lg p-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer bg-white">
                <p class="text-[11px] text-slate-400 mt-1">
                    *Format file wajib PDF/JPG/PNG, ukuran maksimal 2MB.
                </p>
                @error('bukti')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- BARIS 4: ALASAN PERIZINAN -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan Perizinan <span class="text-rose-500">*</span>
                </label>
                <textarea name="alasan"
                          rows="4"
                          required
                          placeholder="Jelaskan secara rinci alasan keperluan perizinan Anda..."
                          class="w-full bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 text-xs rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs leading-relaxed">{{ old('alasan') }}</textarea>
                @error('alasan')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- TOMBOL AKSI (POJOK KANAN BAWAH) -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('perizinan.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-5 py-2.5 rounded-lg transition-colors">
                    Batal
                </a>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-2.5 px-6 rounded-lg shadow-sm hover:shadow transition-colors flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Ajukan Perizinan</span>
                </button>
            </div>

        </form>

    </div>

</div>

@endsection