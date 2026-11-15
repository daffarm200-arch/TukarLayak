@extends('layouts.app')

@section('title', 'Manajemen Barang — Admin')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Barang</h1>
        <p class="text-sm text-gray-500 mt-1">Lihat dan pantau seluruh barang yang diposting pengguna.</p>
    </div>

    {{-- Filter & Pencarian --}}
    <form method="GET" action="{{ route('admin.items.index') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Cari --}}
            <div class="md:col-span-2">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari judul atau nama pemilik..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                >
            </div>

            {{-- Kategori --}}
            <div>
                <select name="category"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div>
                <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="">Semua Status</option>
                    <option value="available" {{ $selectedStatus === 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="reserved"  {{ $selectedStatus === 'reserved'  ? 'selected' : '' }}>Dipesan</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            {{-- Jenis --}}
            <div class="flex gap-2">
                <select name="type"
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="">Semua Jenis</option>
                    <option value="free"     {{ $selectedType === 'free'     ? 'selected' : '' }}>Gratis</option>
                    <option value="exchange" {{ $selectedType === 'exchange' ? 'selected' : '' }}>Barter</option>
                </select>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    Cari
                </button>
            </div>
        </div>

        @if($search || $selectedCategory || $selectedStatus || $selectedType)
        <div class="mt-2 text-xs text-gray-500">
            Filter aktif —
            <a href="{{ route('admin.items.index') }}" class="text-green-600 hover:underline">Reset semua filter</a>
        </div>
        @endif
    </form>

    {{-- Tabel Barang --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-100 text-sm text-gray-500">
            Menampilkan <strong class="text-gray-800">{{ $items->total() }}</strong> barang
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left text-gray-600 uppercase text-xs tracking-wide">
                        <th class="px-5 py-3">Barang</th>
                        <th class="px-5 py-3">Pemilik</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3 text-center">Jenis</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Barang --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($item->image_path)
                                    <img src="{{ Storage::url($item->image_path) }}"
                                         alt="{{ $item->title }}"
                                         class="w-10 h-10 rounded-lg object-cover border border-gray-200 flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-semibold text-gray-800 max-w-[180px] truncate">{{ $item->title }}</div>
                                    <div class="text-xs text-gray-400 capitalize">{{ $item->condition }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Pemilik --}}
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-800">{{ $item->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $item->user->email ?? '' }}</div>
                        </td>

                        {{-- Kategori --}}
                        <td class="px-5 py-4 text-gray-600">{{ $item->category->name ?? '-' }}</td>

                        {{-- Jenis --}}
                        <td class="px-5 py-4 text-center">
                            @if($item->type === 'free')
                                <span class="inline-block bg-emerald-100 text-emerald-700 text-xs font-semibold px-2 py-1 rounded-full">Gratis</span>
                            @else
                                <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full">Barter</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4 text-center">
                            @if($item->status === 'available')
                                <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Tersedia</span>
                            @elseif($item->status === 'reserved')
                                <span class="inline-block bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-1 rounded-full">Dipesan</span>
                            @else
                                <span class="inline-block bg-gray-200 text-gray-600 text-xs font-semibold px-2 py-1 rounded-full">Selesai</span>
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">
                            {{ $item->created_at->format('d M Y') }}
                        </td>

                        {{-- Lihat detail --}}
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('items.show', $item) }}"
                               class="text-green-600 hover:text-green-800 text-xs font-medium hover:underline">
                                Lihat →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="font-medium">Tidak ada barang yang ditemukan.</p>
                            @if($search || $selectedCategory || $selectedStatus || $selectedType)
                                <a href="{{ route('admin.items.index') }}" class="text-green-600 hover:underline text-sm mt-1 inline-block">
                                    Hapus filter
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $items->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
