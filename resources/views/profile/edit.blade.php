@extends('layouts.app')

@section('page-title', 'Profil Pengguna')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    <!-- PAGE HEADER -->
    <div class="rounded-2xl bg-white shadow-md shadow-slate-100/80 border border-slate-100/50 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Pengaturan Profil Saya
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Kelola informasi biodata, foto profil, dan kredensial keamanan akun Anda.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold capitalize border
                @if($user->role === 'super_admin') bg-purple-50 text-purple-700 border-purple-200/60
                @elseif($user->role === 'admin_instansi') bg-blue-50 text-blue-700 border-blue-200/60
                @elseif($user->role === 'pembimbing_instansi') bg-indigo-50 text-indigo-700 border-indigo-200/60
                @else bg-emerald-50 text-emerald-700 border-emerald-200/60 @endif">
                <i class="fa-solid fa-shield-halved mr-1"></i> {{ str_replace('_', ' ', $user->role) }}
            </span>
        </div>
    </div>

    <!-- ERROR VALIDATION -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI: KARTU FOTO PROFIL & IDENTITAS -->
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl bg-white shadow-md shadow-slate-100/80 border border-slate-100/50 p-6 text-center">
                <!-- AVATAR / FOTO PREVIEW -->
                <div class="relative w-32 h-32 mx-auto mb-4">
                    <div id="avatarContainer" class="w-32 h-32 rounded-full overflow-hidden border-4 border-blue-50 shadow-sm bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-4xl font-black mx-auto">
                        @if($user->foto_profil_url)
                            <img id="avatarPreview" src="{{ $user->foto_profil_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <img id="avatarPreview" src="" alt="{{ $user->name }}" class="w-full h-full object-cover hidden">
                            <span id="avatarInitials">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>

                <h3 class="text-base font-extrabold text-slate-800">{{ $user->name }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $user->email }}</p>

                <div class="mt-4 pt-4 border-t border-slate-100/80 space-y-3 text-xs text-left">
                    <div class="flex items-center justify-between text-slate-500">
                        <span class="font-medium">Peran:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize
                            @if($user->role === 'super_admin') bg-purple-50 text-purple-700 border-purple-200/60
                            @elseif($user->role === 'admin_instansi') bg-blue-50 text-blue-700 border-blue-200/60
                            @elseif($user->role === 'pembimbing_instansi') bg-indigo-50 text-indigo-700 border-indigo-200/60
                            @else bg-emerald-50 text-emerald-700 border-emerald-200/60 @endif">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>

                    @if($user->instansi)
                        <div class="flex items-center justify-between text-slate-500">
                            <span class="font-medium">Instansi:</span>
                            <strong class="text-slate-800 font-semibold truncate max-w-[150px]">{{ $user->instansi->nama_instansi }}</strong>
                        </div>
                    @endif

                    @if($user->divisi)
                        <div class="flex items-center justify-between text-slate-500">
                            <span class="font-medium">Divisi:</span>
                            <strong class="text-blue-600 font-semibold">{{ $user->divisi->nama_divisi }}</strong>
                        </div>
                    @elseif($user->teknisi)
                        <div class="flex items-center justify-between text-slate-500">
                            <span class="font-medium">Teknisi:</span>
                            <strong class="text-blue-600 font-semibold">{{ $user->teknisi->nama }}</strong>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-slate-500">
                        <span class="font-medium">Status Akun:</span>
                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-semibold px-3 py-1 rounded-full text-xs inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> Aktif Terverifikasi
                        </span>
                    </div>
                </div>

                <!-- PESERTA BIMBINGAN LIST (Jika role Pembimbing) -->
                @if($user->role === 'pembimbing_instansi' && $user->pesertaBimbingan->count() > 0)
                    <div class="mt-4 pt-4 border-t border-slate-100/80 text-left">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">
                            Peserta yang Dibimbing ({{ $user->pesertaBimbingan->count() }}):
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($user->pesertaBimbingan as $pb)
                                <span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-purple-200/60">
                                    {{ $pb->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- KANAN: FORM BIODATA & KEAMANAN -->
        <div class="lg:col-span-2 space-y-6">

            <!-- FORM BIODATA & FOTO -->
            <div class="rounded-2xl bg-white shadow-md shadow-slate-100/80 border border-slate-100/50 p-6 md:p-8">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Biodata & Informasi Kontak</h2>
                        <p class="text-xs text-slate-400">Perbarui data diri dan foto profil akun Anda</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- UPLOAD FOTO PROFIL -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Ganti Foto Profil
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="file"
                                   id="fotoProfilInput"
                                   name="foto_profil"
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl p-1.5">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB)</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NAMA LENGKAP -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Email <span class="text-rose-500">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                        </div>

                        <!-- NOMOR TELEPON -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                No. Telepon / WhatsApp
                            </label>
                            <input type="text"
                                   name="nomor_telepon"
                                   value="{{ old('nomor_telepon', $user->nomor_telepon ?? $user->no_hp) }}"
                                   placeholder="081234567890"
                                   class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                        </div>

                        <!-- ROLE SPECIFIC FIELD 1 -->
                        @if($user->role === 'peserta')
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    NISN / NIM
                                </label>
                                <input type="text"
                                       name="nim_nisn"
                                       value="{{ old('nim_nisn', $user->nim_nisn ?? $user->nim) }}"
                                       class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                            </div>
                        @elseif($user->role === 'pembimbing_instansi')
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    NIP (Nomor Induk Pegawai)
                                </label>
                                <input type="text"
                                       name="nip"
                                       value="{{ old('nip', $user->nip) }}"
                                       class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Jabatan di Instansi
                                </label>
                                <input type="text"
                                       name="jabatan"
                                       value="{{ old('jabatan', $user->jabatan) }}"
                                       class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                            </div>
                        @endif

                        <!-- ROLE SPECIFIC FIELD 2 -->
                        @if($user->role === 'peserta')
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Asal Sekolah / Perguruan Tinggi
                                </label>
                                <input type="text"
                                       name="asal_sekolah_pt"
                                       value="{{ old('asal_sekolah_pt', $user->asal_sekolah_pt) }}"
                                       class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                            </div>
                        @elseif($user->role === 'pembimbing_instansi')
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Jabatan / Posisi Kerja
                                </label>
                                <input type="text"
                                       name="jabatan"
                                       value="{{ old('jabatan', $user->jabatan) }}"
                                       class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                            </div>
                        @endif

                        <!-- ALAMAT DOMISILI -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Alamat Domisili Lengkap
                            </label>
                            <textarea name="alamat"
                                      rows="2"
                                      placeholder="Alamat tempat tinggal saat ini..."
                                      class="w-full bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md shadow-blue-100 hover:shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer text-xs sm:text-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM GANTI PASSWORD -->
            <div class="rounded-2xl bg-white shadow-md shadow-slate-100/80 border border-slate-100/50 p-6 md:p-8">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Keamanan & Ganti Password</h2>
                        <p class="text-xs text-slate-400">Pastikan akun Anda menggunakan password yang kuat dan aman</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input type="password"
                               name="current_password"
                               required
                               placeholder="Ketik password lama Anda"
                               class="w-full bg-white border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Password Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="password"
                                   name="password"
                                   required
                                   placeholder="Minimal 8 karakter"
                                   class="w-full bg-white border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Ulangi Password Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   required
                                   placeholder="Ulangi password baru"
                                   class="w-full bg-white border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 rounded-xl py-2.5 px-4 text-xs text-slate-800 transition-all">
                        </div>
                    </div>

                    <div class="flex justify-end pt-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md shadow-purple-100 hover:shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer text-xs sm:text-sm">
                            <i class="fa-solid fa-shield-check"></i>
                            <span>Perbarui Password</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>

<!-- PREVIEW SCRIPT -->
<script>
document.getElementById('fotoProfilInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('avatarPreview');
            const initials = document.getElementById('avatarInitials');
            preview.src = event.target.result;
            preview.classList.remove('hidden');
            if (initials) initials.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
});
</script>

@endsection
