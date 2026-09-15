@extends('layouts.app')

@section('title', 'Detail Perizinan Peserta')
@section('page-title', 'Detail Perizinan')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER & BACK -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Detail Pengajuan Perizinan Peserta
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Tinjau dokumen bukti dan tentukan status persetujuan izin.
            </p>
        </div>

        <a href="{{ route('admin.perizinan.index') }}"
           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-xl transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-white rounded-2xl shadow p-8 space-y-6">

        <!-- STATUS BANNER -->
        <div class="flex items-center justify-between p-4 rounded-xl border
            @if($perizinan->status === 'disetujui') bg-emerald-50 border-emerald-200 text-emerald-800
            @elseif($perizinan->status === 'ditolak') bg-red-50 border-red-200 text-red-800
            @else bg-yellow-50 border-yellow-200 text-yellow-800 @endif">
            <div class="flex items-center gap-3">
                <i class="fa-solid text-xl
                    @if($perizinan->status === 'disetujui') fa-circle-check text-emerald-600
                    @elseif($perizinan->status === 'ditolak') fa-circle-xmark text-red-600
                    @else fa-hourglass-half text-yellow-600 @endif">
                </i>
                <div>
                    <h3 class="font-bold text-base uppercase">
                        Status: {{ $perizinan->status }}
                    </h3>
                    <p class="text-xs opacity-80 mt-0.5">
                        @if($perizinan->status === 'disetujui')
                            Perizinan telah Anda setujui.
                        @elseif($perizinan->status === 'ditolak')
                            Perizinan telah Anda tolak.
                        @else
                            Menunggu keputusan persetujuan dari Admin Instansi.
                        @endif
                    </p>
                </div>
            </div>

            @if($perizinan->disetujui_pada)
                <div class="text-right text-xs">
                    <p class="font-semibold">Diputuskan Oleh:</p>
                    <p>{{ $perizinan->admin->name ?? 'Admin' }} ({{ $perizinan->disetujui_pada->format('d M Y, H:i') }} WIB)</p>
                </div>
            @endif
        </div>

        <!-- DETAIL GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Peserta</label>
                <p class="text-base font-bold text-gray-800 mt-1">
                    {{ $perizinan->user->name }}
                </p>
                <p class="text-xs text-gray-500">{{ $perizinan->user->email }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Jenis Perizinan</label>
                <p class="text-base font-bold text-gray-800 mt-1 capitalize">
                    {{ str_replace('_', ' ', $perizinan->jenis_izin) }}
                </p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Izin</label>
                <p class="text-base font-bold text-gray-800 mt-1">
                    {{ $perizinan->tanggal->format('d F Y') }}
                </p>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rentang Waktu Izin</label>
                <p class="text-base font-bold text-gray-800 mt-1">
                    @if($perizinan->jam_mulai_izin)
                        {{ substr($perizinan->jam_mulai_izin, 0, 5) }} - {{ substr($perizinan->jam_selesai_izin, 0, 5) }} WIB
                    @else
                        <span class="text-gray-500 font-normal">Izin Seharian Penuh</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- ALASAN -->
        <div class="pt-4 border-t border-gray-100">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Alasan Pengajuan</label>
            <div class="mt-2 bg-gray-50 p-4 rounded-xl text-gray-700 text-sm leading-relaxed border border-gray-100">
                {{ $perizinan->alasan }}
            </div>
        </div>

        <!-- BUKTI LAMPIRAN -->
        <div class="pt-4 border-t border-gray-100">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bukti Lampiran (Surat / Foto)</label>
            <div class="mt-3">
                @if($perizinan->bukti)
                    @php
                        $ext = pathinfo($perizinan->bukti, PATHINFO_EXTENSION);
                    @endphp

                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                        <div class="space-y-3">
                            <img src="{{ asset('storage/' . $perizinan->bukti) }}"
                                 alt="Bukti Perizinan"
                                 class="max-h-96 rounded-xl border object-contain shadow-sm">
                            <a href="{{ asset('storage/' . $perizinan->bukti) }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 text-sm text-blue-600 font-medium hover:underline">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Gambar Ukuran Penuh
                            </a>
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 rounded-xl border flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Dokumen Lampiran (PDF)</p>
                                    <p class="text-xs text-gray-400">Klik untuk membuka atau mengunduh</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $perizinan->bukti) }}"
                               target="_blank"
                               class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                                Unduh / Lihat PDF
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-red-500 text-sm italic">Peserta tidak mengunggah dokumen bukti lampiran.</p>
                @endif
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        @if($perizinan->status === 'menunggu')
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <form action="{{ route('admin.perizinan.tolak', $perizinan->id_perizinan) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menolak perizinan ini?');">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow transition">
                        <i class="fa-solid fa-xmark"></i> Tolak Perizinan
                    </button>
                </form>

                <form action="{{ route('admin.perizinan.setujui', $perizinan->id_perizinan) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menyetujui perizinan ini?');">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow transition">
                        <i class="fa-solid fa-check"></i> Setujui Perizinan
                    </button>
                </form>
            </div>
        @endif

    </div>

</div>

@endsection