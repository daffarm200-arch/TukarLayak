@extends('layouts.app')

@section('title', 'Katalog Barang')

@section('content')
<div class="space-y-8">

    <!-- Hero / Header Section -->
    <div class="bg-gradient-to-br from-brand-800 via-brand-700 to-emerald-600 rounded-3xl p-8 sm:p-10 text-white shadow-sm relative overflow-hidden">
        <div class="max-w-2xl relative z-10">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm mb-3">
                ♻️ Berbagi & Tukar Barang Layak Pakai
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Katalog TukarLayak</h1>
            <p class="mt-2 text-brand-100 text-sm sm:text-base leading-relaxed">
                Temukan barang layak pakai yang Anda butuhkan secara gratis atau tukarkan dengan barang bermanfaat milik Anda.
            </p>
        </div>
        <div class="absolute -right-12 -bottom-12 w-56 h-56 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Filter & Pencarian Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">

            <!-- Input Pencarian -->
            <div class="sm:col-span-5">
                <label for="q" class="sr-only">Cari barang</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="q"
                        id="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama barang atau deskripsi..."
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                    >
                </div>
            </div>

            <!-- Filter Kategori -->
            <div class="sm:col-span-4">
                <select
                    name="category"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-slate-700 bg-white"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jenis Penawaran -->
            <div class="sm:col-span-3 flex gap-2">
                <select
                    name="type"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-slate-700 bg-white"
                >
                    <option value="">Semua Jenis</option>
                    <option value="exchange" {{ request('type') == 'exchange' ? 'selected' : '' }}>Bertukar</option>
                    <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Gratis</option>
                </select>

                <button
                    type="submit"
                    class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center shadow-sm"
                >
                    Filter
                </button>
            </div>

        </form>
    </div>

    <!-- Grid Daftar Barang -->
    @if ($items->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($items as $item)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition group flex flex-col">

                    <!-- Gambar Barang -->
                    <div class="aspect-video w-full bg-slate-100 relative overflow-hidden flex items-center justify-center">
                        @if ($item->image_path)
                            <img
                                src="{{ asset('storage/' . $item->image_path) }}"
                                alt="{{ $item->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            >
                        @else
                            <!-- Placeholder Gambar jika tidak upload foto -->
                            <div class="flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                <svg class="w-10 h-10 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs">Tidak ada foto</span>
                            </div>
                        @endif

                        <!-- Badge Jenis Penawaran -->
                        <div class="absolute top-3 left-3">
                            @if ($item->type === 'free')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-600 text-white shadow-sm">
                                    🎁 Gratis
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-sm">
                                    🔄 Bertukar
                                </span>
                            @endif
                        </div>

                        <!-- Badge Kondisi -->
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-black/60 text-white backdrop-blur-sm">
                                {{ $item->condition }}
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Barang -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-semibold text-brand-600 uppercase tracking-wider block mb-1">
                                {{ $item->category->name }}
                            </span>
                            <h2 class="font-bold text-slate-900 text-base line-clamp-1 group-hover:text-brand-600 transition">
                                <a href="{{ route('items.show', $item) }}">
                                    {{ $item->title }}
                                </a>
                            </h2>
                            <p class="text-slate-500 text-xs mt-1.5 line-clamp-2 leading-relaxed">
                                {{ $item->description }}
                            </p>
                        </div>

                        <!-- Info Pemilik & Tombol Aksi -->
                        <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-xs text-slate-500">
                                <span class="block text-slate-400 text-[10px]">Pemilik:</span>
                                <span class="font-medium text-slate-700">{{ $item->user->name }}</span>
                            </div>
                            <a
                                href="{{ route('items.show', $item) }}"
                                class="inline-flex items-center text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition"
                            >
                                Detail
                                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $items->links() }}
        </div>
    @else
        <!-- Tampilan Kosong -->
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-3xl mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Belum ada barang di katalog</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Jadilah yang pertama memasang barang layak pakai untuk ditukarkan atau dibagikan secara gratis kepada orang lain.
            </p>
            <div class="mt-6">
                <a
                    href="{{ route('items.create') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl text-sm shadow-sm transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Pasang Barang Sekarang
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
