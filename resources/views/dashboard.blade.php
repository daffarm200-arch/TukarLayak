@extends('layouts.app')

@section('title', 'Dashboard Pengguna')

@section('content')
<div class="space-y-6">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-brand-700 via-brand-600 to-emerald-600 rounded-3xl p-8 text-white shadow-sm relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm mb-3">
                🌱 Akun Pengguna Aktif
            </span>
            <h1 class="text-3xl font-bold tracking-tight">Halo, {{ $user->name }}!</h1>
            <p class="mt-2 text-brand-100 text-sm leading-relaxed">
                Selamat datang di platform <strong>TukarLayak</strong>. Di sini Anda dapat menukarkan barang layak pakai yang sudah tidak digunakan dengan barang yang Anda butuhkan, mengurangi limbah dan menghemat pengeluaran.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-brand-800 text-xs sm:text-sm font-bold rounded-xl shadow-sm hover:bg-brand-50 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Pasang Barang Baru
                </a>
                <a href="{{ route('items.index') }}" class="inline-flex items-center px-4 py-2 bg-brand-800/40 text-white text-xs sm:text-sm font-semibold rounded-xl backdrop-blur-sm hover:bg-brand-800/60 transition">
                    Lihat Katalog Barang
                </a>
            </div>
        </div>

        <!-- Decorative Circle -->
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Grid Kartu Informasi & Profil -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Kartu Profil Pengguna -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil Akun Saya
            </h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-slate-400 block text-xs">Nama Lengkap</span>
                    <span class="font-semibold text-slate-800">{{ $user->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">Alamat Email</span>
                    <span class="font-semibold text-slate-800">{{ $user->email }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">Peran (Role)</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 mt-0.5">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">Bergabung Sejak</span>
                    <span class="text-slate-600">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Kartu Status Keamanan & Hak Akses -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm md:col-span-2">
            <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Status Hak Akses & Keamanan Sesi
            </h2>
            <div class="space-y-4 text-sm text-slate-600">
                <p>
                    Akun Anda memiliki hak akses <strong>Pengguna Biasa (User)</strong>. Berdasarkan kebijakan keamanan sistem:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                        <div class="font-medium text-slate-800 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                            Pertukaran & Penawaran Barang
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Anda dapat mengajukan permohonan hibah atau bertukar barang dengan pengguna lain secara aman.</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                        <div class="font-medium text-slate-800 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-red-400 mr-2"></span>
                            Proteksi Transaksi & IDOR
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Hanya pemilik barang yang dapat menyetujui, menolak, atau menyelesaikan transaksi pertukaran.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Aktivitas Barang & Permintaan -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Ringkasan Aktivitas Saya
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">

            <a href="{{ route('items.my') }}" class="border border-slate-200 hover:border-brand-300 rounded-xl p-5 text-center transition group bg-slate-50/50 hover:bg-brand-50/20">
                <div class="text-2xl font-bold text-slate-800 group-hover:text-brand-600">{{ $user->items()->count() }}</div>
                <div class="text-xs font-medium text-slate-500 mt-1">Barang Saya</div>
                <span class="inline-block mt-2 text-[10px] text-brand-700 bg-brand-100/60 px-2.5 py-0.5 rounded-full font-semibold">Kelola Barang</span>
            </a>

            <a href="{{ route('requests.incoming') }}" class="border border-slate-200 hover:border-amber-300 rounded-xl p-5 text-center transition group bg-slate-50/50 hover:bg-amber-50/20">
                <div class="text-2xl font-bold text-slate-800 group-hover:text-amber-600">{{ $incomingPendingCount }}</div>
                <div class="text-xs font-medium text-slate-500 mt-1">Permintaan Masuk Pending</div>
                <span class="inline-block mt-2 text-[10px] text-amber-700 bg-amber-100/60 px-2.5 py-0.5 rounded-full font-semibold">Perlu Tanggapan</span>
            </a>

            <a href="{{ route('requests.my') }}" class="border border-slate-200 hover:border-blue-300 rounded-xl p-5 text-center transition group bg-slate-50/50 hover:bg-blue-50/20">
                <div class="text-2xl font-bold text-slate-800 group-hover:text-blue-600">{{ $myPendingCount }}</div>
                <div class="text-xs font-medium text-slate-500 mt-1">Permintaan Saya Pending</div>
                <span class="inline-block mt-2 text-[10px] text-blue-700 bg-blue-100/60 px-2.5 py-0.5 rounded-full font-semibold">Menunggu Respon</span>
            </a>

            <div class="border border-slate-200 rounded-xl p-5 text-center bg-slate-50/50">
                <div class="text-2xl font-bold text-emerald-600">{{ $completedExchangesCount }}</div>
                <div class="text-xs font-medium text-slate-500 mt-1">Pertukaran Berhasil</div>
                <span class="inline-block mt-2 text-[10px] text-emerald-700 bg-emerald-100/60 px-2.5 py-0.5 rounded-full font-semibold">Selesai</span>
            </div>

        </div>
    </div>

</div>
@endsection
