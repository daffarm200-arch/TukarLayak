@extends('layouts.app')

@section('title', 'Detail Pengguna: ' . $user->name . ' - Admin TukarLayak')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Tombol Kembali -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-purple-600 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Pengguna
        </a>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-2xl border border-purple-200">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        @if($user->isAdmin())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                Administrator
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Pengguna Reguler
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $user->email }}</p>
                    <p class="text-xs text-slate-400 mt-1">Terdaftar sejak {{ $user->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <!-- Security Assurance Note -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs text-slate-500 max-w-xs">
                🔒 <strong>Privasi & Keamanan:</strong> Kredensial rahasia, hash password, dan session token pengguna terlindungi dan tidak ditampilkan pada antarmuka.
            </div>
        </div>

        <!-- 3 Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                <div class="text-2xl font-bold text-slate-900">{{ $totalItems }}</div>
                <div class="text-xs text-slate-500 mt-1">Total Barang Dipasang</div>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $totalSentRequests }}</div>
                <div class="text-xs text-slate-500 mt-1">Permintaan Diajukan (Keluar)</div>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ $totalReceivedRequests }}</div>
                <div class="text-xs text-slate-500 mt-1">Permintaan Diterima (Masuk)</div>
            </div>
        </div>
    </div>

    <!-- 2 Kolom Riwayat: Barang Milik Pengguna & Permintaan yang Diajukan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Barang Milik Pengguna -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                <span>📦 Barang yang Dipasang ({{ $totalItems }})</span>
            </h2>

            @if ($userItems->count() > 0)
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach ($userItems as $item)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-brand-600 transition block">
                                    {{ $item->title }}
                                </a>
                                <div class="text-slate-400 text-[11px] mt-0.5">
                                    Kategori: {{ $item->category->name }} • {{ $item->type === 'free' ? '🎁 Gratis' : '🔄 Barter' }}
                                </div>
                            </div>
                            <div>
                                @if ($item->status === 'available')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                                @elseif ($item->status === 'reserved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Reserved</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Selesai</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pt-3 border-t border-slate-100">
                    {{ $userItems->links() }}
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center italic">Pengguna belum memasang barang apa pun.</p>
            @endif
        </div>

        <!-- Permintaan yang Pernah Diajukan -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                <span>📤 Permintaan yang Diajukan ({{ $totalSentRequests }})</span>
            </h2>

            @if ($userRequests->count() > 0)
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach ($userRequests as $req)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-900">
                                    Mengajukan untuk: <span class="text-brand-700">{{ $req->item->title ?? '[Barang Dihapus]' }}</span>
                                </div>
                                <div class="text-slate-400 text-[11px] mt-0.5">
                                    @if ($req->offeredItem)
                                        Barter: <span class="text-blue-600">{{ $req->offeredItem->title }}</span>
                                    @else
                                        <span>Permintaan Hibah Gratis</span>
                                    @endif
                                    • {{ $req->created_at->format('d/m/Y') }}
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
                <div class="pt-3 border-t border-slate-100">
                    {{ $userRequests->links() }}
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center italic">Pengguna belum pernah mengajukan permintaan.</p>
            @endif
        </div>

    </div>

</div>
@endsection
