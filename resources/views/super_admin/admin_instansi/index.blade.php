@extends('layouts.app')

@section('page-title', 'Admin Instansi')
@section('title', 'Kelola Admin Instansi')

@section('content')

<div class="space-y-6">

    <!-- HEADER & ACTION BAR -->
    <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-8 border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 border border-blue-100">
                <i class="fa-solid fa-user-tie"></i> Hak Akses
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">
                Data Admin Instansi
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Kelola akun administrator penanggung jawab setiap instansi mitra magang.
            </p>
        </div>

        <a href="{{ route('admin-instansi.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all duration-150 transform hover:-translate-y-0.5 self-start md:self-auto cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Admin</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 shadow-xs">
            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm flex-shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="font-medium leading-relaxed">{{ session('success') }}</div>
        </div>
    @endif

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/90 text-slate-500 uppercase tracking-wider font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Nama Administrator</th>
                        <th class="px-6 py-4">Email Login</th>
                        <th class="px-6 py-4">Instansi Terkait</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($admins as $admin)
                        <tr class="odd:bg-white even:bg-slate-50/40 hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm block">
                                    {{ $admin->name }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-600 font-medium">
                                {{ $admin->email }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-700">
                                    {{ $admin->instansi->nama_instansi ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/60 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fa-solid fa-shield text-[10px]"></i>
                                    <span>Admin Instansi</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 text-slate-300 block"></i>
                                <span class="font-medium text-slate-500">Belum ada akun admin instansi yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection