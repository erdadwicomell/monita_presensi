@extends('layouts.app')

@section('page-title', 'Data Pengguna')
@section('title', 'Kelola Seluruh Pengguna')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-slate-100">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
            Data Semua Pengguna Sistem
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Daftar lengkap seluruh entitas akun pengguna terdaftar pada platform MONITA.
        </p>
    </div>

    <!-- DATA TABLE CONTAINER (MODERN LUXURY CARD) -->
    <div class="bg-white rounded-2xl shadow-md shadow-slate-100/80 border border-slate-100/50 p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <!-- THEAD (HEADER TABEL RESMI) -->
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-semibold text-xs tracking-wider uppercase border-b border-slate-100">
                        <th class="py-4 px-6 w-16 text-center">No</th>
                        <th class="py-4 px-6">Nama Pengguna</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6">Peran (Role)</th>
                        <th class="py-4 px-6">Instansi Terkait</th>
                    </tr>
                </thead>

                <!-- TBODY & TR (ZEBRA STRIPING + HOVER LEMBUT) -->
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="py-4 px-6 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs uppercase border border-slate-200/60">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-800 text-sm">
                                        {{ $user->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-slate-600 font-medium">
                                {{ $user->email }}
                            </td>

                            <td class="py-4 px-6">
                                @if($user->role === 'super_admin')
                                    <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 border border-purple-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-crown text-[10px]"></i> Super Admin
                                    </span>
                                @elseif($user->role === 'admin_instansi')
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-user-tie text-[10px]"></i> Admin Instansi
                                    </span>
                                @elseif($user->role === 'pembimbing_instansi')
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-chalkboard-user text-[10px]"></i> Pembimbing
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-user-graduate text-[10px]"></i> Peserta
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-700">
                                    {{ $user->instansi->nama_instansi ?? 'Sistem Pusat' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-users-slash text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada pengguna terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection