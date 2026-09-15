@extends('layouts.app')

@section('page-title', 'Pendaftaran Peserta Magang')

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
        <div>
            <span class="text-xs bg-blue-50 text-blue-600 font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-user-plus mr-1"></i> Data Peserta
            </span>
            <h1 class="text-2xl font-black text-gray-800 mt-2">
                Pendaftaran Akun Peserta Magang
            </h1>
            <p class="text-gray-500 text-xs mt-1">
                Daftarkan peserta magang baru. Peserta akan menerima kode OTP untuk aktivasi dan mengatur password akunnya sendiri.
            </p>
        </div>

        <a href="{{ route('peserta.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-5 py-2.5 rounded-xl text-xs transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- ERROR VALIDATION -->
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- FORM -->
    <form action="{{ route('peserta.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- NAMA PESERTA -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Nama Lengkap Peserta <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: Muhammad Farhan"
                       required>
            </div>

            <!-- ASAL SEKOLAH / PERGURUAN TINGGI -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Asal Sekolah / Perguruan Tinggi <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="asal_sekolah_pt"
                       value="{{ old('asal_sekolah_pt') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: Universitas Indonesia / SMKN 1 Jakarta"
                       required>
            </div>

            <!-- NISN / NIM -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    NISN / NIM Peserta <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="nim_nisn"
                       value="{{ old('nim_nisn') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: 210101102 / 0054321987"
                       required>
            </div>

            <!-- EMAIL -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Email Peserta (Untuk OTP & Login) <span class="text-rose-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="peserta@gmail.com"
                       required>
            </div>

            <!-- NOMOR TELEPON -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="nomor_telepon"
                       value="{{ old('nomor_telepon') }}"
                       class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="081298765432"
                       required>
            </div>

            <!-- PEMBIMBING INSTANSI / LAPANGAN -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Pembimbing Instansi / Lapangan <span class="text-rose-500">*</span>
                </label>
                <select name="pembimbing_id"
                        required
                        class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Pembimbing Instansi / Lapangan --</option>
                    @foreach($pembimbings as $pembimbing)
                        <option value="{{ $pembimbing->id }}" {{ old('pembimbing_id') == $pembimbing->id ? 'selected' : '' }}>
                            {{ $pembimbing->name }} {{ $pembimbing->jabatan ? '('.$pembimbing->jabatan.')' : '' }}
                        </option>
                    @endforeach
                </select>
                @if($pembimbings->isEmpty())
                    <p class="text-[11px] text-amber-600 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> Belum ada pembimbing terdaftar. Silakan tambahkan pembimbing terlebih dahulu di menu Pembimbing Instansi.
                    </p>
                @endif
            </div>

            <!-- DIVISI (KANTOR / PEMERINTAHAN) -->
            @if(auth()->user()->instansi && in_array(auth()->user()->instansi->jenis_instansi, ['kantor', 'pemerintahan']))
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        Divisi Penempatan <span class="text-rose-500">*</span>
                    </label>
                    <select name="divisi_id"
                            required
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Divisi Penempatan --</option>
                        @foreach($divisis as $divisi)
                            <option value="{{ $divisi->id }}" {{ old('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                {{ $divisi->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- TEKNISI (LAPANGAN) -->
            @if(auth()->user()->instansi && auth()->user()->instansi->jenis_instansi === 'lapangan')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">
                        Teknisi Lapangan <span class="text-rose-500">*</span>
                    </label>
                    <select name="teknisi_id"
                            required
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Teknisi Lapangan --</option>
                        @foreach($teknisis as $teknisi)
                            <option value="{{ $teknisi->id }}" {{ old('teknisi_id') == $teknisi->id ? 'selected' : '' }}>
                                {{ $teknisi->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- ALAMAT DOMISILI -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    Alamat Domisili Peserta <span class="text-rose-500">*</span>
                </label>
                <textarea name="alamat"
                          rows="2"
                          class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Alamat lengkap tempat tinggal atau kos peserta..."
                          required>{{ old('alamat') }}</textarea>
            </div>

        </div>

        <!-- INFO OTP MANDIRI -->
        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-start gap-3">
            <i class="fa-solid fa-shield-halved text-blue-600 text-lg mt-0.5"></i>
            <div>
                <h4 class="text-xs font-bold text-blue-900">Aktivasi Akun Mandiri Berbasis OTP</h4>
                <p class="text-[11px] text-blue-700 mt-0.5 leading-relaxed">
                    Setelah tombol simpan ditekan, sistem secara otomatis menerbitkan <strong>Kode OTP Aktivasi</strong>. Peserta magang dapat langsung mengaktifkan akun dan mengatur password akunnya sendiri melalui halaman verifikasi OTP.
                </p>
            </div>
        </div>

        <!-- BUTTON -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('peserta.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Daftarkan Peserta & Terbitkan OTP</span>
            </button>
        </div>

    </form>

</div>

@endsection