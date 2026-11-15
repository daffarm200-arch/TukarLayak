@extends('layouts.app')

@section('title', 'Dashboard Administrator')

@section('content')
<div class="space-y-8">

    <!-- Admin Welcome Header -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-brand-900 rounded-3xl p-8 text-white shadow-sm relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-200 border border-purple-400/30 backdrop-blur-sm mb-3">
                🛡️ Hak Akses: Administrator Sistem
            </span>
            <h1 class="text-3xl font-extrabold tracking-tight">Panel Pengelola TukarLayak</h1>
            <p class="mt-2 text-slate-300 text-sm leading-relaxed">
                Selamat bertugas, <strong>{{ $admin->name }}</strong>. Pantau metrik data riil pengguna, status inventaris barang, perputaran transaksi barter, serta laporan aktivitas sistem secara komprehensif.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-brand-500/20 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Quick Navigation Pills -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 text-white shadow-sm">
            📊 Ringkasan Metrik
        </a>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition">
            👥 Manajemen Pengguna ({{ $totalUsers }})
        </a>
        <a href="{{ route('admin.items.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition">
            📦 Inventaris Barang ({{ $totalItems }})
        </a>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition">
            📁 Kategori ({{ $totalCategories }})
        </a>
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition">
            📈 Laporan & Ekspor CSV
        </a>
    </div>

    <!-- 6 Metrik Utama Wajib (Data Riil Database) -->
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3">Statistik Utama Sistem</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

            <!-- 1. Total Pengguna -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Pengguna</span>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">👥</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-slate-900">{{ $totalUsers }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $totalReguler }} reguler • {{ $totalAdmin }} admin</div>
                </div>
            </div>

            <!-- 2. Barang Tersedia -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Barang Tersedia</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">🟢</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-emerald-600">{{ $itemsAvailable }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Siap ditukar / diambil</div>
                </div>
            </div>

            <!-- 3. Barang Reserved -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Barang Dipesan</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">⏳</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-amber-600">{{ $itemsReserved }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Dalam proses barter</div>
                </div>
            </div>

            <!-- 4. Barang Completed -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Barang Selesai</span>
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">✓</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-slate-800">{{ $itemsCompleted }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Berhasil diserahkan</div>
                </div>
            </div>

            <!-- 5. Permintaan Pending -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Permintaan Pending</span>
                    <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">📩</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-purple-700">{{ $requestsPending }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Menunggu konfirmasi</div>
                </div>
            </div>

            <!-- 6. Pertukaran Selesai -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Pertukaran Selesai</span>
                    <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">🤝</span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold text-brand-700">{{ $exchangesCompleted }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Transaksi tuntas</div>
                </div>
            </div>

        </div>
    </div>

    <!-- Aktivitas Terbaru (2 Kolom: Permintaan Terbaru & Barang Terbaru) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. Permintaan Transaksi Terbaru -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <span class="mr-2">🔄</span> Permintaan & Transaksi Terbaru
                </h2>
                <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">
                    Lihat Laporan &rarr;
                </a>
            </div>

            @if ($recentRequests->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach ($recentRequests as $req)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <div class="font-bold text-slate-800">
                                    {{ $req->requester->name }}
                                    <span class="text-slate-400 font-normal">meminta</span>
                                    <span class="text-brand-700 font-semibold">{{ $req->item->title }}</span>
                                </div>
                                <div class="text-slate-400 text-[11px]">
                                    @if ($req->offeredItem)
                                        Tawaran barter: <span class="text-blue-600 font-medium">{{ $req->offeredItem->title }}</span>
                                    @else
                                        <span class="text-emerald-600 font-medium">Permintaan Hibah Gratis</span>
                                    @endif
                                    • {{ $req->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div>
                                @if ($req->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                @elseif ($req->status === 'accepted')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Accepted</span>
                                @elseif ($req->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                @elseif ($req->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Cancelled</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-xs text-slate-400 italic">Belum ada aktivitas permintaan.</p>
            @endif
        </div>

        <!-- 2. Barang Terbaru yang Ditambahkan -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <span class="mr-2">📦</span> Barang Terbaru Dipasang
                </h2>
                <a href="{{ route('admin.items.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">
                    Kelola Semua ({{ $totalItems }}) &rarr;
                </a>
            </div>

            @if ($recentItems->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach ($recentItems as $item)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-brand-600 transition block">
                                    {{ $item->title }}
                                </a>
                                <div class="text-slate-400 text-[11px]">
                                    Kategori: <span class="text-slate-600">{{ $item->category->name }}</span> •
                                    Pemilik: <span class="text-slate-600 font-medium">{{ $item->user->name }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($item->status === 'available')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700">Tersedia</span>
                                @elseif ($item->status === 'reserved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Reserved</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Selesai</span>
                                @endif
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-xs text-slate-400 italic">Belum ada barang diunggah.</p>
            @endif
        </div>

    </div>

</div>
@endsection
