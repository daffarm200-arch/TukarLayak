@extends('layouts.app')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori barang yang tersedia di TukarLayak.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </a>
    </div>

    {{-- Pesan sukses / error --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabel Kategori --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left text-gray-600 uppercase text-xs tracking-wide">
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Nama Kategori</th>
                    <th class="px-6 py-3">Deskripsi</th>
                    <th class="px-6 py-3 text-center">Jumlah Barang</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories as $index => $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">{{ $categories->firstItem() + $index }}</td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-800">{{ $category->name }}</span>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $category->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                        {{ $category->description ?: '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($category->items_count > 0)
                            <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">
                                {{ $category->items_count }} barang
                            </span>
                        @else
                            <span class="inline-block bg-gray-100 text-gray-400 text-xs px-2 py-1 rounded-full">
                                Kosong
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs font-medium px-3 py-1.5 rounded border border-blue-200 hover:border-blue-400 transition">
                                Edit
                            </a>

                            @if($category->items_count > 0)
                                <button disabled
                                    title="Tidak bisa dihapus: masih ada {{ $category->items_count }} barang"
                                    class="text-gray-400 text-xs font-medium px-3 py-1.5 rounded border border-gray-200 cursor-not-allowed">
                                    Hapus
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori \'{{ addslashes($category->name) }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-800 text-xs font-medium px-3 py-1.5 rounded border border-red-200 hover:border-red-400 transition">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <p class="font-medium">Belum ada kategori.</p>
                        <a href="{{ route('admin.categories.create') }}" class="text-green-600 hover:underline text-sm mt-1 inline-block">
                            Tambah kategori pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
