@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Admin TukarLayak')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-purple-600 hover:underline">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau seluruh akun pengguna dan administrator yang terdaftar di sistem</p>
        </div>
        <div class="text-xs bg-purple-50 text-purple-700 px-3.5 py-2 rounded-xl font-semibold border border-purple-200">
            Total Pengguna: {{ $users->total() }} Akun
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">

            <div class="sm:col-span-8">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari berdasarkan nama atau alamat email pengguna..."
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    >
                </div>
            </div>

            <div class="sm:col-span-4 flex gap-2">
                <select
                    name="role"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-slate-700"
                >
                    <option value="">Semua Peran (Role)</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User (Pengguna Biasa)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Administrator)</option>
                </select>

                <button
                    type="submit"
                    class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-semibold transition shadow-sm"
                >
                    Cari
                </button>
            </div>

        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Pengguna</th>
                        <th class="py-4 px-6">Peran (Role)</th>
                        <th class="py-4 px-6 text-center">Barang Dimiliki</th>
                        <th class="py-4 px-6 text-center">Permintaan Diajukan</th>
                        <th class="py-4 px-6">Bergabung</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($users as $userItem)
                        <tr class="hover:bg-slate-50/80 transition">

                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs border border-slate-200">
                                        {{ strtoupper(substr($userItem->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $userItem->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $userItem->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                @if($userItem->isAdmin())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                        admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        user
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-center font-semibold text-slate-700">
                                {{ $userItem->items_count }}
                            </td>

                            <td class="py-4 px-6 text-center font-semibold text-slate-700">
                                {{ $userItem->sent_requests_count }}
                            </td>

                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $userItem->created_at->format('d/m/Y') }}
                            </td>

                            <td class="py-4 px-6 text-right">
                                <a
                                    href="{{ route('admin.users.show', $userItem) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-700 text-xs font-semibold rounded-lg transition"
                                >
                                    Detail &rarr;
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                Tidak ditemukan data pengguna yang cocok dengan pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
